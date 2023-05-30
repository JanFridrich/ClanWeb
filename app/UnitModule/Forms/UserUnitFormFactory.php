<?php declare(strict_types = 1);

namespace App\UnitModule\Forms;

class UserUnitFormFactory
{

	private \App\UnitModule\Model\UnitService $unitService;

	private \App\UnitModule\Model\UserUnitService $userUnitService;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\App\UnitModule\Model\UnitService $unitService,
		\App\UnitModule\Model\UserUnitService $userUnitService,
		\App\UserModule\Model\UserService $userService
	)
	{
		$this->unitService = $unitService;
		$this->userUnitService = $userUnitService;
		$this->userService = $userService;
	}


	public function create(callable $onSuccess, \App\UserModule\Model\User $user, array $options): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$userUnits = $user->getUnits();
		$allUnits = $this->unitService->getAll($options);
		/** @var \App\UnitModule\Model\Unit $unit */
		foreach ($allUnits as $unit) {
			$unitForm = new \Nette\Forms\Container();
			$unitForm->addSelect(\App\UnitModule\Model\UserUnitMapping::COLUMN_LEVEL, 'Level', $unit->getLevelsArray())
				->setDefaultValue(isset($userUnits[$unit->getId()]) ? $userUnits[$unit->getId()]->getLevel() : 0)
				->setRequired()
			;
			$unitForm->addSelect(\App\UnitModule\Model\UserUnitMapping::COLUMN_LINE, 'Veterancy line', \App\UnitModule\Model\Unit::VETERANCIES)
				->setDefaultValue(isset($userUnits[$unit->getId()]) ? $userUnits[$unit->getId()]->getUserVeterancyLine() : \App\UnitModule\Model\Unit::VETERANCY_HYBRID)
				->setRequired()
			;
			if ($unit->getMaxMastery() > 0) {
				$unitForm->addSelect(\App\UnitModule\Model\UserUnitMapping::COLUMN_MASTERY, 'Mastery', $unit->getMasteriesArray())
					->setDefaultValue(isset($userUnits[$unit->getId()]) ? $userUnits[$unit->getId()]->getUserMastery() : 0)
					->setRequired()
				;
			}

			$form->addComponent($unitForm, (string) $unit->getId());
		}
		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess, $user, $allUnits): void {
			$maxedUnits = $this->userUnitService->saveFormData(['values' => $values, 'units' => $allUnits], $user);
			$user->setLastUpdatedUnits(new \Dibi\DateTime());
			$user->setMaxedUnits($maxedUnits);
			$this->userService->saveFormData($user->toArray(), $user);
			$onSuccess();
		};

		return $form;
	}

}
