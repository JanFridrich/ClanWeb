<?php declare(strict_types = 1);

namespace App\ArmorModule\Model\UserArmorType;

class UserArmorTypeService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::class;

	private \Nette\DI\Container $container;

	private \App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\Dibi\Connection $connection,
		\Nette\DI\Container $container
	)
	{
		parent::__construct($connection);
		$this->container = $container;
	}


	public function injectServices(
		\App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService,
		\App\UserModule\Model\UserService $userService
	): void
	{
		$this->armorTypeService = $armorTypeService;
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

		foreach ($values as $armorTypeId => $value) {
			$value[$this->mappingClass::COLUMN_USER] = $entity->getId();
			$value[$this->mappingClass::COLUMN_ARMOR_TYPE] = $armorTypeId;

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
			/** @var \App\ArmorModule\Model\ArmorType\ArmorType $armorType */
			$armorType = $this->armorTypeService->get($entityData[$this->mappingClass::COLUMN_ARMOR_TYPE]);
			/** @var \App\UserModule\Model\User $user */
			$user = $this->userService->get($entityData[$this->mappingClass::COLUMN_USER]);
			if ( ! $armorType || ! $user) {
				return NULL;
			}
			$entity = new \App\ArmorModule\Model\UserArmorType\UserArmorType(
				$entityData[$this->mappingClass::COLUMN_ID],
				$armorType,
				$user,
				$entityData[$this->mappingClass::COLUMN_LEADERSHIP]
			);
		} catch (\Exception $e) {
			return NULL;
		}

		return $entity;
	}

}
