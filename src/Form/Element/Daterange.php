<?php

namespace Gsdk\Form\Element;

use Gsdk\Stdlib\Format;
use Gsdk\Stdlib\DateTime;

class Daterange extends Date {

	const delimiter = ' - ';

	protected function prepareValue($value) {
		if (!is_string($value) || !$value)
			return null;

		$dates = explode(self::delimiter, $value);
		return [
			'valueFrom' => $dates[0] ? DateTime::serverDate($dates[0]) : null,
			'valueTo' => (isset($dates[1]) && $dates[1]) ? DateTime::serverDate($dates[1]) : null
		];
	}

	public function isEmpty(): bool {
		return empty($this->value) || !($this->value['valueFrom'] && $this->value['to']);
	}

	public function getHtml() {
		$d = '';
		if ($this->getValue()) {
			$a = [];
			foreach ($this->getValue() as $date) {
				if ($date)
					$a[] = DateTime::factory($date)->format('date');
			}
			$d = implode(self::delimiter, $a);
		}

		return '<input type="' . $this->inputType . '"' . $this->attrToString() . ' value="' . $d . '" />';
	}

}
