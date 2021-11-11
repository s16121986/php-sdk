<?php

namespace Gsdk\Grid\Column;

class Enum extends AbstractColumn {

	protected array $options = [
		'icon' => false
	];

	public function formatValue($value, $row = null) {
		return call_user_func([$this->enum, 'getLabel'], $value);
	}

	public function renderer($row, $value): string {
		$class = strtolower(call_user_func([$this->enum, 'getKey'], $value));
		$label = call_user_func([$this->enum, 'getLabel'], $value);
		if ($this->icon) {
			return '<i class="' . $class . '" title="' . $label . '"></i>';
		} else {
			return '<span class="' . $class . '">' . $label . '</span>';
		}
	}

}
