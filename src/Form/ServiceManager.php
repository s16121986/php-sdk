<?php

namespace Gsdk\Form;

class ServiceManager {

	private static array $extendedNamespaces = [];
	private static array $extendedElements = [];

	public static function registerNamespace($path) {
		self::$extendedPaths = $path;
	}

	public static function extend($type, $class) {
		self::$extendedElements[$type] = $class;
	}

	public static function elementFactory($name, $type, $options) {
		if (isset(self::$extendedElements[$type]))
			$class = self::$extendedElements[$type];
		else {
			$class = null;

			foreach (self::$extendedNamespaces as $ns) {
				$tmp = $ns . '\\' . ucfirst($type);
				if (!class_exists($tmp, true))
					continue;

				self::extend($type, $tmp);
				$class = $tmp;
				break;
			}

			if (null === $class)
				$class = __NAMESPACE__ . '\Element\\' . ucfirst($type);
		}

		return new $class($name, $options);
	}

}