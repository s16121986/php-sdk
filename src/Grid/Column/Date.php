<?php

namespace Gsdk\Grid\Column;

use Gsdk\Format;

class Date extends AbstractColumn {

	protected array $options = [
		'format' => 'd.m.Y'
	];

	public function formatValue($value, $row = null) {
		return parent::formatValue(Format::date($value, $this->format), $row);
	}

}
