<?php

namespace Gsdk\Form\Element;

class Hidden extends AbstractInput {

	protected $options = [
		'inputType' => 'hidden',
		'nullValue' => ''
	];

	protected function prepareValue($value) {
		if ($this->nullValue === $value)
			$value = null;
		return parent::prepareValue($value);
	}

}
