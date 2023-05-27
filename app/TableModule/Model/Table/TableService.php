<?php declare(strict_types = 1);

namespace App\TableModule\Model\Table;

class TableService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\TableModule\Model\Table\TableMapping::class;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\Dibi\Connection $connection,
		\App\UserModule\Model\UserService $userService
	)
	{
		parent::__construct($connection);
		$this->userService = $userService;
	}


	/**
	 * @return \App\TableModule\Model\Table\Table|null
	 */
	protected function constructEntity(?\Dibi\Row $entityData): ?\App\CoreModule\Model\Entity
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
				$user
			);
		} catch (\Exception $e) {
			return NULL;
		}

		return $table;
	}

}
