<?php declare(strict_types = 1);

namespace App\UnitModule\AdminModule\Presenters;

class UserUnitPresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\UnitModule\Grid\UserUnitDataGridFactory $dataGridFactory
	)
	{
		parent::__construct($userService);
		$this->dataGridFactory = $dataGridFactory;
	}

}
