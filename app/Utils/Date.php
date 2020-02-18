<?php declare(strict_types=1);

namespace App\Utils;

use Nette\Utils\DateTime;
use Nette\InvalidStateException;

class Date extends DateTime
{

	public function __construct(string $date = 'now')
	{
		parent::__construct($date, null);
		parent::setTime(0, 0, 0, 0);
	}

	/**
	 * @param int $hour
	 * @param int $minute
	 * @param int $second
	 * @param int $microseconds
	 * @return false|static
	 * @throws InvalidStateException
	 * @deprecated
	 */
	public function setTime($hour, $minute, $second = 0, $microseconds = 0): DateTime
	{
		if ($hour !== 0 || $minute !== 0 || $second !== 0 || $microseconds !== 0) {
			throw new InvalidStateException('Date cannot contain time.');
		}
		return $this;
	}
}
