<?php declare(strict_types = 1);

namespace App\PageModule\Grid;

class PageDataGridFactory
{

	private \App\PageModule\Model\PageService $pageService;


	public function __construct(
		\App\PageModule\Model\PageService $pageService
	)
	{
		$this->pageService = $pageService;
	}


	public function create(string $locale): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid->setDataSource($this->pageService->getAllPages($locale));

		$grid->addColumnText(\App\PageModule\Model\PageMapping::COLUMN_TEXT_TITLE, \App\PageModule\Model\PageMapping::COLUMN_TEXT_TITLE)
			->setSortable()
			->setFilterText()
		;

		$grid->addAction('edit', '✏️', ':Page:Admin:Page:default', [
			'id' => 'id',
		]);

		return $grid;
	}


}
