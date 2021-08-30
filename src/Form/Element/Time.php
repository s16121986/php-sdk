<?php

namespace Gsdk\Form\Element;

class Time extends AbstractInput {

	protected $options = [
		'inputType' => 'time'
	];

	protected $attributes = ['autocomplete', 'list', 'readonly', 'step'];

	public function checkValue($value) {
		return ('' === $value || preg_match('/\d{2}:\d{2}/', $value));
	}

	protected function prepareValue($value) {
		if ($value && preg_match('/\d{2}:\d{2}/', $value))
			return $value;

		return null;
	}

}
