<?php

namespace Gsdk\Form\Element;

class Color extends AbstractInput {

	protected $options = [
		'inputType' => 'color'
	];
	protected $attributes = ['list', 'autocomplete'];

	public function checkValue($value) {
		return preg_match('/^#[0-9abcdef]{6}$/i', $value);
	}

}
