<?php declare(strict_types = 1);

namespace App\PageModule\Model;

class Page extends \App\CoreModule\Model\Entity
{

	public const ACTION_SIGN_IN = 'Core:Sign:in';
	public const ACTION_SIGN_UP = 'Core:Sign:up';
	public const ACTION_PROFILE = 'User:Profile:default';
	public const ACTION_FORGOT_PASSWORD = 'Core:Sign:forgotPassword';
	public const ACTION_RESTORE_PASSWORD = 'Core:Sign:restorePassword';
	public const ACTION_UNITS = 'Unit:Unit:default';
	public const ACTION_ARMORS = 'Armor:Armor:default';
	public const ACTION_CREATE_TABLE = 'Table:Table:create';
	public const ACTION_TABLE_UNITS = 'Table:Table:units';
	public const ACTION_TABLE_ROLES = 'Table:Table:roles';
	public const ACTION_TABLE_PREVIEW = 'Table:Table:preview';
	public const ACTION_RANKING = 'Ranking:Ranking:default';

	public const ACTIONS = [
		self::ACTION_SIGN_IN => 'sign in',
		self::ACTION_SIGN_UP => 'sign up',
		self::ACTION_PROFILE => 'profile',
		self::ACTION_FORGOT_PASSWORD => 'forgot password',
		self::ACTION_RESTORE_PASSWORD => 'restore password',
		self::ACTION_UNITS => 'units',
		self::ACTION_ARMORS => 'armors',
		self::ACTION_CREATE_TABLE => 'create table',
		self::ACTION_TABLE_UNITS => 'table units',
		self::ACTION_TABLE_ROLES => 'table roles',
		self::ACTION_TABLE_PREVIEW => 'table preview',
		self::ACTION_RANKING => 'ranking',
	];

	protected bool $includeHeader;

	protected bool $includeFooter;

	protected ?string $module;

	protected ?string $presenter;

	protected ?string $action;

	protected ?string $uid;

	protected string $lang;

	protected string $title;

	protected ?\App\PageModule\Model\Page $parentPage;

	protected ?int $sort;

	protected ?string $alias;


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
