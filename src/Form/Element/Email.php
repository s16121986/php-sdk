<?php

namespace Gsdk\Form\Element;

class Email extends AbstractInput {

	protected $options = [
		'inputType' => 'email'
	];
	protected $attributes = ['maxlength', 'autocomplete', 'minlength', 'multiple', 'pattern', 'placeholder', 'readonly', 'size'];

	public function checkValue($value) {
		return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
	}

}
