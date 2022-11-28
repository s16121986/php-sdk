<?php

namespace Gsdk\Format\Rules;

use Gsdk\Contracts\Format\Rule;
use Illuminate\Support\DateFactory;

class Date implements Rule {

	protected static function dateFactory($date) {
		$factory = new DateFactory();
		if ($date instanceof \DateTime)
			return $factory->createFromTimestamp($date->getTimestamp());
		else if (is_numeric($date))
			$date = $factory->createFromTimestamp($date);
		else if (is_string($date))
			$date = $factory->parse($date);
		else
			return null;

		$date->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		return $date;
	}

	public function format($value, $format = null): string {
		$date = $this->dateFactory($value);
		$format = app('format')->getFormat($format ?? 'date');

		return $date->format($format);
	}

}
