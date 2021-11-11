<?php

namespace Gsdk\Form\Element;

use DateTime;

class Date extends AbstractInput {

	protected $options = [
		'inputType' => 'date',
		'max' => null,
		'min' => null,
		'step' => 1,
		'format' => 'd.m.Y',
		'autocomplete' => 'off',
		'emptyValue' => false
	];
	protected $attributes = ['min', 'max', 'step'];

	protected function prepareValue($value) {
		if (empty($value))
			return null;

		if ($value instanceof DateTime)
			$date = $value;
		else if (is_numeric($value)) {
			$date = new DateTime();
			$date->setTimestamp($value);
		} else if (is_string($value))
			$date = new DateTime($value);
		else
			return null;

		$Ymd = $date->format('Y-m-d');
		if ($this->max && ($Ymd > $this->max))
			return null;

		if ($this->min && ($Ymd < $this->min))
			return null;

		return $date;
	}

	public function getHtml(): string {
		$date = $this->getValue();

		return '<input type="' . $this->inputType . '"' . $this->attributes . ' value="' . ($date ? $date->format('Y-m-d') : '') . '" />';
	}

}
