<?php

namespace Gsdk;

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

		if (is_callable($format))
			return $format($value);

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
		if (is_string($format) && isset(self::$default[$format])) {
			return self::$default[$format];
		} else if (null === $format) {
			return $default;
		}
		return $format;
	}

	public static function formatString($string, $format = null): string {
		return (string)$string;
	}

	public static function formatDate($date, $format = null): string {
		return (new DateTime($date))->format(self::getDefault($format, self::DATE_FORMAT));
	}

	public static function formatTime($time, $format = self::TIME_FORMAT): string {
		return (new DateTime($time))->format(self::getDefault($format, self::TIME_FORMAT));
	}

	public static function formatNumber($number, $format = null) {
		$format = self::parseFormat(self::getDefault($format), [
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

	public static function formatPrice($price, $format = null) {
		$format = self::parseFormat(self::getDefault($format, self::PRICE_FORMAT), [
			self::ND => 8,
			self::NFD => 2,
			self::NDS => ',',
			self::NGS => ' ',
			self::NZ => '0',
			self::NLZ => false
		]);
		return self::formatNumber($price, $format);
	}

	public static function formatHours($minutes, $format = 'H:i'): string {
		$nn = ($minutes < 0);
		if ($nn)
			$minutes = -$minutes;

		$h = floor($minutes / 60);
		$m = $minutes - ($h * 60);
		$s = 0;
		$formatTemp = $format;
		if ($format == 'label') {
			$formatTemp = '';
			if ($h)
				$formatTemp .= $h . ' ' . lang('hours&' . numberLabelPrefix($h));
			if ($m)
				$formatTemp .= ' ' . $m . ' ' . lang('minuts&' . numberLabelPrefix($m));
		} else {
			$h = str_pad($h, 2, '0', STR_PAD_LEFT);
			$m = str_pad($m, 2, '0', STR_PAD_LEFT);
			$s = str_pad($s, 2, '0', STR_PAD_LEFT);
			$formatTemp = str_replace(['H', 'i', 's'], [$h, $m, $s], $formatTemp);
		}
		return ($nn ? '-' : '') . $formatTemp;
	}

	public static function formatPhone($phone) {
		return self::callFormat('phone', $phone);
		/*if (preg_match('/^\d{10}$/', $phone))
			return '8 (' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6, 2) . '-' . substr($phone, 8, 2);

		return $phone;*/
	}

	public static function formatBoolean($value, $format) {
		$format = self::parseFormat($format, [
			self::BT => lang('True'),
			self::BF => lang('False')
		]);
		return ($value ? $format[self::BT] : $format[self::BF]);
	}

	public static function formatText($value, $format) {
		$format = self::parseFormat($format, [
			self::TL => 255,
			self::TE => '',
			self::TA => ''
		]);
	}

	public static function formatFileSize($size, $format = null) {
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
			return self::formatNumber($size, $numberFormat) . ' ' . $units[$i];
		} else {
			return self::formatNumber($size, $numberFormat);
		}
	}

	public static function formatNumberUnits($number, $format = null) {
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
			return self::formatNumber($number, $numberFormat) . ' ' . $units[$i];
		} else {
			return self::formatNumber($number, $numberFormat);
		}
	}

	public static function format($value, $format) {
		$type = gettype($value);
		switch ($type) {
			case 'integer':
				return self::formatNumber($value, $format);
			case 'float':
			case 'double':
				return self::formatNumber($value, $format);
			case 'boolean':
				return self::formatBoolean($value, $format);
			//case 'string':
			//	return self::formatText($value, $format);
			case 'object':
				if ($value instanceof \DateTime)
					return self::formatDate($value, $format);
		}
		return $value;
	}

}