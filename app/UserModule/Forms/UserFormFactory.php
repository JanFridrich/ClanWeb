<?php declare(strict_types = 1);

namespace App\UserModule\Forms;

class UserFormFactory
{

	private \Kdyby\Translation\Translator $translator;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\Kdyby\Translation\Translator $translator,
		\App\UserModule\Model\UserService $userService
	)
	{
		$this->translator = $translator;
		$this->userService = $userService;
	}


	public function create(callable $onSuccess, \App\UserModule\Model\User $user, string $locale = \App\PageModule\Model\PageService::DEFAULT_LANGUAGE): \Nette\Application\UI\Form
	{
		$this->translator->setLocale($locale);

		$form = new \Nette\Application\UI\Form;
		$form->addText(\App\UserModule\Model\UserMapping::COLUMN_LOGIN, $this->translator->translate("messages.forms.username"))
			->setRequired($this->translator->translate("messages.forms.please_enter_username"))
			->setDefaultValue($user->getLogin())
		;

		$form->addEmail(\App\UserModule\Model\UserMapping::COLUMN_EMAIL, $this->translator->translate("messages.forms.email"))
			->setDisabled()
			->setDefaultValue($user->getEmail())
		;
		$form->addText(\App\UserModule\Model\UserMapping::COLUMN_CREATED, $this->translator->translate("messages.forms.created"))
			->setDisabled()
			->setDefaultValue($user->getCreated())
		;
		$form->addCheckbox(\App\UserModule\Model\UserMapping::COLUMN_IS_ACTIVE, $this->translator->translate("messages.forms.is_active"))
			->setDefaultValue($user->isActive())
		;
		$form->addSubmit('send', $this->translator->translate("messages.forms.change"));
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess, $user): void {
			try {
				$this->userService->saveFormData($user, (array) $values);
			} catch (\App\CoreModule\Model\DuplicateNameException $e) {
				$form->addError($this->translator->translate("messages.forms.username_already_registered"));

				return;
			}
			$onSuccess();
		};

		return $form;
	}

}
