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


	public function get(int $id, array $options = []): ?\App\CoreModule\Model\Entity
	{
		$select = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::COLUMN_ID . ' = %i', $id)
		;
		$this->addOptions($select, $options);
		$entityData = $select->fetch();

		return $this->constructEntity($entityData);
	}


	public function getAll(array $options = []): array
	{
		$entities = [];
		$select = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
		;
		if (\defined($this->mappingClass . '::COLUMN_SORT')) {
			$select->orderBy($this->mappingClass::COLUMN_SORT);
		}
		$this->addOptions($select, $options);

		$entitiesData = $select->fetchAll();

		foreach ($entitiesData as $entityData) {
			$entity = $this->constructEntity($entityData, $options);
			if ( ! $entity) {
				continue;
			}
			$entities[$entity->getId()] = $entity;
		}

		return $entities;
	}


	public function delete(int $id, array $options = []): void
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
	public function saveFormData(array $values, \App\CoreModule\Model\Entity $entity)
	{
		return $this->connection->update($this->mappingClass::TABLE_NAME, $values)
			->where($this->mappingClass::COLUMN_ID . ' = %i', $entity->getId())
			->execute()
		;
	}


	protected function constructEntity(?\Dibi\Row $entityData, array $options = []): ?\App\CoreModule\Model\Entity
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


	protected function addOptions(\Dibi\Fluent $select, array $options): void
	{
		if (isset($options['where'])) {
			foreach ($options['where'] as $column => $value) {
				if ($value === NULL) {
					$select->where($column . ' IS NULL');
					continue;
				}
				if (\is_string($value)) {
					$select->where($column . ' = %s', $value);
					continue;
				}
				if (\is_int($value)) {
					$select->where($column . ' = %i', $value);
					continue;
				}
				if (\is_bool($value)) {
					$select->where($column . ' = %b', $value);
					continue;
				}
				if (\is_float($value)) {
					$select->where($column . ' = %f', $value);
					continue;
				}
				if (\is_array($value) && $value !== []) {
					$select->where($column . ' IN %in', $value);
					continue;
				}
				if ($value instanceof \Dibi\DateTime) {
					$select->where($column . ' = %d', $value);
					continue;
				}
				if ($value instanceof CustomWhere) {
					$select->where($value->getSql());
					continue;
				}
			}
		}
	}
}
