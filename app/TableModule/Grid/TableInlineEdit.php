<?php declare(strict_types = 1);

namespace App\TableModule\Grid;

class TableInlineEdit
{

	private \App\TableModule\Model\Table\TableService $tableService;


	public function __construct(
		\App\TableModule\Model\Table\TableService $tableService
	)
	{
		$this->tableService = $tableService;
	}


	public function onControlAdd(\Nette\Forms\Container $container): void
	{
		$container->addSelect(
			\App\TableModule\Model\Table\TableMapping::COLUMN_IS_ACTIVE,
			\App\TableModule\Model\Table\TableMapping::COLUMN_IS_ACTIVE,
			[1 => 'YES', 0 => 'NO']
		)
			->setRequired()
		;
	}


	public function onSetDefaults(\Nette\Forms\Container $container, \Dibi\Row $table): void
	{
		$container->setDefaults([
			\App\TableModule\Model\Table\TableMapping::COLUMN_IS_ACTIVE => $table[\App\TableModule\Model\Table\TableMapping::COLUMN_IS_ACTIVE] ? 1 : 0,
		]);
	}


	public function onSubmit(string $id, \Nette\Utils\ArrayHash $values): void
	{
		$this->tableService->saveGridData($values, (int) $id);
	}

}
