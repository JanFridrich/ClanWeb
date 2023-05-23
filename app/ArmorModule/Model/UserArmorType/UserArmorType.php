<?php declare(strict_types = 1);

namespace App\ArmorModule\Model\UserArmorType;

class UserArmorType extends \App\CoreModule\Model\Entity
{

	protected \App\ArmorModule\Model\ArmorType\ArmorType $armorType;

	protected \App\UserModule\Model\User $user;

	protected int $leadership;


	public function __construct(
		int $id,
		\App\ArmorModule\Model\ArmorType\ArmorType $armorType,
		\App\UserModule\Model\User $user,
		int $leadership
	)
	{
		$this->id = $id;
		$this->armorType = $armorType;
		$this->user = $user;
		$this->leadership = $leadership;
	}


	public function getArmorType(): \App\ArmorModule\Model\ArmorType\ArmorType
	{
		return $this->armorType;
	}


	public function setArmorType(\App\ArmorModule\Model\ArmorType\ArmorType $armorType): void
	{
		$this->armorType = $armorType;
	}


	public function getUser(): \App\UserModule\Model\User
	{
		return $this->user;
	}


	public function setUser(\App\UserModule\Model\User $user): void
	{
		$this->user = $user;
	}


	public function getLeadership(): int
	{
		return $this->leadership;
	}


	public function setLeadership(int $leadership): void
	{
		$this->leadership = $leadership;
	}

}
