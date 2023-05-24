<?php declare(strict_types = 1);

namespace App\UnitModule\Model;

class UnitService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\UnitModule\Model\UnitMapping::class;

	private \App\UnitModule\Model\UserUnitService $userUnitService;


	public function __construct(
		\Dibi\Connection $connection,
		\App\UnitModule\Model\UserUnitService $userUnitService
	)
	{
		parent::__construct($connection);
		$this->userUnitService = $userUnitService;
	}


	public function createNew(array $values): int
	{
		$values[\App\UnitModule\Model\UnitMapping::COLUMN_SORT] = $values[\App\UnitModule\Model\UnitMapping::COLUMN_SORT] ?? 0;
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

		return parent::saveFormData($values, $unit);
	}


	public function getUnitsForUser(int $userId): array
	{
		$unitsIds = $this->userUnitService->getAllUnitIdsForUser($userId);
		$units = [];
		$entitiesData = $this->connection->select($this->mappingClass::TABLE_NAME . '.*, ' . \App\UnitModule\Model\UserUnitMapping::COLUMN_LEVEL . ', ' . \App\UnitModule\Model\UserUnitMapping::COLUMN_LINE . ', ' . \App\UnitModule\Model\UserUnitMapping::COLUMN_MASTERY)
			->from($this->mappingClass::TABLE_NAME)
			->leftJoin(\App\UnitModule\Model\UserUnitMapping::TABLE_NAME)->on(\App\UnitModule\Model\UserUnitMapping::TABLE_NAME . '.' . \App\UnitModule\Model\UserUnitMapping::COLUMN_UNIT . ' = ' . $this->mappingClass::TABLE_NAME . '.' . $this->mappingClass::COLUMN_ID . ' AND ' . \App\UnitModule\Model\UserUnitMapping::TABLE_NAME . '.' . \App\UnitModule\Model\UserUnitMapping::COLUMN_USER . ' = %i', $userId)
			->where($this->mappingClass::TABLE_NAME . '.' . $this->mappingClass::COLUMN_ID . ' IN %in', $unitsIds)
			->orderBy($this->mappingClass::COLUMN_SORT)
			->fetchAll()
		;
		foreach ($entitiesData as $entityData) {
			$unit = $this->constructEntity($entityData);
			if ( ! $unit) {
				continue;
			}
			$units[$unit->getId()] = $unit;
		}

		return $units;
	}


	public function getAll(): array
	{
		$entities = [];
		$entitiesData = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
			->orderBy($this->mappingClass::COLUMN_SORT)
			->fetchAll()
		;
		foreach ($entitiesData as $entityData) {
			$entity = $this->constructEntity($entityData);
			if ( ! $entity) {
				continue;
			}
			$entities[$entity->getId()] = $entity;
		}

		return $entities;
	}


	protected function constructEntity(?\Dibi\Row $unitData): ?\App\UnitModule\Model\Unit
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

			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}
	}

}
