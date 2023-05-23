<?php declare(strict_types = 1);

namespace App\UserModule\Grid;

class UserDataGridFactory extends \App\CoreModule\GridFactory\DataGridFactory
{

	private \App\UserModule\Model\UserService $userService;

	private \App\UserModule\Grid\Renderer $renderer;


	public function __construct(
		\App\UserModule\Model\UserService $userService,
		\App\UserModule\Grid\Renderer $renderer

	)
	{
		$this->userService = $userService;
		$this->renderer = $renderer;
	}


	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid->setDataSource($this->userService->getAll());

		$grid->addColumnText(\App\UserModule\Model\UserMapping::COLUMN_LOGIN, \App\UserModule\Model\UserMapping::COLUMN_LOGIN)
			->setSortable()
			->setFilterText()
		;

		$grid->addColumnText(\App\UserModule\Model\UserMapping::COLUMN_ROLE, \App\UserModule\Model\UserMapping::COLUMN_ROLE)
			->setSortable()
			->setFilterText()
		;

		$grid->addColumnText(\App\UserModule\Model\UserMapping::COLUMN_IS_ACTIVE, \App\UserModule\Model\UserMapping::COLUMN_IS_ACTIVE)
			->setSortable()
			->setRenderer([$this->renderer, 'renderIsActive'])
		;

		$grid->addAction('edit', '✏️', ':User:Admin:User:default', [
			'id' => 'id',
		]);

		return $grid;
	}
}
