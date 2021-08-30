<?php

namespace Gsdk\Form\Element;

class Number extends AbstractInput {

	protected $options = [
		'inputType' => 'number',
		'allowZero' => true,
		'fractionDigits' => 0,
		'nonnegative' => false
	];

	protected $attributes = ['min', 'max', 'readonly', 'step', 'autocomplete', 'list'];

	public function checkValue($value) {
		$pv = $this->prepareValue($value);
		if ($pv === null)
			return true;

		if ($this->nonnegative && $pv < 0)
			return false;

		if (false === $this->allowZero && $pv == 0)
			return false;

		return parent::checkValue($pv);
	}

	protected function prepareValue($value) {
		if (self::isNullValue($value))
			return null;

		if (is_string($value))
			$value = str_replace([',', ' '], '', $value);

		return ($this->fractionDigits ? (float)$value : (int)$value);
	}

	public function isEmpty() {
		return (0 !== $this->value && empty($this->value));
	}

	private static function isNullValue($value) {
		return ('' === $value || null === $value);
	}

}
