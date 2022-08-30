<?php

namespace Gsdk\Predicator\Comparison;

class Equal extends AbstractComparison {

	public function expression(string $identifier, $value): string {
		if (null === $value)
			return $this->quoteIdentifier($identifier) . ' IS NULL';

		return $this->quoteIdentifier($identifier) . '=' . $this->quoteValue($value);
	}

}
