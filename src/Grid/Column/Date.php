<?php

namespace Gsdk\Grid\Column;

use Gsdk\DateTime;

class Date extends AbstractColumn {

	protected array $options = [
		'format' => 'd.m.Y'
	];

	public function formatValue($value, $row = null) {
		if (is_string($value)) {
			if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value))
				$value = new \DateTime($value);
			else
				$value = new DateTime($value);
		} else
			$value = new DateTime($value);

		return parent::formatValue($value->format($this->format), $row);
	}

}
