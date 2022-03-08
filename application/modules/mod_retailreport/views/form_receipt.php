<?php	
	require 'vendor/autoload.php';
	// use PhpOffice\PhpSpreadsheet\Helper\Sample;
	// use PhpOffice\PhpSpreadsheet\IOFactory;
	// use PhpOffice\PhpSpreadsheet\Spreadsheet;
	// use PhpOffice\PhpSpreadsheet\Writer\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('reportfile/hello world.xlsx');
?>

//	button report billvat
						var input_receipt = $('input#btn_receipt');
						if(input_receipt.val() == 1 && reporttype == 'bill_report'){
							btn_specialbill += "<button id='btnsp' class='btnsp_report btn btn-sm btn-light mr-2' value='bill_receipt'>ออกใบเสร็จรับเงิน</button>";
						}