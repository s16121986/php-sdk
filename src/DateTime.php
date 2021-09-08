<?php

namespace Gsdk;

use DateTime as BaseDateTime;
use DateTimeZone as BaseDateTimeZone;
use Gsdk\DateTime\Format;

class DateTime extends BaseDateTime {

	public static function factory($date, $timezone = null): DateTime {
		$factoryDate = new self('now', DateTimezone::getServer());

		if ($date instanceof self) {
			$factoryDate->setTimezone($date->getTimezone());
			$factoryDate->setTimestamp($date->getTimestamp());
		} else if (is_int($date)) {
			$factoryDate->setTimestamp($date);
		} else if (is_string($date)) {
			//$dt = new BaseDateTime($date, $timezone);
			//$dt = self::createFromFormat('Y-m-d', $date);
			$factoryDate->setTimestamp(strtotime($date));
		}

		$factoryDate->setTimezone($timezone);

		return $factoryDate;
	}

	public static function now(): DateTime {
		return self::factory(null);
	}

	public function __construct($time = 'now', BaseDateTimeZone $timezone = null) {
		parent::__construct($time);
		$this->setTimezone($timezone);
	}

	public function setTimezone($timezone): DateTime {
		if (null === $timezone)
			$timezone = DateTimeZone::getClient();
		if (is_string($timezone) && DateTimeZone::get($timezone))
			$timezone = DateTimeZone::get($timezone);

		if (!$timezone)
			return $this;

		return parent::setTimezone($timezone);
	}

	public function format($format) {
		return Format::format($this, $format);
	}

	public function formatTime() {
		return self::format('time');
	}

	public function formatDate() {
		return self::format('date');
	}

	public function formatDatetime() {
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
