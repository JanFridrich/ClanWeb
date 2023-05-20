<?php declare(strict_types = 1);

namespace App\CoreModule\Model;

class XLSXFileResponseGetter
{

	public function get(array $usersToExport, \Nette\Utils\DateTime $timeStamp): \Nette\Application\Responses\FileResponse
	{
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->fromArray($usersToExport);
		$sheet->getStyle('A2:A' . count($usersToExport))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save(\ROOT_DIR . '/temp/export-users-' . $timeStamp . '.xlsx');

		$response = new \Nette\Application\Responses\FileResponse(\ROOT_DIR . '/temp/export-users-' . $timeStamp . '.xlsx');

		return $response;
	}

}
