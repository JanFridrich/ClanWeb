<?php declare(strict_types = 1);

namespace App\SettingModule\AdminModule\Presenters;

class SettingPresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	private \App\SettingModule\Forms\SettingFormFactory $settingFormFactory;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\SettingModule\Forms\SettingFormFactory $settingFormFactory
	)
	{
		parent::__construct($userService);
		$this->settingFormFactory = $settingFormFactory;
	}


	public function createComponentEditForm(): \Nette\Application\UI\Form
	{
		return $this->settingFormFactory->create(
			function (): void {
				$this->flashMessage('Setting was successfully updated', 'success');
				$this->redirect('this');
			}, $this->getUserEntity()
		);
	}

}
