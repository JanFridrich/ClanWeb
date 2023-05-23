<?php declare(strict_types = 1);

namespace App\UnitModule\Model;

class Unit extends \App\CoreModule\Model\Entity
{

	public const
		TIER_GREY = 'grey',
		TIER_GREEN = 'green',
		TIER_BLUE = 'blue',
		TIER_PURPLE = 'purple',
		TIER_ORANGE = 'orange';

	public const TIERS = [
		self::TIER_GREY => self::TIER_GREY,
		self::TIER_GREEN => self::TIER_GREEN,
		self::TIER_BLUE => self::TIER_BLUE,
		self::TIER_PURPLE => self::TIER_PURPLE,
		self::TIER_ORANGE => self::TIER_ORANGE,
	];

	public const
		CATEGORY_RANGED = 'ranged',
		CATEGORY_MELEE = 'melee',
		CATEGORY_CAVALRY = 'cavalry';

	public const CATEGORIES = [
		self::CATEGORY_RANGED => self::CATEGORY_RANGED,
		self::CATEGORY_MELEE => self::CATEGORY_MELEE,
		self::CATEGORY_CAVALRY => self::CATEGORY_CAVALRY,
	];

	public const PRIORITY_MOST_WANTED = 'most wanted',
		PRIORITY_WANTED = 'wanted',
		PRIORITY_GOOD_TO_HAVE = 'good to have',
		PRIORITY_FILL = 'fill',
		PRIORITY_DONT_CARE = 'dont care';

	public const PRIORITIES = [
		self::PRIORITY_MOST_WANTED => self::PRIORITY_MOST_WANTED,
		self::PRIORITY_WANTED => self::PRIORITY_WANTED,
		self::PRIORITY_GOOD_TO_HAVE => self::PRIORITY_GOOD_TO_HAVE,
		self::PRIORITY_FILL => self::PRIORITY_FILL,
		self::PRIORITY_DONT_CARE => self::PRIORITY_DONT_CARE,
	];

	public const VETERANCY_LINE_UP = 'up',
		VETERANCY_HYBRID = 'hybrid',
		VETERANCY_DOWN = 'down';

	public const VETERANCIES = [
		self::VETERANCY_LINE_UP => self::VETERANCY_LINE_UP,
		self::VETERANCY_HYBRID => self::VETERANCY_HYBRID,
		self::VETERANCY_DOWN => self::VETERANCY_DOWN,
	];

	public const DEFAULT_MAX_LEVELS = [
		self::TIER_GREY => 10,
		self::TIER_GREEN => 18,
		self::TIER_BLUE => 18,
		self::TIER_PURPLE => 24,
		self::TIER_ORANGE => 30,
	];

	protected int $id;

	protected string $name;

	protected string $tier;

	protected string $category;

	protected int $leadership;

	protected int $maxLevel;

	protected string $image;

	protected string $veterancyLine;

	protected string $priority;

	protected ?int $level;

	protected ?string $userLine;

	protected int $sort;


	public function __construct(
		int $id,
		string $name,
		string $tier,
		string $veterancyLine,
		string $priority,
		string $category,
		int $leadership,
		int $maxLevel,
		string $image,
		int $sort,
		?int $level,
		?string $userLine
	)
	{
		$this->id = $id;
		$this->name = $name;
		$this->tier = $tier;
		$this->category = $category;
		$this->leadership = $leadership;
		$this->maxLevel = $maxLevel;
		$this->image = $image;
		$this->veterancyLine = $veterancyLine;
		$this->priority = $priority;
		$this->level = $level;
		$this->userLine = $userLine;
		$this->sort = $sort;
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function setId(int $id): void
	{
		$this->id = $id;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function setName(string $name): void
	{
		$this->name = $name;
	}


	public function getTier(): string
	{
		return $this->tier;
	}


	public function setTier(string $tier): void
	{
		$this->tier = $tier;
	}


	public function getCategory(): string
	{
		return $this->category;
	}


	public function setCategory(string $category): void
	{
		$this->category = $category;
	}


	public function getLeadership(): int
	{
		return $this->leadership;
	}


	public function setLeadership(int $leadership): void
	{
		$this->leadership = $leadership;
	}


	public function getMaxLevel(): int
	{
		return $this->maxLevel;
	}


	public function setMaxLevel(int $maxLevel): void
	{
		$this->maxLevel = $maxLevel;
	}


	public function getImage(): string
	{
		return $this->image;
	}


	public function setImage(string $image): void
	{
		$this->image = $image;
	}


	public function getVeterancyLine(): string
	{
		return $this->veterancyLine;
	}


	public function setVeterancyLine(string $veterancyLine): void
	{
		$this->veterancyLine = $veterancyLine;
	}


	public function getPriority(): string
	{
		return $this->priority;
	}


	public function setPriority(string $priority): void
	{
		$this->priority = $priority;
	}


	public function getUserVeterancyLine(): ?string
	{
		return $this->userLine;
	}


	public function setUserVeterancyLine(?string $userLine): void
	{
		$this->userLine = $userLine;
	}


	public function getLevel(): ?int
	{
		return $this->level;
	}


	public function setLevel(?int $level): void
	{
		$this->level = $level;
	}


	/**
	 * @return array<int,null|string|int>
	 */
	public function getLevelsArray(): array
	{
		$levels = [NULL];
		for ($i = 1; $i < $this->maxLevel; $i++) {
			$levels[$i] = $i;
		}
		$levels[$this->maxLevel] = 'max';

		return $levels;
	}


	public function getSort(): int
	{
		return $this->sort;
	}


	public function setSort(int $sort): void
	{
		$this->sort = $sort;
	}

}
