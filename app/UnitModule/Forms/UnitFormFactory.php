<?php declare(strict_types = 1);

namespace App\UnitModule\Forms;

class UnitFormFactory
{

	private \App\UnitModule\Model\UnitService $unitService;


	public function __construct(\App\UnitModule\Model\UnitService $unitService)
	{
		$this->unitService = $unitService;
	}


	public function createNew(callable $onSuccess): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$form->addText(\App\UnitModule\Model\UnitMapping::COLUMN_NAME, \App\UnitModule\Model\UnitMapping::COLUMN_NAME)
			->setRequired()
		;

		$form->addSelect(\App\UnitModule\Model\UnitMapping::COLUMN_TIER, \App\UnitModule\Model\UnitMapping::COLUMN_TIER, \App\UnitModule\Model\Unit::TIERS)
			->setRequired()
		;
		$form->addSelect(\App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY, \App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY, \App\UnitModule\Model\Unit::CATEGORIES)
			->setRequired()
		;
		$form->addSelect(\App\UnitModule\Model\UnitMapping::COLUMN_PRIORITY, \App\UnitModule\Model\UnitMapping::COLUMN_PRIORITY, \App\UnitModule\Model\Unit::PRIORITIES)
			->setRequired()
		;
		$form->addSelect(\App\UnitModule\Model\UnitMapping::COLUMN_VETERANCY_LINE, \App\UnitModule\Model\UnitMapping::COLUMN_VETERANCY_LINE, \App\UnitModule\Model\Unit::VETERANCIES)
			->setRequired()
		;
		$form->addInteger(\App\UnitModule\Model\UnitMapping::COLUMN_LEADERSHIP, \App\UnitModule\Model\UnitMapping::COLUMN_LEADERSHIP)
			->setRequired()
		;
		$form->addInteger(\App\UnitModule\Model\UnitMapping::COLUMN_MAX_LEVEL, \App\UnitModule\Model\UnitMapping::COLUMN_MAX_LEVEL)
			->setDefaultValue(\App\UnitModule\Model\Unit::DEFAULT_MAX_LEVELS[\App\UnitModule\Model\Unit::TIER_GREY])
		;
		$form->addInteger(\App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY, \App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY);
		$form->addInteger(\App\UnitModule\Model\UnitMapping::COLUMN_SORT, \App\UnitModule\Model\UnitMapping::COLUMN_SORT);
		$form->addUpload(\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE, \App\UnitModule\Model\UnitMapping::COLUMN_IMAGE);

		$form->addSubmit('send', 'Create');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess): void {

			$id = $this->unitService->createNew($values);
			$onSuccess($id);
		};

		return $form;
	}


	public function createEdit(callable $onSuccess, int $unitId): \Nette\Application\UI\Form
	{
		/** @var \App\UnitModule\Model\Unit|null $unit */
		$unit = $this->unitService->get($unitId);
		$form = new \Nette\Application\UI\Form();
		if ( ! $unit) {
			return $form;
		}
		$form->addText(\App\UnitModule\Model\UnitMapping::COLUMN_NAME, \App\UnitModule\Model\UnitMapping::COLUMN_NAME)
			->setRequired()
			->setDefaultValue($unit->getName())
		;

		$form->addSelect(\App\UnitModule\Model\UnitMapping::COLUMN_TIER, \App\UnitModule\Model\UnitMapping::COLUMN_TIER, \App\UnitModule\Model\Unit::TIERS)
			->setRequired()
			->setDefaultValue($unit->getTier())
		;
		$form->addSelect(\App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY, \App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY, \App\UnitModule\Model\Unit::CATEGORIES)
			->setRequired()
			->setDefaultValue($unit->getCategory())
		;
		$form->addSelect(\App\UnitModule\Model\UnitMapping::COLUMN_PRIORITY, \App\UnitModule\Model\UnitMapping::COLUMN_PRIORITY, \App\UnitModule\Model\Unit::PRIORITIES)
			->setRequired()
			->setDefaultValue($unit->getPriority())
		;
		$form->addSelect(\App\UnitModule\Model\UnitMapping::COLUMN_VETERANCY_LINE, \App\UnitModule\Model\UnitMapping::COLUMN_VETERANCY_LINE, \App\UnitModule\Model\Unit::VETERANCIES)
			->setRequired()
			->setDefaultValue($unit->getVeterancyLine())
		;
		$form->addInteger(\App\UnitModule\Model\UnitMapping::COLUMN_LEADERSHIP, \App\UnitModule\Model\UnitMapping::COLUMN_LEADERSHIP)
			->setDefaultValue($unit->getLeadership())
		;
		$form->addInteger(\App\UnitModule\Model\UnitMapping::COLUMN_MAX_LEVEL, \App\UnitModule\Model\UnitMapping::COLUMN_MAX_LEVEL)
			->setDefaultValue($unit->getMaxLevel())
		;
		$form->addInteger(\App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY, \App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY)
			->setDefaultValue($unit->getMaxMastery())
		;
		$form->addInteger(\App\UnitModule\Model\UnitMapping::COLUMN_SORT, \App\UnitModule\Model\UnitMapping::COLUMN_SORT)
			->setDefaultValue($unit->getSort())
		;
		$form->addUpload(\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE, \App\UnitModule\Model\UnitMapping::COLUMN_IMAGE);

		$form->addSubmit('send', 'Save');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess, $unit): void {

			$this->unitService->saveFormData($values, $unit);
			$onSuccess();
		};

		return $form;
	}
}
