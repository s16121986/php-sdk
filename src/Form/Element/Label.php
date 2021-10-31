<?php

namespace Gsdk\Form\Element;

class Label extends Xhtml {

	protected $options = [
		'readable' => false
	];

	protected $attributes = ['for'];

	public function isSubmittable() {
		return false;
	}

	public function getHtml(): string {
		return '<span' . $this->attributes . '>' . $this->getValue() . '</span>';
	}

}
