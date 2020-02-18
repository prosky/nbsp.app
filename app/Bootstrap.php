<?php

declare(strict_types=1);

namespace App;

use Tracy;
use Nette\Configurator;

define('APP_DIR', __DIR__);
define('ROOT_DIR', dirname(__DIR__));
define('SESSIONS_DIR', ROOT_DIR . '/sessions');
define('ASSETS_DIR', WWW_DIR . '/assets');
define('TEMP_DIR', ROOT_DIR . '/temp');
define('LOG_DIR', ROOT_DIR . '/log');

Tracy\Debugger::$maxDepth = 6;
Tracy\Debugger::$maxLength = 500;
Tracy\Debugger::$editor = 'http://localhost:8091?message=%file:%line';
Tracy\Debugger::$showLocation = true;

// nastavovani prav zapisu pro temp NEMAZAT!
umask(0);
register_shutdown_function(static function () {
	register_shutdown_function(static function () {
		session_id() && @chmod(SESSIONS_DIR . '/sess_' . session_id(), 0777);
	});
});

class Bootstrap
{
	public const DEBUG_SECRET = 'Blbn3tt3';
	public const ALLOWED_IP = ['212.20.107.230', '127.0.0.1', '::1'];

	public static function boot(): Configurator
	{
		$configurator = new Configurator;

		/*
		 * Pro aktivaci debug modu přidejte cookie tracy-debug s hodnotou Blbn3tt3.
		 * Např javascriptem: document.cookie = 'nette-debug=Blbn3tt3;path=/;'
		 */
		$ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR') ?: php_uname('n');
		$secret = filter_input(INPUT_COOKIE, Configurator::COOKIE_SECRET, FILTER_SANITIZE_STRING);
		$configurator->setDebugMode($secret === self::DEBUG_SECRET && in_array($ip, self::ALLOWED_IP, true));


		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->addConfig(__DIR__ . '/config/local.neon');

		return $configurator;
	}
}
