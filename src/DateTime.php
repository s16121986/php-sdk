<?php

namespace Gsdk;

use DateTime as BaseDateTime;
use DateTimeZone;

class DateTime extends BaseDateTime {

	private static array $formats = [
		'date' => 'Y-m-d',
		'time' => 'H:i:s',
		'datetime' => 'Y-m-d H:i:s'
	];

	public static function now($timezone = null): DateTime {
		return new DateTime('now', $timezone);
	}

	public static function setFormats($formats) {
		self::$formats = $formats;
	}

	public static function getFormat($format) {
		return self::$formats[$format] ?? null;
	}

	public function __construct($time = 'now', ?DateTimeZone $timezone = null) {
		if (null === $time)
			return parent::__construct('now', $timezone);
		else if (is_int($time)) {
			parent::__construct('now', $timezone);
			$this->setTimestamp($time);
		} else
			return parent::__construct($time, $timezone);
	}

	public function clone($timezone = null): DateTime {
		return new static($this, $timezone);
	}

	public function format($format): string {
		return parent::format(self::getFormat($format) ?? $format);
	}

	public function setYear($year): DateTime {
		$this->setDate($year, $this->getMonth(), $this->getDay());
		return $this;
	}

	public function setMonth($month): DateTime {
		$this->setDate($this->getYear(), $month, $this->getDay());
		return $this;
	}

	public function setDay($day): DateTime {
		$this->setDate($this->getYear(), $this->getMonth(), $day);
		return $this;
	}

	public function setHours($hours): DateTime {
		$this->setTime($hours, $this->getMinute());
		return $this;
	}

	public function setMinutes($minutes): DateTime {
		$this->setTime($this->getHour(), $minutes);
		return $this;
	}

	public function setSeconds($seconds): DateTime {
		$this->setTime($this->getHour(), $this->getMinute(), $seconds);
		return $this;
	}

	public function getYear(): int {
		return (int)$this->format('Y');
	}

	public function getMonth(): int {
		return (int)$this->format('n');
	}

	public function getDay(): int {
		return (int)$this->format('j');
	}

	public function getWeekDay(): int {
		return (int)$this->format('N');
	}

	public function getHour(): int {
		return (int)$this->format('H');
	}

	public function getMinute(): int {
		return (int)$this->format('i');
	}

	public function getSecond(): int {
		return (int)$this->format('s');
	}

}
