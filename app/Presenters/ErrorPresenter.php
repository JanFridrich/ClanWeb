<?php declare(strict_types = 1);

namespace App\Presenters;

final class ErrorPresenter implements \Nette\Application\IPresenter
{
	use \Nette\SmartObject;

	/** @var \Tracy\ILogger */
	private $logger;


	public function __construct(\Tracy\ILogger $logger)
	{
		$this->logger = $logger;
	}


	public function run(\Nette\Application\Request $request): \Nette\Application\Response
	{
		$e = $request->getParameter('exception');

		if ($e instanceof \Nette\Application\BadRequestException) {
			[$module, , $sep] = \Nette\Application\Helpers::splitName($request->getPresenterName());
			$errorPresenter = $module . $sep . 'Error4xx';

			return new \Nette\Application\Responses\ForwardResponse($request->setPresenterName($errorPresenter));
		}

		$this->logger->log($e, \Tracy\ILogger::EXCEPTION);

		return new \Nette\Application\Responses\CallbackResponse(
			function (\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse): void {
				if (preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'))) {
					require __DIR__ . '/templates/Error/500.phtml';
				}
			}
		);
	}
}
