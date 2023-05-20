<?php declare(strict_types = 1);

namespace App\CoreModule\Forms;

final class ForgotPasswordFormFactory
{

	private \Kdyby\Translation\Translator $translator;

	private \App\CoreModule\Model\RestorePasswordManager $restorePasswordManager;


	public function __construct(
		\Kdyby\Translation\Translator $translator,
		\App\CoreModule\Model\RestorePasswordManager $restorePasswordManager
	)
	{
		$this->translator = $translator;
		$this->restorePasswordManager = $restorePasswordManager;
	}


	public function create(callable $onSuccess, string $locale): \Nette\Application\UI\Form
	{
		$this->translator->setLocale($locale);

		$form = new \Nette\Application\UI\Form;
		$form->addText('email', $this->translator->translate("messages.forms.email"))
			->setRequired($this->translator->translate("messages.forms.please_enter_email"))
		;

		$form->addSubmit('send', $this->translator->translate("messages.forms.request_password"));

		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess, $locale): void {
			try {
				$this->restorePasswordManager->generateRestoreForEmail($values->email, $locale);
			} catch (\App\CoreModule\Model\UserNotFoundException $exception) {
				$form->addError($this->translator->translate("messages.forms.not_existing_user_by_email"));

				return;
			}
			$onSuccess($values->email);
		};

		return $form;
	}

}
