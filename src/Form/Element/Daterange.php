<?php

namespace Gsdk\Form\Element;

use Gsdk\DateTime;

class Daterange extends Date {

	const delimiter = ' - ';

	protected function prepareValue($value) {
		if (!is_string($value) || !$value)
			return null;

		$dates = explode(self::delimiter, $value);
		return [
			'valueFrom' => $dates[0] ? (new DateTime($dates[0]))->format('Y-m-d') : null,
			'valueTo' => (isset($dates[1]) && $dates[1]) ? (new DateTime($dates[1]))->format('Y-m-d') : null
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
					$a[] = DateTime::factory($date)->format('date');
			}
			$d = implode(self::delimiter, $a);
		}

		return '<input type="text"'
			. $this->attributes . ' value="' . $d . '" />'
			. $this->attributes->getHtml();
	}

}
