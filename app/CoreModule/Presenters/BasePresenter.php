<?php declare(strict_types = 1);

namespace App\CoreModule\Presenters;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	/** @persistent */
	public $locale;

	/** @persistent */
	public $pageId;

	/** @var \Kdyby\Translation\Translator @inject */
	public $translator;

	/**
	 * @var \App\PageModule\Model\Page|null
	 */
	private $pageObject;

	private \App\PageModule\Model\PageService $pageService;

	private \App\UserModule\Model\UserService $userService;

	protected ?\App\UserModule\Model\User $userEntity;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService
	)
	{
		parent::__construct();
		$this->pageObject = NULL;
		$this->userEntity = NULL;
		$this->pageService = $pageService;
		$this->userService = $userService;
	}


	public function formatLayoutTemplateFiles(): array
	{
		$layouts[] = __DIR__ . "/templates/@layout.latte";

		return $layouts;
	}


	public function beforeRender(): void
	{
		$this->translator->setLocale($this->locale);
		$this->template->setTranslator($this->translator);
		$this->template->flags = $this->loadFlagSvg();
		$this->template->locale = $this->locale;
		$page = $this->pageService->getPageFromAlias($this->getHttpRequest()->getUrl()->getPath());
		if ($page === NULL && ! isset(\App\PageModule\Model\PageService::LANGUAGES[$this->locale]) && $this->getHttpRequest()->getUrl()->getPath() !== '') {
			$this->error();
		}
		$this->pageObject = $page;
		$mutations = [];
		foreach (\App\PageModule\Model\PageService::LANGUAGES as $LANGUAGE) {
			$mutations[$LANGUAGE] = $page && $this->pageService->getAliasForLanguageMutationById($LANGUAGE, $page->getId()) ? $this->pageService->getAliasForLanguageMutationById($LANGUAGE, $page->getId())->alias : NULL;
		}
		$this->template->aliasMutations = $mutations;
		$this->template->alias = $page && $page->getAlias() ? $page->getAlias() : '';
		$this->template->title = $page && $page->getTitle() ? $page->getTitle() . ' | ' : '';
		$this->template->singUpPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_SIGN_UP);
		$this->template->logInPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_LOG_IN);
		$this->template->profilePage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_PROFILE);
		$this->template->unitsPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_UNITS);
		$this->template->armorPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_ARMOR);
		$this->template->tablePage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_CREATE_TABLE);

		$this->template->user = $this->getUser();

		$this->template->userEntity = $this->getUserEntity();

		parent::beforeRender();
	}


	protected function getPageId(): ?int
	{
		$page = $this->pageObject;
		if ( ! $page) {
			return NULL;
		}

		return $page->getId();
	}


	private function loadFlagSvg(): array
	{
		return [
			'sk' => [
				'svg' => \file_get_contents(\ROOT_DIR . '/www/data/svg/flags/sk.svg'),
				'text' => ' Slovenčina',
			],
			'en' => [
				'svg' => \file_get_contents(\ROOT_DIR . '/www/data/svg/flags/en.svg'),
				'text' => ' English',
			],
		];
	}


	public function handleLogout(): void
	{
		$this->getUser()->logout(TRUE);
		$this->redirect('this');
	}


	protected function getUserEntity(): ?\App\UserModule\Model\User
	{
		if ($this->userEntity === NULL && $this->getUser()->getIdentity()) {
			$this->userEntity = $this->userService->get($this->getUser()->getIdentity()->getId());
		}

		return $this->userEntity;
	}

}
