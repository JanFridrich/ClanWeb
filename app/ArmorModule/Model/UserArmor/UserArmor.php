<?php declare(strict_types = 1);

namespace App\ArmorModule\Model\UserArmor;

class UserArmor extends \App\CoreModule\Model\Entity
{

	protected \App\ArmorModule\Model\Armor\Armor $armor;

	protected \App\UserModule\Model\User $user;

	protected string $prefer;


	public function __construct(
		int $id,
		\App\ArmorModule\Model\Armor\Armor $armor,
		\App\UserModule\Model\User $user,
		string $prefer
	)
	{
		$this->id = $id;
		$this->armor = $armor;
		$this->user = $user;
		$this->prefer = $prefer;
	}


	public function getArmor(): \App\ArmorModule\Model\Armor\Armor
	{
		return $this->armor;
	}


	public function setArmor(\App\ArmorModule\Model\Armor\Armor $armor): void
	{
		$this->armor = $armor;
	}


	public function getUser(): \App\UserModule\Model\User
	{
		return $this->user;
	}


	public function setUser(\App\UserModule\Model\User $user): void
	{
		$this->user = $user;
	}


	public function getPrefer(): string
	{
		return $this->prefer;
	}


	public function setPrefer(string $prefer): void
	{
		$this->prefer = $prefer;
	}

}
