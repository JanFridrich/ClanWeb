<?php declare(strict_types = 1);

namespace App\MailModule\Model\MailerProvider;

class SMTPMailerProvider
{

	private string $host;

	private string $username;

	private string $password;

	private string $secure;


	public function __construct(
		string $host,
		string $username,
		string $password,
		string $secure
	)
	{
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->secure = $secure;
	}


	public function provide(): \Nette\Mail\SmtpMailer
	{
		return new \Nette\Mail\SmtpMailer([
			'host' => $this->host,
			'username' => $this->username,
			'password' => $this->password,
			'secure' => $this->secure,
		]);
	}

}
