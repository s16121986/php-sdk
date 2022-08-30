<?php

namespace Gsdk\Predicator\Comparison;

class Less extends AbstractComparison {

	public function expression(string $identifier, $valueTo): string {
		return $this->boundExpression($identifier, $valueTo, '<');
	}

	protected function boundExpression(string $identifier, $valueTo, $sign): string {
		return $this->quoteIdentifier($identifier) . $sign . $this->quoteValue($valueTo);
	}

}
