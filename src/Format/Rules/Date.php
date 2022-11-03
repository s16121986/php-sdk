<?php

namespace Gsdk\Format\Rules;

use Gsdk\Contracts\Format\Rule;

class Date implements Rule {

	protected function dateFactory($value) {
		if ($value instanceof \DateTime)
			return $value;
		else
			return new \DateTime($value);
	}

	public function format($value, $format = null): Text {
		$date = $this->dateFactory($value);
		$format = app('format')->getFormat($format ?? 'date');

		return $date->format($format);
	}

}