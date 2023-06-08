<?php declare(strict_types = 1);

namespace App\TableModule\Model\Table;

class Table extends \App\CoreModule\Model\Entity
{

	public const STATUS_STARTED = 0;
	public const STATUS_UNITS_ASSIGNED = 1;
	public const STATUS_FINISHED = 2;
	public const STATUSES = [
		self::STATUS_STARTED => 'started',
		self::STATUS_UNITS_ASSIGNED => 'units assigned',
		self::STATUS_FINISHED => 'finished',
	];

	protected string $name;

	protected int $status;

	protected \Dibi\DateTime $created;

	protected \App\UserModule\Model\User $createdBy;

	protected int $rows;

	protected array $tableItems;

	protected bool $isActive;


	public function __construct(
		int $id,
		string $name,
		int $status,
		int $rows,
		\Dibi\DateTime $created,
		\App\UserModule\Model\User $createdBy,
		bool $isActive,
		array $tableItems
	)
	{
		$this->id = $id;
		$this->name = $name;
		$this->status = $status;
		$this->created = $created;
		$this->createdBy = $createdBy;
		$this->rows = $rows;
		$this->tableItems = $tableItems;
		$this->isActive = $isActive;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function setName(string $name): void
	{
		$this->name = $name;
	}


	public function getStatus(): int
	{
		return $this->status;
	}


	public function setStatus(int $status): void
	{
		$this->status = $status;
	}


	public function getCreated(): \Dibi\DateTime
	{
		return $this->created;
	}


	public function setCreated(\Dibi\DateTime $created): void
	{
		$this->created = $created;
	}


	public function getCreatedBy(): \App\UserModule\Model\User
	{
		return $this->createdBy;
	}


	public function setCreatedBy(\App\UserModule\Model\User $createdBy): void
	{
		$this->createdBy = $createdBy;
	}


	public function getRows(): int
	{
		return $this->rows;
	}


	public function setRows(int $rows): void
	{
		$this->rows = $rows;
	}


	public function getTableItems(): array
	{
		return $this->tableItems;
	}


	public function setTableItems(array $tableItems): void
	{
		$this->tableItems = $tableItems;
	}


	public function isActive(): bool
	{
		return $this->isActive;
	}


	public function setIsActive(bool $isActive): void
	{
		$this->isActive = $isActive;
	}

}
