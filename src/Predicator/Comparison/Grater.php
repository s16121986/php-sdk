<?php

namespace Gsdk\Predicator\Comparison;

class Grater extends AbstractComparison {

	public function expression(string $identifier, $valueFrom): string {
		return $this->boundExpression($identifier, $valueFrom, '>');
	}

	protected function boundExpression(string $identifier, $valueFrom, $sign): string {
		return $this->quoteIdentifier($identifier) . $sign . $this->quoteValue($valueFrom);
	}

}
