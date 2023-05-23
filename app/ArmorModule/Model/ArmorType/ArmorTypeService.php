<?php declare(strict_types = 1);

namespace App\ArmorModule\Model\ArmorType;

class ArmorTypeService extends \App\CoreModule\Model\Service
{

	protected string $mappingClass = \App\ArmorModule\Model\ArmorType\ArmorTypeMapping::class;


	public function getArmorTypePairs(): array
	{
		$armorTypes = $this->getAll();
		$pairs = [];
		foreach ($armorTypes as $armorType) {
			$pairs[$armorType->getId()] = $armorType->getName();
		}

		return $pairs;
	}


	protected function constructEntity(?\Dibi\Row $entityData): ?\App\CoreModule\Model\Entity
	{
		if ( ! $entityData) {
			return NULL;
		}
		try {
			$entity = new ArmorType(
				$entityData[ArmorTypeMapping::COLUMN_ID],
				$entityData[ArmorTypeMapping::COLUMN_NAME]
			);
		} catch (\Exception $exception) {
			return NULL;
		}

		return $entity;
	}

}
