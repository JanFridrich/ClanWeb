<?php declare(strict_types = 1);

namespace App\ArmorModule\Model\ArmorType;

class ArmorType extends \App\CoreModule\Model\Entity
{

	protected string $name;


	public function __construct(
		int $id,
		string $name
	)
	{
		$this->id = $id;
		$this->name = $name;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function setName(string $name): void
	{
		$this->name = $name;
	}

}
