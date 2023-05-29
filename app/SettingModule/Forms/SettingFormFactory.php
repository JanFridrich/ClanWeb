<?php declare(strict_types = 1);

namespace App\SettingModule\Forms;

class SettingFormFactory
{

	private \App\SettingModule\Model\SettingService $settingService;


	public function __construct(
		\App\SettingModule\Model\SettingService $settingService
	)
	{
		$this->settingService = $settingService;
	}


	public function create(callable $onSuccess, \App\UserModule\Model\User $user): \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();
		$form->addTextArea(\App\SettingModule\Model\Setting::KEY_HOMEPAGE_CONTENT, 'Homepage Text' )
		->setDefaultValue($this->settingService->getByKey(\App\SettingModule\Model\Setting::KEY_HOMEPAGE_CONTENT)->getValue());
		$form->addSubmit('submit', 'save');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, array $values) use ($onSuccess, $user): void {
			$this->settingService->saveFormData(
				[
					\App\SettingModule\Model\SettingMapping::COLUMN_VALUE => $values[\App\SettingModule\Model\Setting::KEY_HOMEPAGE_CONTENT],
					\App\SettingModule\Model\SettingMapping::COLUMN_KEY => \App\SettingModule\Model\Setting::KEY_HOMEPAGE_CONTENT,
					\App\SettingModule\Model\SettingMapping::COLUMN_EDITED_BY => $user->getId(),
					\App\SettingModule\Model\SettingMapping::COLUMN_EDITED => new \Dibi\DateTime(),
				]
			);
			$onSuccess();
		};

		return $form;
	}
}
