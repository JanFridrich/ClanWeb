<?php declare(strict_types = 1);

namespace App\UnitModule\Grid;

class UnitDataGridFactory extends \App\CoreModule\GridFactory\DataGridFactory
{

	private \App\UnitModule\Model\UnitService $unitService;


	public function __construct(
		\App\UnitModule\Model\UnitService $unitService
	)
	{
		$this->unitService = $unitService;
	}


	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid->setDataSource($this->unitService->getAll());
		$grid->addColumnText(\App\UnitModule\Model\UnitMapping::COLUMN_NAME, \App\UnitModule\Model\UnitMapping::COLUMN_NAME)
			->setSortable()
			->setFilterText()
		;
		$grid->addColumnText(\App\UnitModule\Model\UnitMapping::COLUMN_TIER, \App\UnitModule\Model\UnitMapping::COLUMN_TIER)
			->setSortable()
			->setFilterText()
		;
		$grid->addColumnText(\App\UnitModule\Model\UnitMapping::COLUMN_PRIORITY, \App\UnitModule\Model\UnitMapping::COLUMN_PRIORITY)
			->setSortable()
			->setFilterText()
		;
		$grid->addColumnText(\App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY, \App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY)
			->setSortable()
			->setFilterText()
		;
		$grid->addColumnText(\App\UnitModule\Model\UnitMapping::COLUMN_LEADERSHIP, \App\UnitModule\Model\UnitMapping::COLUMN_LEADERSHIP)
			->setSortable()
			->setFilterText()
		;
		$grid->addColumnText(\App\UnitModule\Model\UnitMapping::COLUMN_MAX_LEVEL, \App\UnitModule\Model\UnitMapping::COLUMN_MAX_LEVEL)
			->setSortable()
			->setFilterText()
		;
		$grid->addColumnText(\App\UnitModule\Model\UnitMapping::COLUMN_SORT, \App\UnitModule\Model\UnitMapping::COLUMN_SORT)
			->setSortable()
			->setFilterText()
		;
		$grid->addAction('edit', '✏️', ':Unit:Admin:Unit:default', [
			'id' => 'id',
		]);

		$grid->addAction('remove', 'X ️', ':Unit:Admin:Unit:remove', [
			'id' => 'id',
		]);

		return $grid;
	}
}
