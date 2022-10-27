<?php

namespace Gsdk\Http\LastModified\Middleware;

use Gsdk\Support\Facades\LastModified;
use Illuminate\Support\Carbon;
use Closure;

class HandleLastModified {

	public function handle($request, Closure $next) {
		$response = $next($request);

		if (is_null($lastModifiedAt = LastModified::get()))
			return $response;

		if (!in_array(strtoupper($request->getMethod()), ['GET', 'HEAD']))
			return $response;

		if ($response instanceof Response)
			$response->header('Last-Modified', $lastModifiedAt->toRfc7231String());

		$requestDateTimeString = request()->header('If-Modified-Since');
		if (!is_string($requestDateTimeString))
			return $response;

		$modifiedSince = Carbon::createFromTimeString($requestDateTimeString, 'GMT');
		if ($lastModifiedAt->lessThanOrEqualTo($modifiedSince))
			abort(304);

		return $response;
	}

}
