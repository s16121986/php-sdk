<?php

namespace Gsdk;

use DateTime;
use Illuminate\Support\DateFactory;

abstract class Format {

	const S = ';';

	/*const SUC = 'SUC';
	const SLC = 'SLC';
	const SST = 'SST';
	const SUC = 'SUC';
	const SUC = 'SUC';*/

	const TL = 'TL';
	const TE = '';
	const TA = '';

	//Строка, представляющая логическое значение Ложь.
	const BF = 'BF';
	//Строка, представляющая логическое значение Истина.
	const BT = 'BT';

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

	//
	const FU = 'FU';
	const FFD = 'FFD';
	const FDS = 'FDS';
	const FGS = 'FGS';
	const FZ = 'FZ';

	const NUU = 'NUU';
	const NUFD = 'NUFD';
	const NUDS = 'NUDS';
	const NUGS = 'NUGS';
	const NUZ = 'NUZ';

	const PRICE_FORMAT = 'NFD=2;NDS=,;NGS= ;';
	const NUMBER_FORMAT = 'ND=7;NGS=;NLZ=1';
	const DATE_FORMAT = 'date';
	const TIME_FORMAT = 'time';
	const DATETIME_FORMAT = 'datetime';

	private static array $default = [];

	protected static function parseFormat($format, $elements): array {
		if (is_string($format)) {
			$formatTemp = $format;
			$format = [];
			$ei = array_keys($elements);
			$parts = explode(self::S, $formatTemp);
			if ('' === $parts[count($parts) - 1]) {
				array_pop($parts);
			}
			foreach ($parts as $i => $part) {
				$pp = explode('=', $part);
				if (isset($pp[1])) {
					if (isset($elements[$pp[0]])) {
						$format[$pp[0]] = $pp[1];
					}
				} else {
					if (isset($ei[$i])) {
						$format[$ei[$i]] = $pp[0];
					}
				}
			}
			/*foreach ($elements as $k => $v) {
				if (preg_match('/' . $k . '=(.*)' . self::S . '/U', $formatTemp, $c)) {
					$format[$k] = $c[1];
				}
			}*/
		} else if (!is_array($format))
			$format = [];

		return array_merge($elements, $format);
	}

	protected static function callFormat($formatString, $value) {
		$format = self::getDefault($formatString);
		if (!$format)
			return $value;
		else if (is_callable($format))
			return $format($value);
		else
			return $value;
	}

	public static function setDefaults(array $formats) {
		foreach ($formats as $k => $f) {
			self::setDefault($k, $f);
		}
	}

	public static function setDefault($type, $format) {
		self::$default[$type] = $format;
	}

	public static function getDefault($format, $default = null) {
		if (is_string($format) && isset(self::$default[$format]))
			return self::$default[$format];
		else if (null === $format)
			return $default;
		else
			return $format;
	}

	public static function date($date, $format = null): string {
		$factory = new DateFactory();
		if ($date instanceof DateTime)
			$date = $factory->createFromTimestamp($date->getTimestamp());
		else if (is_numeric($date)) {
			$date = $factory->createFromTimestamp($date);
		} else if (is_string($date))
			$date = $factory->parse($date);
		else
			return '';

		return $date->format(self::getDefault($format, self::DATE_FORMAT));
	}

	public static function time($time, $format = self::TIME_FORMAT): string {
		return self::date($time, self::getDefault($format, self::TIME_FORMAT));
	}

	public static function datetime($date, $format = null): string {
		return self::date($date, self::getDefault($format, self::DATETIME_FORMAT));
	}

	public static function number($number, $format = null) {
		$format = self::parseFormat(self::getDefault($format, self::NUMBER_FORMAT), [
			self::ND => 8,
			self::NFD => 0,
			self::NDS => ',',
			self::NGS => ' ',
			self::NZ => '0',
			self::NLZ => false
		]);

		if (0 == $number && false !== $format[self::NZ])
			return $format[self::NZ];

		if ($number == (int)$number)
			$format[self::NFD] = 0;

		$v = number_format($number, $format[self::NFD], $format[self::NDS], $format[self::NGS]);
		if (1 == $format[self::NLZ])
			$v = str_pad($v, $format[self::ND], '0', STR_PAD_LEFT);

		return $v;
	}

	public static function price($price, $format = null) {
		$format = self::parseFormat(self::getDefault($format, self::PRICE_FORMAT), [
			self::ND => 8,
			self::NFD => 2,
			self::NDS => ',',
			self::NGS => ' ',
			self::NZ => '0',
			self::NLZ => false
		]);
		return self::number($price, $format);
	}

	public static function phone($phone) {
		return self::callFormat('phone', $phone);
	}

	public static function boolean($value, $format) {
		$format = self::parseFormat($format, [
			self::BT => 'True',
			self::BF => 'False'
		]);
		return ($value ? $format[self::BT] : $format[self::BF]);
	}

	public static function string($string, $format = null): string {
		return (string)$string;
	}

	public static function text($value, $format) {
		$format = self::parseFormat($format, [
			self::TL => 255,
			self::TE => '',
			self::TA => ''
		]);
	}

	public static function fileSize($size, $format = null) {
		$format = self::parseFormat($format, [
			self::FU => '',
			self::FFD => 1,
			self::FDS => ',',
			self::FGS => ' ',
			self::FZ => 'n/a'
		]);
		if (0 == $size && false !== $format[self::FZ]) {
			return $format[self::FZ];
		}
		$numberFormat = 'NFD=' . $format[self::FFD]
			. ';NDS=' . $format[self::FDS]
			. ';NGS=' . $format[self::FGS]
			. ';NZ=' . $format[self::FZ];
		if ($format[self::FU]) {
			$units = explode(',', $format[self::FU]);
			$i = floor(log($size, 1024));
			while (!isset($units[$i])) {
				$i--;
			}
			$size = $size / pow(1024, $i);
			return self::number($size, $numberFormat) . ' ' . $units[$i];
		} else {
			return self::number($size, $numberFormat);
		}
	}

	public static function numberUnits($number, $format = null) {
		$format = self::parseFormat($format, [
			self::NUU => '',
			self::NUFD => 1,
			self::NUDS => ',',
			self::NUGS => ' ',
			self::NUZ => 'n/a'
		]);
		if (0 == $number && false !== $format[self::NUZ]) {
			return $format[self::NUZ];
		}
		$numberFormat = 'NFD=' . $format[self::NUFD]
			. ';NDS=' . $format[self::NUDS]
			. ';NGS=' . $format[self::NUGS]
			. ';NZ=' . $format[self::NUZ];
		if ($format[self::NUU]) {
			$units = explode(',', $format[self::NUU]);
			$i = floor(log($number, 1000));
			while (!isset($units[$i])) {
				$i--;
			}
			$number = $number / pow(1000, $i);
			return self::number($number, $numberFormat) . ' ' . $units[$i];
		} else {
			return self::number($number, $numberFormat);
		}
	}

	public static function params($params, array $data = null) {
		return new Format\Params($params, $data);
	}

	public static function format($value, $format) {
		$type = gettype($value);
		switch ($type) {
			case 'integer':
				return self::number($value, $format);
			case 'float':
			case 'double':
				return self::number($value, $format);
			case 'boolean':
				return self::boolean($value, $format);
			//case 'string':
			//	return self::text($value, $format);
			case 'object':
				if ($value instanceof \DateTime)
					return self::date($value, $format);
		}
		return $value;
	}

}