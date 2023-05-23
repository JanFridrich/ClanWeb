<?php declare(strict_types = 1);

namespace App\ArmorModule\AdminModule\Presenters;

class ArmorTypePresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	private \App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService;

	private \App\ArmorModule\Forms\ArmorTypeFormFactory $armorTypeFormFactory;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService,
		\App\ArmorModule\Grid\ArmorTypeDataGridFactory $dataGridFactory,
		\App\ArmorModule\Forms\ArmorTypeFormFactory $armorTypeFormFactory
	)
	{
		parent::__construct($userService);
		$this->armorTypeService = $armorTypeService;
		$this->dataGridFactory = $dataGridFactory;
		$this->armorTypeFormFactory = $armorTypeFormFactory;
	}


	public function renderRemove(int $id): void
	{
		$this->checkStuffAndRedirectIfTrue(
			! $this->checkPermission(\App\UserModule\Model\User::ROLE_ADMIN),
			'You dont have permission to view that!',
			'warning',
			':Armor:Admin:ArmorType:list'
		);

		$this->armorTypeService->delete($id);
		$this->redirect(':Armor:Admin:ArmorType:list');
	}


	public function renderDefault(int $id): void
	{
		$this->template->armorType = $this->armorTypeService->get($id);
	}


	public function createComponentAddForm(): \Nette\Application\UI\Form
	{
		return $this->armorTypeFormFactory->createNew(
			function (): void {
				$this->flashMessage('ArmorType created!', 'success');
				$this->redirect(':Armor:Admin:ArmorType:list');
			}
		);
	}


	public function createComponentEditForm(): \Nette\Application\UI\Form
	{
		return $this->armorTypeFormFactory->createEdit(
			function (): void {
				$this->flashMessage('ArmorType edited!', 'success');
				$this->redirect('this');
			}
			, (int) $this->getParameter('id')
		);
	}
}
