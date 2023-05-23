<?php declare(strict_types = 1);

namespace App\CoreModule\AdminModule\Presenters;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	/** @persistent */
	public string $locale = 'en';

	/** @var \Kdyby\Translation\Translator @inject */
	public \Kdyby\Translation\Translator $translator;

	private \App\UserModule\Model\UserService $userService;

	/**
	 * @var \App\UserModule\Model\User|null
	 */
	protected $userEntity;


	protected \App\CoreModule\GridFactory\DataGridFactory $dataGridFactory;


	public function __construct(
		\App\UserModule\Model\UserService $userService
	)
	{
		parent::__construct();
		$this->userService = $userService;
		$this->userEntity = NULL;
	}


	public function beforeRender(): void
	{
		if ($this->getParameter('locale')) {
			$this->locale = $this->getParameter('locale');
		}
		if ($this->getUser()->getIdentity()) {
			$this->userEntity = $this->userService->get($this->getUser()->getIdentity()->getId());
		}
		if ( ! $this->checkUserForRoles()) {
			$this->getUser()->logout(TRUE);
			$this->flashMessage('Byl si odhlášen nebo nemáš patřičnou roli pro přístup do DesiGnu');
			$this->redirect(':Core:Admin:Login:');
		}
		$this->translator->setLocale($this->locale);
		$this->template->setTranslator($this->translator);
		$this->template->locale = $this->locale;
		$this->template->action = ':' . $this->getRequest()->getPresenterName() . ':' . $this->getAction();
		$this->template->currentUser = $this->userEntity;
	}


	public function formatLayoutTemplateFiles(): array
	{
		$layouts[] = __DIR__ . "/templates/@layout.latte";

		return $layouts;
	}


	public function handleLogout(): void
	{
		$this->getUser()->logout(TRUE);
		$this->redirect(':Core:Admin:Login:');
	}


	protected function checkUserForRoles(): bool
	{
		return $this->userEntity && $this->userEntity->getRole();
	}


	protected function checkStuffAndRedirectIfTrue(bool $stuff, string $message, string $typeOfMessage = 'warning', string $redirect = 'this'): void
	{
		if ($stuff) {
			$this->flashMessage($message, $typeOfMessage);
			$this->redirect($redirect);
		}
	}


	public function checkPermission(string $role): bool
	{
		if ($this->userEntity) {
			return $this->userEntity->getRole() === $role;
		}

		return FALSE;
	}

	public function createComponentGrid(): \Ublaboo\DataGrid\DataGrid
	{
		return $this->dataGridFactory->create($this->locale);
	}

}
