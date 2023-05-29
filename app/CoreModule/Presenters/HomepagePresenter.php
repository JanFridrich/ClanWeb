<?php declare(strict_types = 1);

namespace App\CoreModule\Presenters;

final class HomepagePresenter extends \App\CoreModule\Presenters\BasePresenter
{

	private \App\SettingModule\Model\SettingService $settingService;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService,
		\App\SettingModule\Model\SettingService $settingService
	)
	{
		parent::__construct($pageService, $userService);
		$this->settingService = $settingService;
	}


	public function renderDefault(): void
	{
		$this->template->content = $this->settingService->getByKey(\App\SettingModule\Model\Setting::KEY_HOMEPAGE_CONTENT)->getValue();
	}

}
