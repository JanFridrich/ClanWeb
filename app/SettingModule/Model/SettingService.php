<?php declare(strict_types = 1);

namespace App\SettingModule\Model;

class SettingService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = SettingMapping::class;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\Dibi\Connection $connection,
		\App\UserModule\Model\UserService $userService
	)
	{
		parent::__construct($connection);
		$this->userService = $userService;
	}


	public function getByKey(string $key): ?Setting
	{
		$entityData = $this->connection->select('*')
			->from($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::TABLE_NAME . '.' . SettingMapping::COLUMN_KEY . ' = %s', $key)
			->fetch()
		;

		return $this->constructEntity($entityData);
	}


	/**
	 * @param \Dibi\Row|null $entityData
	 * @param array $options
	 *
	 * @return \App\SettingModule\Model\Setting|null
	 */
	protected function constructEntity(?\Dibi\Row $entityData, array $options = []): ?\App\CoreModule\Model\Entity
	{
		if ($entityData === NULL) {
			return NULL;
		}
		try {
			$setting = new Setting(
				$entityData[SettingMapping::COLUMN_ID],
				$entityData[SettingMapping::COLUMN_KEY],
				$entityData[SettingMapping::COLUMN_VALUE],
				$entityData[SettingMapping::COLUMN_EDITED],
				$this->userService->get($entityData[SettingMapping::COLUMN_EDITED_BY])
			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}

		return $setting;
	}


	/**
	 * @throws \Dibi\Exception
	 *
	 * @return \Dibi\Result|null
	 *
	 * @param array<string, mixed> $values
	 */
	public function saveFormData(array $values, ?\App\CoreModule\Model\Entity $entity = NULL)
	{
		try {
			return $this->connection->update($this->mappingClass::TABLE_NAME, $values)
				->where($this->mappingClass::TABLE_NAME . '.' . SettingMapping::COLUMN_KEY . ' = %s', $values[SettingMapping::COLUMN_KEY])
				->execute()
			;
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);
			return $this->connection->insert($this->mappingClass::TABLE_NAME, $values)
				->execute()
			;
		}
	}

}
