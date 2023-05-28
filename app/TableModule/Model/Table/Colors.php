<?php declare(strict_types = 1);

namespace App\TableModule\Model\Table;

class Colors
{

	public const colors = ['Tomato', 'MediumSeaGreen', 'Yellow', 'DodgerBlue', 'Orange',];
	public const secondaryColors = ['#ffb3b3', '#b3ffb3', '#ffffb3', '#b3ffff', '#ffd9b3',];


	public static function getNextColor(string $color): string
	{
		$key = \array_search($color, self::colors, TRUE) ?? 0;
		if ($key === FALSE) {
			return self::colors[0];
		}

		return self::colors[$key + 1] ?? self::colors[0];
	}


	public static function getSecondaryColor(string $color): string
	{
		$key = \array_search($color, self::colors, TRUE) ?? 0;
		if ($key === FALSE) {
			return self::secondaryColors[0];
		}

		return self::secondaryColors[$key];
	}


	public static function getFirstColor(): string
	{
		return self::colors[0];
	}
}
