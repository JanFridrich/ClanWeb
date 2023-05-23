<?php declare(strict_types = 1);

namespace App\ArmorModule\Model\Armor;

class ArmorService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\ArmorModule\Model\Armor\ArmorMapping::class;

	private \App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService;


	public function __construct(
		\Dibi\Connection $connection,
		\App\ArmorModule\Model\ArmorType\ArmorTypeService $armorTypeService
	)
	{
		parent::__construct($connection);
		$this->armorTypeService = $armorTypeService;
	}


	public function createNew(array $values): int
	{
		$values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT] = $values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT] ?? 0;
		$image = $values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE];
		if ($image->isOk()) {
			$file = \fopen('data/images/armors/' . $image->getSanitizedName(), 'wb+');
			\fwrite($file, $image->getContents());
			\fclose($file);
			$values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE] = $image->getSanitizedName();
		} else {
			unset($values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE]);
		}

		return parent::createNew($values);
	}


	/**
	 * @throws \Dibi\Exception
	 *
	 * @param \App\ArmorModule\Model\Armor\Armor $entity
	 *
	 * @param array<string, mixed> $values
	 */
	public function saveFormData(array $values, \App\CoreModule\Model\Entity $entity)
	{
		$values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT] = $values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT] ?? 0;
		$image = $values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE];
		if ($image->isOk()) {
			if ($entity->getImage() && \file_exists('data/images/armors/' . $entity->getImage())) {
				\unlink('data/images/armors/' . $entity->getImage());
			}

			$file = \fopen('data/images/armors/' . $image->getSanitizedName(), 'wb+');
			\fwrite($file, $image->getContents());
			\fclose($file);
			$values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE] = $image->getSanitizedName();
		} else {
			unset($values[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE]);
		}

		return parent::saveFormData($values, $entity);
	}


	public function getAllByUser(int $userId): array
	{
		$armors = [];
		$entitiesData = $this->connection->select($this->mappingClass::TABLE_NAME . '.*, ' . \App\ArmorModule\Model\UserArmor\UserArmorMapping::COLUMN_PREFER . ', ' . \App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::COLUMN_LEADERSHIP)
			->from($this->mappingClass::TABLE_NAME)
			->leftJoin(\App\ArmorModule\Model\UserArmor\UserArmorMapping::TABLE_NAME)->on(\App\ArmorModule\Model\UserArmor\UserArmorMapping::TABLE_NAME . '.' . \App\ArmorModule\Model\UserArmor\UserArmorMapping::COLUMN_ARMOR . ' = ' . $this->mappingClass::TABLE_NAME . '.' . $this->mappingClass::COLUMN_ID . ' AND ' . \App\ArmorModule\Model\UserArmor\UserArmorMapping::TABLE_NAME . '.' . \App\ArmorModule\Model\UserArmor\UserArmorMapping::COLUMN_USER . ' = %i', $userId)
			->leftJoin(\App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::TABLE_NAME)->on(\App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::TABLE_NAME . '.' . \App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::COLUMN_ARMOR_TYPE . ' = ' . $this->mappingClass::TABLE_NAME . '.' . $this->mappingClass::COLUMN_ARMOR_TYPE . ' AND ' . \App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::TABLE_NAME . '.' . \App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::COLUMN_USER . ' = %i', $userId)
			->where(\App\ArmorModule\Model\UserArmor\UserArmorMapping::TABLE_NAME . '.' . \App\ArmorModule\Model\UserArmor\UserArmorMapping::COLUMN_USER . ' = %i', $userId)
			->fetchAll()
		;
		\Tracy\Debugger::barDump($entitiesData);
		foreach ($entitiesData as $entityData) {
			$armor = $this->constructEntity($entityData);
			if ( ! $armor) {
				continue;
			}
			$armors[$armor->getId()] = $armor;
		}

		return $armors;
	}


	/**
	 * @return \App\ArmorModule\Model\Armor\Armor|null
	 */
	protected function constructEntity(?\Dibi\Row $entityData): ?\App\CoreModule\Model\Entity
	{
		if ( ! $entityData) {
			return NULL;
		}
		/** @var \App\ArmorModule\Model\ArmorType\ArmorType|null $armorType */
		$armorType = $this->armorTypeService->get($entityData[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_ARMOR_TYPE]);
		if ( ! $armorType) {
			return NULL;
		}
		try {
			$entity = new \App\ArmorModule\Model\Armor\Armor(
				$entityData[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_ID],
				$armorType,
				$entityData[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_NAME],
				$entityData[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_IMAGE],
				$entityData[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT],
				$entityData[\App\ArmorModule\Model\UserArmor\UserArmorMapping::COLUMN_PREFER] ?? NULL,
				$entityData[\App\ArmorModule\Model\UserArmorType\UserArmorTypeMapping::COLUMN_LEADERSHIP] ?? NULL,

			);
		} catch (\Exception $exception) {
			return NULL;
		}

		return $entity;
	}

}
