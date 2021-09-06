<?php

use Gsdk\DateTime;

function __initDateTimeArgument($date) {
	return DateTime::factory($date);
}

function CurrentDate() {
	return __initDateTimeArgument(null);
}

function Year($date = null) {
	return __initDateTimeArgument($date)->getYear();
}

function Month($date = null) {
	return __initDateTimeArgument($date)->getMonth();
}

function Day($date = null) {
	return __initDateTimeArgument($date)->getDay();
}

function Hour($date = null) {
	return __initDateTimeArgument($date)->getHour();
}

function Minute($date = null) {
	return __initDateTimeArgument($date)->getMinute();
}

function Second($date = null) {
	return __initDateTimeArgument($date)->getSecond();
}

function DayOfYear($date = null) {
	return (int)__initDateTimeArgument($date)->format('z');
}

function WeekOfYear($date = null) {
	return (int)__initDateTimeArgument($date)->format('W');
}

function BegOfYear($date = null) {
	$datetime = __initDateTimeArgument($date);
	$datetime
		->setDate($datetime->getYear(), 1, 1)
		->setTime(0, 0, 0);
	return $datetime;
}

function EndOfYear($date = null) {
	$datetime = __initDateTimeArgument($date);
	$datetime
		->setDate($datetime->getYear(), 12, 31)
		->setTime(23, 59, 59);
	return $datetime;
}

function BegOfQuarter($date = null) {

}

function EndOfQuarter($date = null) {

}

function BegOfMonth($date = null) {
	$datetime = __initDateTimeArgument($date);
	$datetime->modify('first day of this month');
	$datetime->setTime(0, 0, 0);
	return $datetime;
}

function EndOfMonth($date = null) {
	$datetime = __initDateTimeArgument($date);
	$datetime->modify('last day of this month');
	$datetime->setTime(23, 59, 59);
	return $datetime;
}

function AddMonth($date = null) {
	$datetime = __initDateTimeArgument($date);
	$datetime->modify('+1 month');
	return $datetime;
}

function WeekDay($date = null) {
	return __initDateTimeArgument($date)->getWeekDay();
}

function BegOfWeek($date = null) {
	$datetime = __initDateTimeArgument($date);
	$d = $datetime->getWeekDay();
	if ($d > 1) {
		$datetime->modify('-' . ($d - 1) . ' day');
	}
	$datetime->setTime(0, 0, 0);
	return $datetime;
}

function EndOfWeek($date = null) {
	$datetime = __initDateTimeArgument($date);
	$d = $datetime->getWeekDay();
	if ($d < 7) {
		$datetime->modify('+' . (7 - $d) . ' day');
	}
	$datetime->setTime(23, 59, 59);
	return $datetime;
}

function BegOfHour($date = null) {

}

function EndOfHour($date = null) {

}

function BegOfMinute($date = null) {

}

function EndOfMinute($date = null) {

}

function BegOfDay($date = null) {
	$datetime = __initDateTimeArgument($date);
	$datetime->setTime(0, 0, 0);
	return $datetime;
}

function EndOfDay($date = null) {
	$datetime = __initDateTimeArgument($date);
	$datetime->setTime(23, 59, 59);
	return $datetime;
}