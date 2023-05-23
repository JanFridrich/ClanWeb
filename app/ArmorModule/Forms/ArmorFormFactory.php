<?php declare(strict_types = 1);

namespace App\ArmorModule\Forms;

class ArmorFormFactory
{

	private \App\ArmorModule\Model\Armor\ArmorService $armorService;

	private \App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService;


	public function __construct(
		\App\ArmorModule\Model\Armor\ArmorService $armorService,
		\App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService
	)
	{
		$this->armorService = $armorService;
		$this->armorTypeService = $armorTypeService;
	}


	public function createNew(callable $onSuccess): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$form->addText(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_NAME, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_NAME)
			->setRequired()
		;
		$form->addInteger(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT);
		$form->addSelect(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_ARMOR_TYPE, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_ARMOR_TYPE, $this->armorTypeService->getArmorTypePairs())
			->setRequired()
		;
		$form->addUpload(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE)
		;
		$form->addSubmit('save', 'Create');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess): void {
			$id = $this->armorService->createNew($values);
			$onSuccess($id);
		};

		return $form;
	}


	public function createEdit(callable $onSuccess, int $armorId): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		/** @var \App\ArmorModule\Model\Armor\Armor $armor */
		$armor = $this->armorService->get($armorId);
		$form->addText(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_NAME, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_NAME)
			->setRequired()
			->setDefaultValue($armor->getName())
		;
		$form->addInteger(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT)
			->setDefaultValue($armor->getSort())
		;
		$form->addSelect(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_ARMOR_TYPE, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_ARMOR_TYPE, $this->armorTypeService->getArmorTypePairs())
			->setDefaultValue($armor->getArmorType()->getId())
			->setRequired()
		;
		$form->addUpload(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE)
		;
		$form->addSubmit('send', 'Edit');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess, $armor): void {
			$this->armorService->saveFormData($values, $armor);
			$onSuccess();
		};

		return $form;
	}
}
