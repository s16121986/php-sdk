<?php

namespace Gsdk\Form\Element;

class Select extends Xhtml {

	const EMPTY_VALUE = '';

	private static $_autoTextKeys = ['text', 'name', 'presentation'];
	private static $_autoValueKeys = ['id', 'key', 'value'];

	protected $_groups = null;
	protected $_items = null;
	protected $attributes = ['size', 'multiple', 'required', 'autofocus'];
	protected $options = [
		'valueInList' => true,
		'valueIndex' => 'id',
		'textIndex' => 'name',
		'groupIndex' => '',
		'emptyItem' => false,
		'emptyValue' => null,
		'emptyItemValue' => '',

		'validation' => false,
		'idKey' => 'id',
		'nameKey' => 'name'
	];

	private static function getValueId($value) {
		if (is_string($value) && preg_match('/^\d+$/', $value))
			$value = (int)$value;

		return $value;
	}

	private static function getItemParam($object, $key, $autoKeys = []) {
		if (isset($object->$key))
			return $object->$key;

		foreach ($autoKeys as $key) {
			if (isset($object->$key))
				return $object->$key;
		}
		return null;
	}

	protected function initItem($value, $text = '') {
		$item = new \stdClass();

		if (is_array($text) || is_object($text)) {
			$text = (object)$text;
			$item->value = self::getItemParam($text, $this->valueIndex, self::$_autoValueKeys);
			$item->text = self::getItemParam($text, $this->textIndex, self::$_autoTextKeys);
			$item->attr = self::getItemParam($text, 'attr');
			$item->parent_id = self::getItemParam($text, 'parent_id');
			if ($this->groupIndex)
				$item->{$this->groupIndex} = self::getItemParam($text, $this->groupIndex);
		} else {
			$item->value = $value;
			$item->text = $text;
			$item->attr = null;
			$item->parent_id = null;
		}

		$this->_items[] = $item;
	}

	protected function initGroup($text = '') {
		$item = new \stdClass();

		if (is_object($text) || is_array($text)) {
			$text = (object)$text;
			$item->id = self::getItemParam($text, 'id');
			$item->text = self::getItemParam($text, $this->textIndex, self::$_autoTextKeys);
		} else {
			$item = new \stdClass();
			$item->id = null;
			$item->text = $text;
		}

		$this->_groups[] = $item;
	}

	public function getOption($name) {
		switch ($name) {
			case 'allowNotExists':
				return parent::getOption('validation');
		}

		return parent::getOption($name);
	}

	public function setOption($key, $option) {
		switch ($key) {
			case 'allowNotExists':
				return $this->setOption('validation', $option);
		}

		return parent::setOption($key, $option);
	}

	public function getGroups() {
		if (null === $this->_groups) {
			$this->_groups = [];
			$itemsTemp = [];
			$itemsData = $this->groups;
			if (is_iterable($itemsData)) {
				foreach ($itemsData as $v) {
					$this->initGroup($v);
				}
			}
			foreach ($itemsTemp as $v) {
				$this->initGroup($v);
			}
		}
		return $this->_groups;
	}

	public function getItems() {
		//if (isset($_GET['test']) && $this->name == 'param16') var_dump($this->_items);
		if (null === $this->_items) {
			$this->_items = [];
			$itemsTemp = [];
			$itemsData = $this->items;
			if (is_iterable($itemsData)) {
				foreach ($itemsData as $k => $v) {
					$this->initItem($k, $v);
				}
			}
			foreach ($itemsTemp as $v) {
				$this->initItem($v);
			}
		}
		return $this->_items;
	}

	public function getItem($value) {
		foreach ($this->getItems() as $item) {
			if ($item->value == $value)
				return $item;
		}
		return null;
	}

	public function addItem($value, $text = null) {
		$this->initItem($value, $text);
		return $this;
	}

	public function valueExists($value): bool {
		return (bool)$this->getItem($value);
	}

	public function checkValue($value) {
		if ($value === self::EMPTY_VALUE)
			return true;

		if ($this->multiple)
			return is_iterable($value) || empty($value);
		else
			return ($this->allowNotExists || $this->valueExists($value));
	}

	public function getValue() {
		$value = parent::getValue();
		if (null === $value && false === $this->emptyItem && $this->_items) {
			return $this->_items[0]->value;
		}
		return $value;
	}

	public function addValue($value) {
		if ($this->checkValue($value)) {
			$this->value[] = $this->prepareValue($value);
			return true;
		}
	}

	public function isEmpty(): bool {
		return $this->value === $this->emptyValue;
	}

	protected function prepareValue($value) {
		if ($value === self::EMPTY_VALUE)
			return $this->emptyValue;

		if ($this->multiple) {
			if (!is_iterable($value))
				return [];

			$valueTemp = $value;
			$value = [];
			foreach ($valueTemp as $val) {
				if (is_object($val))
					$val = $val->id;

				if ($this->allowNotExists || $this->valueExists($val))
					$value[] = self::getValueId($val);
			}
		} else
			$value = self::getValueId($value);

		return $value;
	}

	private function _option($item) {
		return '<option value="' . self::escape($item->value) . '"'
			. ($item->attr ? ' ' . $item->attr : '')
			. ($this->isSelected($item->value) ? ' selected' : '')
			. '>' . $item->text . '</option>';
	}

	protected function getOptionsHtml() {
		$html = '';

		if (false !== $this->emptyItem)
			$html .= '<option value="' . self::EMPTY_VALUE . '">' . $this->emptyItem . '</option>';

		//$data['value'] = isset($text->{$this->valueIndex}) ? $text->{$this->valueIndex} : self::getAutoKey(self::$_autoValueKeys, $text);
		//$data['text'] = isset($text->{$this->textIndex}) ? $text->{$this->textIndex} : self::getAutoKey(self::$_autoTextKeys, $text);
		if ($this->groups && $this->groupIndex) {
			$items = $this->getItems();

			foreach ($items as $item) {
				if (isset($item->{$this->groupIndex}) && $item->{$this->groupIndex})
					continue;

				$html .= $this->_option($item);
			}

			foreach ($this->getGroups() as $group) {
				$html .= '<optgroup label="' . $group->text . '">';
				foreach ($items as $item) {
					if (!isset($item->{$this->groupIndex}) || $item->{$this->groupIndex} != $group->id)
						continue;

					$html .= $this->_option($item);
				}
				$html .= '</optgroup>';
			}
		} else {
			foreach ($this->getItems() as $item) {
				$html .= $this->_option($item);
			}
		}
		//var_dump($this->default);

		return $html;
	}

	public function getValuePresentation() {
		foreach ($this->getItems() as $item) {
			if ($item->value == $this->value)
				return $item->text;
		}
		return '';
	}

	public function isSelected($value): bool {
		$value = self::getValueId($value);
		$selected = $this->getValue();
		if (!$this->multiple)
			return ($value === $selected);

		if (!is_array($selected))
			return false;

		return in_array($value, $selected);
	}

	public function getInputName() {
		$name = parent::getInputName();
		if ($this->multiple) {
			$name .= '[]';
		}
		return $name;
	}

	public function getHtml(): string {
		$html = '<select' . $this->attributes . '>';
		$html .= $this->getOptionsHtml();
		$html .= '</select>';
		return $html;
	}

}
