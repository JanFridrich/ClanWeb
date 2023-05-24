<?php declare(strict_types = 1);

namespace App\ArmorModule\Presenters;

class ArmorPresenter extends \App\CoreModule\Presenters\BasePresenter
{

	private \App\ArmorModule\Model\Armor\ArmorService $armorService;

	private \App\ArmorModule\Forms\UserArmorTypeFormFactory $userArmorTypeFormFactory;

	private \App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService;

	private \App\ArmorModule\Forms\UserArmorFormFactory $userArmorFormFactory;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService,
		\App\ArmorModule\Model\Armor\ArmorService $armorService,
		\App\ArmorModule\Forms\UserArmorTypeFormFactory $userArmorTypeFormFactory,
		\App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService,
		\App\ArmorModule\Forms\UserArmorFormFactory $userArmorFormFactory
	)
	{
		parent::__construct($pageService, $userService);
		$this->armorService = $armorService;
		$this->userArmorTypeFormFactory = $userArmorTypeFormFactory;
		$this->armorTypeService = $armorTypeService;
		$this->userArmorFormFactory = $userArmorFormFactory;
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
		$this->template->armors = $this->armorService->getAll();
		$this->template->armorTypes = $this->armorTypeService->getAll();
	}


	public function createComponentArmorTypeForm(): \Nette\Application\UI\Form
	{
		return $this->userArmorTypeFormFactory->create(
			function (): void {
				$this->flashMessage('Leadership saved.');
				$this->redirect('this');
			}
			, $this->getUserEntity()
		);
	}


	public function createComponentArmorForm(): \Nette\Application\UI\Form
	{
		return $this->userArmorFormFactory->create(
			function (): void {
				$this->flashMessage('Armors saved.');
				$this->redirect('this');
			}
			, $this->getUserEntity()
		);
	}

}
