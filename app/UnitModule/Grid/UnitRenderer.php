<?php declare(strict_types = 1);

namespace App\UnitModule\Grid;

class UnitRenderer
{

	public function renderShow(\App\UnitModule\Model\Unit $unit): string
	{
		return $unit->isShow() ? 'Yes' : 'No';
	}

}
