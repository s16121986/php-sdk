<?php

namespace Gsdk;

use DateTime as BaseDateTime;
use DateTimeZone;
use Exception;

class DateTime extends BaseDateTime {

	private static array $formats = [
		'date' => 'Y-m-d',
		'time' => 'H:i:s',
		'datetime' => 'Y-m-d H:i:s'
	];
	private static DateTimeZone $clientTimeZone;
	private static DateTimeZone $serverTimeZone;

	public static function setClientTimeZone(string|DateTimeZone $timezone) {
		self::$clientTimeZone = is_string($timezone) ? new DateTimeZone($timezone) : $timezone;
	}

	public static function setServerTimeZone(string|DateTimeZone $timezone) {
		self::$serverTimeZone = is_string($timezone) ? new DateTimeZone($timezone) : $timezone;
	}

	public static function now($timezone = null): DateTime {
		return new DateTime('now', $timezone);
	}

	public static function setFormats($formats) {
		self::$formats = $formats;
	}

	public static function getFormat($format) {
		return self::$formats[$format] ?? null;
	}

	public function __construct($time = 'now', $timezone = null) {
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

		parent::__construct('now', self::$serverTimeZone);
		$this->setTimestamp($time);
		$this->setTimezone($timezone);
	}

	public function setTimezone($timezone): DateTime {
		if (null === $timezone)
			$timezone = 'client';
		else if ($timezone instanceof DateTimeZone)
			return parent::setTimezone($timezone);
		else if (!is_string($timezone))
			throw new Exception('$timezone type is invalid');

		switch ($timezone) {
			case 'client':
			case 'default':
				if (self::$clientTimeZone)
					return parent::setTimezone(self::$clientTimeZone);
				else if (self::$serverTimeZone)
					return parent::setTimezone(self::$serverTimeZone);
				else
					throw new Exception('Client timezone not specified');
			case 'server':
				if (self::$serverTimeZone)
					return parent::setTimezone(self::$serverTimeZone);
				throw new Exception('Server timezone not specified');
			default:
				//throw new Exception('$timezone must be of type DateTimeZone');
				return parent::setTimezone(new DateTimeZone($timezone));
		}
	}

	public function clone($timezone = null): DateTime {
		return new DateTime($this, $timezone);
	}

	public function format($format): string {
		return parent::format(self::getFormat($format) ?? $format);
	}

	public function serverFormat($format): string {
		$timezone = $this->getTimezone();
		if ($timezone->getName() === self::$serverTimeZone->getName())
			return parent::format($format);

		return $this->clone('server')->format($format);
	}

	public function serverDate(): string {
		return $this->serverFormat('Y-m-d');
	}

	public function serverTime(): string {
		return $this->serverFormat('H:i:s');
	}

	public function serverDatetime(): string {
		return $this->serverFormat('Y-m-d H:i:s');
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
