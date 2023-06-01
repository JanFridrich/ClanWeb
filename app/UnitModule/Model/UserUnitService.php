<?php declare(strict_types = 1);

namespace App\UnitModule\Model;

class UserUnitService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\UnitModule\Model\UserUnitMapping::class;


	public function __construct(
		\Dibi\Connection $connection,
		UnitService $unitService
	)
	{
		parent::__construct($connection);
		$this->unitService = $unitService;
	}


	public function getAllUnitIdsForUser(int $userId): array
	{
		return $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::COLUMN_USER . ' = %i', $userId)
			->fetchPairs($this->mappingClass::COLUMN_UNIT, $this->mappingClass::COLUMN_UNIT)
		;
	}


	/**
	 * @throws \Dibi\Exception
	 *
	 * @return int
	 *
	 * @param array<string|int, mixed> $values
	 * @param \App\UserModule\Model\User $entity
	 */
	public function saveFormData(array $values, \App\CoreModule\Model\Entity $entity)
	{
		try {
			$this->connection->delete($this->mappingClass::TABLE_NAME)
				->where($this->mappingClass::COLUMN_USER . ' = %i', $entity->getId())
				->where($this->mappingClass::COLUMN_UNIT . ' IN %in', \array_keys($values['units']))
				->execute()
			;
		} catch (\Dibi\Exception $e) {
			\Tracy\Debugger::barDump($e);
		}

		$values = $values['values'];

		foreach ($values as $unitId => $value) {
			if ($value[$this->mappingClass::COLUMN_LEVEL] === 0) {
				continue;
			}
			$value[$this->mappingClass::COLUMN_USER] = $entity->getId();
			$value[$this->mappingClass::COLUMN_UNIT] = $unitId;

			$this->connection->insert($this->mappingClass::TABLE_NAME, $value)
				->execute()
			;
		}

		$maxedUnits = (\round($this->getMaxedUnits($entity->getId(), [\App\UnitModule\Model\UnitService::SHOW_ALL => FALSE]) / $this->unitService->getCountOfUnits([\App\UnitModule\Model\UnitService::SHOW_ALL => FALSE]), 1)) * 100;

		return (int) ($maxedUnits);
	}


	public function getMaxedUnits(int $userId, array $options = []): int
	{
		$select = $this->connection->select('COUNT(*)')
			->from($this->mappingClass::TABLE_NAME)
			->leftJoin(\App\UnitModule\Model\UnitMapping::TABLE_NAME)->on($this->mappingClass::TABLE_NAME . '.' . $this->mappingClass::COLUMN_UNIT . ' = ' . \App\UnitModule\Model\UnitMapping::TABLE_NAME . '.' . \App\UnitModule\Model\UnitMapping::COLUMN_ID)
			->where($this->mappingClass::COLUMN_USER . ' = %i', $userId)
			->where($this->mappingClass::COLUMN_LEVEL . ' = ' . \App\UnitModule\Model\UnitMapping::COLUMN_MAX_LEVEL)
		;
		if (isset($options[\App\UnitModule\Model\UnitService::SHOW_ALL]) && $options[\App\UnitModule\Model\UnitService::SHOW_ALL] === FALSE) {
			$select->where(\App\UnitModule\Model\UnitMapping::TABLE_NAME . '.' . \App\UnitModule\Model\UnitMapping::COLUMN_SHOW . ' = %i', TRUE);
		}

		return $select->fetchSingle();
	}


	protected function constructEntity(?\Dibi\Row $entityData, array $options = []): ?\App\UnitModule\Model\UserUnit
	{
		if ( ! $entityData) {
			return NULL;
		}
		try {

			return new \App\UnitModule\Model\UserUnit(
				$entityData[\App\UnitModule\Model\UserUnitMapping::COLUMN_ID],
				$entityData[\App\UnitModule\Model\UserUnitMapping::COLUMN_USER],
				$entityData[\App\UnitModule\Model\UserUnitMapping::COLUMN_UNIT],
				$entityData[\App\UnitModule\Model\UserUnitMapping::COLUMN_LINE],
				$entityData[\App\UnitModule\Model\UserUnitMapping::COLUMN_LEVEL],
				$entityData[\App\UnitModule\Model\UserUnitMapping::COLUMN_MASTERY],
			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}
	}

}
