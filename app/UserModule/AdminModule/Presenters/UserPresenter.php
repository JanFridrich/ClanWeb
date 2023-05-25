<?php declare(strict_types = 1);

namespace App\UserModule\AdminModule\Presenters;

class UserPresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	private \App\UserModule\Model\UserService $userService;

	private \App\UserModule\Forms\UserFormFactory $userFormFactory;

	private \App\CoreModule\Model\XLSXFileResponseGetter $XLSXFileResponseGetter;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\UserModule\Forms\UserFormFactory $userFormFactory,
		\App\CoreModule\Model\XLSXFileResponseGetter $XLSXFileResponseGetter,
		\App\UserModule\Grid\UserDataGridFactory $dataGridFactory
	)
	{
		parent::__construct($userService);
		$this->userService = $userService;
		$this->userFormFactory = $userFormFactory;
		$this->XLSXFileResponseGetter = $XLSXFileResponseGetter;
		$this->dataGridFactory = $dataGridFactory;
	}


	public function renderList(): void
	{
		$this->template->users = $this->userService->getAll();
	}


	public function renderDefault(int $id): void
	{
		$this->template->userEntity = $this->userService->get($id);
	}


	public function handleExportAll(): void
	{
		$this->sendResponse($this->XLSXFileResponseGetter->get($this->userService->prepareDataForExport(), new \Nette\Utils\DateTime()));
	}


	public function actionList(): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:default'
		);
	}


	public function actionDefault(): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:default'
		);
	}


	public function createComponentUserForm(): \Nette\Application\UI\Form
	{
		return $this->userFormFactory->create(
			function (): void {
				$this->redirect('this');
			}, $this->userService->get((int) $this->getParameters()['id'])
		);
	}

}
