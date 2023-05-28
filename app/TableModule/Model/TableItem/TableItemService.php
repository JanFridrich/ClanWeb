<?php declare(strict_types = 1);

namespace App\TableModule\Model\TableItem;

class TableItemService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = TableItemMapping::class;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\Dibi\Connection $connection,
		\App\UserModule\Model\UserService $userService
	)
	{
		parent::__construct($connection);
		$this->userService = $userService;
	}


	public function getAll(array $options = []): array
	{
		$entities = [];
		$select = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
			->orderBy($this->mappingClass::COLUMN_POSITION)
		;
		$this->addOptions($select, $options);

		$entitiesData = $select->fetchAll();
		$entitiesData = $this->groupByPosition($entitiesData);
		foreach ($entitiesData as $entityData) {
			$entity = $this->constructTableItem($entityData, $options);
			if ( ! $entity) {
				continue;
			}
			$entities[$entity->getId()] = $entity;
		}
		\usort($entities, function (\App\TableModule\Model\TableItem\TableItem $a, \App\TableModule\Model\TableItem\TableItem $b) {
			return $a->getSquad() <=> $b->getSquad();
		});

		return $entities;
	}


	public function clearTableItems(int $tableId, array $types): void
	{
		foreach ($types as $type) {
			$this->connection->delete($this->mappingClass::TABLE_NAME)
				->where([
					$this->mappingClass::COLUMN_TABLE => $tableId,
					$this->mappingClass::COLUMN_TYPE => $type,
				])
				->execute()
			;
		}
	}


	protected function constructTableItem(?array $data, array $options = []): ?\App\CoreModule\Model\Entity
	{
		if ($data === NULL) {
			return NULL;
		}
		$this->groupByPosition($data);
		$units = [];
		$armor = '';
		$squad = '';
		$note = '';
		$id = 0;
		$user = 0;
		$groupLeader = FALSE;
		foreach ($data as $row) {
			switch ($row[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TYPE]) {
				case(\App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_UNIT):
					$units[] = $row[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE];
					break;
				case(\App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_ARMOR):
					$armor = $row[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE];
					break;
				case(\App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_SQUAD):
					$user = $row[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_USER];
					$squad = $row[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE];
					$id = $row[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_POSITION];
					break;
				case(\App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_NOTE):
					$note = $row[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE];
					break;
				case(\App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_GROUP_LEADER):
					$groupLeader = (bool) $row[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE];
					break;
			}
		}

		return new \App\TableModule\Model\TableItem\TableItem(
			$id,
			$options['table'],
			$this->userService->get($user),
			$units,
			$note,
			$squad,
			$armor,
			$groupLeader
		);
	}


	private function groupByPosition(array $entityData): array
	{
		$grouped = [];
		foreach ($entityData as $item) {
			$grouped[$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_POSITION]][] = $item;
		}

		return $grouped;
	}
}
