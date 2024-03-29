<?php

namespace Gsdk\Form;

class Fieldset {

	protected static $defaultElementOptions = [
		'requiredLabel' => ''
	];

	protected $elements = [];
	protected $values = [];
	protected $parent = null;
	protected array $options = [
		'name' => null,
		'baseParams' => [],
	];

	public static function setDefaults($defaults) {
		self::$defaultElementOptions = $defaults;
	}

	public function __set($name, $value) {
		$this->setOption($name, $value);
	}

	public function __get($name) {
		switch ($name) {
			case 'name':
				return $this->getName();
			case 'id':
				return $this->getId();
			case 'data':
				return $this->getData();
			case 'errors':
				return $this->getErrors();
		}
		if (isset($this->options[$name]))
			return $this->options[$name];
		return $this->getElement($name);
	}

	public function __construct($options = null) {
		if (is_array($options))
			$this->setOptions($options);
	}

	public function setOptions($options): static {
		foreach ($options as $k => $v) {
			$this->setOption($k, $v);
		}
		return $this;
	}

	public function setOption($key, $option): static {
		switch ($key) {
			case 'elements':
				foreach ($option as $k => $el) {
					if (is_array($el)) {
						$type = (isset($el['type']) ? $el['type'] : $this->defaultType);
						$this->addElement($k, $type, $el);
					}
				}
				break;
			default:
				$this->options[$key] = $option;
		}

		return $this;
	}

	public function setId($method) {
		return $this->setOption('id', $method);
	}

	public function getName() {
		return $this->options['name'];
	}

	public function getId() {
		if (isset($this->options['id']))
			return $this->options['id'];

		$class = get_class($this);
		$class = str_replace(__NAMESPACE__ . '\\', '', $class);
		$class = str_replace('\\', '_', $class);

		return ($this->name ? strtolower($class) . '_' . $this->name : null);
	}

	public function setParent($parent) {
		$this->parent = $parent;
		return $this;
	}

	public function getForm() {
		if ($this->parent)
			return ($this->parent instanceof Form ? $this->parent : $this->parent->getForm());

		return null;
	}

	public function hasElement($name): bool {
		return isset($this->elements[$name]);
	}

	public function addElement($element, $type = null, array $options = []) {
		if (!is_array($options))
			$options = [];

		foreach (self::$defaultElementOptions as $k => $v) {
			if (!isset($options[$k]))
				$options[$k] = ($this->$k === null ? $v : $this->$k);
		}

		if (is_array($element)) {
			$options = $element;
			$type = $element['type'];
			$element = $element['name'];
			unset($options['name'], $options['type']);
		}

		if (is_string($element) || is_integer($element))
			$element = ServiceManager::elementFactory($element, $type, $options);
		else if ($element instanceof Element) {

		} else {

		}

		$element->setParent($this);
		$this->elements[$element->name] = $element;

		return $this;
	}

	public function getElement($name) {
		return (isset($this->elements[$name]) ? $this->elements[$name] : null);
	}

	public function getElements() {
		return $this->elements;
	}

	public function getValue($key) {
		if (isset($this->values[$key]))
			return $this->values[$key];

		return (isset($this->elements[$key]) ? $this->elements[$key]->getValue() : null);
	}

	public function setValue($key, $value = null) {
		if (isset($this->elements[$key]))
			return $this->elements[$key]->setValue($value);

		$this->values[$key] = $value;
		return true;
	}

	public function getData() {
		$data = [];

		foreach ($this->elements as $element) {
			if ($element->disabled || !$element->readable)
				continue;

			switch ($element->type) {
				case 'label':
					break;
				case 'password':
				case 'file':
				case 'image':
					if (!$element->isEmpty())
						$data[$element->name] = $element->getValue();
					break;
				default:
					$data[$element->name] = $element->getValue();
			}
		}

		foreach ($this->values as $k => $v) {
			$data[$k] = $v;
		}

		return $data;
	}

	public function getFilledData(): array {
		$data = [];

		foreach ($this->elements as $element) {
			if ($element->disabled || !$element->readable || $element->isEmpty())
				continue;

			switch ($element->type) {
				case 'label':
					break;
				default:
					$data[$element->name] = $element->getValue();
			}
		}

		foreach ($this->values as $k => $v) {
			$data[$k] = $v;
		}

		return $data;
	}

	public function setData($data) {
		foreach ($this->elements as $element) {
			if (isset($data[$element->name])) {
				$element->setValue($data[$element->name]);
			}
		}
		return $this;
	}

	public function hasUpload(): bool {
		foreach ($this->getElements() as $element) {
			if ($element->isFileUpload()) {
				return true;
			}
		}
		return false;
	}

	public function isValid(): bool {
		foreach ($this->elements as $element) {
			if (!$element->isValid())
				return false;
		}
		return true;
	}

	public function isSubmitted(): bool {
		if (($form = $this->getForm())) {
			return $form->isSubmitted();
		}
		return false;
	}

	public function render(): string {
		$elements = func_get_args();
		if (empty($elements)) {
			$elements = array_keys($this->elements);
		} else if (is_array($elements[0])) {
			$elements = $elements[0];
		}
		$html = '';
		foreach ($elements as $k) {
			$element = $this->getElement($k);
			if (!$element || !$element->render || $element->isRendered())
				continue;
			$html .= $this->renderElement($element);
		}
		return $html;
	}

	public function renderElement($element): string {
		if (is_string($element)) {
			$element = $this->getElement($element);
			if (!$element) {
				return '';
			}
		} else if (!($element instanceof Element || $element instanceof Fieldset)) {
			return '';
		}
		if (in_array($element->type, ['hidden']) && !$element->label) {
			return $element->render();
		}
		$html = '';
		$error = null;
		$cls = 'form-field field-' . $element->type . ' field-' . $element->name . '';
		if ($this->isSubmitted() && !$element->isValid() && !($element instanceof self)) {
			$cls .= ' field-invalid';
			$error = $element->getError();
		}
		if ($element->required) {
			$cls .= ' field-required';
		}
		$html .= '<div class="' . $cls . '">';
		$renderData = [
			'label' => '',
			'input' => $element->render(),
			'hint' => ($element->hint ? '<div class="form-element-hint">' . $element->hint . '</div>' : ''),
			'error' => ($error && is_string($error) ? '<span class="error">' . $error . '</span>' : '')
		];
		if ($element->label) {
			$renderData['label'] = $element->renderLabel();
		}
		$renderTpl = ($element->renderTpl ? $element->renderTpl : '%label%%input%%error%%hint%');
		foreach ($renderData as $k => $v) {
			$renderTpl = str_replace('%' . $k . '%', $v, $renderTpl);
		}
		$html .= $renderTpl;
		$html .= '</div>';
		return $html;
	}


}