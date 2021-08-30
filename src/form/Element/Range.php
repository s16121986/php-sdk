<?php

namespace Gsdk\Form\Element;

class Range extends AbstractInput {

	protected $options = [
		'inputType' => 'range'
	];
	protected $attributes = ['autocomplete', 'list', 'max', 'min', 'step'];

}
