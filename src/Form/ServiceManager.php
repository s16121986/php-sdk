<?php

namespace Gsdk\Form;

class ServiceManager {

	private static array $extendedElements = [];

	public static function extend($type, $class) {
		self::$extendedElements[$type] = $class;
	}

	public static function elementFactory($name, $type, $options) {
		if (isset(self::$extendedElements[$type]))
			$class = self::$extendedElements[$type];
		else
			$class = __NAMESPACE__ . '\Element\\' . ucfirst($type);

		return new $class($name, $options);
	}

}