<?php declare(strict_types = 1);

namespace App\ArmorModule\AdminModule\Presenters;

class ArmorPresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	private \App\ArmorModule\Model\Armor\ArmorService $armorService;

	private \App\ArmorModule\Forms\ArmorFormFactory $armorFormFactory;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\ArmorModule\Model\Armor\ArmorService $armorService,
		\App\ArmorModule\Grid\ArmorDataGridFactory $dataGridFactory,
		\App\ArmorModule\Forms\ArmorFormFactory $armorFormFactory
	)
	{
		parent::__construct($userService);
		$this->armorService = $armorService;
		$this->dataGridFactory = $dataGridFactory;
		$this->armorFormFactory = $armorFormFactory;
	}


	public function renderRemove(int $id): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Armor:Admin:Armor:list'
		);

		$this->armorService->delete($id);
		$this->redirect(':Armor:Admin:Armor:list');
	}


	public function renderDefault(int $id): void
	{
		$this->template->armor = $this->armorService->get($id);
	}


	public function createComponentAddForm(): \Nette\Application\UI\Form
	{
		return $this->armorFormFactory->createNew(
			function (int $armorId): void {
				$this->flashMessage('Armor created!', 'success');
				$this->redirect(':Armor:Admin:Armor:default', ['id' => $armorId]);
			}
		);
	}


	public function createComponentEditForm(): \Nette\Application\UI\Form
	{
		return $this->armorFormFactory->createEdit(
			function (): void {
				$this->flashMessage('Armor edited!', 'success');
				$this->redirect('this');
			}
			, (int) $this->getParameter('id')
		);
	}

}
