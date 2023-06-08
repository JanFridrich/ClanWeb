<?php declare(strict_types = 1);

namespace App\TableModule\Forms;

class TableUnitFormFactory
{

	public const SQUADS = [
		'Alpha',
		'Bravo',
		'Charlie',
		'Delta',
		'Echo',
		'Foxtrot',
		'Juliet',
		'Killo',
		'Lima',
		'Mike',
		'November',
		'Oscar',
		'Papa',
		'Rokie',
	];

	private \App\UserModule\Model\UserService $userService;

	private \App\TableModule\Model\TableItem\TableItemService $tableItemService;

	private \App\TableModule\Model\Table\TableService $tableService;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\TableModule\Model\TableItem\TableItemService $tableItemService,
		\App\TableModule\Model\Table\TableService $tableService
	)
	{
		$this->userService = $userService;
		$this->tableItemService = $tableItemService;
		$this->tableService = $tableService;
	}


	public function create(callable $onSuccess, \App\TableModule\Model\Table\Table $table, array $options): \Nette\Application\UI\Form
	{
		$tableId = $table->getId();
		$columns = [
			'names',
			'unit',
			'unit',
			'unit',
			'armor',
			'squad',
		];//TODO změnit na získávání s jiného formu ??

		$users = ['-'];
		$units = ['-'];
		$armors = ['-'];
		$userIds = ['-'];
		$formatted = [];

		/** @var \App\UserModule\Model\User $user */
		foreach ($this->userService->getAll($options) as $user) {
			$userIds[] = $user->getId();
			$users[] = $user->getLogin();
			$formatted[$user->getLogin()][] = '-';
			foreach ($user->getUnits() as $unit) {
				if ($unit->getLevel() > 5) {
					$formatted[$user->getLogin()][$unit->getId()] = \str_replace('_', ' ', $unit->getName());
				}
			}
			$units[] = $formatted[$user->getLogin()];
			$armors[] = $user->getArmors();
		}

		$data = [
			'names' => $users,
			'unit' => $units,
			'armor' => $armors,
			'squad' => static::SQUADS,
		];

		$form = new \Nette\Application\UI\Form();

		for ($i = 1; $i < $table->getRows() + 1; $i++) {
			foreach ($columns as $colNumber => $column) {
				$form->addSelect($i . $colNumber . $column, '', $data[$column])->setDefaultValue(0);
			}
		}

		$form->addSubmit('send', 'Create');

		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess, $data, $table, $userIds): void {

			$tableId = $table->getId();
			$this->tableItemService->clearTableItems($tableId, \App\TableModule\Model\TableItem\TableItem::ITEM_TYPES);
			$itemId = 0;
			$items = [];
			$skip = FALSE;
			foreach ($values as $index => $value) {
				if (self::isColumnName($index)) {
					if ($value === 0) {
						$skip = TRUE;
						continue;
					} else {
						$skip = FALSE;
					}
					$itemId++;
					$items[$itemId][\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_USER] = $value;
					$items[$itemId][\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TABLE] = $tableId;
					$items[$itemId]['units'] = [];
				} elseif ( ! $skip) {
					if (self::isSquadName($index)) {
						$items[$itemId][\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TYPE] = \App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_SQUAD;
						$items[$itemId][\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE] = self::SQUADS[$value];
					} elseif (self::isArmorName($index)) {
						if ($value === 0) {
							$items[$itemId]['armor'] = 'notSelected';
							continue;
						}
						$items[$itemId]['armor'] = $data['armor'][$items[$itemId][\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_USER]][$value]->getName();
					} else {
						$items[$itemId]['units'][] = $data['unit'][$items[$itemId][\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_USER]][$value];
					}
				}
			}
			foreach ($items as $itemId => $item) {
				$armor = $item['armor'];
				unset($item['armor']);
				$units = $item['units'];
				unset($item['units']);
				$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_POSITION] = $itemId;
				$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_USER] = $userIds[$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_USER]];
				$this->tableItemService->createNew($item);
				foreach ($units as $unit) {
					if ($unit === '-') {
						continue;
					}
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TYPE] = \App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_UNIT;
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE] = $unit;
					$this->tableItemService->createNew($item);
				}
				$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TYPE] = \App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_ARMOR;
				$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE] = $armor;
				$this->tableItemService->createNew($item);
			}
			$table->setStatus(\App\TableModule\Model\Table\Table::STATUS_UNITS_ASSIGNED);
			$this->tableService->saveFormData((array) $table, $table);
			$onSuccess($tableId);
		};

		return $form;
	}


	public static function isColumnName($name): bool
	{
		return FALSE !== \strpos((string) $name, "names");
	}


	public static function isSquadName($name): bool
	{
		return FALSE !== strpos((string) $name, "squad");
	}


	public static function isArmorName($name): bool
	{
		return FALSE !== strpos((string) $name, "armor");
	}

}
