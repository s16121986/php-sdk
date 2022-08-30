<?php

namespace Gsdk\Predicator\Comparison;

use DateTime;

abstract class AbstractComparison {

	protected function quoteIdentifier(string $identifier): string {
		return $identifier;
	}

	protected function quoteValue($value): string {
		if (is_null($value))
			return 'NULL';
		else if (is_numeric($value))
			return $value;
		else if ($value instanceof DateTime)
			return '"' . $value->format('Y-m-d H:i:s') . '"';
		else
			return '"' . addslashes($value) . '"';
	}

}
