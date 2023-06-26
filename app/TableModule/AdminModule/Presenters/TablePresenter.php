<?php declare(strict_types = 1);

namespace App\TableModule\AdminModule\Presenters;

class TablePresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	private \App\TableModule\Model\Table\TableService $tableService;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\TableModule\Grid\TableGridFactory $dataGridFactory,
		\App\TableModule\Model\Table\TableService $tableService
	)
	{
		parent::__construct($userService);
		$this->dataGridFactory = $dataGridFactory;
		$this->tableService = $tableService;
	}


	public function actionList(): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:'
		);
	}


	public function actionEdit(int $id): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:'
		);
		$this->template->table = $this->tableService->get($id);
	}

}
