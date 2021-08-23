<?php

namespace Corelib\Grid\Column;

class Email extends AbstractColumn {

	protected array $options = [
		'href' => 'mailto:{value}'
	];

	protected function init() {
		$this->setOption('href', 'mailto:{' . $this->name . '}');
	}

}