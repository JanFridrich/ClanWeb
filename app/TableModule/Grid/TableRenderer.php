<?php declare(strict_types = 1);

namespace App\TableModule\Grid;

class TableRenderer
{

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\App\UserModule\Model\UserService $userService
	)
	{
		$this->userService = $userService;
	}


	public function renderIsActive(\Dibi\Row $table): string
	{
		return $table[\App\TableModule\Model\Table\TableMapping::COLUMN_IS_ACTIVE] ? 'YES' : 'NO';
	}


	public function renderCreatedBy(\Dibi\Row $table): string
	{
		return $this->userService->get($table[\App\TableModule\Model\Table\TableMapping::COLUMN_CREATED_BY])->getLogin();
	}
}
