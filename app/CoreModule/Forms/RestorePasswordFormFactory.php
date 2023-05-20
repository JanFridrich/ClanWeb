<?php declare(strict_types = 1);

namespace App\CoreModule\Forms;

final class RestorePasswordFormFactory
{

	private \Kdyby\Translation\Translator $translator;

	private \App\CoreModule\Model\RestorePasswordManager $restorePasswordManager;

	private \Nette\Security\User $user;


	public function __construct(
		\Kdyby\Translation\Translator $translator,
		\Nette\Security\User $user,
		\App\CoreModule\Model\RestorePasswordManager $restorePasswordManager
	)
	{
		$this->translator = $translator;
		$this->restorePasswordManager = $restorePasswordManager;
		$this->user = $user;
	}


	public function create(callable $onSuccess, string $email, string $locale, ?string $token): \Nette\Application\UI\Form
	{
		$this->translator->setLocale($locale);
		$form = new \Nette\Application\UI\Form;
		$form->addText('token', $this->translator->translate("messages.forms.token"))
			->setRequired($this->translator->translate("messages.forms.please_enter_token"))
			->setDefaultValue($token)
		;

		$form->addEmail('email', $this->translator->translate("messages.forms.email"))
			->setRequired($this->translator->translate("messages.forms.please_enter_email"))
			->setDefaultValue($email)
		;
		$form->addPassword('password', $this->translator->translate("messages.forms.new_password"))
			->setOption('description', \sprintf('at least %d characters', \App\CoreModule\Forms\SignUpFormFactory::PASSWORD_MIN_LENGTH))
			->setRequired($this->translator->translate("messages.forms.please_enter_password"))
			->addRule($form::MIN_LENGTH, NULL, \App\CoreModule\Forms\SignUpFormFactory::PASSWORD_MIN_LENGTH)
		;

		$form->addPassword('repeatPassword', $this->translator->translate("messages.forms.repeat_password"))
			->setOption('description', \sprintf('at least %d characters', \App\CoreModule\Forms\SignUpFormFactory::PASSWORD_MIN_LENGTH))
			->setRequired($this->translator->translate("messages.forms.please_enter_password"))
			->addRule($form::MIN_LENGTH, NULL, \App\CoreModule\Forms\SignUpFormFactory::PASSWORD_MIN_LENGTH)
		;

		$form->addSubmit('send', $this->translator->translate("messages.forms.reset_password"));

		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				if ($values->password !== $values->repeatPassword) {
					$form->addError($this->translator->translate("messages.forms.password_must_be_same"));

					return;
				}
				$this->restorePasswordManager->updatePasswordForUser(\trim($values->email), $values->password, \trim($values->token));
				$this->user->login($values->email, $values->password);
			} catch (\App\CoreModule\Model\UserNotFoundException $exception) {
				$form->addError($this->translator->translate("messages.forms.email_doesnt_exist"));

				return;
			} catch (\App\CoreModule\Model\WrongTokenException $exception) {
				$form->addError($this->translator->translate("messages.forms.wrong_token"));

				return;
			} catch (\App\CoreModule\Model\TokenExpiredException $exception) {
				$form->addError($this->translator->translate("messages.forms.token_expired"));

				return;
			} catch (\Nette\Security\AuthenticationException $e) {
				$form->addError($this->translator->translate("messages.forms.credentials_incorrect"));

				return;
			}
			$onSuccess();
		};

		return $form;
	}

}
