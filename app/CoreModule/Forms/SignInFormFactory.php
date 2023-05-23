<?php declare(strict_types = 1);

namespace App\CoreModule\Forms;

final class SignInFormFactory
{

	/**
	 * @var \Nette\Security\User
	 */
	private $user;

	private \Kdyby\Translation\Translator $translator;


	public function __construct(
		\Nette\Security\User $user,
		\Kdyby\Translation\Translator $translator
	)
	{
		$this->user = $user;
		$this->translator = $translator;
	}


	public function create(callable $onSuccess, string $locale): \Nette\Application\UI\Form
	{
		$this->translator->setLocale($locale);
		$form = new \Nette\Application\UI\Form;
		$form->addText('username', $this->translator->translate("messages.forms.username_or_email"))
			->setRequired($this->translator->translate("messages.forms.please_enter_username"))
		;

		$form->addPassword('password', $this->translator->translate("messages.forms.password"))
			->setRequired($this->translator->translate("messages.forms.please_enter_password"))
		;

		$form->addSubmit('send', $this->translator->translate("messages.forms.sign_in"));

		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				$this->user->setExpiration('14 days');
				$this->user->login($values->username, $values->password);
			} catch (\Nette\Security\AuthenticationException $e) {
				$form->addError($this->translator->translate($e->getMessage()));

				return;
			}
			$onSuccess();
		};

		return $form;
	}

}
