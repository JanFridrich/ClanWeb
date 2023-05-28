<?php declare(strict_types = 1);

namespace App\CoreModule\Model;

class CustomWhere
{

	private string $sql;


	public function __construct(
		string $sql
	)
	{
		$this->sql = $sql;
	}


	public function getSql(): string
	{
		return $this->sql;
	}


	public function setSql(string $sql): void
	{
		$this->sql = $sql;
	}

}
