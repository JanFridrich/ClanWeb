<?php declare(strict_types = 1);

namespace App\UnitModule\Model;

class UnitService extends \App\CoreModule\Model\Service
{

	public const SHOW_ALL = 'showAll';

	protected string $mappingClass = \App\UnitModule\Model\UnitMapping::class;


	public function __construct(
		\Dibi\Connection $connection
	)
	{
		parent::__construct($connection);
	}


	protected function addOptions(\Dibi\Fluent $select, array $options): void
	{
		if (isset($options[self::SHOW_ALL]) && $options[self::SHOW_ALL] === FALSE) {
			$options['where'][\App\UnitModule\Model\UnitMapping::TABLE_NAME . '.' . \App\UnitModule\Model\UnitMapping::COLUMN_SHOW] = TRUE;
		}
		parent::addOptions($select, $options);
	}


	public function createNew(array $values): int
	{
		$values[\App\UnitModule\Model\UnitMapping::COLUMN_SORT] = $values[\App\UnitModule\Model\UnitMapping::COLUMN_SORT] ?? 0;
		$values[\App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY] = $values[\App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY] ?? 0;
		$image = $values[\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE];
		if ($image->isOk()) {
			$file = \fopen('data/images/units/' . $image->getSanitizedName(), 'wb+');
			\fwrite($file, $image->getContents());
			\fclose($file);
			$values[\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE] = $image->getSanitizedName();
		} else {
			unset($values[\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE]);
		}

		return parent::createNew($values);
	}


	/**
	 * @throws \Dibi\Exception
	 *
	 * @param \App\UnitModule\Model\Unit $unit
	 * @param array<string, mixed> $values
	 *
	 * @return \Dibi\Result|null
	 *
	 */
	public function saveFormData(array $values, \App\CoreModule\Model\Entity $unit)
	{
		$values[\App\UnitModule\Model\UnitMapping::COLUMN_SORT] = $values[\App\UnitModule\Model\UnitMapping::COLUMN_SORT] ?? 0;
		$image = $values[\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE];
		if ($image->isOk()) {
			if ($unit->getImage() && \file_exists('data/images/units/' . $unit->getImage())) {
				\unlink('data/images/units/' . $unit->getImage());
			}

			$file = \fopen('data/images/units/' . $image->getSanitizedName(), 'wb+');
			\fwrite($file, $image->getContents());
			\fclose($file);
			$values[\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE] = $image->getSanitizedName();
		} else {
			unset($values[\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE]);
		}
		$values[\App\UnitModule\Model\UnitMapping::COLUMN_SORT] = $values[\App\UnitModule\Model\UnitMapping::COLUMN_SORT] ?? 0;
		$values[\App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY] = $values[\App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY] ?? 0;

		return parent::saveFormData($values, $unit);
	}


	public function getUnitsForUser(int $userId, array $options = []): array
	{
		$units = [];
		$select = $this->connection->select($this->mappingClass::TABLE_NAME . '.*, ' . \App\UnitModule\Model\UserUnitMapping::COLUMN_LEVEL . ', ' . \App\UnitModule\Model\UserUnitMapping::COLUMN_LINE . ', ' . \App\UnitModule\Model\UserUnitMapping::COLUMN_MASTERY)
			->from($this->mappingClass::TABLE_NAME)
			->leftJoin(\App\UnitModule\Model\UserUnitMapping::TABLE_NAME)->on(\App\UnitModule\Model\UserUnitMapping::TABLE_NAME . '.' . \App\UnitModule\Model\UserUnitMapping::COLUMN_UNIT . ' = ' . $this->mappingClass::TABLE_NAME . '.' . $this->mappingClass::COLUMN_ID . ' AND ' . \App\UnitModule\Model\UserUnitMapping::TABLE_NAME . '.' . \App\UnitModule\Model\UserUnitMapping::COLUMN_USER . ' = %i', $userId)
			->where(\App\UnitModule\Model\UserUnitMapping::TABLE_NAME . '.' . \App\UnitModule\Model\UserUnitMapping::COLUMN_USER . ' = %i', $userId)
			->orderBy($this->mappingClass::COLUMN_SORT)
		;
		$this->addOptions($select, $options);
		$entitiesData = $select->fetchAll();
		foreach ($entitiesData as $entityData) {
			$unit = $this->constructEntity($entityData);
			if ( ! $unit) {
				continue;
			}
			$units[$unit->getId()] = $unit;
		}

		return $units;
	}


	public function getCountOfUnits(): int
	{
		return $this->connection->select('COUNT(*)')->from($this->mappingClass::TABLE_NAME)->fetchSingle();
	}


	protected function constructEntity(?\Dibi\Row $unitData, array $options = []): ?\App\UnitModule\Model\Unit
	{
		if ( ! $unitData) {
			return NULL;
		}
		try {

			return new \App\UnitModule\Model\Unit(
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_ID],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_NAME],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_TIER],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_VETERANCY_LINE],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_PRIORITY],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_CATEGORY],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_LEADERSHIP],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_MAX_LEVEL],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_IMAGE],
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_SORT],
				$unitData[\App\UnitModule\Model\UserUnitMapping::COLUMN_LEVEL] ?? NULL,
				$unitData[\App\UnitModule\Model\UserUnitMapping::COLUMN_LINE] ?? NULL,
				$unitData[\App\UnitModule\Model\UserUnitMapping::COLUMN_MASTERY] ?? NULL,
				$unitData[\App\UnitModule\Model\UnitMapping::COLUMN_MAX_MASTERY],
				(bool) $unitData[\App\UnitModule\Model\UnitMapping::COLUMN_SHOW],
			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}
	}

}
