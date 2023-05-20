<?php declare(strict_types = 1);

namespace App\UserModule\Presenters;

class ProfilePresenter extends \App\CoreModule\Presenters\BasePresenter
{

	private \App\CoreModule\Forms\ChangePasswordFormFactory $passwordFormFactory;

	private \App\UserModule\Forms\ProfileFormFactory $profileFormFactory;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\CoreModule\Forms\ChangePasswordFormFactory $passwordFormFactory,
		\App\UserModule\Forms\ProfileFormFactory $profileFormFactory,
		\App\UserModule\Model\UserService $userService
	)
	{
		parent::__construct($pageService, $userService);
		$this->passwordFormFactory = $passwordFormFactory;
		$this->profileFormFactory = $profileFormFactory;
	}


	public function renderDefault(): void
	{
		if ( ! $this->getUser()->isLoggedIn()) {
			$this->redirectUrl( '/' . $this->locale . '/');
		}
	}


	public function createComponentChangePasswordForm(): \Nette\Application\UI\Form
	{
		return $this->passwordFormFactory->create(
			function (): void {
				$this->redirect('this');
			}, $this->locale
		);
	}


	public function createComponentProfileForm(): \Nette\Application\UI\Form
	{
		return $this->profileFormFactory->create(
			function (): void {
				$this->redirect('this');
			}, $this->locale
		);
	}

}
