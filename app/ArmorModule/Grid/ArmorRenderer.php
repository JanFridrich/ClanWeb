<?php declare(strict_types = 1);

namespace App\ArmorModule\Grid;

class ArmorRenderer
{

	public function renderArmorType(\App\ArmorModule\Model\Armor\Armor $armor): string
	{
		return $armor->getArmorType()->getName();
	}

}
