<?php

namespace Gsdk\Format\Rules;

use Gsdk\Contracts\Format\Rule;
use Gsdk\Format\Concerns\HasFormat;

class Number implements Rule {

	use HasFormat;

	//Общее число отображаемых десятичных разрядов целой части. Исходное число округляется при этом в соответствии с правилами округления, заданными для конфигурации. Если указан этот параметр, то для отображения дробной части числа обязательно указание параметра ЧДЦ, иначе дробная часть отображаться не будет.
	const ND = 'ND';
	//Число десятичных разрядов в дробной части. Исходное число округляется при этом в соответствии с правилами округления, заданными для конфигурации.
	const NFD = 'NFD';
	//Символ-разделитель целой и дробной части.
	const NDS = 'NDS';
	//Символ-разделитель групп целой части числа.
	const NGS = 'NGS';
	//Строка, представляющая нулевое значение числа. Если не задано, то представление в виде пустой строки. Если задано "ЧН=", то в виде "0". Не используется для числовых полей ввода.
	const NZ = 'NZ';
	//Нужно ли выводить лидирующие нули. Значение данного параметра не задается, собственно наличие параметра определяет вывод лидирующих нулей.
	const NLZ = 'NLZ';

	protected $format = [
		'ND' => 8,
		'NFD' => 0,
		'NDS' => ',',
		'NGS' => ' ',
		'NZ' => '0',
		'NLZ' => false
	];

	public function fromString(string $number): float {
		return (float)str_replace([' ', ','], ['', '.'], $number);
	}

	public function prepareValue($number): ?float {
		if (null === $number)
			return null;
		else if (is_string($number))
			return $this->fromString($number);
		else if (is_numeric($number))
			return (float)$number;
		else
			return null;
	}

	public function format($value, string $format = null): string {
		$format = $this->parseFormat($format ?? 'number');
		$number = $this->prepareValue($value);

		if (null === $number || 0.0 === $number && false !== $format[static::NZ])
			return $format[static::NZ];

		$string = number_format(
			$number,
			$format[static::NFD],
			$format[static::NDS],
			$format[static::NGS]
		);

		if ($format[static::NLZ])
			return str_pad($string, $format[static::ND], '0', STR_PAD_LEFT);
		else
			return $string;
	}

}