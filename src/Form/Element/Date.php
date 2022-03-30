<?php

namespace Gsdk\Form\Element;

use DateTime;
use Illuminate\Support\Facades\Date as DateFacade;
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

		$date = new DateFacade($value);

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
