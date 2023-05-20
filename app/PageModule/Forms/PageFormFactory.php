<?php declare(strict_types = 1);

namespace App\PageModule\Forms;

class PageFormFactory
{

	private \App\PageModule\Model\PageService $pageService;


	public function __construct(
		\App\PageModule\Model\PageService $pageService
	)
	{
		$this->pageService = $pageService;
	}


	public function createNew(callable $onSuccess): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$form->addSelect(\App\PageModule\Model\PageMapping::COLUMN_ACTION, 'Action', \App\PageModule\Model\Page::ACTIONS)
			->setRequired()
		;
		$form->addText(\App\PageModule\Model\PageMapping::COLUMN_UID, 'Uid');
		$form->addText(\App\PageModule\Model\PageMapping::COLUMN_TEXT_TITLE, 'Title')
			->setRequired()
		;
		$form->addSelect(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG, 'Mutation', \App\PageModule\Model\PageService::LANGUAGES)
			->setRequired()
		;
		$form->addInteger(\App\PageModule\Model\PageMapping::COLUMN_TEXT_SORT, 'Sort')
			->addRule(\Nette\Application\UI\Form::MIN, 'must be bigger than 0', 0)
		;
		$form->addSubmit('send', 'Create');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess): void {

			$this->pageService->createNewPageFromForm(\array_merge((array) $values, [\App\PageModule\Model\PageMapping::COLUMN_TEXT_ALIAS => ((array) $values)[\App\PageModule\Model\PageMapping::COLUMN_TEXT_TITLE]]));
			$onSuccess();
		};

		return $form;
	}


	public function createEdit(callable $onSuccess, int $pageId, string $locale): \Nette\Application\UI\Form
	{
		$page = $this->pageService->get($pageId, $locale);

		$actionDefaultValue = $page->getActionForUser();
		if (\in_array($actionDefaultValue, \App\PageModule\Model\Page::ACTIONS, TRUE)) {
			$actionDefaultValue = \array_search($actionDefaultValue, \App\PageModule\Model\Page::ACTIONS, TRUE);
		}

		$form = new \Nette\Application\UI\Form();
		$form->addSelect(\App\PageModule\Model\PageMapping::COLUMN_ACTION, 'Action', \App\PageModule\Model\Page::ACTIONS)
			->setDefaultValue($actionDefaultValue)
			->setRequired()
		;
		$form->addText(\App\PageModule\Model\PageMapping::COLUMN_UID, 'Uid')
			->setDefaultValue($page->getUid())
		;
		$form->addText(\App\PageModule\Model\PageMapping::COLUMN_TEXT_TITLE, 'Title')
			->setDefaultValue($page->getTitle())
			->setRequired()
		;
		$form->addSelect(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG, 'Mutation', \App\PageModule\Model\PageService::LANGUAGES)
			->setDefaultValue($locale)
			->setRequired()
		;
		$form->addInteger(\App\PageModule\Model\PageMapping::COLUMN_TEXT_SORT, 'Sort')
			->setDefaultValue($page->getSort())
			->addRule(\Nette\Application\UI\Form::MIN, 'must be bigger than 0', 0)
		;
		$form->addText(\App\PageModule\Model\PageMapping::COLUMN_TEXT_ALIAS, 'Alias')
			->setDefaultValue($page->getAlias())
			->setRequired()
		;
		$form->addSubmit('send', 'Save');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess, $pageId): void {
			$this->pageService->editPage((array) $values, $pageId);
			$onSuccess();
		};

		return $form;
	}

}
