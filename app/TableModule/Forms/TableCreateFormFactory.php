<?php declare(strict_types = 1);

namespace App\TableModule\Forms;

class TableCreateFormFactory
{

	private \App\TableModule\Model\Table\TableService $tableService;


	public function __construct(\App\TableModule\Model\Table\TableService $tableService)
	{
		$this->tableService = $tableService;
	}


	public function create(callable $onSuccess, \App\UserModule\Model\User $user): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$form->addText(\App\TableModule\Model\Table\TableMapping::COLUMN_NAME, \App\TableModule\Model\Table\TableMapping::COLUMN_NAME)
			->setRequired()
		;
		$form->addInteger(\App\TableModule\Model\Table\TableMapping::COLUMN_ROWS, \App\TableModule\Model\Table\TableMapping::COLUMN_ROWS)
			->setRequired()
			->setDefaultValue(1)
			->addRule(\Nette\Application\UI\Form::INTEGER)
			->addRule(\Nette\Application\UI\Form::RANGE, 'Rows have to be between 1 and 100', [1, 100])
		;
		$form->addSelect('tierLock', 'tierLock', \App\UnitModule\Model\Unit::TIERS)
			->setDefaultValue(\App\UnitModule\Model\Unit::TIER_ORANGE)
		;

		$form->addSubmit('submit', 'Create');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess, $user) {
			$values[\App\TableModule\Model\Table\TableMapping::COLUMN_CREATED_BY] = $user->getId();
			$values[\App\TableModule\Model\Table\TableMapping::COLUMN_CREATED] = new \Dibi\DateTime();
			$values[\App\TableModule\Model\Table\TableMapping::COLUMN_STATUS] = 0;
			$tier = $values['tierLock'];
			unset($values['tierLock']);
			$id = $this->tableService->createNew($values);
			$onSuccess($id, $tier);
		};

		return $form;
	}
}
