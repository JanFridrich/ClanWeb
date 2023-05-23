<?php declare(strict_types = 1);

namespace App\ArmorModule\Presenters;

class ArmorPresenter extends \App\CoreModule\Presenters\BasePresenter
{

	private \App\ArmorModule\Model\Armor\ArmorService $armorService;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService,
		\App\ArmorModule\Model\Armor\ArmorService $armorService
	)
	{
		parent::__construct($pageService, $userService);
		$this->armorService = $armorService;
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
	}

}
