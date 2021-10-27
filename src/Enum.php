<?php
/**
 * Класс перечислений
 */

namespace Gsdk;

use ReflectionClass;

abstract class Enum {

	protected static $constants;

	/**
	 * Получить список констант класса
	 *
	 * @return array
	 */
	public static function constants(): array {
		if (self::$constants)
			return self::$constants;

		return self::$constants = (new ReflectionClass(get_called_class()))->getConstants();
	}

	/**
	 * Получить значения констант
	 *
	 * @return array
	 */
	public static function values(): array {
		return array_values(self::constants());
	}

	/**
	 * Получить имена констант
	 *
	 * @return array
	 */
	public static function keys(): array {
		return array_keys(self::constants());
	}

	/**
	 * Получить локализованные имена констант
	 *
	 * @return array
	 */
	public static function labels(): array {
		$cls = self::getName();
		$array = [];
		foreach (self::constants() as $k => $v) {
			$array[$v] = lang($cls . '::' . $k);
		}
		return $array;
	}

	/**
	 * Проверить наличие константы по значению
	 *
	 * @param $value Значение константы
	 * @return bool
	 */
	public static function valueExists($value): bool {
		return in_array($value, self::constants());
	}

	/**
	 * Проверить наличие константы по наименованию
	 *
	 * @param string $key Имя константы
	 * @return bool
	 */
	public static function keyExists(string $key): bool {
		return array_key_exists($key, self::constants());
	}

	/**
	 * Получить значение константы
	 *
	 * @param $key Имя константы
	 * @return mixed
	 */
	public static function getValue($key) {
		//$key = strtoupper($key);
		foreach (self::constants() as $k => $v) {
			if ($k == $key)
				return $v;
		}
		return null;
	}

	/**
	 * Получить имя константы по значению
	 *
	 * @param $value Значение константы
	 * @return string
	 */
	public static function getKey($value) {
		foreach (self::constants() as $k => $v) {
			if ($v == $value)
				return $k;
		}
		return null;
	}

	/**
	 * Получить полное наименование константы, включая имя класса
	 *
	 * @param $value Значение константы
	 * @return string
	 */
	public static function getName($value = null): string {
		return str_replace(__NAMESPACE__ . '\\', '', get_called_class()) . (null === $value ? '' : '::' . self::getKey($value));
	}

	/**
	 * Получить локализованное имя константы
	 *
	 * @param $val Значение константы
	 * @return string
	 */
	public static function getLabel($val): string {
		$const = self::constants();
		return (in_array($val, $const) ? lang(self::getName() . '::' . array_search($val, $const)) : '');
	}

	/**
	 * Получить константу по умолчанию (первую по счету)
	 *
	 * @return mixed
	 */
	public static function default() {
		$const = self::constants();
		return array_shift($const);
	}

	/**
	 * Получить константы в виде массива
	 *
	 * @param bool $valueIndex (ключами выступают: true - значения, false - имена констант)
	 * @return array
	 */
	public static function asArray($valueIndex = false): array {
		$array = [];
		foreach (self::constants() as $k => $v) {
			$array[$v] = $k;
		}
		return $array;
	}

	public static function getHtml($val): string {
		$cls = self::getName();
		$const = self::constants();
		return '<span class="' . $cls . ' ' . self::getKey($val) . '">' . (in_array($val, $const) ? lang($cls . '::' . array_search($val, $const)) : '') . '</span>';
	}

}
