<?php declare(strict_types = 1);

namespace App;

define('ROOT_DIR', realpath(__DIR__ . "/.."));

use Nette\Configurator;

class Bootstrap
{

	public static function boot(): Configurator
	{
		if ('https' === getenv('HTTP_X_FORWARDED_PROTO')) {
			\Nette\Http\Url::$defaultPorts['https'] = (int) getenv('SERVER_PORT');
		}
		$configurator = new Configurator;

		$configurator->setDebugMode(isset($_COOKIE['DLEMBOR_DEBUG']) && $_COOKIE['DLEMBOR_DEBUG']); // enable for your remote IP
		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register()
		;

		$configurator
			->addConfig(__DIR__ . '/config/common.neon')
			->addConfig(__DIR__ . '/config/local.neon')
		;

		return $configurator;
	}


	public static function bootForTests(): Configurator
	{
		$configurator = self::boot();
		\Tester\Environment::setup();

		return $configurator;
	}
}
