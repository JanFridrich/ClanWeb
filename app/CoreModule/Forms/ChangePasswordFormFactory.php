<?php declare(strict_types = 1);

namespace App\CoreModule\Forms;

final class ChangePasswordFormFactory
{

	private const PASSWORD_MIN_LENGTH = 7;

	private \App\CoreModule\Model\UserManager $userManager;

	private \Nette\Security\User $user;

	private \Kdyby\Translation\Translator $translator;


	public function __construct(
		\Nette\Security\User $user,
		\App\CoreModule\Model\UserManager $userManager,
		\Kdyby\Translation\Translator $translator
	)
	{
		$this->userManager = $userManager;
		$this->user = $user;
		$this->translator = $translator;
	}


	public function create(callable $onSuccess, string $locale): \Nette\Application\UI\Form
	{
		$this->translator->setLocale($locale);

		$form = new \Nette\Application\UI\Form;

		$form->addPassword('oldPassword', $this->translator->translate("messages.forms.old_password"))
			->setOption('description', \sprintf('at least %d characters', self::PASSWORD_MIN_LENGTH))
			->setRequired($this->translator->translate("messages.forms.please_enter_password"))
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH)
		;

		$form->addPassword('password', $this->translator->translate("messages.forms.new_password"))
			->setOption('description', \sprintf('at least %d characters', self::PASSWORD_MIN_LENGTH))
			->setRequired($this->translator->translate("messages.forms.please_enter_password"))
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH)
		;

		$form->addPassword('repeatPassword', $this->translator->translate("messages.forms.repeat_password"))
			->setOption('description', \sprintf('at least %d characters', self::PASSWORD_MIN_LENGTH))
			->setRequired($this->translator->translate("messages.forms.please_enter_password"))
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH)
		;

		$form->addSubmit('send', $this->translator->translate("messages.forms.update_password"));

		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				if ($values->password !== $values->repeatPassword) {
					$form->addError($this->translator->translate("messages.forms.password_must_be_same"));

					return;
				}

				$rememberUserLogin = $this->user->getIdentity()->getData()[\App\CoreModule\Model\UserManager::COLUMN_LOGIN];
				if ($this->userManager->changePassword($this->user->getIdentity()->getData()[\App\CoreModule\Model\UserManager::COLUMN_EMAIL], $values->password, $values->oldPassword) === NULL) {
					$form->addError($this->translator->translate("Error when changing password, please contact developers"));

					return;
				}
				$this->user->logout(TRUE);
				$this->user->login($rememberUserLogin, $values->password);
			} catch (\Nette\Security\AuthenticationException $e) {
				$form->addError($this->translator->translate("messages.forms.credentials_incorrect"));

				return;
			} catch (\App\CoreModule\Model\ChangePasswordException $e) {

				$form->addError($this->translator->translate("messages.forms.current_password_wrong"));

				return;
			}
			$onSuccess();
		};

		return $form;
	}

}
