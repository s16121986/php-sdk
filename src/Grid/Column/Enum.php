<?php

namespace Gsdk\Grid\Column;

class Enum extends AbstractColumn {

	protected array $options = [
		'icon' => false
	];

	public function formatValue($value, $row = null) {
		return call_user_func([$this->enum, 'getLabel'], $value);
	}

	public function render($value, $row) {
		$class = $this->enum . ' ' . $this->enum . '_' . call_user_func([$this->enum, 'getKey'], $value);
		if ($this->icon) {
			return '<i class="' . $class . '" title="' . parent::render($value, $row) . '"></i>';
		} else {
			return '<span class="' . $class . '">' . parent::render($value, $row) . '</span>';
		}
	}

}
