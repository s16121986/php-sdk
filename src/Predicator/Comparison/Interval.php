<?php

namespace Gsdk\Predicator\Comparison;

use Gsdk\Predicator\Exception;
use DateTime;
use stdClass;

class Interval extends AbstractComparison {

	public function expression(string $identifier, $valueFrom, $valueTo): string {
		return $this->boundsExpression($identifier, $valueFrom, $valueTo, '>', '<');
	}

	protected function boundsExpression(string $identifier, $valueFrom, $valueTo, $fromSign, $toSign): string {
		$quotedIdentifier = $this->quoteIdentifier($identifier);

		return $quotedIdentifier . $fromSign . $this->quoteValue($valueFrom)
			. ' AND ' . $quotedIdentifier . $toSign . $this->quoteValue($valueTo);
	}

}
