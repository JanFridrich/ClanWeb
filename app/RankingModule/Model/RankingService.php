<?php declare(strict_types = 1);

namespace App\RankingModule\Model;

class RankingService
{

	public const RANK_WOOD = 'wood';
	public const RANK_IRON = 'iron';
	public const RANK_BRONZE = 'bronze';
	public const RANK_SILVER = 'silver';
	public const RANK_GOLD = 'gold';
	public const RANK_PLATINUM = 'platinum';
	public const RANK_DIAMOND = 'diamond';
	public const RANK_MASTER = 'master';
	public const RANK_GRANDMASTER = 'grandmaster';
	public const RANK_CHALLENGER = 'challenger';

	public const RANKS = [
		10 => self::RANK_WOOD,
		20 => self::RANK_IRON,
		30 => self::RANK_BRONZE,
		40 => self::RANK_SILVER,
		50 => self::RANK_GOLD,
		60 => self::RANK_PLATINUM,
		70 => self::RANK_DIAMOND,
		80 => self::RANK_MASTER,
		90 => self::RANK_GRANDMASTER,
		100 => self::RANK_CHALLENGER,
	];

	public const OFFSET_FOR_RANK = [
		10 => 0,
		20 => 1,
		30 => 2,
		40 => 3,
		50 => 4,
		60 => 5,
		70 => 6,
		80 => 7,
		90 => 8,
		100 => 9,
	];

	private \Dibi\Connection $connection;


	public function __construct(
		\Dibi\Connection $connection
	)
	{
		$this->connection = $connection;
	}


	public function getRankDistributions(): array
	{


		$rankDistributions = $this->connection
			->select(\App\UserModule\Model\UserMapping::COLUMN_MAXED_UNITS . ', COUNT(' . \App\UserModule\Model\UserMapping::COLUMN_MAXED_UNITS . ') AS `count`')
			->from(\App\UserModule\Model\UserMapping::TABLE_NAME)
			->groupBy(\App\UserModule\Model\UserMapping::COLUMN_MAXED_UNITS)
			->orderBy(\App\UserModule\Model\UserMapping::COLUMN_MAXED_UNITS)->fetchAll()
		;
		$rankDistributionsFormatted[self::RANK_WOOD] = 0;
		$rankDistributionsFormatted[self::RANK_IRON] = 0;
		$rankDistributionsFormatted[self::RANK_BRONZE] = 0;
		$rankDistributionsFormatted[self::RANK_SILVER] = 0;
		$rankDistributionsFormatted[self::RANK_GOLD] = 0;
		$rankDistributionsFormatted[self::RANK_PLATINUM] = 0;
		$rankDistributionsFormatted[self::RANK_DIAMOND] = 0;
		$rankDistributionsFormatted[self::RANK_MASTER] = 0;
		$rankDistributionsFormatted[self::RANK_GRANDMASTER] = 0;
		$rankDistributionsFormatted[self::RANK_CHALLENGER] = 0;

		foreach ($rankDistributions as $rankDistribution) {
			$maxedUnits = $rankDistribution->maxedUnits;
			if ($rankDistribution->maxedUnits === 0){
				$maxedUnits = 10;
			}
			$rankDistributionsFormatted[self::RANKS[$maxedUnits]] = $rankDistribution->count;
		}

		return $rankDistributionsFormatted;
	}
}
