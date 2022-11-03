<?php

namespace Gsdk\Format\Concerns;

trait HasFormat {

	protected function parseFormat(?string $formatString) {
		$format = $this->format;
		if (null === $formatString)
			return $format;

		$formatString = app('format')->getFormat($formatString);

		$flagKeys = array_keys($format);

		$parts = explode(';', trim($formatString, ';'));

		foreach ($parts as $i => $part) {
			if (str_contains($part, '=')) {
				[$flag, $value] = explode('=', $part);

				if (isset($format[$flag]))
					$format[$flag] = $value;
			} else if (isset($flagKeys[$part]))
				$format[$flagKeys[$part]] = true;
		}

		return $format;
	}

}
