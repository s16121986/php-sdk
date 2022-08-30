<?php

namespace Gsdk\Predicator\Predicate;

class FloatPredicate extends AbstractPredicate {

	public function formatValue($value) {
		return is_scalar($value) ? (float)$value : null;
	}

}
