<?php declare(strict_types = 1);

namespace App\SettingModule\Model;

class Setting extends \App\CoreModule\Model\Entity
{

	public const KEY_HOMEPAGE_CONTENT = 'homepageText';
	protected string $key;

	protected string $value;

	protected \Dibi\DateTime $edited;

	protected \App\UserModule\Model\User $editedBy;


	public function __construct(
		int $id,
		string $key,
		string $value,
		\Dibi\DateTime $edited,
		\App\UserModule\Model\User $editedBy
	)
	{
		$this->id = $id;
		$this->key = $key;
		$this->value = $value;
		$this->edited = $edited;
		$this->editedBy = $editedBy;
	}


	public function getKey(): string
	{
		return $this->key;
	}


	public function setKey(string $key): void
	{
		$this->key = $key;
	}


	public function getValue(): string
	{
		return $this->value;
	}


	public function setValue(string $value): void
	{
		$this->value = $value;
	}


	public function getEdited(): \Dibi\DateTime
	{
		return $this->edited;
	}


	public function setEdited(\Dibi\DateTime $edited): void
	{
		$this->edited = $edited;
	}


	public function getEditedBy(): \App\UserModule\Model\User
	{
		return $this->editedBy;
	}


	public function setEditedBy(\App\UserModule\Model\User $editedBy): void
	{
		$this->editedBy = $editedBy;
	}

}
