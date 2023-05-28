<?php declare(strict_types = 1);

namespace App\TableModule\Model\TableItem;

class TableItem extends \App\CoreModule\Model\Entity
{

	public const ITEM_TYPE_SQUAD = 'squad';
	public const ITEM_TYPE_UNIT = 'unit';
	public const ITEM_TYPE_ARMOR = 'class';
	public const ITEM_TYPE_NOTE = 'note';
	public const ITEM_TYPE_ARTILLERY = 'artillery';
	public const ITEM_TYPE_GROUP_LEADER = 'groupLeader';

	public const ITEM_TYPES = [
		self::ITEM_TYPE_SQUAD => self::ITEM_TYPE_SQUAD,
		self::ITEM_TYPE_UNIT => self::ITEM_TYPE_UNIT,
		self::ITEM_TYPE_ARMOR => self::ITEM_TYPE_ARMOR,
		self::ITEM_TYPE_NOTE => self::ITEM_TYPE_NOTE,
		self::ITEM_TYPE_ARTILLERY => self::ITEM_TYPE_ARTILLERY,
		self::ITEM_TYPE_GROUP_LEADER => self::ITEM_TYPE_GROUP_LEADER,
	];

	protected \App\TableModule\Model\Table\Table $table;

	protected \App\UserModule\Model\User $user;

	protected array $units;

	protected string $note;

	protected string $squad;

	protected string $armor;

	protected bool $groupLeader;


	public function __construct(
		int $id,
		\App\TableModule\Model\Table\Table $table,
		\App\UserModule\Model\User $user,
		array $units,
		string $note,
		string $squad,
		string $armor,
		bool $groupLeader
	)
	{
		$this->id = $id;
		$this->table = $table;
		$this->user = $user;
		$this->units = $units;
		$this->note = $note;
		$this->squad = $squad;
		$this->armor = $armor;
		$this->groupLeader = $groupLeader;
	}


	public function getTable(): \App\TableModule\Model\Table\Table
	{
		return $this->table;
	}


	public function setTable(\App\TableModule\Model\Table\Table $table): void
	{
		$this->table = $table;
	}


	public function getUser(): \App\UserModule\Model\User
	{
		return $this->user;
	}


	public function setUser(\App\UserModule\Model\User $user): void
	{
		$this->user = $user;
	}


	public function getUnits(): array
	{
		return $this->units;
	}


	public function setUnits(array $units): void
	{
		$this->units = $units;
	}


	public function getNote(): string
	{
		return $this->note;
	}


	public function setNote(string $note): void
	{
		$this->note = $note;
	}


	public function getSquad(): string
	{
		return $this->squad;
	}


	public function setSquad(string $squad): void
	{
		$this->squad = $squad;
	}


	public function getArmor(): string
	{
		return $this->armor;
	}


	public function setArmor(string $armor): void
	{
		$this->armor = $armor;
	}


	public function isGroupLeader(): bool
	{
		return $this->groupLeader;
	}


	public function setGroupLeader(bool $groupLeader): void
	{
		$this->groupLeader = $groupLeader;
	}

}
