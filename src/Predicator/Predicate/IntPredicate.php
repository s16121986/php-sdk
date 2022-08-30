<?php

namespace Gsdk\Predicator\Predicate;

class IntPredicate extends AbstractPredicate {

	public function formatValue($value) {
		return is_scalar($value) ? (int)$value : null;
	}

}
