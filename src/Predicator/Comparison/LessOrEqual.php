<?php

namespace Gsdk\Predicator\Comparison;

class LessOrEqual extends Less {

	public function expression(string $identifier, $valueTo): string {
		return $this->boundExpression($identifier, $valueTo, '<=');
	}

}
