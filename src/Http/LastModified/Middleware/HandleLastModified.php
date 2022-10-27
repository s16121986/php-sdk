<?php

namespace Gsdk\Http\LastModified\Middleware;

use Gsdk\Support\Facades\LastModified;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;
use Closure;

class HandleLastModified {

	public function handle($request, Closure $next) {
		$response = $next($request);

		if (is_null($lastModifiedAt = LastModified::get()))
			return $response;

		if (!in_array(strtoupper($request->getMethod()), ['GET', 'HEAD']))
			return $response;

		if ($response instanceof Response)
			$response->header('Last-Modified', static::dateFormat($lastModifiedAt));

		$requestDateTimeString = request()->header('If-Modified-Since');
		if (!is_string($requestDateTimeString))
			return $response;

		$modifiedSince = Carbon::createFromTimeString($requestDateTimeString, 'GMT');
		if ($lastModifiedAt->getTimestamp() < $modifiedSince->getTimestamp())
			abort(304);

		return $response;
	}

	private static function dateFormat($date) {
		return $date->format(DATE_RFC7231);
	}

}
