<?php declare(strict_types = 1);

namespace App\UnitModule\Forms;

class UnitSelectFormFactory
{

	public function create(callable $onSuccess, array $parameters): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$form->addSelect('tier', 'Tier', \array_merge(['all'], \App\UnitModule\Model\Unit::TIERS))
			->setDefaultValue($parameters['tier'] ?? 0)
		;
		$form->addSelect('type', 'Type', \array_merge(['all'], \App\UnitModule\Model\Unit::CATEGORIES))
			->setDefaultValue($parameters['type'] ?? 0)
		;
		$form->addSubmit('submit', 'search');
		$form->onSuccess[] = static function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess): void {
			if ($values['tier'] === 0) {
				$values['tier'] = NULL;
			}
			if ($values['type'] === 0) {
				$values['type'] = NULL;
			}

			$onSuccess($values['tier'], $values['type']);
		};

		return $form;
	}

}
