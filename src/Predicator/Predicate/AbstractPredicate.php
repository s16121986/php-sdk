<?php

namespace Gsdk\Predicator\Predicate;

use Gsdk\Predicator\Comparison;
use Gsdk\Predicator\ComparisonFactory;
use Gsdk\Predicator\Exception;

abstract class AbstractPredicate {

	protected string $identifier;

	protected array $options = [];

	abstract public function formatValue($value);

	public function __construct(string $identifier, array $options = []) {
		$this->identifier = $identifier;
		$this->options = $options;
	}

	public function __get(string $name) {
		return $this->options[$name] ?? null;
	}

	public function getIdentifier(): string {
		return $this->identifier;
	}

	public function expression($value, $comparison = null): ?string {
		$comparison = ComparisonFactory::valueFactory($value, $comparison);

		switch (true) {
			case $comparison instanceof Comparison\Grater:
				return $comparison->expression(
					$this->getIdentifier(),
					$this->getLowerBound($value) ?? $this->formatValue($this->getValue($value))
				);
			case $comparison instanceof Comparison\Less:
				return $comparison->expression(
					$this->getIdentifier(),
					$this->getUpperBound($value) ?? $this->formatValue($this->getValue($value))
				);
			case $comparison instanceof Comparison\Interval:
				return $comparison->expression(
					$this->getIdentifier(),
					$this->getLowerBound($value),
					$this->getUpperBound($value)
				);
			default:
				return $comparison->expression(
					$this->getIdentifier(),
					$this->formatValue($this->getValue($value))
				);
		}
	}

	public function query($query, $value, $comparison = null): void {
		$expression = $this->expression($value, $comparison);
		if (null !== $expression)
			$query->whereRaw($expression);
	}

	protected function getValue($value) {
		return $value['value'] ?? $value;
	}

	protected function getValueFrom($value) {
		return $value['valueFrom'] ?? $value['from'] ?? $value[0] ?? null;
	}

	protected function getValueTo($value) {
		return $value['valueTo'] ?? $value['to'] ?? $value[1] ?? null;
	}

	protected function getLowerBound($value) {
		return $this->formatValue($this->getValueFrom($value));
	}

	protected function getUpperBound($value) {
		return $this->formatValue($this->getValueTo($value));
	}

}
