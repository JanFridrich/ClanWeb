<?php declare(strict_types = 1);

namespace App\UnitModule\Presenters;

class UnitPresenter extends \App\CoreModule\Presenters\BasePresenter
{

	private \App\UnitModule\Forms\UserUnitFormFactory $userUnitFormFactory;

	private \App\UnitModule\Model\UnitService $unitService;

	private \App\UnitModule\Forms\UnitSelectFormFactory $unitSelectFormFactory;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService,
		\App\UnitModule\Forms\UserUnitFormFactory $userUnitFormFactory,
		\App\UnitModule\Model\UnitService $unitService,
		\App\UnitModule\Forms\UnitSelectFormFactory $unitSelectFormFactory
	)
	{
		parent::__construct($pageService, $userService);
		$this->userUnitFormFactory = $userUnitFormFactory;
		$this->unitService = $unitService;
		$this->unitSelectFormFactory = $unitSelectFormFactory;
	}


	public function actionDefault(?string $tier = NULL, ?string $type = NULL): void
	{
		if ( ! $this->getUserEntity()) {
			$this->flashMessage("You don't have permission to view that!");
			$this->redirectUrl('/' . $this->locale . '/');
		}
	}


	public function renderDefault(?string $tier = NULL, ?string $type = NULL): void
	{
		$options = [\App\UnitModule\Model\UnitService::SHOW_ALL => FALSE];
		if ($tier !== NULL) {
			$options['where'][\App\UnitModule\Model\UnitMapping::COLUMN_TIER] = $tier;
		}
		if ($type !== NULL) {
			$options['where'][\App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY] = $type;
		}
		$this->template->units = $this->unitService->getAll($options);
	}


	public function createComponentUnitsForm(): \Nette\Application\UI\Form
	{
		$options = [\App\UnitModule\Model\UnitService::SHOW_ALL => FALSE];
		if ($this->getParameter('tier')) {
			$options['where'][\App\UnitModule\Model\UnitMapping::COLUMN_TIER] = $this->getParameter('tier');
		}
		if ($this->getParameter('type')) {
			$options['where'][\App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY] = $this->getParameter('type');
		}
		return $this->userUnitFormFactory->create(function (): void {
			$this->redirect('this');
		}, $this->getUserEntity(), $options);
	}


	public function createComponentSelectUnitsForm(): \Nette\Application\UI\Form
	{
		return $this->unitSelectFormFactory->create(function (?string $tier, ?string $type): void {
			$this->redirect('this', ['tier' => $tier, 'type' => $type]);
		}, $this->getParameters());
	}
}
