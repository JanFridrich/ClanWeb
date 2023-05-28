<?php declare(strict_types = 1);

namespace App\TableModule\Forms;

class TableRoleFormFactory
{

	private \App\TableModule\Model\TableItem\TableItemService $tableItemService;


	public function __construct(\App\TableModule\Model\TableItem\TableItemService $tableItemService)
	{
		$this->tableItemService = $tableItemService;
	}


	public function create(callable $onSuccess, \App\TableModule\Model\Table\Table $table): \Nette\Application\UI\Form
	{
		$tableId = $table->getId();
		$userIds = [];
		$form = new \Nette\Application\UI\Form();
		/** @var \App\TableModule\Model\TableItem\TableItem $tableItem */
		foreach ($table->getTableItems() as $tableItem) {
			$form->addText($tableItem->getId() . 'note', 'Note');
			$form->addCheckbox($tableItem->getId() . 'GL');
			$userIds[$tableItem->getId()] = $tableItem->getUser()->getId();
		}
		$form->addSubmit('submit', 'Next');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess, $tableId, $userIds): void {
			$this->tableItemService->clearTableItems($tableId, [
				\App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_NOTE,
				\App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_GROUP_LEADER,
			]);
			foreach ($values as $index => $value) {
				$note = \strpos($index, 'note');
				if ($note !== FALSE) {
					$note = \substr($index, 0, $note);
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_POSITION] = $note;
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_USER] = $userIds[$note];
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TABLE] = $tableId;
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE] = $value;
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TYPE] = \App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_NOTE;
					$this->tableItemService->createNew($item);
				}
				$gl = \strpos($index, 'GL');
				if ($gl !== FALSE) {
					$gl = \substr($index, 0, $gl);
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_POSITION] = $gl;
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_USER] = $userIds[$gl];
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TABLE] = $tableId;
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_VALUE] = $value;
					$item[\App\TableModule\Model\TableItem\TableItemMapping::COLUMN_TYPE] = \App\TableModule\Model\TableItem\TableItem::ITEM_TYPE_GROUP_LEADER;
					$this->tableItemService->createNew($item);
				}
			}
			$onSuccess($tableId);
		};

		return $form;
	}

}
