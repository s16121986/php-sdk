<?php

namespace Gsdk\Grid\Column;

use Gsdk\DateTime;

class Date extends AbstractColumn {

	protected array $options = [
		'format' => 'd.m.Y'
	];

	public function formatValue($value, $row = null) {
		$t = strtotime($value);
		if ($t > 0)
			$d = (new DateTime($t))->format($this->format);
		else
			$d = '';

		return parent::formatValue($d, $row);
	}

}
