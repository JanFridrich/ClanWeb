<?php declare(strict_types = 1);

namespace App\TableModule\Grid;

class TableGridFactory extends \App\CoreModule\GridFactory\DataGridFactory
{

	private \App\TableModule\Model\Table\TableService $tableService;

	private \App\TableModule\Grid\TableRenderer $tableRenderer;



	public function __construct(
		\App\TableModule\Model\Table\TableService $tableService,
		\App\TableModule\Grid\TableRenderer $tableRenderer
	)
	{
		$this->tableService = $tableService;
		$this->tableRenderer = $tableRenderer;
	}


	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$dataGrid = parent::create($locale);
		$dataGrid->setDataSource(
			$this->tableService->getAll(
				[
					'where' =>
						[
							\App\TableModule\Model\Table\TableMapping::COLUMN_STATUS => \App\TableModule\Model\Table\Table::STATUS_FINISHED,
						],
				]
			)
		);
		$dataGrid->addColumnNumber('id', 'Id')
			->setSortable()
			->setAlign('left')
		;

		$dataGrid->addColumnText(\App\TableModule\Model\Table\TableMapping::COLUMN_NAME, \App\TableModule\Model\Table\TableMapping::COLUMN_NAME)
			->setSortable()
			->setFilterText()
		;
		$dataGrid->addColumnText(\App\TableModule\Model\Table\TableMapping::COLUMN_CREATED, \App\TableModule\Model\Table\TableMapping::COLUMN_CREATED)
			->setSortable()
			->setFilterText()
		;

		$dataGrid->addColumnText(\App\TableModule\Model\Table\TableMapping::COLUMN_CREATED_BY, \App\TableModule\Model\Table\TableMapping::COLUMN_CREATED_BY)
			->setSortable()
			->setFilterText()
		;

		$dataGrid->addColumnText(\App\TableModule\Model\Table\TableMapping::COLUMN_IS_ACTIVE, 'Status')
			->setSortable()
			->setRenderer([$this->tableRenderer, 'renderIsActive'])
			->setFilterText()
		;
		$dataGrid->addAction('edit', 'Edit')
			->setIcon('pencil')
			->setTitle('Edit')
			;

		return $dataGrid;
	}
}
