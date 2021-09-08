<?php

namespace Gsdk;

use DateTimeZone as Base;

use Exception;

class DateTimeZone extends Base {

	private array $formats = [
		'date' => 'Y-m-d',
		'time' => 'Y-m-d H:i:s',
		'datetime' => 'Y-m-d H:i:s'
	];

	public static function factory($timezone): DateTimeZone {
		if ($timezone instanceof DateTimeZone)
			return $timezone;
		else if ($timezone instanceof Base) {
			return new self($timezone->getName());
		} else if (is_string($timezone))
			return new self($timezone);
		else
			throw new Exception('Timezone factory failed');
	}

	public function setFormats($formats): DateTimeZone {
		$this->formats = $formats;
		return $this;
	}

	public function getFormat($alias) {
		return $this->formats[$alias] ?? null;
	}

}