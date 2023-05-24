<?php declare(strict_types = 1);

namespace App\UserModule\Model;

class User extends \App\CoreModule\Model\Entity
{

	public const ROLE_MEMBER = 'member';
	public const ROLE_ADMIN = 'admin';
	public const ROLE_WARLORD = 'warlord';

	protected string $email;

	protected string $login;

	protected bool $isActive;

	protected \Dibi\DateTime $created;

	protected string $role;

	protected ?string $skillLevel;

	protected string $note;

	protected array $units;

	protected ?\Dibi\DateTime $lastUpdatedUnits;

	protected array $armors;

	protected array $armorTypes;


	public function __construct(
		int $id,
		string $email,
		string $login,
		bool $isActive,
		\Dibi\DateTime $created,
		string $role,
		?string $skillLevel,
		string $note,
		?\Dibi\DateTime $lastUpdatedUnits,
		array $units,
		array $armors,
		array $armorTypes
	)
	{
		$this->id = $id;
		$this->email = $email;
		$this->login = $login;
		$this->isActive = $isActive;
		$this->created = $created;
		$this->role = $role;
		$this->skillLevel = $skillLevel;
		$this->note = $note;
		$this->units = $units;
		$this->lastUpdatedUnits = $lastUpdatedUnits;
		$this->armors = $armors;
		$this->armorTypes = $armorTypes;
	}


	public function getEmail(): string
	{
		return $this->email;
	}


	public function setEmail(string $email): void
	{
		$this->email = $email;
	}


	public function getLogin(): string
	{
		return $this->login;
	}


	public function setLogin(string $login): void
	{
		$this->login = $login;
	}


	public function isActive(): bool
	{
		return $this->isActive;
	}


	public function setIsActive(bool $isActive): void
	{
		$this->isActive = $isActive;
	}


	public function getCreated(): \Dibi\DateTime
	{
		return $this->created;
	}


	public function setCreated(\Dibi\DateTime $created): void
	{
		$this->created = $created;
	}


	public function getRole(): string
	{
		return $this->role;
	}


	public function setRole(string $role): void
	{
		$this->role = $role;
	}


	public function getNote(): string
	{
		return $this->note;
	}


	public function setNote(string $note): void
	{
		$this->note = $note;
	}


	public function getSkillLevel(): ?string
	{
		return $this->skillLevel;
	}


	public function setSkillLevel(?string $skillLevel): void
	{
		$this->skillLevel = $skillLevel;
	}


	/**
	 * @return array<int, \App\UnitModule\Model\Unit>
	 */
	public function getUnits(): array
	{
		return $this->units;
	}


	/**
	 * @param array<int, \App\UnitModule\Model\Unit> $units
	 */
	public function setUnits(array $units): void
	{
		$this->units = $units;
	}


	public function getLastUpdatedUnits(): ?\Dibi\DateTime
	{
		return $this->lastUpdatedUnits;
	}


	public function setLastUpdatedUnits(?\Dibi\DateTime $lastUpdatedUnits): void
	{
		$this->lastUpdatedUnits = $lastUpdatedUnits;
	}


	/**
	 * @return array<int, \App\ArmorModule\Model\Armor\Armor>
	 */
	public function getArmors(): array
	{
		return $this->armors;
	}


	/**
	 * @param array<int, \App\ArmorModule\Model\Armor\Armor> $armors
	 */
	public function setArmors(array $armors): void
	{
		$this->armors = $armors;
	}


	/**
	 * @return array<int, \App\ArmorModule\Model\ArmorType\ArmorType>
	 */
	public function getArmorTypes(): array
	{
		return $this->armorTypes;
	}


	/**
	 * @param array<int, \App\ArmorModule\Model\ArmorType\ArmorType> $armorTypes
	 */
	public function setArmorTypes(array $armorTypes): void
	{
		$this->armorTypes = $armorTypes;
	}

}
