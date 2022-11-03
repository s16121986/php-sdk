<?php

namespace Gsdk\Format\Rules;

use Gsdk\Contracts\Format\Rule;

class Custom implements Rule {

	protected $handler;

	public function __construct(callable $handler) {
		$this->handler = $handler;
	}

	public function format($value, $format = null): Text {
		return call_user_func($this->handler, $value, $format);
	}

}