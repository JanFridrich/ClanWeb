<?php declare(strict_types = 1);

namespace App\UnitModule\Presenters;

class UnitPresenter extends \App\CoreModule\Presenters\BasePresenter
{

	private \App\UnitModule\Forms\UserUnitFormFactory $userUnitFormFactory;

	private \App\UnitModule\Model\UnitService $unitService;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService,
		\App\UnitModule\Forms\UserUnitFormFactory $userUnitFormFactory,
		\App\UnitModule\Model\UnitService $unitService
	)
	{
		parent::__construct($pageService, $userService);
		$this->userUnitFormFactory = $userUnitFormFactory;
		$this->unitService = $unitService;
	}


	public function actionDefault(): void
	{
		if ( ! $this->getUserEntity()) {
			$this->flashMessage("You don't have permission to view that!");
			$this->redirectUrl('/' . $this->locale . '/');
		}
	}


	public function renderDefault(): void
	{
		$this->template->units = $this->unitService->getAll();
	}


	public function createComponentUnitsForm(): \Nette\Application\UI\Form
	{
		return $this->userUnitFormFactory->create(function (): void {
			$this->redirect('this');
		}, $this->getUserEntity());
	}
}
