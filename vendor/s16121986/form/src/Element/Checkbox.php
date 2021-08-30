<?php

namespace Gsdk\Form\Element;

class Checkbox extends AbstractInput {

	protected $checked = false;

	protected $options = [
		'checkedValue' => 1,
		'uncheckedValue' => 0
	];
	protected $attributes = [];//'indeterminate'

	public function setOptions($options) {
		parent::setOptions($options);

		$curValue = $this->getValue();
		$test = [$this->checkedValue, $this->uncheckedValue];
		if (!in_array($curValue, $test))
			$this->setValue($curValue);


		return $this;
	}

	public function isValid() {
		return (!$this->required || null !== $this->value);
	}

	public function getValue() {
		$value = parent::getValue();
		return $value == $this->checkedValue ? $this->checkedValue : $this->uncheckedValue;
	}

	public function setValue($value) {
		if ($value == $this->checkedValue) {
			parent::setValue($this->checkedValue);
			$this->checked = true;
		} else {
			parent::setValue($this->uncheckedValue);
			$this->checked = false;
		}
		return $this;
	}

	public function setChecked($flag) {
		$this->checked = (bool)$flag;
		if ($this->checked)
			$this->setValue($this->checkedValue);
		else
			$this->setValue($this->uncheckedValue);

		return $this;
	}

	public function isChecked() {
		return ($this->getValue() == $this->checkedValue);
	}

	public function getHtml() {
		return '<input type="checkbox"' . $this->attributes . ' value="' . $this->checkedValue . '"' . ($this->isChecked() ? ' checked="checked"' : '') . ' />';
	}

}
