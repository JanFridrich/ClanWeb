<?php declare(strict_types = 1);

namespace App\UserModule\Model;

class UserService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\UserModule\Model\UserMapping::class;

	private \Dibi\Connection $database;

	private \Nette\Security\Passwords $passwords;

	private \App\UnitModule\Model\UnitService $unitService;


	public function __construct(
		\Dibi\Connection $database,
		\Nette\Security\Passwords $passwords,
		\App\UnitModule\Model\UnitService $unitService
	)
	{
		parent::__construct($database);
		$this->database = $database;
		$this->passwords = $passwords;
		$this->unitService = $unitService;
	}


	/**
	 * @throws \App\CoreModule\Model\DuplicateNameException
	 * @throws \Dibi\Exception
	 *
	 * @param \App\UserModule\Model\User $user
	 */
	public function saveFormData(array $values, \App\CoreModule\Model\Entity $user)
	{
		unset($values[\App\UserModule\Model\UserMapping::COLUMN_CREATED], $values[\App\UserModule\Model\UserMapping::COLUMN_EMAIL], $values['units']);

		try {
			return parent::saveFormData($values, $user);
		} catch (\Dibi\UniqueConstraintViolationException $e) {
			throw new \App\CoreModule\Model\DuplicateNameException;
		}
	}


	public function prepareDataForExport(): array
	{
		$users = [['email', 'login', 'created',]];
		$select = $this->database->select('*')
			->from(\App\UserModule\Model\UserMapping::TABLE_NAME)
		;
		foreach ($select->fetchAll() as $user) {
			$users[] = [
				$user->email,
				$user->login,
				(string) $user->created,
			];
		}

		return $users;
	}


	public function create(array $values): void
	{
		$values[\App\UserModule\Model\UserMapping::COLUMN_IS_ACTIVE] = TRUE;
		$values[\App\UserModule\Model\UserMapping::COLUMN_PASSWORD] = $this->passwords->hash($values[\App\UserModule\Model\UserMapping::COLUMN_PASSWORD]);
		unset($values['repeatPassword']);
		$values[\App\UserModule\Model\UserMapping::COLUMN_CREATED] = new \Nette\Utils\DateTime();
		try {
			$this->database->insert(\App\UserModule\Model\UserMapping::TABLE_NAME, $values)
				->execute()
			;
		} catch (\Dibi\UniqueConstraintViolationException $e) {
			\Tracy\Debugger::barDump($e);
			throw new \App\CoreModule\Model\DuplicateNameException;
		}
	}


	public function prepareDataForGrid(): array
	{
		$users = $this->getAll();
		$gridData = [];
		$units = $this->unitService->getAll();

		/** @var \App\UserModule\Model\User $user */
		foreach ($users as $user) {
			$userId = $user->getId();
			$gridData[$userId]['user'] = $user->getLogin();
			$gridData[$userId]['id'] = $userId;
			$userUnits = $user->getUnits();
			/** @var \App\UnitModule\Model\Unit $unit */
			foreach ($units as $unitId => $unit) {
				$level = 'X';
				if (isset($userUnits[$unitId])) {
					$level = $userUnits[$unitId]->getLevel();
					if ($level === $unit->getMaxLevel()) {
						$level = 'max';
					}
				}
				$gridData[$userId]['unit' . $unitId] = $level;
			}
		}

		return $gridData;
	}


	protected function constructEntity(?\Dibi\Row $userData): ?\App\UserModule\Model\User
	{
		if ( ! $userData) {
			return NULL;
		}
		try {
			$user = new \App\UserModule\Model\User(
				$userData[\App\UserModule\Model\UserMapping::COLUMN_ID],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_EMAIL],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_LOGIN],
				(bool) $userData[\App\UserModule\Model\UserMapping::COLUMN_IS_ACTIVE],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_CREATED],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_ROLE],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_SKILL_LEVEL],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_NOTE],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_LAST_UPDATED_UNITS],
				$this->unitService->getUnitsForUser($userData[\App\UserModule\Model\UserMapping::COLUMN_ID]),

			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}

		return $user;
	}

}
