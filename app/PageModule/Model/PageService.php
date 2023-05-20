<?php declare(strict_types = 1);

namespace App\PageModule\Model;

class PageService
{

	public const LANGUAGES =
		[
			'en' => 'en',
		];
	public const DEFAULT_LANGUAGE = 'en';
	public const UID_LOG_IN = 'UID_LOG_IN';
	public const UID_SIGN_UP = 'UID_SIGN_UP';
	public const UID_PROFILE = 'UID_PROFILE';
	public const UID_FORGOT_PASSWORD = 'UID_FORGOT_PASSWORD';
	public const UID_RESTORE_PASSWORD = 'UID_RESTORE_PASSWORD';

	private \Dibi\Connection $database;


	public function __construct(
		\Dibi\Connection $database

	)
	{
		$this->database = $database;
	}


	public function get(int $id, string $locale): ?\App\PageModule\Model\Page
	{
		$pageData = $this->database->select('*')
			->from(\App\PageModule\Model\PageMapping::TABLE_NAME)
			->where(\App\PageModule\Model\PageMapping::COLUMN_ID . ' = %i', $id)
			->fetch()
		;
		if ($pageData === NULL) {
			return NULL;
		}
		$pageText = $this->database->select('*')
			->from(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_PAGE . ' = %i', $pageData->id)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $locale)
			->fetch()
		;
		if ($pageText === NULL) {
			return NULL;
		}

		return $this->constructPage($pageData, $pageText, $locale);
	}


	public function getByAlias(string $alias, string $locale): ?\App\PageModule\Model\Page
	{
		$pageText = $this->database->select('*')
			->from(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_ALIAS . ' = %s', $alias)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $locale)
			->fetch()
		;
		if ($pageText === NULL) {
			return NULL;
		}
		$pageData = $this->database->select('*')
			->from(\App\PageModule\Model\PageMapping::TABLE_NAME)
			->where(\App\PageModule\Model\PageMapping::COLUMN_ID . ' = %i', $pageText->page)
			->fetch()
		;
		if ($pageData === NULL) {
			return NULL;
		}

		return $this->constructPage($pageData, $pageText, $locale);
	}


	public function getPageFromAlias(string $httpPath): ?\App\PageModule\Model\Page
	{
		$separated = \explode("/", $httpPath);
		$separated = \array_filter($separated);

		if ( ! $separated) {
			return NULL;
		}
		$alias = \end($separated);
		$lang = self::LANGUAGES[$separated[1]] ?? self::DEFAULT_LANGUAGE;

		return $this->getByAlias($alias, $lang);
	}


	/**
	 * @return \App\PageModule\Model\Page[]
	 */
	public function getAllPages(string $locale): array
	{
		$pages = [];
		$pagesData = $this->database->select('*')
			->from(\App\PageModule\Model\PageMapping::TABLE_NAME)
			->leftJoin(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME)
			->on(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME . '.' . \App\PageModule\Model\PageMapping::COLUMN_TEXT_PAGE . ' = ' . \App\PageModule\Model\PageMapping::TABLE_NAME . '.' . \App\PageModule\Model\PageMapping::COLUMN_ID)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $locale)
			->fetchAll()
		;
		foreach ($pagesData as $pageData) {
			$page = $this->constructPageMergedData($pageData, $locale);
			if ($page) {
				$pages[$page->getId()] = $page;
			}
		}

		return $pages;
	}


	public function createNewPageFromForm(array $data): void
	{
		$pageId = $this->database->insert(
			\App\PageModule\Model\PageMapping::TABLE_NAME,
			$this->preparePageData($data)
		)->execute(\dibi::IDENTIFIER);
		try {
			$this->database->insert(
				\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME,
				$this->preparePageTextData($data, $pageId)
			)->execute();
		} catch (\Dibi\UniqueConstraintViolationException $exception) {
			$this->database->insert(
				\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME,
				$this->preparePageTextData($data, $pageId, TRUE)
			)->execute();
		}
	}


	public function editPage(array $data, int $pageId): void
	{
		$this->database->update(
			\App\PageModule\Model\PageMapping::TABLE_NAME,
			$this->preparePageData($data)
		)
			->where(\App\PageModule\Model\PageMapping::COLUMN_ID . ' = %i', $pageId)
			->execute()
		;
		try {
			$this->database->update(
				\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME,
				$this->preparePageTextData($data, $pageId)
			)
				->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_PAGE . ' = %i', $pageId)
				->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $data[\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG])
				->execute()
			;
		} catch (\Dibi\UniqueConstraintViolationException $exception) {
			$this->database->update(
				\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME,
				$this->preparePageTextData($data, $pageId, TRUE)
			)
				->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_PAGE . ' = %i', $pageId)
				->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $data[\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG])
				->execute()
			;
		}
	}


	private function getAllAliases(string $lang): array
	{
		return $this->database->select(\App\PageModule\Model\PageMapping::COLUMN_TEXT_ALIAS)
			->from(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $lang)
			->fetchAll()
		;
	}


	/**
	 * @return \App\PageModule\Model\Page[]
	 */
	public function getChildPages(int $id, string $lang): array
	{
		$pages = [];
		$pagesId = $this->database->select(\App\PageModule\Model\PageMapping::COLUMN_ID)
			->from(\App\PageModule\Model\PageMapping::TABLE_NAME)
			->leftJoin(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME)->on(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME . '.' . \App\PageModule\Model\PageMapping::COLUMN_TEXT_PAGE . ' = ' . \App\PageModule\Model\PageMapping::TABLE_NAME . '.' . \App\PageModule\Model\PageMapping::COLUMN_ID)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_PARENT_PAGE . ' = %i', $id)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $lang)
			->fetchAll()
		;
		foreach ($pagesId as $pageId) {
			$page = $this->get($pageId->id, $lang);
			if ($page === NULL) {
				continue;
			}
			$pages[] = $page;
		}

		return $pages;
	}


	public function getPageByUid(string $lang, string $uid): ?\App\PageModule\Model\Page
	{
		$pageData = $this->database->select('*')
			->from(\App\PageModule\Model\PageMapping::TABLE_NAME)
			->where(\App\PageModule\Model\PageMapping::COLUMN_UID . ' = %s', $uid)
			->fetch()
		;
		if ($pageData === NULL) {
			return NULL;
		}
		$pageText = $this->database->select('*')
			->from(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_PAGE . ' = %i', $pageData->id)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $lang)
			->fetch()
		;
		if ($pageText === NULL) {
			return NULL;
		}

		return $this->constructPage($pageData, $pageText, $lang);
	}


	public function getAliasForLanguageMutationById(string $language, int $id): ?\Dibi\Row
	{
		return $this->database->select(\App\PageModule\Model\PageMapping::COLUMN_TEXT_ALIAS)
			->from(\App\PageModule\Model\PageMapping::TEXT_TABLE_NAME)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_PAGE . ' = %i', $id)
			->where(\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG . ' = %s', $language)
			->fetch()
		;
	}


	private function constructPage(\Dibi\Row $pageData, \Dibi\Row $pageText, string $locale): ?\App\PageModule\Model\Page
	{
		$parentPage = NULL;
		if ($pageText->parent_page) {
			$parentPage = $this->get($pageText->parent_page, $locale);
		}
		try {
			$page = new \App\PageModule\Model\Page(
				$pageData->id,
				(bool) $pageData->include_header,
				(bool) $pageData->include_footer,
				$pageData->module,
				$pageData->presenter,
				$pageData->action,
				$pageData->uid,
				$pageText->language,
				$pageText->title,
				$parentPage,
				$pageText->sort,
				$pageText->alias,

			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}

		return $page;
	}


	public function constructPageMergedData(\Dibi\Row $pageData, string $locale): ?\App\PageModule\Model\Page
	{
		$parentPage = NULL;
		if ($pageData->parent_page) {
			$parentPage = $this->get($pageData->parent_page, $locale);
		}
		try {
			$page = new \App\PageModule\Model\Page(
				$pageData->id,
				(bool) $pageData->include_header,
				(bool) $pageData->include_footer,
				$pageData->module,
				$pageData->presenter,
				$pageData->action,
				$pageData->uid,
				$pageData->language,
				$pageData->title,
				$parentPage,
				$pageData->sort,
				$pageData->alias,

			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}

		return $page;
	}


	private function preparePageData(array $data, ?int $id = NULL): array
	{
		$keys = \explode(':', $data[\App\PageModule\Model\PageMapping::COLUMN_ACTION], PHP_INT_MAX);

		$preparedData = [
			\App\PageModule\Model\PageMapping::COLUMN_MODULE => $keys[0] ?? NULL,
			\App\PageModule\Model\PageMapping::COLUMN_PRESENTER => $keys[1] ?? NULL,
			\App\PageModule\Model\PageMapping::COLUMN_ACTION => $keys[2] ?? NULL,
			\App\PageModule\Model\PageMapping::COLUMN_UID => $data[\App\PageModule\Model\PageMapping::COLUMN_UID],
		];

		$preparedData[\App\PageModule\Model\PageMapping::COLUMN_INCLUDE_HEADER] = 1;
		$preparedData[\App\PageModule\Model\PageMapping::COLUMN_INCLUDE_FOOTER] = 1;
		if ($id) {
			$preparedData[\App\PageModule\Model\PageMapping::COLUMN_ID] = $id;
		}

		return $preparedData;
	}


	private function preparePageTextData(array $data, int $pageId, bool $tryAlternateAlias = FALSE): array
	{
		$alias = \Nette\Utils\Strings::webalize(\trim($data[\App\PageModule\Model\PageMapping::COLUMN_TEXT_ALIAS]));
		if ($tryAlternateAlias) {
			$alias .= '-' . \time();
		}

		return [
			\App\PageModule\Model\PageMapping::COLUMN_TEXT_PAGE => $pageId,
			\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG => $data[\App\PageModule\Model\PageMapping::COLUMN_TEXT_LANG],
			\App\PageModule\Model\PageMapping::COLUMN_TEXT_TITLE => $data[\App\PageModule\Model\PageMapping::COLUMN_TEXT_TITLE],
			\App\PageModule\Model\PageMapping::COLUMN_TEXT_SORT => $data[\App\PageModule\Model\PageMapping::COLUMN_TEXT_SORT],
			\App\PageModule\Model\PageMapping::COLUMN_TEXT_ALIAS => $alias,
		];
	}

}
