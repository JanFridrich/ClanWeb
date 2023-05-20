<?php declare(strict_types = 1);

namespace App\CoreModule\Model;

final class RestorePasswordManager
{

	public const
		TABLE_NAME = 'restorePassword',
		COLUMN_ID = 'id',
		COLUMN_USER = 'user',
		COLUMN_TOKEN = 'token',
		COLUMN_EXPIRE = 'expire';

	private \Nette\Database\Explorer $database;

	private \App\CoreModule\Model\UserManager $userManager;

	private \App\MailModule\Model\MailSender\MailSender $mailSender;


	public function __construct(
		\Nette\Database\Explorer $database,
		\App\CoreModule\Model\UserManager $userManager,
		\App\MailModule\Model\MailSender\MailSender $mailSender
	)
	{
		$this->database = $database;
		$this->userManager = $userManager;
		$this->mailSender = $mailSender;
	}


	/**
	 * @throws \App\CoreModule\Model\UserNotFoundException
	 */
	public function generateRestoreForEmail(string $email, string $locale): void
	{
		$user = $this->userManager->getUserByEmail($email);
		if ($user === NULL) {
			throw new \App\CoreModule\Model\UserNotFoundException;
		}
		$restorePassword = $this->getRestorePasswordByUserId($user->id);
		if ($restorePassword === NULL) {
			$datetime = new \Nette\Utils\DateTime();
			$datetime->modify('+1 day');
			$token = \bin2hex(\random_bytes(10));
			$this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_USER => $user->id,
				self::COLUMN_TOKEN => $token,
				self::COLUMN_EXPIRE => $datetime,
			]);
		} else {
			$token = $restorePassword->token;
			$this->updateExpireForToken($token, $user->id);
		}
		$this->mailSender->sendRestorePasswordEmail($email, $locale, $token);
	}


	public function updatePasswordForUser(string $email, string $password, string $token): void
	{
		$user = $this->userManager->getUserByEmail($email);
		if ($user === NULL) {
			throw new \App\CoreModule\Model\UserNotFoundException;
		}
		$restorePassword = $this->getRestorePasswordByUserToken($token);
		if ($restorePassword === NULL) {
			throw new \App\CoreModule\Model\WrongTokenException;
		}
		$now = new \Nette\Utils\DateTime();
		if ($now > $restorePassword->expire) {
			throw new \App\CoreModule\Model\TokenExpiredException;
		}
		$this->userManager->changePasswordAfterVerified($email, $password);
		$restorePassword->delete();
	}


	/**
	 * @param string|int $userId
	 */
	public function getRestorePasswordByUserId($userId): ?\Nette\Database\Table\ActiveRow
	{
		return $this->database->table(self::TABLE_NAME)->where([self::COLUMN_USER => $userId])->fetch();
	}


	/**
	 * @param string|int $userId
	 */
	public function getRestorePasswordByUserToken($token): ?\Nette\Database\Table\ActiveRow
	{
		return $this->database->table(self::TABLE_NAME)->where([self::COLUMN_TOKEN => $token])->fetch();
	}


	private function updateExpireForToken(string $token, int $userId): void
	{
		$datetime = new \Nette\Utils\DateTime();
		$datetime->modify('+1 day');
		$this->database->table(self::TABLE_NAME)->where([
			self::COLUMN_TOKEN => $token,
			self::COLUMN_USER => $userId,
		])->update(
			[self::COLUMN_EXPIRE => $datetime,]
		);
	}

}
