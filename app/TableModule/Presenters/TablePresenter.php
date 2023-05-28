<?php declare(strict_types = 1);

namespace App\TableModule\Presenters;

class TablePresenter extends \App\CoreModule\Presenters\BasePresenter
{

	private \App\TableModule\Forms\TableCreateFormFactory $tableCreateFormFactory;

	private \App\PageModule\Model\PageService $pageService;

	private ?\App\PageModule\Model\Page $unitsPage;

	private ?\App\PageModule\Model\Page $rolesPage;

	private ?\App\PageModule\Model\Page $previewPage;

	private \App\TableModule\Forms\TableUnitFormFactory $tableUnitFormFactory;

	private \App\TableModule\Model\Table\TableService $tableService;

	private \App\UnitModule\Model\UnitService $unitService;

	private \App\UserModule\Model\UserService $userService;

	private ?\App\TableModule\Model\Table\Table $table;

	private array $options;


	public function __construct(
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService,
		\App\TableModule\Forms\TableCreateFormFactory $tableCreateFormFactory,
		\App\TableModule\Forms\TableUnitFormFactory $tableUnitFormFactory,
		\App\TableModule\Model\Table\TableService $tableService,
		\App\UnitModule\Model\UnitService $unitService
	)
	{
		parent::__construct($pageService, $userService);
		$this->tableCreateFormFactory = $tableCreateFormFactory;
		$this->pageService = $pageService;
		$this->unitsPage = NULL;
		$this->rolesPage = NULL;
		$this->previewPage = NULL;
		$this->tableUnitFormFactory = $tableUnitFormFactory;
		$this->tableService = $tableService;
		$this->unitService = $unitService;
		$this->userService = $userService;
		$this->options = [];
	}


	public function beforeRender(): void
	{
		parent::beforeRender();
		$this->previewPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_PREVIEW_TABLE);
	}


	public function actionCreate(): void
	{
		$this->unitsPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_ASSIGN_UNITS);
	}


	public function actionUnits(int $tableId, ?string $tierLock = NULL): void
	{
		if ($tierLock) {
			foreach (\App\UnitModule\Model\Unit::TIERS as $tier) {
				if ($tier === $tierLock) {
					$this->options['tierLock'][] = $tier;
					break;
				}
				$this->options['tierLock'][] = $tier;
			}
		}
		$this->table = $this->tableService->get($tableId);

		$units[] = ['-'];
		$armors[] = ['-'];
		$formatted = [];
		$armorsFormatted = [];

		foreach ($this->userService->getAll($this->options) as $user) {
			$formatted[$user->getLogin()][] = '-';
			/** @var \App\UnitModule\Model\Unit $unit */
			foreach ($user->getUnits() as $unit) {
				if ($unit->getLevel() > 5) {
					$formatted[$user->getLogin()][$unit->getId()] = \str_replace('_', ' ', $unit->getName()) . '-' .
						$unit->getLevelsArray()[$unit->getLevel()] . '=' . $unit->getLeadership();
				}
			}
			$armorsFormatted[$user->getLogin()][] = '-';
			/** @var \App\ArmorModule\Model\Armor\Armor $armor */
			foreach ($user->getArmors() as $armor) {
				$login = $user->getLogin();
				$armorsFormatted[$login][$armor->getId()] = $armor->getName() . '-' . $armor->getPrefer() . '=' . $armor->getLeadership();
			}
			$armors[] = $armorsFormatted[$user->getLogin()];
			$units[] = $formatted[$user->getLogin()];
		}

		$this->rolesPage = $this->pageService->getPageByUid($this->locale, \App\PageModule\Model\PageService::UID_ROLES_TABLE);
		$this->template->consts = $units;
		$this->template->rows = $this->table->getRows() + 1;
		$this->template->constsClasses = $armors;
	}


	public function createComponentCreateForm(): \Nette\Application\UI\Form
	{
		return $this->tableCreateFormFactory->create(
			function (int $id, string $tierLock) {
				$this->redirect(':' . \App\PageModule\Model\Page::ACTION_TABLE_UNITS, [
					'pageId' => $this->unitsPage->getId(),
					'tableId' => $id,
					'tierLock' => $tierLock,
				]);
			},
			$this->getUserEntity()
		);
	}


	public function createComponentUnitsForm(): \Nette\Application\UI\Form
	{

		return $this->tableUnitFormFactory->create(
			function () {
				$this->redirect('this');
			},
			$this->getUserEntity(),
			$this->table,
			$this->options
		);
	}

}
