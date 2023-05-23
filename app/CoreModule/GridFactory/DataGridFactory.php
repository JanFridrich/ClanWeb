<?php declare(strict_types = 1);

namespace App\CoreModule\GridFactory;

abstract class DataGridFactory
{

	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		return $grid;
	}

}
