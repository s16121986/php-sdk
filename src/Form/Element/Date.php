<?php

namespace Gsdk\Form\Element;

use Gsdk\Format;

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
		if ($value) {
			if (is_numeric($value))
				$t = $value;
			else
				$t = strtotime($value);

			if ($t > 0) {
				$value = date('Y-m-d', $t);
				if ($this->max && ($value > $this->max))
					return null;

				if ($this->min && ($value < $this->min))
					return null;

				return $value;
			}
		} else if (false !== $this->emptyValue && $this->emptyValue === $value)
			return $value;

		return null;
	}

	public function getHtml(): string {
		$d = '';
		if ($this->getValue()) {
			$t = strtotime($this->prepareValue($this->getValue()));
			if ($t > 0)
				$d = Format::date($t);
		}

		return '<input type="' . $this->inputType . '"' . $this->attrToString() . ' value="' . $d . '" />';
	}

}
