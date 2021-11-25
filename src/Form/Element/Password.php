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

	public function getHtml(): string {
		return '<input type="' . $this->inputType . '"' . $this->attributes . ' />';
	}

}
