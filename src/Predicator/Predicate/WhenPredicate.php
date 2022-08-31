<?php

namespace Gsdk\Predicator\Predicate;

class WhenPredicate {

	protected $callback;

	protected array $options = [];

	public function __construct($callback, array $options = []) {
		$this->callback = $callback;
		$this->options = $options;
	}

	public function query($query, $value) {
		call_user_func($this->callback, $query, $value);
	}

}
