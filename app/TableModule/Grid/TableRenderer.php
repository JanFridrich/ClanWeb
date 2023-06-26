<?php declare(strict_types = 1);

namespace App\TableModule\Grid;

class TableRenderer
{

	public function renderIsActive(\App\TableModule\Model\Table\Table $table): string
	{
		return $table->isActive() ? 'YES' : 'NO';
	}

}
