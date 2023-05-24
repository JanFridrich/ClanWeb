<?php declare(strict_types = 1);

namespace App\ArmorModule\Forms;

class UserArmorTypeFormFactory
{

	private \App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService;

	private \App\ArmorModule\Model\UserArmorType\UserArmorTypeService $userArmorTypeService;


	public function __construct(
		\App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService,
		\App\ArmorModule\Model\UserArmorType\UserArmorTypeService $userArmorTypeService
	)
	{
		$this->armorTypeService = $armorTypeService;
		$this->userArmorTypeService = $userArmorTypeService;
	}


	public function create(callable $onSuccess, \App\UserModule\Model\User $user): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$userArmors = $user->getArmors();
		/** @var \App\ArmorModule\Model\ArmorType\ArmorType $armorType */
		foreach ($this->armorTypeService->getAll() as $armorType) {
			$armorTypeForm = new \Nette\Forms\Container();
			$leadership = \App\ArmorModule\Model\Armor\Armor::DEFAULT_LEADERSHIP;
			foreach ($userArmors as $armor) {
				if ($armor->getArmorType()->getId() === $armorType->getId()) {
					$leadership = $armor->getLeadership();
					break;
				}
			}
			$armorTypeForm->addInteger(\App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::COLUMN_LEADERSHIP, $armorType->getName())
				->setDefaultValue($leadership)
				->setRequired()
			;

			$form->addComponent($armorTypeForm, (string) $armorType->getId());
		}
		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess, $user): void {
			$this->userArmorTypeService->saveFormData($values, $user);
			$onSuccess();
		};

		return $form;
	}

}
