<?php

namespace Gsdk\Predicator\Comparison;

class InList extends AbstractComparison {

	public function expression(string $identifier, $value): string {
		return $this->quoteIdentifier($identifier) . ' IN (' . implode(',', $this->getValueList()) . ')';
	}

	protected function getValueList($value): array {
		if (!is_array($value))
			return [];

		return array_map(function ($v) {
			return $this->quoteValue($v);
		}, $value);
	}

}
