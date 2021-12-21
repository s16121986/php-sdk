<?php
//https://github.com/Rukudzo/laravel-image-renderer
namespace Gsdk\Filesystem;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends BaseController {

	protected function notModified(Request $request, File $file, array $options = []) {
		return in_array($this->hash($file, $options), $request->getETags());
	}

	/**
	 * Get the headers to attach to the response.
	 *
	 * @param string $path
	 * @return array
	 */
	protected function responseHeaders(File $file, array $options = []) {
		$cacheControl =
			(config('renderer.cache.public') ? 'public' : 'private') .
			',max-age=' . config('renderer.cache.duration');

		return [
			'Content-Type' => $file->mime_type,
			'Cache-Control' => $cacheControl,
			'ETag' => $this->getETag($file, $options),
		];
	}

	/**
	 * Get the E-Tag from the last modified date of the file.
	 *
	 * @param string $path
	 * @return string
	 */
	protected function getETag(File $file, array $options = []) {
		return $this->hash($file, $options);
	}

	/**
	 * Get an MD5 hash of the files last modification time and the query string.
	 *
	 * @param string $path
	 * @param array $options
	 * @return string
	 */
	protected function hash(File $file, array $options = []) {
		$query = http_build_query($options);

		return md5($file->lastModified() . '?' . $query);
	}

	/**
	 * Response for missing files.
	 *
	 * @param Request $request
	 * @param string $filename
	 */
	protected function sendMissingFileResponse(Request $request, ?File $file) {
		$message = 'File ' . ($file ? $file->name : '') . ' was not found.';

		throw new NotFoundHttpException($message);
	}

	/**
	 * Get a rendered image response.
	 *
	 * @param string $path
	 * @param array $options
	 * @return \Illuminate\Http\Response
	 */
	protected function render(File $file, array $options = []) {
		//$make = ImageRenderer::render($path, $options);

		$response = Response::make($file->content());
		$response->headers->add($this->responseHeaders($file, $options));

		return $response;
	}

	/**
	 * Store a new user.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function file(Request $request, $guid) {
		$file = File::findByGuid($guid);

		if (!$file || !$file->exists())
			$this->sendMissingFileResponse($request, $file);

		if ($this->notModified($request, $file, $request->input()))
			return Response::make()->setNotModified();

		return $this->render($file, $request->input());
	}

	public function image(Request $request, $guid) {
		return $this->file($request, $guid);
	}

	public function uploads(Request $request, $path) {
		$storage = Storage::disk('upload');
		$filename = $storage->path($path);
		if (!file_exists($filename) || !is_file($filename))
			return abort(404);

		$response = Response::make(file_get_contents($filename));
		$response->headers->add([
			'Content-Type' => $storage->getMimeType($path)
		]);

		return $response;
	}

}
