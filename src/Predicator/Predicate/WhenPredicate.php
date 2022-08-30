<?php

namespace Gsdk\Predicator\Predicate;

class WhenPredicate {

	protected string $callback;

	protected array $options = [];

	public function __construct(string $callback, array $options = []) {
		$this->callback = $callback;
		$this->options = $options;
	}

	public function query($query, $value) {
		$this->callback($value);
	}

}
