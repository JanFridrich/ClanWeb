<?php declare(strict_types = 1);

namespace App\PageModule\Model;

class Page
{

	public const ACTIONS = [
		'Core:Sign:in' => 'Sign in',
		'Core:Sign:up' => 'Sign up',
		'User:Profile:default' => 'Profile',
		'Core:Sign:forgotPassword' => 'Forgot password',
		'Core:Sign:restorePassword' => 'Restore password',
	];


	private int $id;

	private bool $includeHeader;

	private bool $includeFooter;

	private ?string $module;

	private ?string $presenter;

	private ?string $action;

	private ?string $uid;

	private string $lang;

	private string $title;

	private ?\App\PageModule\Model\Page $parentPage;

	private ?int $sort;

	private ?string $alias;


	public function __construct(
		int $id,
		bool $includeHeader,
		bool $includeFooter,
		?string $module,
		?string $presenter,
		?string $action,
		?string $uid,
		string $lang,
		string $title,
		?\App\PageModule\Model\Page $parentPage,
		?int $sort,
		?string $alias
	)
	{
		$this->id = $id;
		$this->includeHeader = $includeHeader;
		$this->includeFooter = $includeFooter;
		$this->module = $module;
		$this->presenter = $presenter;
		$this->action = $action;
		$this->uid = $uid;
		$this->lang = $lang;
		$this->title = $title;
		$this->parentPage = $parentPage;
		$this->sort = $sort;
		$this->alias = $alias;
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function setId(int $id): void
	{
		$this->id = $id;
	}


	public function isIncludeHeader(): bool
	{
		return $this->includeHeader;
	}


	public function setIncludeHeader(bool $includeHeader): void
	{
		$this->includeHeader = $includeHeader;
	}


	public function isIncludeFooter(): bool
	{
		return $this->includeFooter;
	}


	public function setIncludeFooter(bool $includeFooter): void
	{
		$this->includeFooter = $includeFooter;
	}


	public function getModule(): ?string
	{
		return $this->module;
	}


	public function setModule(?string $module): void
	{
		$this->module = $module;
	}


	public function getPresenter(): ?string
	{
		return $this->presenter;
	}


	public function setPresenter(?string $presenter): void
	{
		$this->presenter = $presenter;
	}


	public function getAction(): ?string
	{
		return $this->action ?: 'default';
	}


	public function setAction(?string $action): void
	{
		$this->action = $action;
	}


	public function getUid(): ?string
	{
		return $this->uid;
	}


	public function setUid(?string $uid): void
	{
		$this->uid = $uid;
	}


	public function getLang(): string
	{
		return $this->lang;
	}


	public function setLang(string $lang): void
	{
		$this->lang = $lang;
	}


	public function getTitle(): string
	{
		return $this->title;
	}


	public function setTitle(string $title): void
	{
		$this->title = $title;
	}


	public function getParentPage(): ?\App\PageModule\Model\Page
	{
		return $this->parentPage;
	}


	public function setParentPage(?\App\PageModule\Model\Page $parentPage): void
	{
		$this->parentPage = $parentPage;
	}


	public function getSort(): ?int
	{
		return $this->sort;
	}


	public function setSort(?int $sort): void
	{
		$this->sort = $sort;
	}


	public function getAlias(): ?string
	{
		return $this->alias;
	}


	public function setAlias(?string $alias): void
	{
		$this->alias = $alias;
	}


	public function getActionForUser(): string
	{
		$index = $this->getModule() . ':' . $this->getPresenter() . ':' . $this->getAction();

		return static::ACTIONS[$index] ?? $index;
	}


	public function getActionSettingInArrayFromString(string $index): array
	{
		$keys = \array_search($index, static::ACTIONS);
		$keys = \explode(':', $keys, PHP_INT_MAX);

		return [
			\App\PageModule\Model\PageMapping::COLUMN_MODULE => $keys[0] ?? NULL,
			\App\PageModule\Model\PageMapping::COLUMN_PRESENTER => $keys[1] ?? NULL,
			\App\PageModule\Model\PageMapping::COLUMN_ACTION => $keys[2] ?? NULL,

		];
	}

}
