<?php

namespace Gsdk\Form\Element;

use DateTime;

class Daterange extends Date {

	const delimiter = ' - ';

	protected $options = [
		'pattern' => '',
		'format' => 'd.m.Y',
		'inputType' => 'daterange'
	];

	protected function prepareValue($value) {
		if (!is_string($value) || !$value)
			return null;

		$dates = explode(self::delimiter, $value);
		return [
			'valueFrom' => $dates[0] ? new DateTime($dates[0]) : null,
			'valueTo' => (isset($dates[1]) && $dates[1]) ? new DateTime($dates[1]) : null
		];
	}

	public function isEmpty(): bool {
		return empty($this->value) || !($this->value['valueFrom'] || $this->value['valueTo']);
	}

	public function getHtml(): string {
		$d = '';
		if ($this->getValue()) {
			$a = [];
			foreach ($this->getValue() as $date) {
				if ($date)
					$a[] = $date->format($this->format);
			}
			$d = implode(self::delimiter, $a);
		}

		return '<input type="' . $this->inputType . '"'
			. $this->attributes . ' value="' . $d . '" />'
			. $this->attributes->getHtml();
	}

}
