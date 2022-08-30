<?php

namespace Gsdk\Predicator\Comparison;

class GraterOrEqual extends Grater {

	public function expression(string $identifier, $valueFrom): string {
		return $this->boundExpression($identifier, $valueFrom, '>=');
	}

}
