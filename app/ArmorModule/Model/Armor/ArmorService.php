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
				$entityData[\App\ArmorModule\Model\Armor\ArmorMapping::COLUMN_SORT]

			);
		} catch (\Exception $exception) {
			return NULL;
		}

		return $entity;
	}

}
