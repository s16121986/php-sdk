<?php

namespace Corelib\Form\Element;

use Corelib\Stdlib\Format;
use Corelib\Stdlib\DateTime;

class Daterange extends Date {

	const delimeter = ' - ';

	protected function prepareValue($value) {
		if (!is_string($value) || !$value)
			return null;

		$dates = explode(self::delimeter, $value);
		return [
			'valueFrom' => $dates[0] ? DateTime::serverDate($dates[0]) : null,
			'valueTo' => (isset($dates[1]) && $dates[1]) ? DateTime::serverDate($dates[1]) : null
		];
	}

	public function getHtml() {
		$d = '';
		if ($this->getValue()) {
			$a = [];
			foreach ($this->getValue() as $date) {
				if ($date)
					$a[] = DateTime::factory($date)->format('date');
			}
			$d = implode(self::delimeter, $a);
		}

		return '<input type="' . $this->inputType . '"' . $this->attrToString() . ' value="' . $d . '" />';
	}

}
