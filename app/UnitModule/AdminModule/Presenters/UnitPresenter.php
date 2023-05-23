<?php declare(strict_types = 1);

namespace App\UnitModule\AdminModule\Presenters;

class UnitPresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	private \App\UnitModule\Forms\UnitFormFactory $unitFormFactory;

	private \App\UnitModule\Model\UnitService $unitService;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\UnitModule\Grid\UnitDataGridFactory $dataGridFactory,
		\App\UnitModule\Forms\UnitFormFactory $unitFormFactory,
		\App\UnitModule\Model\UnitService $unitService
	)
	{
		parent::__construct($userService);
		$this->dataGridFactory = $dataGridFactory;
		$this->unitFormFactory = $unitFormFactory;
		$this->unitService = $unitService;
	}


	public function renderList(): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_WARLORD) && ! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:'
		);
	}


	public function renderDefault(int $id): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_WARLORD) && ! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:'
		);
		$this->template->unit = $this->unitService->get($id);
	}


	public function renderAdd(): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_WARLORD) && ! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:'
		);
	}


	public function renderRemove(int $id): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Unit:Admin:Unit:list'
		);

		$this->unitService->delete($id);
		$this->redirect(':Unit:Admin:Unit:list');
	}


	public function createComponentAddForm(): \Nette\Application\UI\Form
	{
		return $this->unitFormFactory->createNew(function ($id): void {
			$this->flashMessage('Created');

			$this->redirect(':Unit:Admin:Unit:default', ['id' => $id]);
		});
	}


	public function createComponentEditForm(): \Nette\Application\UI\Form
	{
		return $this->unitFormFactory->createEdit(function (): void {
			$this->flashMessage('Saved');
			$this->redirect('this');
		},
			(int) $this->getParameters()['id'],
		);
	}

}
