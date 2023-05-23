<?php declare(strict_types = 1);

namespace App\ImageModule\Resizer;

class Resizer
{

	public static function resize(string $imageName, string $imagePath, int $width, int $height): void
	{
		try {
			if ( ! \file_exists(\ROOT_DIR . $imagePath . $imageName)) {
				return;
			}
			$image = \Nette\Utils\Image::fromFile(\ROOT_DIR . $imagePath . $imageName);

			if ( ! \file_exists(\ROOT_DIR . $imagePath . 'resized/' . $width . '_' . $height . '/' . $imageName)) {
				$image->resize($width, $height);
				if ($image->getHeight() !== $height || $image->getWidth() !== $width) {
					$image->resize($width, $height, \Nette\Utils\Image::EXACT);
				}
				if ( ! \file_exists(\ROOT_DIR . $imagePath . 'resized/' . $width . '_' . $height . '/')) {
					\mkdir(\ROOT_DIR . $imagePath . 'resized/' . $width . '_' . $height . '/', 0777, TRUE);
				}
				$image->save(\ROOT_DIR . $imagePath . 'resized/' . $width . '_' . $height . '/' . $imageName);
			}
		}catch (\Exception $exception){
			\Tracy\Debugger::barDump($exception);
		}

	}

}
