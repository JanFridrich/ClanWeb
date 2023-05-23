<?php declare(strict_types = 1);

namespace App\CoreModule\Model;

final class UserManager implements \Nette\Security\Authenticator
{
	use \Nette\SmartObject;

	public const
		TABLE_NAME = 'user',
		COLUMN_ID = 'id',
		COLUMN_LOGIN = 'login',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_IS_ACTIVE = 'isActive',
		COLUMN_EMAIL = 'email',
		COLUMN_CREATED = 'created',
		COLUMN_ROLE = 'role'
	;

	/** @var \Nette\Database\Explorer */
	private $database;

	/** @var \Nette\Security\Passwords */
	private $passwords;


	public function __construct(\Nette\Database\Explorer $database, \Nette\Security\Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}


	/**
	 * Performs an authentication.
	 *
	 * @throws \Nette\Security\AuthenticationException
	 * @throws \App\CoreModule\Model\UserNotActiveException
	 */
	public function authenticate(string $user, string $password): \Nette\Security\IIdentity
	{
		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_LOGIN, $user)
			->fetch()
		;
		if ($row === NULL) {
			$row = $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_EMAIL, $user)
				->fetch()
			;
		}
		if ( ! $row) {
			throw new \Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if ( ! $row[self::COLUMN_PASSWORD_HASH] || ! $this->passwords->verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new \Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}
		if ( ! $row[self::COLUMN_IS_ACTIVE]) {
			throw new \Nette\Security\AuthenticationException('Account is not active.', self::NOT_APPROVED);
		}

		if ($this->passwords->needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update([
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
			]);
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);

		return new \Nette\Security\SimpleIdentity($row[self::COLUMN_ID], NULL, $arr);
	}


	/**
	 * @throws \App\CoreModule\Model\DuplicateNameException
	 * @throws \Nette\Utils\AssertionException
	 * @throws \App\CoreModule\Model\DuplicateEmailException
	 *
	 * @return \Nette\Database\Table\ActiveRow|int|bool
	 */
	public function add(?string $username, string $email, ?string $school, ?string $password, bool $isMailing = FALSE)
	{
		\Nette\Utils\Validators::assert($email, 'email');
		try {
			return $this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_LOGIN => $username,
				self::COLUMN_PASSWORD_HASH => $password ? $this->passwords->hash($password) : NULL,
				self::COLUMN_EMAIL => $email,
				self::COLUMN_CREATED => new \Nette\Utils\DateTime(),
				self::COLUMN_IS_ACTIVE => TRUE,
				self::COLUMN_IS_MAILING => $isMailing,
				self::COLUMN_SCHOOL => $school
			]);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			$user = $this->getUserByEmail($email);
			if ($user) {
				throw new \App\CoreModule\Model\DuplicateEmailException;
			}
			throw new \App\CoreModule\Model\DuplicateNameException;
		}
	}


	/**
	 * @throws \App\CoreModule\Model\ChangePasswordException
	 */
	public function changePassword($email, $newPassword, $oldPassword): ?bool
	{
		$user = $this->getUserByEmail($email);
		if ( ! $user) {
			return NULL;
		}

		if ( ! $this->passwords->verify($oldPassword, $user->password)) {
			throw new \App\CoreModule\Model\ChangePasswordException;
		}

		return $user->update([
			self::COLUMN_PASSWORD_HASH => $this->passwords->hash($newPassword),
		]);
	}


	public function changePasswordAfterVerified($email, $newPassword): ?bool
	{
		$user = $this->getUserByEmail($email);
		if ( ! $user) {
			return NULL;
		}

		return $user->update([
			self::COLUMN_PASSWORD_HASH => $this->passwords->hash($newPassword),
		]);
	}


	public function getUserByEmail(string $email): ?\Nette\Database\Table\ActiveRow
	{
		return $this->database->table(self::TABLE_NAME)->where([self::COLUMN_EMAIL => $email])->fetch();
	}


	/**
	 * @throws \App\CoreModule\Model\DuplicateNameException
	 */
	public function editByEmail(string $email, string $username, bool $isMailing, string $school): ?bool
	{
		try {
			return $this->database->table(self::TABLE_NAME)->where([self::COLUMN_EMAIL => $email])->fetch()->update([
				self::COLUMN_LOGIN => $username,
				self::COLUMN_IS_MAILING => $isMailing,
				self::COLUMN_SCHOOL => $school,
			]);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			throw new \App\CoreModule\Model\DuplicateNameException;
		}
	}


	/**
	 * @throws \App\CoreModule\Model\DuplicateEmailException
	 * @throws \Nette\Utils\AssertionException
	 * @throws \Exception
	 */
	public function addOnlyNewsletter(string $email)
	{
		\Nette\Utils\Validators::assert($email, 'email');
		$username = 'user' . \time();
		try {
			return $this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_LOGIN => $username,
				self::COLUMN_EMAIL => $email,
				self::COLUMN_CREATED => new \Nette\Utils\DateTime(),
				self::COLUMN_IS_ACTIVE => TRUE,
				self::COLUMN_IS_MAILING => TRUE,
			]);
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			if ($this->getUserByEmail($email)) {
				throw new \App\CoreModule\Model\DuplicateEmailException;
			}

			return $this->addOnlyNewsletter($email);
		}
	}

}
