<?php declare(strict_types = 1);

namespace App\UserModule\Grid;

class Renderer
{

	public function renderIsActive(\App\UserModule\Model\User $user): string
	{
		return $user->isActive() ? 'YES' : 'NO';
	}


	public function renderProgress(\App\UserModule\Model\User $user): string
	{
		return $user->getMaxedUnits() . '%';
	}

}
