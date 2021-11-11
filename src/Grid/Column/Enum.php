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
		$class = call_user_func([$this->enum, 'getName'], $value);
		if ($this->icon) {
			return '<i class="' . $class . '" title="' . $value . '"></i>';
		} else {
			return '<span class="' . $class . '">' . $value . '</span>';
		}
	}

}
