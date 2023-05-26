<?php declare(strict_types = 1);

namespace App\UnitModule\Grid;

class UserUnitDataGridFactory extends \App\CoreModule\GridFactory\DataGridFactory
{

	private \App\UnitModule\Model\UnitService $unitService;

	private \App\UnitModule\Grid\UserUnitDataGridFactory\DataSourceGetter $dataSourceGetter;


	public function __construct(
		\App\UnitModule\Grid\UserUnitDataGridFactory\DataSourceGetter $dataSourceGetter,
		\App\UnitModule\Model\UnitService $unitService
	)
	{
		$this->unitService = $unitService;
		$this->dataSourceGetter = $dataSourceGetter;
	}


	public function createLevel(): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid->setDataSource($this->dataSourceGetter->getLevelDataForGrid());
		$grid->addColumnText('user', 'login')
			->setSortable()
			->setFilterText()
		;
		/** @var \App\UnitModule\Model\Unit $unit */
		foreach ($this->unitService->getAll() as $unit) {
			$grid->addColumnText('unit' . $unit->getId(), $unit->getName())
				->setSortable()
				->setFilterText()
			;
		}

		return $grid;
	}


	public function createVeterancy(): \Ublaboo\DataGrid\DataGrid
	{

		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid->setDataSource($this->dataSourceGetter->getVeterancyDataForGrid());
		$grid->addColumnText('user', 'login')
			->setSortable()
			->setFilterText()
		;
		/** @var \App\UnitModule\Model\Unit $unit */
		foreach ($this->unitService->getAll() as $unit) {
			$grid->addColumnText('unit' . $unit->getId(), $unit->getName())
				->setSortable()
				->setFilterText()
			;
		}

		return $grid;
	}


	public function createMastery(): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid->setDataSource($this->dataSourceGetter->getMasteryDataForGrid());
		$grid->addColumnText('user', 'login')
			->setSortable()
			->setFilterText()
		;
		/** @var \App\UnitModule\Model\Unit $unit */
		foreach ($this->unitService->getAll() as $unit) {
			if ($unit->getMaxMastery() === 0) {
				continue;
			}
			$grid->addColumnText('unit' . $unit->getId(), $unit->getName())
				->setSortable()
				->setFilterText()
			;
		}

		return $grid;
	}

}
