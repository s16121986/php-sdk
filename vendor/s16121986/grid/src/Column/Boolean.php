<?php

namespace Corelib\Grid\Column;

class Boolean extends AbstractColumn {

	protected array $options = [
		'trueText' => 'да',
		'falseText' => 'нет'
	];

	public function formatValue($value, $row = null) {
		$value = (bool)$value;
		$flagText = ($value ? $this->trueText : $this->falseText);

		return '<span class="boolean-' . ($value ? 'true' : 'false') . '">' . $flagText . '</span>';
	}

}