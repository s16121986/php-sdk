<?php

namespace Gsdk\Predicator\Predicate;

use DateTime;

class DatePredicate extends AbstractPredicate {

	protected array $options = [
		'dateFractions' => 'datetime'
	];

	public function formatValue($value) {
		return match ($this->dateFractions) {
			'datetime' => static::formatDate($value, 'Y-m-d H:i:s'),
			'time' => static::formatDate($value, 'H:i:s'),
			default => static::formatDate($value, 'Y-m-d'),
		};
	}

	/*protected function getLowerBound($value) {
		switch ($this->dateFractions) {
			case 'datetime':
				return static::formatDate($this->getValueFrom($value), 'Y-m-d 00:00:00');
			case 'date':
				return static::formatDate($this->getValueFrom($value), 'Y-m-d');
			case 'time':
				return static::formatDate($this->getValueFrom($value), 'H:i:s');
		}
	}

	protected function getUpperBound($value) {
		return static::formatDate($this->getValueTo($value), 'Y-m-d 23:59:59');
	}*/

	private static function formatDate($value, $format): ?string {
		$dateValue = static::dateFactory($value);

		return $dateValue ? $dateValue->format($format) : null;
	}

	private static function dateFactory($value): ?DateTime {
		if (null === $value)
			return null;
		else if ($value instanceof DateTime)
			return $value;
		else
			return new DateTime($value);
	}

}
