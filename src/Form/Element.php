<?php

namespace Gsdk\Form;

use Gsdk\Form\Element\Util\Attributes;

abstract class Element {

	private static $defaultOptions = [
		'required' => false,
		'disabled' => false,
		'readable' => true,
		'render' => true,
		'requiredText' => ''
	];
	protected $options = [];
	protected $rendered = false;
	protected $error;
	protected $value = null;
	protected $label = null;
	protected $parent;
	protected $attributes;

	public static function factory($name, $type, $options = null) {
		if (!is_array($options))
			$options = [];

		$cls = __NAMESPACE__ . '\\Element\\' . ucfirst($type);
		$element = new $cls($name, $options);

		return $element;
	}

	public function __construct($name, $options = []) {
		if (!isset($options['class']))
			$options['class'] = '';

		$options['type'] = strtolower(str_replace(__NAMESPACE__ . '\\Element\\', '', get_class($this)));
		$options['class'] .= ' field-' . $options['type'];

		$this->setName($name)
			->setOptions(array_merge(self::$defaultOptions, $options));

		$this->attributes = new Attributes($this, $this->attributes);

		$this->init();
	}

	public function __set($name, $value) {
		$this->setOption($name, $value);
	}

	public function __get($name) {
		return $this->getOption($name);
	}

	public function setOptions($options) {
		$lastOptions = [];
		foreach (['default', 'value'] as $k) {
			if (array_key_exists($k, $options)) {
				$lastOptions[$k] = $options[$k];
				unset($options[$k]);
			}
		}

		foreach ($options as $k => $v) {
			$this->setOption($k, $v);
		}

		foreach ($lastOptions as $k => $v) {
			$this->setOption($k, $v);
		}

		return $this;
	}

	public function getOption($name) {
		switch ($name) {
			case 'value':
				return $this->getValue();
			case 'id':
				return $this->getId();
			case 'inputName':
				return $this->getInputName();
		}

		if (isset($this->options[$name]))
			return $this->options[$name];

		//if (isset(self::$_default[$name])) return self::$_default[$name];
		return null;
	}

	public function setOption($key, $option) {
		switch ($key) {
			case 'value':
				$this->setValue($option);
				break;
			case 'default':
				$this->options[$key] = $this->prepareValue($option);
				$this->setValue($option);
				break;
			default:
				$this->options[$key] = $option;
		}
		return $this;
	}

	public function setParent($parent) {
		$this->parent = $parent;
		/*if ($parent instanceof \Form) {
			$this->id = $this->_parent->getName() . '_formfield_' . $this->name;
		} else {
			$this->id = $this->_parent->id . '_' . $this->name;
		}*/
		return $this;
	}

	public function getForm() {
		return ($this->parent instanceof Form ? $this->parent : null);
	}

	public function setForm($form) {
		return $this->setParent($form);
	}

	public function getId() {
		if (!isset($this->options['id'])) {
			$parts = [];
			if ($this->parent && $this->parent->getId())
				$parts[] = $this->parent->getId();
			$parts[] = $this->name;
			$this->setId(implode('_', $parts));
		}
		return $this->options['id'];
	}

	public function setId($id) {
		return $this->setOption('id', $id);
	}

	public function getInputName() {
		if (isset($this->options['inputName']))
			return $this->options['inputName'];

		if ($this->parent) {
			switch (true) {
				case $this->parent instanceof Element\Fieldset:
				case $this->parent instanceof self:
					$name = $this->parent->getInputName();
					break;
				default:
					$name = $this->parent->getName();
			}

			if ($name && $this->name) {
				$name .= '[' . $this->name . ']';
			} else {
				$name = $this->name;
			}
			$this->options['inputName'] = $name;
		} else
			$this->options['inputName'] = $this->name;

		return $this->options['inputName'];
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->options['name'] = $name;
		return $this;
	}

	public function checkValue($value) {
		return true;
	}

	public function getValuePresentation() {
		return $this->getValue();
	}

	public function getValue() {
		if (null === $this->value && null !== $this->default) {
			//return $this->default;
		}
		return $this->value;
	}

	public function setValue($value) {
		if ($this->checkValue($value)) {
			$this->value = $this->prepareValue($value);
			$this->setError(null);
			return true;
		} else if (null !== $value) {
			$this->value = null;
			if ($this->required) {
				//$this->setError(true);
			} else {
				$this->setError(null);
			}
		}
		return false;
	}

	public function getLabel() {
		if (null === $this->label) {
			$this->label = new Label(array_merge($this->options, ['text' => $this->getOption('label')]));
			$this->label->setElement($this);
		}

		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	public function getError() {
		if ($this->error)
			return $this->error;

		if (($this->required && !$this->disabled && $this->isEmpty()))
			return $this->requiredText;

		return null;
	}

	public function setError($error) {
		$this->error = $error;
		return $this;
	}

	public function isEmpty(): bool {
		return empty($this->value);
	}

	public function isValid() {
		if ($this->error || ($this->required && !$this->disabled && $this->isEmpty()))
			return false;

		return true;
	}

	public function isRendered($flag = null) {
		if (null === $flag)
			return $this->rendered;

		$this->rendered = (bool)$flag;

		return $this;
	}

	public function renderLabel() {
		return $this->getLabel()->render();
	}

	public function renderInput() {
		return $this->getHtml();
	}

	public function isSubmittable() {
		return true;
	}

	public function isFileUpload() {
		return false;
	}

	public function reset() {
		$this->value = null;
		$this->error = null;
		$this->rendered = false;
		return $this;
	}

	protected function prepareValue($value) {
		return $value;
	}

	protected function init() { }

	public function render() {
		$this->rendered = true;
		return $this->getHtml();
	}

	public static function escape($val) {
		if (is_array($val))
			$val = implode(',', $val);
		else if (is_float($val))
			return str_replace(',', '.', $val);

		return str_replace('"', '&quot;', $val);
	}

}