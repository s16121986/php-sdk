<?php

namespace Gsdk\Form\Element;

class Url extends Text {

	protected $options = [
		'inputType' => 'url'
	];

	public function checkValue($value) {
		return '' === $value || (bool)filter_var($value, FILTER_VALIDATE_URL);
		is_sc
	}


}
