<?php

namespace Gsdk;

use DateTime as BaseDateTime;
use DateTimeZone;
use Exception;

class DateTime extends BaseDateTime {

	private static array $formats = [];
	private static string $clientTimeZone;
	private static string $serverTimeZone;

	public static function setClientTimeZone(string $timezone) {
		self::$clientTimeZone = $timezone;
	}

	public static function getClientTimeZone(): string {
		return self::$clientTimeZone;
	}

	public static function setServerTimeZone(string $timezone) {
		self::$serverTimeZone = $timezone;
	}

	public static function getServerTimeZone(): string {
		return self::$serverTimeZone;
	}

	public static function now($timezone = null): DateTime {
		return new self('now', $timezone);
	}

	public static function setFormats($formats, $timezone = 'default') {
		self::$formats[$timezone] = $formats;
	}

	public static function getFormat($format, $timezone = 'default') {
		if ($timezone === 'default')
			return self::$formats[$timezone]['default'] ?? null;
		else
			return self::$formats[$timezone][$format] ?? self::$formats[$timezone]['default'] ?? null;
	}

	public function __construct($time = 'now', $timezone = null) {
		$this->setTimezone('server');

		if ($time instanceof self) {
			$this->setTimezone($time->getTimezone());
			$this->setTimestamp($time->getTimestamp());
		} else if (is_int($time))
			$this->setTimestamp($time);
		else if (is_string($time))
			$this->setTimestamp(strtotime($time));
		else
			parent::__construct($time);

		$this->setTimezone($timezone);
	}

	public function setTimezone($timezone): DateTime {
		if (null === $timezone)
			$timezone = 'client';
		else if ($timezone instanceof DateTimeZone)
			return parent::setTimezone($timezone);
		else if (!is_string($timezone))
			throw new Exception('Timezone format invalid');

		switch ($timezone) {
			case 'client':
			case 'default':
				if (self::$clientTimeZone)
					return $this->setTimezone(self::$clientTimeZone);
				else if (self::$serverTimeZone)
					return $this->setTimezone(self::$serverTimeZone);
				else
					throw new Exception('Client timezone not specified');
			case 'server':
				if (self::$serverTimeZone)
					return $this->setTimezone(self::$serverTimeZone);
				throw new Exception('Server timezone not specified');
			default:
				return parent::setTimezone($timezone);
		}
	}

	public function format($format): string {
		$timezone = $this->getTimezone()->getName();

		$format = self::getFormat($format, $timezone) ?? $format;

		return parent::format($format);
	}

	public function formatTime(): string {
		return self::format('time');
	}

	public function formatDate(): string {
		return self::format('date');
	}

	public function formatDatetime(): string {
		return self::format('datetime');
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
