<?php

namespace Gsdk\Predicator\Comparison;

use Gsdk\Predicator\Exception;
use stdClass;

class IntervalIncludingLowerBound extends Interval {

	public function expression(string $identifier, $valueFrom, $valueTo): string {
		return $this->boundsExpression($identifier, $valueFrom, $valueTo, '>=', '<');
	}

}
