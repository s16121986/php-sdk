<?php

namespace Gsdk\Predicator\Comparison;

class NotInList extends InList {

	public function expression(string $identifier, $value): string {
		return $this->quoteIdentifier($identifier) . ' NOT IN (' . implode(',', $this->getValueList()) . ')';
	}

}
