<?php

namespace Gsdk\Predicator;

abstract class ComparisonFactory {

	private static array $_comparisonTypesAssoc = [
		'=' => 'Equal',
		'<>' => 'NotEqual',
		'!=' => 'NotEqual',
		'>' => 'Greater',
		'>=' => 'GreaterOrEqual',
		'<' => 'Less',
		'<=' => 'LessOrEqual',
		'IN' => 'InList',
		'NOT IN' => 'NotInList',
		'LIKE' => 'Contains',
		'NOT LIKE' => 'NotContains',
	];

	public static function valueFactory($value, $comparison = null) {
		if (null === $comparison)
			return static::fromValue($value);
		else if (is_string($comparison))
			return static::fromString($comparison);
		else if ($comparison instanceof Comparison\AbstractComparison)
			return $comparison;
		else
			throw new Exception('Comparison type invalid');
	}

	public static function fromString(string $string) {
		$k = strtoupper($string);
		if (isset(static::$_comparisonTypesAssoc[$k]))
			return static::createInstance(static::$_comparisonTypesAssoc[$k]);
		else
			return static::createInstance(ucfirst($string));
	}

	public static function fromValue($value) {
		if (!is_array($value))
			return static::equal();
		else if (isset($value['comparison']))
			return static::fromString($value['comparison']);

		$isNull = function ($value) {
			return (null === $value || '' === $value);
		};
		$hasValue = function ($key) use ($value, $isNull) {
			return isset($value[$key]) && !$isNull($value[$key]);
		};

		return match (true) {
			$hasValue('valueFrom') && $hasValue('valueTo') => static::intervalIncludingBounds(),
			$hasValue('valueFrom') => static::greaterOrEqual(),
			$hasValue('valueTo') => static::lessOrEqual(),
			is_array($value) => static::inList(),
			default => static::equal(),
		};
	}

	public static function createInstance($type) {
		$cls = __NAMESPACE__ . '\\Comparison\\' . ucfirst($type);

		if (!class_exists($cls, true))
			throw new Exception('Comparison class [' . $cls . '] not exists');

		return new $cls();
	}

	public static function equal(): Comparison\Equal {
		return new Comparison\Equal();
	}

	public static function notEqual(): Comparison\NotEqual {
		return new Comparison\NotEqual();
	}

	public static function greater(): Comparison\Greater {
		return new Comparison\Greater();
	}

	public static function greaterOrEqual(): Comparison\GreaterOrEqual {
		return new Comparison\GreaterOrEqual();
	}

	public static function less(): Comparison\Less {
		return new Comparison\Less();
	}

	public static function lessOrEqual(): Comparison\LessOrEqual {
		return new Comparison\LessOrEqual();
	}

	public static function inList(): Comparison\Inlist {
		return new Comparison\Inlist();
	}

	public static function notInList(): Comparison\NotInlist {
		return new Comparison\NotInlist();
	}

	public static function contains(): Comparison\Contains {
		return new Comparison\Contains();
	}

	public static function notContains(): Comparison\NotContains {
		return new Comparison\NotContains();
	}

	public static function interval(): Comparison\Interval {
		return new Comparison\Interval();
	}

	public static function intervalIncludingLowerBound(): Comparison\IntervalIncludingLowerBound {
		return new Comparison\IntervalIncludingLowerBound();
	}

	public static function intervalIncludingUpperBound(): Comparison\IntervalIncludingUpperBound {
		return new Comparison\IntervalIncludingUpperBound();
	}

	public static function intervalIncludingBounds(): Comparison\IntervalIncludingBounds {
		return new Comparison\IntervalIncludingBounds();
	}

}
