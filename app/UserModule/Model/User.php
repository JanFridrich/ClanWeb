<?php declare(strict_types = 1);

namespace App\UserModule\Model;

class User
{

	public const ROLE_MEMBER = 'member';
	public const ROLE_ADMIN = 'admin';
	public const ROLE_WARLORD = 'warlord';

	private int $id;

	private string $email;

	private string $login;

	private bool $isActive;

	private \Dibi\DateTime $created;

	private string $role;


	public function __construct(
		int $id,
		string $email,
		string $login,
		bool $isActive,
		\Dibi\DateTime $created,
		string $role
	)
	{
		$this->id = $id;
		$this->email = $email;
		$this->login = $login;
		$this->isActive = $isActive;
		$this->created = $created;
		$this->role = $role;
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function setId(int $id): void
	{
		$this->id = $id;
	}


	public function getEmail(): string
	{
		return $this->email;
	}


	public function setEmail(string $email): void
	{
		$this->email = $email;
	}


	public function getLogin(): string
	{
		return $this->login;
	}


	public function setLogin(string $login): void
	{
		$this->login = $login;
	}


	public function isActive(): bool
	{
		return $this->isActive;
	}


	public function setIsActive(bool $isActive): void
	{
		$this->isActive = $isActive;
	}


	public function getCreated(): \Dibi\DateTime
	{
		return $this->created;
	}


	public function setCreated(\Dibi\DateTime $created): void
	{
		$this->created = $created;
	}


	public function getRole(): string
	{
		return $this->role;
	}


	public function setRole(string $role): void
	{
		$this->role = $role;
	}

}
