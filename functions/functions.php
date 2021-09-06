<?php

function transliterate($s) {
	$s = (string)$s; // преобразуем в строковое значение
	$s = strip_tags($s); // убираем HTML-теги
	$s = str_replace(["\n", "\r"], " ", $s); // убираем перевод каретки
	$s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
	$s = trim($s); // убираем пробелы в начале и конце строки
	$s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
	$s = strtr($s, ['а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => '']);
	$s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
	$s = str_replace(" ", "_", $s); // заменяем пробелы знаком минус
	return $s; // возвращаем результат
}

function getWordDeclension($number, $variants, $addNumber = false) {
	$number = (int)abs($number);
	switch (true) {
		case ($number % 100 == 1 || ($number % 100 > 20) && ($number % 10 == 1)):
			$i = 0;
			break;
		case ($number % 100 == 2 || ($number % 100 > 20) && ($number % 10 == 2)):
		case ($number % 100 == 3 || ($number % 100 > 20) && ($number % 10 == 3)):
		case ($number % 100 == 4 || ($number % 100 > 20) && ($number % 10 == 4)):
			$i = 1;
			break;
		default:
			$i = 2;
	}
	if (is_string($variants)) {
		$variants = explode(',', $variants);
	}
	return ($addNumber ? $number . ' ' : '')
		. (isset($variants[$i]) ? $variants[$i] : null);
}

function getNumberDeclension($number, $variants) {
	$number = (int)abs($number);
	switch (true) {
		case ($number % 100 == 1 || ($number % 100 > 20) && ($number % 10 == 1)):
			$i = 0;
			break;
		case ($number % 100 == 3 || ($number % 100 > 20) && ($number % 10 == 3)):
			$i = 2;
			break;
		case ($number % 100 == 2 || ($number % 100 > 20) && ($number % 10 == 2)):
		case ($number % 100 == 3 || ($number % 100 > 20) && ($number % 10 == 6)):
		case ($number % 100 == 3 || ($number % 100 > 20) && ($number % 10 == 7)):
		case ($number % 100 == 3 || ($number % 100 > 20) && ($number % 10 == 8)):
			$i = 1;
			break;
		default:
			$i = 3;
	}
	if (is_string($variants)) {
		$variants = explode(',', $variants);
	}
	return (isset($variants[$i]) ? $variants[$i] : null);
}