<?php declare(strict_types = 1);

namespace App\UnitModule\Model;

class UserUnitService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\UnitModule\Model\UserUnitMapping::class;


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
	 * @return \Dibi\Result|null
	 *
	 * @param array<string|int, mixed> $values
	 * @param \App\UserModule\Model\User $entity
	 */
	public function saveFormData(array $values, \App\CoreModule\Model\Entity $entity)
	{
		$this->connection->delete($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::COLUMN_USER . ' = %i', $entity->getId())
			->execute()
		;

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

		return NULL;
	}


	protected function constructEntity(?\Dibi\Row $entityData): ?\App\UnitModule\Model\UserUnit
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
			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}
	}

}
