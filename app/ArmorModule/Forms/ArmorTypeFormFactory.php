<?php declare(strict_types = 1);

namespace App\ArmorModule\Forms;

class ArmorTypeFormFactory
{

	private \App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService;


	public function __construct(
		\App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService
	)
	{
		$this->armorTypeService = $armorTypeService;
	}


	public function createNew(callable $onSuccess): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();

		$form->addText(\App\ArmorModule\Model\ArmorType\ArmorTypeMapping::COLUMN_NAME, \App\ArmorModule\Model\ArmorType\ArmorTypeMapping::COLUMN_NAME)
			->setRequired()
		;
		$form->addSubmit('submit', 'Create');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess): void {
			$this->armorTypeService->createNew($values);
			$onSuccess();
		};

		return $form;
	}


	public function createEdit(callable $onSuccess, int $armorTypeId): \Nette\Application\UI\Form
	{
		/** @var \App\ArmorModule\Model\ArmorType\ArmorType $armorType */
		$armorType = $this->armorTypeService->get($armorTypeId);
		$form = new \Nette\Application\UI\Form();

		$form->addText(\App\ArmorModule\Model\ArmorType\ArmorTypeMapping::COLUMN_NAME, \App\ArmorModule\Model\ArmorType\ArmorTypeMapping::COLUMN_NAME)
			->setRequired()
			->setDefaultValue($armorType->getName())
		;
		$form->addSubmit('submit', 'Edit');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess, $armorType): void {
			$this->armorTypeService->saveFormData($values, $armorType);
			$onSuccess();
		};

		return $form;
	}
}
