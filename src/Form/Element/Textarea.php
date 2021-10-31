<?php

namespace Gsdk\Form\Element;

class Textarea extends Xhtml {

	protected $options = [
		'stripTags' => true
	];

	protected $attributes = ['placeholder', 'maxlength', 'required', 'autofocus', 'readonly'];

	protected function prepareValue($value) {
		if (is_scalar($value))
			$value = (string)$value;
		else
			return '';

		if ($this->stripTags)
			$value = htmlspecialchars($value, ENT_NOQUOTES);

		return trim($value);
	}

	public function getHtml(): string {
		return '<textarea' . $this->attributes . '>' . $this->getValue() . '</textarea>';
	}

}
