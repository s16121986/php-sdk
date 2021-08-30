<?php

namespace Gsdk\Form\Element;

abstract class AbstractInput extends Xhtml {

	protected $options = [
		'inputType' => 'text'
	];

	protected function init() {
		$this->attributes->allow('inputmode');
	}

	public function getHtml() {
		return '<input type="' . $this->inputType . '"'
			. $this->attributes
			. ' value="' . self::escape($this->getValue()) . '" />'
			. $this->attributes->getHtml();
	}

}
