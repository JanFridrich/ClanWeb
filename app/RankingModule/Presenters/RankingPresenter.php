<?php declare(strict_types = 1);

namespace App\RankingModule\Presenters;

class RankingPresenter extends \App\CoreModule\Presenters\BasePresenter
{

	private \App\RankingModule\Model\RankingService $rankingService;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService,
		\App\RankingModule\Model\RankingService $rankingService
	)
	{
		parent::__construct($pageService, $userService);
		$this->rankingService = $rankingService;
	}


	public function actionDefault(): void
	{
		if ( ! $this->getUserEntity()) {
			$this->redirect(':Core:Homepage:default', ['locale' => $this->locale, 'pageId' => 0]);
		}
	}


	public function renderDefault(): void
	{
		$this->template->rankDistributions = $this->rankingService->getRankDistributions();
		$this->template->userRank = \App\RankingModule\Model\RankingService::OFFSET_FOR_RANK[$this->getUserEntity()->getMaxedUnits()];
	}

}
