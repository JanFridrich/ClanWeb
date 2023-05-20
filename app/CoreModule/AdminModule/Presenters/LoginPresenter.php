<?php declare(strict_types = 1);

namespace App\CoreModule\AdminModule\Presenters;

class LoginPresenter extends \App\CoreModule\AdminModule\Presenters\BasePresenter
{

	private \App\CoreModule\Forms\SignInFormFactory $signInFormFactory;

	private \App\PageModule\Model\PageService $pageService;


	public function __construct(
		\App\CoreModule\Forms\SignInFormFactory $signInFormFactory,
		\App\UserModule\Model\UserService $userService,
		\App\PageModule\Model\PageService $pageService
	)
	{
		parent::__construct($userService);
		$this->signInFormFactory = $signInFormFactory;
		$this->pageService = $pageService;
	}


	public function beforeRender(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(':Core:Admin:Homepage:');
		}
		parent::beforeRender();
	}


	public function actionDefault(): void
	{
		$this->template->logInPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_LOG_IN);
		$this->template->forgotPasswordPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_FORGOT_PASSWORD);
	}


	public function formatLayoutTemplateFiles(): array
	{
		return [__DIR__ . "/templates/@loginLayout.latte"];
	}


	protected function createComponentSignInForm(): \Nette\Application\UI\Form
	{
		return $this->signInFormFactory->create(
			function (): void {
				$this->redirect(':Core:Admin:Homepage:');
			}, $this->locale
		);
	}


	protected function checkUserForRoles(): bool
	{
		return $this->userEntity === NULL;
	}

}
