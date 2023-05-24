<?php declare(strict_types = 1);

namespace App\ArmorModule\Forms;

class UserArmorFormFactory
{

	private \App\ArmorModule\Model\Armor\ArmorService $armorService;

	private \App\ArmorModule\Model\UserArmor\UserArmorService $userArmorService;


	public function __construct(
		\App\ArmorModule\Model\Armor\ArmorService $armorService,
		\App\ArmorModule\Model\UserArmor\UserArmorService $userArmorService
	)
	{
		$this->armorService = $armorService;
		$this->userArmorService = $userArmorService;
	}


	public function create(callable $onSuccess, \App\UserModule\Model\User $user): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$userArmors = $user->getArmors();
		/** @var \App\ArmorModule\Model\Armor\Armor $armor */
		foreach ($this->armorService->getAll() as $armor) {
			$armorForm = new \Nette\Forms\Container();

			$armorForm->addSelect(\App\ArmorModule\Model\UserArmor\UserArmorMapping::COLUMN_PREFER, $armor->getName(), \App\ArmorModule\Model\Armor\Armor::SKILL_LEVELS)
				->setDefaultValue(isset($userArmors[$armor->getId()]) ? $userArmors[$armor->getId()]->getPrefer() : 0)
			;

			$form->addComponent($armorForm, (string) $armor->getId());
		}
		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess, $user): void {
			$this->userArmorService->saveFormData($values, $user);
			$onSuccess();
		};

		return $form;
	}

}
