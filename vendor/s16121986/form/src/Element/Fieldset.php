<?php

namespace Gsdk\Form\Element;

use Gsdk\Form\Fieldset as AbstractFieldset;
use Gsdk\Form\Element;

class Fieldset extends AbstractFieldset {

	protected $attributes = ['readonly'];
	protected $rendered = false;

	public function __construct($name, $options = []) {
		$options['name'] = $name;
		$options['type'] = 'fieldset';
		$options['render'] = true;
		parent::__construct($options);
	}

	public function render() {
		$html = '<fieldset>';
		if ($this->legend)
			$html .= '<legend>' . $this->legend . '</legend>';

		$html .= parent::render();
		$html .= '</fieldset>';
		$this->rendered = true;
		return $html;
	}

	public function isRendered($flag = null) {
		if (null === $flag)
			return $this->rendered;

		$this->rendered = (bool)$flag;

		return $this;
	}

	public function getInputName() {
		if (!isset($this->options['inputName'])) {
			$name = $this->name;
			if ($this->parent && $this->parent->name) {
				switch (true) {
					case $this->parent instanceof Fieldset:
					case $this->parent instanceof Element:
						$name = $this->parent->getInputName() . '[' . $name . ']';
						break;
					default:
						$name = $this->parent->getName() . '[' . $name . ']';
				}
			}
			$this->options['inputName'] = $name;
		}

		return $this->options['inputName'];
	}

	public function isFileUpload() {
		return $this->hasUpload();
	}

}
