<?php

namespace Gsdk\Grid\Column;

class Checkbox extends AbstractColumn {

	protected array $options = [
		'text' => '<input type="checkbox" />',
		'valueIndex' => 'id',
		'inputValue' => '%id%',
		'inputName' => '',
		'checked' => null
	];

	public function formatValue($value, $row = null) {
		$inputValue = ($this->valueIndex ? $row->{$this->valueIndex} : $this->inputValue);
		if (is_bool($this->checked)) {
			$checked = $this->checked;
		} else if (is_array($this->checked)) {
			$checked = in_array($inputValue, $this->checked);
		} else {
			$checked = (bool)$value;
		}
		$template = '<input type="checkbox" name="' . $this->inputName . '"' . ($checked ? ' checked="checked"' : '') . ' value="' . $inputValue . '" />';
		//$row['value'] = $value;
		return \Format::formatTemplate($template, $row);
	}

}
