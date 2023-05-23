<?php declare(strict_types = 1);

namespace App\CoreModule\Model;

abstract class Service
{

	protected string $mappingClass = \App\CoreModule\Model\Mapping::class;

	protected \Dibi\Connection $connection;


	public function __construct(
		\Dibi\Connection $connection
	)
	{
		$this->connection = $connection;
	}


	public function get(int $id): ?\App\CoreModule\Model\Entity
	{
		$entityData = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::COLUMN_ID . ' = %i', $id)
			->fetch()
		;

		return $this->constructEntity($entityData);
	}


	public function getByLang(int $id, string $lang): ?\App\CoreModule\Model\Entity
	{
		$entityData = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::COLUMN_ID . ' = %i', $id)
			->where($this->mappingClass::COLUMN_LANG . ' = %s', $lang)
			->fetch()
		;

		return $this->constructEntity($entityData);
	}


	public function getAll(): array
	{
		$entities = [];
		$select = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
		;
		if (\defined($this->mappingClass . '::COLUMN_SORT')) {
			$select->orderBy($this->mappingClass::COLUMN_SORT);
		}
		$entitiesData = $select->fetchAll();

		foreach ($entitiesData as $entityData) {
			$entity = $this->constructEntity($entityData);
			if ( ! $entity) {
				continue;
			}
			$entities[$entity->getId()] = $entity;
		}

		return $entities;
	}


	public function getAllByWeb(string $lang): array
	{
		$entities = [];
		$select = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::COLUMN_LANG . ' = %s', $lang)
		;
		if (\defined($this->mappingClass . '::COLUMN_SORT')) {
			$select->orderBy($this->mappingClass::COLUMN_SORT);
		}
		$entitiesData = $select->fetchAll();

		foreach ($entitiesData as $entityData) {
			$entity = $this->constructEntity($entityData);
			if ( ! $entity) {
				continue;
			}
			$entities[] = $entity;
		}

		return $entities;
	}


	public function delete(int $id): void
	{
		$this->connection->delete($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::COLUMN_ID . ' = %i', $id)
			->execute()
		;
	}


	public function createNew(array $values): int
	{
		return $this->connection->insert($this->mappingClass::TABLE_NAME, $values)
			->execute(\dibi::IDENTIFIER)
		;
	}


	/**
	 * @throws \Dibi\Exception
	 *
	 * @return \Dibi\Result|null
	 *
	 * @param array<string, mixed> $values
	 */
	public function saveFormData(array $values, Entity $entity)
	{
		return $this->connection->update($this->mappingClass::TABLE_NAME, $values)
			->where($this->mappingClass::COLUMN_ID . ' = %i', $entity->getId())
			->execute()
		;
	}


	protected function constructEntity(?\Dibi\Row $entityData): ?\App\CoreModule\Model\Entity
	{
		if ( ! $entityData) {
			return NULL;
		}
		try {
			return new \App\CoreModule\Model\Entity();
		} catch (\Exception $exception) {

		}

		return NULL;
	}
}
