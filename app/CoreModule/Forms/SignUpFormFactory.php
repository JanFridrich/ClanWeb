<?php declare(strict_types = 1);

namespace App\CoreModule\Forms;

final class SignUpFormFactory
{

	public const PASSWORD_MIN_LENGTH = 7;

	private \App\CoreModule\Model\UserManager $userManager;

	private \Nette\Security\User $user;

	private \Kdyby\Translation\Translator $translator;

	private \App\PageModule\Model\PageService $pageService;

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\Nette\Security\User $user,
		\App\CoreModule\Model\UserManager $userManager,
		\Kdyby\Translation\Translator $translator,
		\App\PageModule\Model\PageService $pageService,
		\App\UserModule\Model\UserService $userService
	)
	{
		$this->userManager = $userManager;
		$this->user = $user;
		$this->translator = $translator;
		$this->pageService = $pageService;
		$this->userService = $userService;
	}


	public function create(callable $onSuccess, string $locale): \Nette\Application\UI\Form
	{
		$this->translator->setLocale($locale);
		$form = new \Nette\Application\UI\Form;
		$form->addText(\App\UserModule\Model\UserMapping::COLUMN_LOGIN, $this->translator->translate("messages.forms.username"))
			->setRequired($this->translator->translate("messages.forms.please_enter_username"))
		;

		$form->addEmail(\App\UserModule\Model\UserMapping::COLUMN_EMAIL, $this->translator->translate("messages.forms.email"))
			->setRequired($this->translator->translate("messages.forms.please_enter_email"))
		;

		$form->addPassword(\App\UserModule\Model\UserMapping::COLUMN_PASSWORD, $this->translator->translate("messages.forms.password"))
			->setOption('description', \sprintf('at least %d characters', self::PASSWORD_MIN_LENGTH))
			->setRequired($this->translator->translate("messages.forms.please_enter_password"))
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH)
		;

		$form->addPassword('repeatPassword', $this->translator->translate("messages.forms.repeat_password"))
			->setOption('description', \sprintf('at least %d characters', self::PASSWORD_MIN_LENGTH))
			->setRequired($this->translator->translate("messages.forms.please_enter_password"))
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH)
		;

		$form->addSubmit('send', $this->translator->translate("messages.forms.sign_up"));

		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				if ($values->password !== $values->repeatPassword) {
					$form->addError($this->translator->translate("messages.forms.password_must_be_same"));

					return;
				}
				$this->userService->create((array) $values);
				$this->user->login($values->login, $values->password);
			} catch (\App\CoreModule\Model\DuplicateNameException $e) {
				$form->addError($this->translator->translate("messages.forms.username_already_registered"));

				return;
			} catch (\Nette\Security\AuthenticationException $e) {
				$form->addError($this->translator->translate("messages.forms.credentials_incorrect"));

				return;
			} catch (\App\CoreModule\Model\DuplicateEmailException $e) {
				$form->addError($this->translator->translate("messages.forms.email_already_registered"));

				return;
			}
			$onSuccess();
		};

		return $form;
	}

}
