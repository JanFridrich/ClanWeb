<?php declare(strict_types = 1);

namespace App\MailModule\Model\MailSender;

class MailSender
{

	private \App\MailModule\Model\MailerProvider\SMTPMailerProvider $SMTPMailerProvider;

	private \Nette\Application\LinkGenerator $linkGenerator;

	private \Nette\Bridges\ApplicationLatte\TemplateFactory $templateFactory;

	private \Kdyby\Translation\Translator $translator;

	private \App\PageModule\Model\PageService $pageService;

	private string $domain;


	public function __construct(
		string $domain,
		\App\MailModule\Model\MailerProvider\SMTPMailerProvider $SMTPMailerProvider,
		\Nette\Application\LinkGenerator $linkGenerator,
		\Nette\Bridges\ApplicationLatte\TemplateFactory $templateFactory,
		\Kdyby\Translation\Translator $translator,
		\App\PageModule\Model\PageService $pageService
	)
	{
		$this->SMTPMailerProvider = $SMTPMailerProvider;
		$this->linkGenerator = $linkGenerator;
		$this->templateFactory = $templateFactory;
		$this->translator = $translator;
		$this->pageService = $pageService;
		$this->domain = $domain;
	}


	private function createTemplate(): \Nette\Application\UI\Template
	{
		$template = $this->templateFactory->createTemplate();
		$template->getLatte()->addProvider('uiControl', $this->linkGenerator);

		return $template;
	}


	private function sendWithSMTP(string $body, string $from, string $to, string $subject): void
	{
		$mailer = $this->SMTPMailerProvider->provide();

		$mail = new \Nette\Mail\Message;
		$mail->setHtmlBody($body)
			->setFrom($from)
			->addTo($to)
			->setSubject($subject)
		;

		$mailer->send($mail);
	}


	private function sendUserEmails(string $email, string $templateName, string $text, string $subject, array $data): void
	{
		$template = $this->createTemplate();
		$body = $template->renderToString(
			\ROOT_DIR . '/app/MailModule/template/' . $templateName,
			[
				'email' => $email,
				'text' => $text,
				'data' => $data,
			]
		);
		$this->sendWithSMTP(
			$body,
			\App\MailModule\Model\Mail\MailConstants::INFO_EMAIL,
			$email,
			$subject
		);
	}


	public function sendRestorePasswordEmail(string $email, string $lang, string $token): void
	{
		$this->translator->setLocale($lang);
		$subject = $this->translator->translate('messages.mail.restorePasswordSubject');
		$text = $this->translator->translate('messages.mail.restorePasswordText');
		$linkTitle = $this->translator->translate('messages.mail.restorePasswordLinkTitle');
		$link = $this->domain .
			$lang . '/' .
			$this->pageService->getPageByUid($lang, \App\PageModule\Model\PageService::UID_RESTORE_PASSWORD)->getAlias() .
			'?email=' . $email .
			'&token=' . $token;
		$this->sendUserEmails(
			$email,
			\App\MailModule\Model\Mail\MailConstants::TPL_TOKEN_SEND,
			$text,
			$subject,
			[
				'token' => $token,
				'link' => $link,
				'linkTitle' => $linkTitle,
			]
		);
	}

}
