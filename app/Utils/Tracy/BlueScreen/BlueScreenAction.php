<?php declare(strict_types=1);

namespace App\Utils\Tracy\BlueScreen;

use Throwable;
use Tracy\Helpers;
use ReflectionClass;

class BlueScreenAction
{
	/**
	 * @param null|Throwable $e
	 * @return array|null|string[]
	 */
	public function __invoke(?Throwable $e): ?array
	{
		if (preg_match('# ([\'"])(\w{3,}(?:\\\\\w{3,})+)\\1#i', $e->getMessage(), $m)) {
			$class = $m[2];
			$file = (new ReflectionClass($class))->getFileName();
			return [
				'link' => Helpers::editorUri($file, 1, 'create'),
				'label' => 'class',
			];
		}
	}

}
