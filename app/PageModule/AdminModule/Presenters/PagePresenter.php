<?php declare(strict_types = 1);

namespace App\PageModule\AdminModule\Presenters;

class PagePresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	private \App\PageModule\Model\PageService $pageService;

	private \App\PageModule\Forms\PageFormFactory $pageFormFactory;

	private \App\PageModule\Grid\PageDataGridFactory $dataGridFactory;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\PageModule\Model\PageService $pageService,
		\App\PageModule\Forms\PageFormFactory $pageFormFactory,
		\App\PageModule\Grid\PageDataGridFactory $dataGridFactory
	)
	{
		parent::__construct($userService);
		$this->pageService = $pageService;
		$this->pageFormFactory = $pageFormFactory;
		$this->dataGridFactory = $dataGridFactory;
	}


	public function renderList(): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:'
		);
		$this->template->pages = $this->pageService->getAllPages($this->locale);
	}


	public function renderDefault(int $id): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:'
		);
	}


	public function renderAdd(): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Core:Admin:Homepage:'
		);
	}


	public function createComponentAddForm(): \Nette\Application\UI\Form
	{
		return $this->pageFormFactory->createNew(function (): void {
			$this->redirect('this');
		});
	}


	public function createComponentEditForm(): \Nette\Application\UI\Form
	{
		return $this->pageFormFactory->createEdit(function (): void {
			$this->redirect('this');
		},
			(int) $this->getParameters()['id'],
			$this->locale,
		);
	}


	public function createComponentGrid(): \Ublaboo\DataGrid\DataGrid
	{
		return $this->dataGridFactory->create($this->locale);
	}

}
