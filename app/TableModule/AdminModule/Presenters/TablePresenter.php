<?php declare(strict_types = 1);

namespace App\TableModule\AdminModule\Presenters;

class TablePresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\TableModule\Grid\TableGridFactory $dataGridFactory
	)
	{
		parent::__construct($userService);
		$this->dataGridFactory = $dataGridFactory;
	}

}
