<?php declare(strict_types=1);

namespace App\Utils\Tracy\BlueScreen;

use Throwable;
use Tracy\Helpers;
use ReflectionClass;
use Nette\Utils\Html;
use Nette\Utils\Strings;


/**
 * Panel odkazující na třídy obsažené v chybové zprávě.
 * Class BlueScreenPanel
 * @package App\Utils\Tracy\BlueScreen
 */
class BlueScreenPanel
{

	public const CLASS_REGEX = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';

	/**
	 * @param null|Throwable $e
	 * @return array|null|string[]
	 */
	public function __invoke(?Throwable $e)
	{
		if ($e) {
			$message = $e->getMessage();
			$classes = [];
			foreach (explode(' ', $message) as $word) {
				$word = trim($word, '\'"');
				if (strpos($word, '\\') !== false && class_exists($class = (Strings::before($word, ':') ?: $word))) {
					$rc = new ReflectionClass($class);
					if (($method = Strings::after($word, ':')) && $rc->hasMethod($method)) {
						$line = $rc->getMethod($method)->getStartLine();
					} else {
						$line = $rc->getStartLine();
					}
					$classes[$class] = [$rc->getFileName(), $line];
				}
			}
			$regex = self::CLASS_REGEX;
			$regex = "/$regex(\\\\$regex)+/";
			foreach (Strings::matchAll($message, $regex) as [$class]) {
				if (class_exists($class)) {
					$rc = new ReflectionClass($class);
					$classes[$class] = [$rc->getFileName(), $rc->getStartLine()];
				}
			}
			foreach ($e->getTrace() as $a => $trace) {
				if (isset($trace['file'])) {
					$file = $trace['file'];
					if (strpos($file, '.latte--')) {
						$name = Strings::after($file, '/', -1);
						$name = preg_replace('/--.*/', '', $name);
						$name = str_replace('-', '/', $name);
						$file = APP_DIR . '/' . $name;
						$classes[$name] = [$file, 1];
					}
				}
			}

			if ($classes) {
				return [
					'tab' => 'Class',
					'panel' => implode('', array_map(static function ($class, $point) {
						[$file, $line] = $point;
						return (string)Html::el('div')->addHtml(Html::el('a', ['href' => Helpers::editorUri($file, $line, 'create')])->setText($class));
					}, array_keys($classes), array_values($classes))),
				];
			}

		}
		return null;
	}

}
