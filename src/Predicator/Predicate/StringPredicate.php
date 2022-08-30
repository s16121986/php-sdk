<?php

namespace Gsdk\Predicator\Predicate;

class StringPredicate extends AbstractPredicate {

	public function formatValue($value) {
		return is_scalar($value) ? (string)$value : null;
	}

}
