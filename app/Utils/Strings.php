<?php


namespace App\Utils;


class Strings
{


	public static function mergeCommon(array $strings, string $separator = '/'): ?string
	{
		$strings = array_filter($strings);
		if (!$strings) {
			return null;
		}
		$prefix = \Nette\Utils\Strings::findPrefix(array_map('strval', $strings));
		$diff = [];
		foreach ($strings as $string) {
			$diff[] = \Nette\Utils\Strings::after($string, $prefix);
		}
		return $prefix . implode($separator, array_filter($diff));
	}


}
