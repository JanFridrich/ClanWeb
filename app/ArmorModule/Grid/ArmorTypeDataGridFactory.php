<?php declare(strict_types = 1);

namespace App\ArmorModule\Grid;

class ArmorTypeDataGridFactory extends \App\CoreModule\GridFactory\DataGridFactory
{

	private \App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService;


	public function __construct(
		\App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService
	)
	{
		$this->armorTypeService = $armorTypeService;
	}


	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid->setDataSource($this->armorTypeService->getAll());
		$grid->addColumnText(\App\ArmorModule\Model\ArmorType\ArmorTypeMapping::COLUMN_NAME, \App\ArmorModule\Model\ArmorType\ArmorTypeMapping::COLUMN_NAME);
		$grid->addAction('edit', '✏️', ':Armor:Admin:ArmorType:default', [
			'id' => 'id',
		]);

		$grid->addAction('remove', 'X ️', ':Armor:Admin:ArmorType:remove', [
			'id' => 'id',
		]);

		return $grid;
	}

}
