<?php declare(strict_types = 1);

namespace App\UserModule\Model;

class UserService
{

	private \Dibi\Connection $database;

	private \Nette\Security\Passwords $passwords;


	public function __construct(
		\Dibi\Connection $database,
		\Nette\Security\Passwords $passwords
	)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}


	public function get(int $id): ?\App\UserModule\Model\User
	{
		$userData = $this->database->select('*')
			->from(\App\UserModule\Model\UserMapping::TABLE_NAME)
			->where(\App\UserModule\Model\UserMapping::COLUMN_ID . ' = %i', $id)
			->fetch()
		;

		if ($userData === NULL) {
			return NULL;
		}

		return $this->constructUser($userData);
	}


	/**
	 * @return \App\UserModule\Model\User[]
	 */
	public function getAll(?int $limit = NULL, ?int $offset = NULL): array
	{
		$usersData = $this->database->select('*')
			->from(\App\UserModule\Model\UserMapping::TABLE_NAME)
			->fetchAll($offset, $limit)
		;

		$users = [];
		foreach ($usersData as $userData) {
			$user = $this->constructUser($userData);
			if ($user) {
				$users[$user->getId()] = $user;
			}
		}

		return $users;
	}


	/**
	 * @throws \App\CoreModule\Model\DuplicateNameException
	 * @throws \Dibi\Exception
	 */
	public function saveFormData(\App\UserModule\Model\User $user, array $values): void
	{
		unset($values[UserMapping::COLUMN_CREATED], $values[UserMapping::COLUMN_EMAIL]);
		try {
			$this->database->update(\App\UserModule\Model\UserMapping::TABLE_NAME, $values)
				->where(\App\UserModule\Model\UserMapping::COLUMN_ID . ' = %i', $user->getId())
				->execute()
			;
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
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			\Tracy\Debugger::barDump($e);
			throw new \App\CoreModule\Model\DuplicateNameException;
		}
	}


	private function constructUser(\Dibi\Row $userData): ?\App\UserModule\Model\User
	{
		try {
			$user = new \App\UserModule\Model\User(
				$userData[\App\UserModule\Model\UserMapping::COLUMN_ID],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_EMAIL],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_LOGIN],
				(bool) $userData[\App\UserModule\Model\UserMapping::COLUMN_IS_ACTIVE],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_CREATED],
				$userData[\App\UserModule\Model\UserMapping::COLUMN_ROLE],
			);
		} catch (\Exception $exception) {
			\Tracy\Debugger::barDump($exception);

			return NULL;
		}

		return $user;
	}

}
