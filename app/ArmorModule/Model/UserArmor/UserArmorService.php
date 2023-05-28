<?php declare(strict_types = 1);

namespace App\ArmorModule\Model\UserArmor;

class UserArmorService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\ArmorModule\Model\UserArmor\UserArmorMapping::class;

	private \App\ArmorModule\Model\Armor\ArmorService $armorService;

	private \App\UserModule\Model\UserService $userService;

	private \Nette\DI\Container $container;


	public function __construct(
		\Dibi\Connection $connection,
		\Nette\DI\Container $container

	)
	{
		parent::__construct($connection);
		$this->container = $container;
	}


	public function injectServices(
		\App\ArmorModule\Model\Armor\ArmorService $armorService,
		\App\UserModule\Model\UserService $userService
	): void
	{
		$this->armorService = $armorService;
		$this->userService = $userService;
	}


	/**
	 * @param \App\UserModule\Model\User $entity
	 */
	public function saveFormData(array $values, \App\CoreModule\Model\Entity $entity)
	{
		$this->connection->delete($this->mappingClass::TABLE_NAME)
			->where($this->mappingClass::COLUMN_USER . ' = %i', $entity->getId())
			->execute()
		;

		foreach ($values as $armorId => $value) {
			$value[$this->mappingClass::COLUMN_USER] = $entity->getId();
			$value[$this->mappingClass::COLUMN_ARMOR] = $armorId;

			$this->connection->insert($this->mappingClass::TABLE_NAME, $value)
				->execute()
			;
		}

		return NULL;
	}


	protected function constructEntity(?\Dibi\Row $entityData, array $options = []): ?\App\CoreModule\Model\Entity
	{
		$this->container->callInjects($this);
		if ( ! $entityData) {
			return NULL;
		}

		try {
			/** @var \App\ArmorModule\Model\Armor\Armor $armor */
			$armor = $this->armorService->get($entityData[$this->mappingClass::COLUMN_ARMOR]);
			/** @var \App\UserModule\Model\User $user */
			$user = $this->userService->get($entityData[$this->mappingClass::COLUMN_USER]);
			if ( ! $armor || ! $user) {
				return NULL;
			}
			$entity = new \App\ArmorModule\Model\UserArmor\UserArmor(
				$entityData[$this->mappingClass::COLUMN_ID],
				$armor,
				$user,
				$entityData[$this->mappingClass::COLUMN_PREFER]
			);
		} catch (\Exception $e) {
			return NULL;
		}

		return $entity;
	}

}
