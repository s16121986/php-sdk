<?php

namespace Gsdk\Predicator\Predicate;

class BoolPredicate extends AbstractPredicate {

	public function formatValue($value) {
		if (is_bool($value))
			return $value ? 1 : 0;
		else if ($value === '0')
			return 0;
		else if (is_string($value) && strtolower($value) === 'false')
			return 0;
		else
			return $value ? 1 : 0;
	}

}
