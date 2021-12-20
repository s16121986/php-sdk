<?php

namespace Gsdk;

use DateTime as BaseDateTime;
use DateTimeZone;
use Exception;

class DateTime extends BaseDateTime {

	private static $serverTimeZone;

	private static $regionTimeZone;

	private static array $formats = [
		'date' => 'Y-m-d',
		'time' => 'H:i:s',
		'datetime' => 'Y-m-d H:i:s'
	];

	public static function setServerTimeZone($timezone) {
		if (is_string($timezone))
			$timezone = new DateTimeZone($timezone);

		self::$serverTimeZone = $timezone;
	}

	public static function getServerTimeZone(): DateTimeZone {
		if (null === self::$serverTimeZone)
			self::$serverTimeZone = new DateTimeZone('UTC');

		return self::$serverTimeZone;
	}

	public static function setRegionTimeZone($timezone) {
		if (is_string($timezone))
			$timezone = new DateTimeZone($timezone);

		self::$serverTimeZone = $timezone;
	}

	public static function getRegionTimeZone(): DateTimeZone {
		return self::$regionTimeZone ?? self::getServerTimeZone();
	}

	public static function setFormats($formats) {
		self::$formats = $formats;
	}

	public static function getFormat($format) {
		return self::$formats[$format] ?? null;
	}

	public static function now($timezone = null): DateTime {
		return new static('now', $timezone);
	}

	public function __construct($time = 'now', $timezone = null) {
		if (null === $timezone)
			$timezone = 'client';

		if (null === $time)
			$time = strtotime('now');
		else if ($time instanceof BaseDateTime) {
			parent::__construct('now', $time->getTimezone());
			$this->setTimestamp($time->getTimestamp());
			$this->setTimezone($timezone);
			return;
		} else if (is_string($time))
			$time = strtotime($time);
		else if (!is_int($time))
			throw new Exception('$time type is invalid');

		parent::__construct('now', self::getServerTimeZone());
		$this->setTimestamp($time);
		$this->setTimezone($timezone);
	}

	public function setTimezone($timezone): DateTime {
		if (is_string($timezone)) {
			$timezone = match ($timezone) {
				'offset', 'server' => self::getServerTimeZone(),
				'region', 'client' => self::getRegionTimeZone(),
				default => throw new Exception('Timezone [' . $timezone . '] undefined'),
			};
		}

		return parent::setTimezone($timezone);
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
