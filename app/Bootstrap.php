<?php declare(strict_types = 1);

namespace App;

define('ROOT_DIR', realpath(__DIR__ . "/.."));

class Bootstrap
{

	public static function boot(): \Nette\Configurator
	{
		if ('https' === getenv('HTTP_X_FORWARDED_PROTO')) {
			\Nette\Http\Url::$defaultPorts['https'] = (int) getenv('SERVER_PORT');
		}
		$configurator = new \Nette\Configurator();

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


	public static function bootForTests(): \Nette\Configurator
	{
		$configurator = self::boot();
		\Tester\Environment::setup();

		return $configurator;
	}
}
