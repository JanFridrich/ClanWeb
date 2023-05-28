<?php declare(strict_types = 1);

namespace App\TableModule\Forms;

class TableUnitFormFactory
{

	public const SQUADS = [
		'Alpha',
		'Bravo',
		'Charlie',
		'Delta',
		'Echo',
		'Foxtrot',
		'Juliet',
		'Killo',
		'Lima',
		'Mike',
		'November',
		'Oscar',
		'Papa',
		'Rokie',
	];

	private \App\UserModule\Model\UserService $userService;


	public function __construct(
		\App\UserModule\Model\UserService $userService
	)
	{
		$this->userService = $userService;
	}


	public function create(callable $onSuccess, \App\UserModule\Model\User $userEditor, \App\TableModule\Model\Table\Table $table, array $options): \Nette\Application\UI\Form
	{

		$columns = [
			'names',
			'unit',
			'unit',
			'unit',
			'armor',
			'squad',
		];//TODO změnit na získávání s jiného formu ??

		$users = ['-'];
		$units = ['-'];
		$armors = ['-'];

		$formatted = [];

		/** @var \App\UserModule\Model\User $user */
		foreach ($this->userService->getAll($options) as $user){
			$users[] = $user->getLogin();
			$formatted[$user->getLogin()][] = '-';
			foreach ($user->getUnits() as $unit) {
				if ($unit->getLevel() > 5) {
					$formatted[$user->getLogin()][$unit->getId()] = \str_replace('_', ' ', $unit->getName()) . '-' .
						$unit->getLevel() . '=' . $unit->getLeadership();
				}
			}
			$units[] = $formatted[$user->getLogin()];
			$armors[] = $user->getArmors();
		}


		$data = [
			'names' => $users,
			'unit' => $units,
			'armor' => $armors,
			'squad' => static::SQUADS,
		];

		$form = new \Nette\Application\UI\Form();

		for ($i = 1; $i < $table->getRows() + 1; $i++) {
			foreach ($columns as $colNumber => $column) {
				$form->addSelect($i . $colNumber . $column, '', $data[$column])->setDefaultValue(0);
			}
		}

		$form->addSubmit('send', 'Create');

		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) use ($onSuccess, $data): void {
			\Tracy\Debugger::barDump($values);
		};

		return $form;
	}


	public static function isColumnName($name): bool
	{
		return FALSE !== \strpos((string) $name, "names");
	}


	public static function isSquadName($name): bool
	{
		return FALSE !== strpos((string) $name, "squad");
	}


	public static function isClassName($name): bool
	{
		return FALSE !== strpos((string) $name, "class");
	}

}
