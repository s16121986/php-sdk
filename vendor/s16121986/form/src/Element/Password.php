<?php

namespace Gsdk\Form\Element;

class Password extends AbstractInput {

	protected $options = [
		'inputType' => 'password'
	];
	protected $attributes = ['autocomplete', 'maxlength', 'minlength', 'pattern', 'placeholder', 'readonly', 'required', 'size'];

	public function checkValue($value) {
		return !empty($value);
	}

}
