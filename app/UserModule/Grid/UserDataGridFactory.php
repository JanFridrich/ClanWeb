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

		$grid->addColumnText(\App\UserModule\Model\UserMapping::COLUMN_LAST_UPDATED_UNITS, 'updated units')
			->setSortable()
			->setFilterText()
		;

		$grid->addColumnText(\App\UserModule\Model\UserMapping::COLUMN_MAXED_UNITS, 'progress')
			->setSortable()
			->setRenderer([$this->renderer, 'renderProgress'])
			->setFilterText()


		;

		$grid->addColumnText(\App\UserModule\Model\UserMapping::COLUMN_IS_ACTIVE, \App\UserModule\Model\UserMapping::COLUMN_IS_ACTIVE)
			->setSortable()
			->setRenderer([$this->renderer, 'renderIsActive'])
			->setFilterText()
		;

		$grid->addAction('edit', '✏️', ':User:Admin:User:default', [
			'id' => 'id',
		]);

		$grid->addAction('remove', 'X️ ', 'remove!', [
			'remove' => 'id',
		])->setDataAttribute('id','remove');

		return $grid;
	}
}
