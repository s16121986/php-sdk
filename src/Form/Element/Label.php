<?php
namespace Form\Element;

class Label extends Xhtml{

	protected $options = [
		'readable' => false
	];

	public function isSubmittable() {
		return false;
	}

	public function getHtml() {
		return '<span' . $this->attrToString() . '>' . $this->getValue() . '</span>';
	}

}
