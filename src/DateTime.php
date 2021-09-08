<?php

namespace Gsdk;

use DateTime as BaseDateTime;
use DateTimeZone as BaseDateTimeZone;
use Exception;

class DateTime extends BaseDateTime {

	private static ?DateTimeZone $defaultTimeZone = null;
	private static ?DateTimeZone $serverTimeZone = null;

	public static function setDefaultTimeZone($timezone) {
		self::$defaultTimeZone = DateTimeZone::factory($timezone);
	}

	public static function getDefaultTimeZone(): ?DateTimeZone {
		return self::$defaultTimeZone;
	}

	public static function setServerTimeZone($timezone) {
		self::$serverTimeZone = DateTimeZone::factory($timezone);
	}

	public static function getServerTimeZone(): ?DateTimeZone {
		return self::$serverTimeZone;
	}

	public static function now($timezone = null): DateTime {
		return new self('now', $timezone);
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
			return parent::setTimezone(self::$defaultTimeZone);
		if ($timezone instanceof BaseDateTimeZone)
			return parent::setTimezone($timezone);
		else if (!is_string($timezone))
			throw new Exception('Timezone format invalid');

		switch ($timezone) {
			case 'client':
			case 'default':
				return $this->setTimezone(self::$defaultTimeZone);
			case 'server':
				return $this->setTimezone(self::$serverTimeZone);
			default:
				return parent::setTimezone($timezone);
		}
	}

	public function format($format): string {
		$timezone = $this->getTimezone();
		if ($timezone instanceof DateTimeZone)
			$format = $timezone->getFormat($format) ?? $format;

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
