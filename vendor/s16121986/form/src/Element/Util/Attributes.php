<?php

namespace Corelib\Form\Element\Util;

class Attributes {

	protected $element;
	protected $attributes = [];

	public function __construct($element, $allowed = null) {
		$this->element = $element;
		if ($allowed)
			$this->attributes = $allowed;
	}

	public function allow($name) {
		$this->attributes[] = $name;
	}

	public function getHtml() {
		$html = '';
		foreach ($this->attributes as $k) {
			$html .= self::getAttributeHtml($this->element, $k);
		}
		return $html;
	}

	public function __toString() {
		$element = $this->element;

		$attr = [];

		$attr[] = 'name="' . $element->getInputName() . '"';

		foreach (array_merge(['id', 'class'], $this->attributes) as $k) {
			$value = self::getAttributeValue($element, $k);
			if (null === $value)
				continue;

			$attr[] = $k . '="' . $value . '"';
		}

		if ($element->disabled)
			$attr[] = 'disabled="disabled"';

		if ($element->attr)
			$attr = $element->attr;

		return ' ' . implode(' ', $attr);
	}

	private static function getAttributeValue($element, $name) {
		$value = $element->$name;

		if (null === $value)
			return null;

		switch ($name) {
			case 'checked':
			case 'disabled':
			case 'readonly':
			case 'multiple':
			case 'required':
				return $value ? $name : null;
			case 'minlength':
			case 'maxlength':
			case 'size':
			case 'step':
			case 'tabindex':
				return (int)$value;
			case 'autocomplete':
				return ($value && $value !== 'off') ? 'on' : 'off';
			case 'inputmode':
				static $values = ['none', 'text', 'tel', 'url', 'email', 'numeric', 'decimal', 'search'];
				return in_array($value, $values) ? $value : null;
			case 'list':
				if (is_string($value))
					return $value;
				else if (is_array($value)) {
					return self::getDatalistId($element);
				}
			default:
				return $value;
		}
	}

	private static function getAttributeHtml($element, $name) {
		$value = $element->$name;

		if (null === $value)
			return null;

		switch ($name) {
			case 'list':
				if (!is_array($value))
					return null;

				$html = '<datalist id="' . self::getDatalistId($element) . '">';
				foreach ($value as $s) {
					$html .= '<option value="' . $s . '">';
				}
				$html .= '</datalist>';

				return $html;
			default:
				return null;
		}
	}

	private static function getDatalistId($element) {
		return $element->getId() . '_datalist';
	}

}