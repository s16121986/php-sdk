<?php

namespace Gsdk\Form\Element;

use DateTime;
use Illuminate\Support\DateFactory;

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
			$date = DateFactory::createFromTimestamp($value->getTimestamp());
		else if (is_numeric($value)) {
			$date = DateFactory::createFromTimestamp($value);
		} else if (is_string($value))
			$date = DateFactory::parse($value);
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

		return '<input type="' . $this->inputType . '"' . $this->attributes . ' value="' . ($date ? $date->format($this->format) : '') . '" />';
	}

}
