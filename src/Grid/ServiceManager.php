<?php

namespace Gsdk\Grid;

class ServiceManager {

	private static array $extendedNamespaces = [];
	private static array $extendedColumns = [];

	public static function registerNamespace($namespace) {
		self::$extendedNamespaces[] = $namespace;
	}

	public static function extend($type, $class) {
		self::$extendedColumns[$type] = $class;
	}

	public static function columnFactory($name, $type, $options) {
		if (isset(self::$extendedColumns[$type]))
			$class = self::$extendedColumns[$type];
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
				$class = __NAMESPACE__ . '\Column\\' . ucfirst($type);
		}

		return new $class($name, $options);
	}

}