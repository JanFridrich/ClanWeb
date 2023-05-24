<?php declare(strict_types = 1);

namespace App\UnitModule\Model;

class UserUnit extends \App\CoreModule\Model\Entity
{

	protected int $user;

	protected int $unit;

	protected string $line;

	protected int $level;

	protected int $mastery;


	public function __construct(
		int $id,
		int $user,
		int $unit,
		string $line,
		int $level,
		int $mastery

	)
	{
		$this->id = $id;
		$this->user = $user;
		$this->unit = $unit;
		$this->line = $line;
		$this->level = $level;
		$this->mastery = $mastery;
	}


	public function getUser(): int
	{
		return $this->user;
	}


	public function setUser(int $user): void
	{
		$this->user = $user;
	}


	public function getUnit(): int
	{
		return $this->unit;
	}


	public function setUnit(int $unit): void
	{
		$this->unit = $unit;
	}


	public function getLine(): string
	{
		return $this->line;
	}


	public function setLine(string $line): void
	{
		$this->line = $line;
	}


	public function getLevel(): int
	{
		return $this->level;
	}


	public function setLevel(int $level): void
	{
		$this->level = $level;
	}


	public function getMastery(): int
	{
		return $this->mastery;
	}


	public function setMastery(int $mastery): void
	{
		$this->mastery = $mastery;
	}

}
