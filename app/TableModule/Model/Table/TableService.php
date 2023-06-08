<?php declare(strict_types = 1);

namespace App\TableModule\Model\Table;

class TableService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\TableModule\Model\Table\TableMapping::class;

	private \App\UserModule\Model\UserService $userService;

	private \App\TableModule\Model\TableItem\TableItemService $tableItemService;


	public function __construct(
		\Dibi\Connection $connection,
		\App\UserModule\Model\UserService $userService,
		\App\TableModule\Model\TableItem\TableItemService $tableItemService
	)
	{
		parent::__construct($connection);
		$this->userService = $userService;
		$this->tableItemService = $tableItemService;
	}


	/**
	 * @return \App\TableModule\Model\Table\Table|null
	 */
	protected function constructEntity(?\Dibi\Row $entityData, array $options = []): ?\App\CoreModule\Model\Entity
	{
		if ($entityData === NULL) {
			return NULL;
		}
		/** @var \App\UserModule\Model\User|null $user */
		$user = $this->userService->get($entityData[\App\TableModule\Model\Table\TableMapping::COLUMN_CREATED_BY]);
		if ($user === NULL) {
			return NULL;
		}
		try {
			$table = new \App\TableModule\Model\Table\Table(
				$entityData[\App\TableModule\Model\Table\TableMapping::COLUMN_ID],
				$entityData[\App\TableModule\Model\Table\TableMapping::COLUMN_NAME],
				$entityData[\App\TableModule\Model\Table\TableMapping::COLUMN_STATUS],
				$entityData[\App\TableModule\Model\Table\TableMapping::COLUMN_ROWS],
				$entityData[\App\TableModule\Model\Table\TableMapping::COLUMN_CREATED],
				$user,
				(bool) $entityData[\App\TableModule\Model\Table\TableMapping::COLUMN_IS_ACTIVE],
				[],
			);
			$table->setTableItems(
				$this->tableItemService->getAll([
					'where' => [\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TABLE => $table->getId(),],
					'table' => $table,
				])
			);
		} catch (\Exception $e) {
			\Tracy\Debugger::barDump($e);

			return NULL;
		}

		return $table;
	}

}
