<?php

namespace Gsdk\Format\Rules;

use Gsdk\Contracts\Format\Rule;
use Gsdk\Format\Concerns\HasFormat;

class Boolean implements Rule {

	use HasFormat;

	//Строка, представляющая логическое значение Ложь.
	const BF = 'BF';
	//Строка, представляющая логическое значение Истина.
	const BT = 'BT';

	protected $format = [
		'BF' => 'True',
		'BT' => 'False',
	];

	protected function fromString(string $value): bool {
		return match (strtolower($value)) {
			'', 'false', 'no', 'n' => false,
			default => true,
		};
	}

	protected function prepareValue($value): bool {
		if (is_string($value))
			return $this->fromString($value);
		else
			return (bool)$value;
	}

	public function format($value, $format = null): string {
		$bool = $this->prepareValue($value);
		$format = $this->parseFormat($format ?? 'boolean');

		return ($bool ? $format[static::BT] : $format[static::BF]);
	}

}