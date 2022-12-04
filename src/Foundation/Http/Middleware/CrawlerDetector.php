<?php

namespace Gsdk\Foundation\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class CrawlerDetector {

	private static array $bots = [
		'bot',
		'slurp',
		'crawler',
		'spider',
		'curl',
		'facebook',
		'fetch',
		'okhttp',
		'Chrome-Lighthouse'
	];

	public function handle(Request $request, Closure $next) {
		$userAgent = $request->header('User-Agent');

		$request->attributes->add(['isCrawlerDetected' => $this->detectBot($userAgent)]);

		return $next($request);
	}

	private function detectBot($userAgent): bool {
		foreach (self::$bots as $bot) {
			if (stripos($userAgent, $bot) !== false)
				return true;
		}

		return false;
	}

}
