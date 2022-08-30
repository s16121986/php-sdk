<?php

namespace Gsdk\Predicator\Comparison;

class NotEqual extends AbstractComparison {

	public function expression(string $identifier, $value): string {
		if (null === $value)
			return $this->quoteIdentifier($identifier) . ' IS NOT NULL';

		return $this->quoteIdentifier($identifier) . '<>' . $this->quoteValue($value);
	}

}
