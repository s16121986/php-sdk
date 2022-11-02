<?php

namespace Gsdk\Foundation\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Closure;

class SetClientTimeZone {

	const TIMEZONE_DEFAULT = 'UTC';
	const COOKIE_NAME = 'workspace';

	public function handle(Request $request, Closure $next) {
		try {
			date_default_timezone_set($this->getClientTimezone());
		} catch (\ErrorException $e) {
			date_default_timezone_set(static::TIMEZONE_DEFAULT);
		}

		$request->merge(['timezone' => date_default_timezone_get()]);

		$this->bootTimezone();

		return $next($request);
	}

	protected function bootTimezone() {
		DB::update('SET time_zone = ?', [date('P')]);
	}

	private function getClientTimezone() {
		return Cookie::get(static::COOKIE_NAME) ?? static::TIMEZONE_DEFAULT;
	}

}