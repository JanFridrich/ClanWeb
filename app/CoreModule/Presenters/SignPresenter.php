<?php declare(strict_types = 1);

namespace App\CoreModule\Presenters;

final class SignPresenter extends \App\CoreModule\Presenters\BasePresenter
{

	/** @persistent */
	public $backlink = '';

	private \App\CoreModule\Forms\SignInFormFactory $signInFactory;

	private \App\CoreModule\Forms\SignUpFormFactory $signUpFactory;

	private \App\CoreModule\Forms\ForgotPasswordFormFactory $forgotPasswordFormFactory;

	private \App\CoreModule\Forms\RestorePasswordFormFactory $restorePasswordFormFactory;

	private \App\PageModule\Model\PageService $pageService;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\CoreModule\Forms\SignInFormFactory $signInFactory,
		\App\CoreModule\Forms\SignUpFormFactory $signUpFactory,
		\App\CoreModule\Forms\ForgotPasswordFormFactory $forgotPasswordFormFactory,
		\App\CoreModule\Forms\RestorePasswordFormFactory $restorePasswordFormFactory,
		\App\UserModule\Model\UserService $userService
	)
	{
		parent::__construct($pageService, $userService);
		$this->signInFactory = $signInFactory;
		$this->signUpFactory = $signUpFactory;
		$this->forgotPasswordFormFactory = $forgotPasswordFormFactory;
		$this->restorePasswordFormFactory = $restorePasswordFormFactory;
		$this->pageService = $pageService;
	}


	public function beforeRender(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$profilePage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_PROFILE);
			$this->redirectUrl('/' . $this->locale . '/' . $profilePage->getAlias());
		}
		$this->template->forgotPasswordPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_FORGOT_PASSWORD);
		$this->template->restorePasswordPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_RESTORE_PASSWORD);

		parent::beforeRender();
	}


	protected function createComponentSignInForm(): \Nette\Application\UI\Form
	{
		return $this->signInFactory->create(
			function (): void {
				$this->redirectUrl('/' . $this->locale . '/');
			}, $this->locale
		);
	}


	protected function createComponentForgotPasswordForm(): \Nette\Application\UI\Form
	{
		return $this->forgotPasswordFormFactory->create(
			function ($email): void {
				$restorePasswordPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_RESTORE_PASSWORD);
				$this->flashMessage($this->translator->translate("messages.passwords.token_send_check_email"));
				$this->redirectUrl('/' . $this->locale . '/' . $restorePasswordPage->getAlias() . '?email=' . $email);
			}, $this->locale
		);
	}


	protected function createComponentSignUpForm(): \Nette\Application\UI\Form
	{
		return $this->signUpFactory->create(
			function (): void {
				$this->redirectUrl('/' . $this->locale . '/');
			}, $this->locale
		);
	}


	protected function createComponentRestorePasswordForm(): \Nette\Application\UI\Form
	{

		return $this->restorePasswordFormFactory->create(
			function (): void {
				$this->redirectUrl('/' . $this->locale . '/');
			}, $this->getParameter('email', ''),
			$this->locale,
			$this->getParameter('token', NULL)
		);
	}

}
