<?php declare(strict_types = 1);

namespace App\ArmorModule\Grid;

class ArmorDataGridFactory extends \App\CoreModule\GridFactory\DataGridFactory
{

	private \App\ArmorModule\Model\Armor\ArmorService $armorService;

	private \App\ArmorModule\Grid\ArmorRenderer $armorRenderer;


	public function __construct(
		\App\ArmorModule\Model\Armor\ArmorService $armorService,
		\App\ArmorModule\Grid\ArmorRenderer $armorRenderer
	)
	{
		$this->armorService = $armorService;
		$this->armorRenderer = $armorRenderer;
	}


	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid->setDataSource($this->armorService->getAll());
		$grid->addColumnText(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_NAME, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_NAME)
			->setSortable()
			->setFilterText()
		;
		$grid->addColumnText(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_ARMOR_TYPE, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_ARMOR_TYPE)
			->setSortable()
			->setRenderer([$this->armorRenderer, 'renderArmorType'])
			->setFilterText()
		;
		$grid->addColumnText(\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT, \App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT)
			->setSortable()
			->setFilterText()
		;

		$grid->addAction('edit', '✏️', ':Armor:Admin:Armor:default', [
			'id' => 'id',
		]);

		$grid->addAction('remove', 'X ️', ':Armor:Admin:Armor:remove', [
			'id' => 'id',
		]);

		return $grid;
	}

}
