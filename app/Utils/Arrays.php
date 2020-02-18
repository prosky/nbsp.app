<?php


namespace App\Utils;


class Arrays
{
	public static function group($array, string $property, string $index = null): array
	{
		$value = [];
		foreach ($array as $id => $row) {
			if ($index && isset($row[$index])) {
				$value[$row[$property]][$row[$index]] = $row;
			} else {
				$value[$row[$property]][] = $row;
			}
		}
		return $value;
	}

	public static function flatten(array &$array): array
	{
		$values = [];
		foreach ($array as $id => &$row) {
			if (is_array($row)) {
				foreach (self::flatten($row) as $key => $value) {
					$values["$id.$key"] = $value;
				}
			} else {
				$values[$id] = $row;
			}
		}
		return $values;
	}

	/**
	 * Set an array item to a given value using "dot" notation.
	 * If no key is given to the method, the entire array will be replaced.
	 * @param array $array
	 * @param mixed $key
	 * @param mixed $value
	 * @return array
	 */
	public static function set(&$array, $key, $value): array
	{
		if ($key === null) {
			return $array = $value;
		}
		$keys = explode('.', (string)$key);
		while (count($keys) > 1) {
			$key = array_shift($keys);
			if (!isset($array[$key]) || !is_array($array[$key])) {
				$array[$key] = [];
			}
			$array = &$array[$key];
		}
		$array[array_shift($keys)] = $value;
		return $array;
	}

	/**
	 * Sort array by keys recursively
	 * @param array $array
	 * @param int $sort_flags
	 * @return array
	 */
	public static function rksort(array &$array, $sort_flags = SORT_REGULAR): array
	{
		ksort($array, $sort_flags);
		foreach ($array as &$arr) {
			is_array($arr) && self::rksort($arr, $sort_flags);
		}
		return $array;
	}

	public static function fromEntries($entries): array
	{
		$array = [];
		foreach ($entries as $entry) {
			foreach ($entry as $key => $value) {
				if (is_int($key)) {
					$array[] = $value;
				} else {
					$array[$key] = $value;
				}
			}
		}
		return $array;
	}

	public static function prefixKeys(string $prefix, $in): array
	{
		$out = [];
		foreach ($in as $key => $value) {
			$out[$prefix . $key] = $value;
		}
		return $out;
	}

	public static function extend(&$data, ...$others)
	{
		foreach ($others as $item) {
			foreach ($item as $key => $value) {
				$data[$key] = $value;
			}
		}
		return $data;
	}

	public static function columns(array $items, array $columns): array
	{
		foreach ($items as $id => $item) {
			$data = [];
			foreach ($columns as $column) {
				$data[$column] = $item[$column];
			}
			$items[$id] = $data;
		}
		return $items;
	}


}
