<?php declare(strict_types = 1);

namespace App\UserModule\Forms;

final class ProfileFormFactory
{

	private \Nette\Security\User $user;

	private \Kdyby\Translation\Translator $translator;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\Nette\Security\User $user,
		\Kdyby\Translation\Translator $translator,
		\App\UserModule\Model\UserService $userService
	)
	{
		$this->user = $user;
		$this->translator = $translator;
		$this->userService = $userService;
	}


	public function create(callable $onSuccess, string $locale): \Nette\Application\UI\Form
	{
		$this->translator->setLocale($locale);
		$user = $this->userService->get($this->user->getIdentity()->getData()[\App\CoreModule\Model\UserManager::COLUMN_ID]);
		$form = new \Nette\Application\UI\Form;
		$form->addText(\App\UserModule\Model\UserMapping::COLUMN_LOGIN, $this->translator->translate("messages.forms.username"))
			->setRequired($this->translator->translate("messages.forms.please_enter_username"))
			->setDefaultValue($user->getLogin())
		;

		$form->addSubmit('send', $this->translator->translate("messages.forms.change"));

		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess, $user): void {
			try {
				$this->userService->saveFormData((array) $values, $user);
				/** @var \Nette\Security\SimpleIdentity $newIdentity */
				$newIdentity = $this->user->getIdentity();
				$newIdentity->__set(\App\CoreModule\Model\UserManager::COLUMN_LOGIN, $values->login);
				$this->user->login($newIdentity);
			} catch (\App\CoreModule\Model\DuplicateNameException $e) {
				$form->addError($this->translator->translate("messages.forms.username_already_registered"));

				return;
			}
			$onSuccess();
		};

		return $form;
	}

}
