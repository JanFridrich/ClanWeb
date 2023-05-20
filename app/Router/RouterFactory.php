<?php declare(strict_types = 1);

namespace App\Router;

final class RouterFactory
{

	private \App\PageModule\Model\PageService $pageService;


	public function __construct(
		\App\PageModule\Model\PageService $pageService
	)
	{
		$this->pageService = $pageService;
	}


	public function createRouter(): \Nette\Application\Routers\RouteList
	{
		$router = new \Nette\Application\Routers\RouteList;

		foreach (\App\PageModule\Model\PageService::LANGUAGES as $lang) {
			$pages = $this->pageService->getAllPages($lang);
			$langInUrl = $lang === \App\PageModule\Model\PageService::DEFAULT_LANGUAGE ? '[' . $lang . '/]' : $lang . '/';
			foreach ($pages as $page) {
				if ($page && $page->getAlias() && $page->getPresenter()) {
					$router->addRoute(
						$langInUrl . $page->getAlias(), [
							'module' => $page->getModule() ?? 'Core',
							'presenter' => $page->getPresenter(),
							'action' => $page->getAction() ?? 'default',
							'locale' => $lang,
							'pageId' => $page->getId(),
						]
					);
				}
			}
			$router->addRoute(
				$langInUrl,
				[
					'module' => 'Core',
					'presenter' => 'Homepage',
					'action' => 'default',
					'locale' => $lang,
					'pageId' => 0,
				]
			);
		}

		$router->addRoute("design/<mod>/<presenter>[/<action>][/<id>]", [
			NULL => [
				\Nette\Routing\Route::FILTER_IN => function (array $params): array {
					$params['presenter'] = \ucfirst($params['mod']) . ':Admin:' . $params['presenter'];
					unset($params['mod']);

					return $params;
				},
				\Nette\Routing\Route::FILTER_OUT => function (array $params): array {
					$presenterParts = \explode(':', $params['presenter']);
					if (isset($presenterParts[2])) {
						$params['mod'] = \lcfirst($presenterParts[0]);
						$params['presenter'] = $presenterParts[2];
					}

					return $params;
				},
			],
		]);

		return $router;
	}

}
