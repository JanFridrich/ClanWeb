<?php declare(strict_types = 1);

namespace App\UnitModule\Grid\UserUnitDataGridFactory;

class DataSourceGetter
{

	private \App\UnitModule\Model\UnitService $unitService;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\App\UnitModule\Model\UnitService $unitService,
		\App\UserModule\Model\UserService $userService
	)
	{
		$this->unitService = $unitService;
		$this->userService = $userService;
	}


	public function getVeterancyDataForGrid(): array
	{
		$users = $this->userService->getAll();
		$gridData = [];
		$units = $this->unitService->getAll();

		/** @var \App\UserModule\Model\User $user */
		foreach ($users as $user) {
			$userId = $user->getId();
			$gridData[$userId]['user'] = $user->getLogin();
			$gridData[$userId]['id'] = $userId;
			$userUnits = $user->getUnits();
			/** @var \App\UnitModule\Model\Unit $unit */
			foreach ($units as $unitId => $unit) {
				$level = '';
				if (isset($userUnits[$unitId])) {
					$level = $userUnits[$unitId]->getVeterancyLine();
				}
				$gridData[$userId]['unit' . $unitId] = $level;
			}
		}

		return $gridData;
	}


	public function getMasteryDataForGrid(): array
	{
		$users = $this->userService->getAll();
		$gridData = [];
		$units = $this->unitService->getAll();

		/** @var \App\UserModule\Model\User $user */
		foreach ($users as $user) {
			$userId = $user->getId();
			$gridData[$userId]['user'] = $user->getLogin();
			$gridData[$userId]['id'] = $userId;
			$userUnits = $user->getUnits();
			/** @var \App\UnitModule\Model\Unit $unit */
			foreach ($units as $unitId => $unit) {
				$level = '';
				if (isset($userUnits[$unitId]) && $unit->getMaxMastery() !== 0) {
					$level = $userUnits[$unitId]->getUserMastery();
					if ($level === $unit->getMaxMastery()) {
						$level = 'max';
					}
				}
				$gridData[$userId]['unit' . $unitId] = $level;
			}
		}

		return $gridData;
	}


	public function getLevelDataForGrid(): array
	{
		$users = $this->userService->getAll();
		$gridData = [];
		$units = $this->unitService->getAll();

		/** @var \App\UserModule\Model\User $user */
		foreach ($users as $user) {
			$userId = $user->getId();
			$gridData[$userId]['user'] = $user->getLogin();
			$gridData[$userId]['id'] = $userId;
			$userUnits = $user->getUnits();
			/** @var \App\UnitModule\Model\Unit $unit */
			foreach ($units as $unitId => $unit) {
				$level = '';
				if (isset($userUnits[$unitId])) {
					$level = $userUnits[$unitId]->getLevel();
					if ($level === $unit->getMaxLevel()) {
						$level = 'max';
					}
				}
				$gridData[$userId]['unit' . $unitId] = $level;
			}
		}

		return $gridData;
	}
}
