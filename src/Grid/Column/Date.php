<?php

namespace Gsdk\Grid\Column;

use Gsdk\DateTime;

class Date extends AbstractColumn {

	protected array $options = [
		'format' => 'd.m.Y'
	];

	public function formatValue($value, $row = null) {
		return parent::formatValue((new DateTime($value))->format($this->format), $row);
	}

}
