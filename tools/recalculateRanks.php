<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

$configurator = \App\Bootstrap::boot();
$container = $configurator->createContainer();
/** @var \App\UnitModule\Model\UnitService $unitService */
$unitService = $container->getByType(\App\UnitModule\Model\UnitService::class);

/** @var \App\UnitModule\Model\UserUnitService $userUnitService */
$userUnitService = $container->getByType(\App\UnitModule\Model\UserUnitService::class);

/** @var \App\UserModule\Model\UserService $userService */
$userService = $container->getByType(\App\UserModule\Model\UserService::class);

/**
 * @var int $userId
 * @var \App\UserModule\Model\User $user
 */
foreach ($userService->getAll() as $userId => $user) {

	$maxedUnits = (\round($userUnitService->getMaxedUnits($user->getId(), [\App\UnitModule\Model\UnitService::SHOW_ALL => FALSE]) / $unitService->getCountOfUnits([\App\UnitModule\Model\UnitService::SHOW_ALL => FALSE]), 1)) * 100;
	$user->setMaxedUnits((int)$maxedUnits);
	$userService->saveFormData($user->toArray(), $user);
}

echo 'Done';
