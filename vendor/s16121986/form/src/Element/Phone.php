<?php

namespace Gsdk\Form\Element;

class Phone extends AbstractInput {

	protected $options = [
		'inputType' => 'tel'
	];
	protected $attributes = ['autocomplete', 'maxlength', 'minlength', 'pattern', 'placeholder', 'readonly', 'required', 'size'];

}
