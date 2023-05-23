<?php declare(strict_types = 1);

namespace App\UnitModule\Grid;

class UserUnitDataGridFactory extends \App\CoreModule\GridFactory\DataGridFactory
{

	private \App\UserModule\Model\UserService $userService;

	private \App\UnitModule\Model\UnitService $unitService;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\UnitModule\Model\UnitService $unitService
	)
	{
		$this->userService = $userService;
		$this->unitService = $unitService;
	}


	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		\Tracy\Debugger::barDump($this->userService->prepareDataForGrid());

		$grid->setDataSource($this->userService->prepareDataForGrid());
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

}
