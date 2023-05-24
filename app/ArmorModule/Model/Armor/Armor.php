<?php declare(strict_types = 1);

namespace App\ArmorModule\Model\Armor;

class Armor extends \App\CoreModule\Model\Entity
{

	public const DEFAULT_LEADERSHIP = 700;

	public const SKILL_LEVELS = [
		0 => "Don't have weapon or needed skills",
		1 => "Have weapon with all needed skills, but never played",
		2 => "Playing sometimes, but prefer not to get it",
		3 => "Playing weapon I don't mind using it",
		4 => "I want to use this weapon",
		5 => "I really want to use this weapon",
	];

	protected \App\ArmorModule\Model\ArmorType\ArmorType $armorType;

	protected string $name;

	protected string $image;

	protected int $sort;

	protected ?string $prefer;

	protected ?int $leadership;


	public function __construct(
		int $id,
		\App\ArmorModule\Model\ArmorType\ArmorType $armorType,
		string $name,
		string $image,
		int $sort,
		?string $prefer,
		?int $leadership
	)
	{
		$this->id = $id;
		$this->armorType = $armorType;
		$this->name = $name;
		$this->image = $image;
		$this->sort = $sort;
		$this->prefer = $prefer;
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


	public function getName(): string
	{
		return $this->name;
	}


	public function setName(string $name): void
	{
		$this->name = $name;
	}


	public function getSort(): int
	{
		return $this->sort;
	}


	public function setSort(int $sort): void
	{
		$this->sort = $sort;
	}


	public function getImage(): string
	{
		return $this->image;
	}


	public function setImage(string $image): void
	{
		$this->image = $image;
	}


	public function getPrefer(): ?string
	{
		return $this->prefer;
	}


	public function setPrefer(?string $prefer): void
	{
		$this->prefer = $prefer;
	}


	public function getLeadership(): ?int
	{
		return $this->leadership;
	}


	public function setLeadership(?int $leadership): void
	{
		$this->leadership = $leadership;
	}

}
