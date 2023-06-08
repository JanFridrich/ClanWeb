<?php declare(strict_types = 1);

namespace App\TableModule\Grid;

class TableGridFactory extends \App\CoreModule\GridFactory\DataGridFactory
{

	private \App\TableModule\Model\Table\TableService $tableService;

	private \App\TableModule\Grid\TableRenderer $tableRenderer;

	private \App\TableModule\Grid\TableInlineEdit $inlineEdit;


	public function __construct(
		\App\TableModule\Model\Table\TableService $tableService,
		\App\TableModule\Grid\TableRenderer $tableRenderer,
		\App\TableModule\Grid\TableInlineEdit $inlineEdit
	)
	{
		$this->tableService = $tableService;
		$this->tableRenderer = $tableRenderer;
		$this->inlineEdit = $inlineEdit;
	}


	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$dataGrid = parent::create($locale);
		$dataGrid->setDataSource(
			$this->tableService->prepareSelectForGridAndAll(
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
			->setRenderer([$this->tableRenderer, 'renderCreatedBy'])
			->setFilterText()
		;

		$dataGrid->addColumnText(\App\TableModule\Model\Table\TableMapping::COLUMN_IS_ACTIVE, 'Status')
			->setSortable()
			->setRenderer([$this->tableRenderer, 'renderIsActive'])
			->setFilterText()
		;

		$inlineEdit = $dataGrid->addInlineEdit();
		$inlineEdit->onControlAdd[] = [$this->inlineEdit, 'onControlAdd'];
		$inlineEdit->onSetDefaults[] = [$this->inlineEdit, 'onSetDefaults'];
		$inlineEdit->onSubmit[] = [$this->inlineEdit, 'onSubmit'];
		$inlineEdit->setShowNonEditingColumns();

		return $dataGrid;
	}
}
