<?php

namespace Gsdk\DateTime;

use Gsdk\DateTime;
use Gsdk\DateTimezone;
use Translation\DateInterval as TranslationDateInterval;
use Translation\Calendar;

abstract class Format {

	private static array $formats = [];

	public static function setFormat($alias, $format) {
		//$regexp = '/' . str_replace(array('.', '?', '|'), array('\\.', '\\?', '\\|'), $alias) . '/';
		//self::$formats[$alias] = [$regexp, $format];
		self::$formats[] = [$alias, $format, '~' . count(self::$formats) . '~'];
	}

	public static function getFormat($format) {
		if (isset(self::$formats[$format]))
			return self::$formats[$format][1];
		return $format;
	}

	public static function serverDate($datetime = null) {
		return DateTime::factory($datetime, DateTimezone::getServer())->format('server.date');
	}

	public static function serverTime($datetime = null) {
		return DateTime::factory($datetime, DateTimezone::getServer())->format('server.time');
	}

	public static function serverDatetime($datetime = null) {
		return DateTime::factory($datetime, DateTimezone::getServer())->format('server.datetime');
	}

	public function format($format) {
		foreach (self::$formats as $f) {
			if (is_callable($f[1]))
				$format = str_replace($f[0], $f[2], $format);
			else
				$format = str_replace($f[0], $f[1], $format);
		}

		$format = parent::format($format);

		$datetime = $this;
		$formats = self::$formats;
		return preg_replace_callback('/~(\d+)~/', function ($matches) use ($datetime, $formats) {
			return call_user_func($formats[$matches[1]][1], $datetime);
		}, $format);
	}

	public static function init() {

		DateTime::setFormat('date?time', function (DateTime $datetime) {
			if (DateTime::serverDate() === DateTime::serverDate($datetime))
				return DateTime::getFormat('time');
			return DateTime::getFormat('datetime');
		});
		DateTime::setFormat('date|time', function (DateTime $datetime) {
			if (DateTime::serverDate() === DateTime::serverDate($datetime))
				return DateTime::getFormat('time');
			return DateTime::getFormat('date');
		});
		DateTime::setFormat('time.diff', function (DateTime $datetime) { return Format::interval($datetime); });

		foreach ([
			         'server.datetime' => 'Y-m-d H:i:s',
			         'server.date' => 'Y-m-d',
			         'server.time' => 'H:i:s',
			         'datetime' => 'd.m.Y H:i',
			         'date' => 'd.m.Y',
			         'time' => 'H:i'
		         ] as $alias => $format) {
			DateTime::setFormat($alias, $format);
		}

		DateTime::setFormat('F', function (DateTime $datetime) { return Calendar::getMonth($datetime->getMonth()); });
		DateTime::setFormat('M', function (DateTime $datetime) { return Calendar::getMonthsShort($datetime->getMonth()); });
		DateTime::setFormat('D', function (DateTime $datetime) { return Calendar::getWeekDay($datetime->getWeekDay()); });
		DateTime::setFormat('l', function (DateTime $datetime) { return Calendar::getWeekDayShort($datetime->getWeekDay()); });
	}

}