<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Writer;

#
#	Setting
#
$arr = $this->mdl_report->jsonListmonth($query);

$request = $_REQUEST;
$valdate = $request['valdate'];
$valdateTo = $request['valdateto'];
$arrayset = array(
	'date'			=> $valdate,
	'dateto'		=> $valdateTo
);
$creditnote = $this->mdl_report->jsonCreditnote($arrayset);

if($creditnote['arrayresult']){
	$newarray = array_merge($arr['arrayresult'], $creditnote['arrayresult']);
}else{
	$newarray = $arr['arrayresult'];
}
#
#	group date order
$ardate = unique_multidim_array($newarray, 'date');

#	group order
$order = unique_multidim_array($newarray, 'code');
#
#	new index
#	code
$code = array();
foreach ($order as $row => $val) {
	array_push($code, $val);
}
#	date
$arr_date = array();
foreach ($ardate as $row => $val) {
	array_push($arr_date, $val);
	$arr_groupdate[] = array_keys(array_column($code, 'date'), $val['date']);
}

$startdate = reset($arr_date);
$enddate = end($arr_date);
$dateto = array($startdate['date'], $enddate['date']);

$dataarray = array();
foreach ($arr_date as $rows => $val) {
	foreach ($arr_groupdate[$rows] as $row => $val) {

		$code[$val]['citizen'];

		array_push(
			$dataarray,
			$code[$val]
		);
	}
}

/* echo "<pre>";
echo "date: " . $valdate;
echo "dateto: " . $valdateTo . "<br>";
echo "result =================";
print_r($dataarray);
echo "=================";
print_r($arr);
echo "=================";
print_r($creditnote);
echo "</pre>";
die(); */
#	setting
$fontsize = 10;
$fontsizehead = 9;
$fontfamily = 'Arial';


//	text
#	dateto = array[[0=datestart],[1=dateend]]
if ($arr['dateto'][0] != $arr['dateto'][1]) {
	$textdate = "วันที่ " . date_indent($arr['dateto'][0], "/") . " - " . date_indent($arr['dateto'][1], "/");
} else {
	$textdate = "วันที่ " . date_indent($arr['dateto'][0], "/");
}

$textheader = "รายงานใบกำกับภาษี Retail Online";

$textinvoice = "เลขที่ inv";
$textcode = "code ร้านค้า";
$textdatevat = "วันที่ใบกำกับภาษี";
$textoerderid = "เลขที่ใบกำกับภาษี";
$textdatetrans = "วันที่โอน";
$textmethod = "สื่อ";
$textname = "ชื่อ-นามสกุล";
$textcitizen = "เลขภาษี";
$textparcel = "ค่าพัสดุ";
$textdelivery = "ค่าจัดส่ง";
$textshor = "ค่าธรรมเนียม shopee";
$textdiscount = "ส่วนลด";
$texttax = "ค่าธรรมเนียมเก็บเงินปลายทาง";
$textgateway = "ช่องทาง";
$textvat = "ภาษีมูลค่าเพิ่ม";
$textprice = "ค่าบริการ";
$textnetprice = "ยอดรวม";
$typepay = "ประเภท";

#
$columnname = array(
	'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
	'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
	'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
	'Y', 'Z'
);

$write_array[] = array(
	" ",
	$textinvoice,
	$textcode,
	$textdatevat,
	$textoerderid,

	$textdatetrans,
	$textmethod,

	$textname,
	$textcitizen,
	$textnetprice,
	$textparcel,
	$textshor,
	$textdelivery,
	$textdiscount,
	$texttax,
	$textvat,
	$textprice,
	$textgateway,
	$typepay
);

/* echo "<pre>";
	print_r(array_keys($write_array[0],$textname));
	echo "</pre>";die(); */
// echo count($write_array[0]);die();
// echo array_search($textname,$write_array[0]);die();

foreach ($dataarray as $rows => $val) {
	$i++;

	if ($val['billstatus'] == "C") {
		$textstatus = "เก็บเงินปลายทาง";
	} else {
		$textstatus = "ปกติ";
	}

	if (strpos($val['code'],'FC') !== false) {
		$typepay = "ใบลดหนี้";
	}

	$write_array[] = array(
		$i,
		$val['code'],
		$val['textcode'],
		$val['date'],
		$val['invoice'],

		$val['datetrans'],
		$val['medthod'],

		$val['name'],
		$val['citizen'],
		$val['totalprice'],
		$val['parcel'],
		$val['shor'],
		$val['delivery'],
		$val['discount'],
		$val['tax'],
		$val['vat'],
		$val['price'],
		$val['gateway'],
		$textstatus
	);
}

$index_row_end = $i + 1;
$index_col_end = count($write_array[0]) - 1;
#	setting
$col_start = $columnname[0];
$col_end = $columnname[$index_col_end];

$cell_start = $col_start . "1";
$cell_end = $col_end . $index_row_end;
$cell_col_end = $col_end . "1";
// echo $cell_start." :: ".$cell_end;die();

//	set style
$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
$spreadsheet = new Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->fromArray($write_array, NULL, $cell_start);

#	set dimention

#	set default excel
$spreadsheet->getDefaultStyle()->getFont()->setName($fontfamily);
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

#	array set font
$styleHead = [
	'font' => [
		'bold' => true,
		'name' => 'Arial',
		'size' => 16,
	],
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];

#	array set vat
$styleVat = [
	'font' => [
		'bold' => true,
		'name' => 'Arial',
	],
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];

#	array set vat
$styleRight = [
	'font' => [
		'name' => 'Arial',
	],
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];

#	array set vat
$styleLeft = [
	'font' => [
		'name' => 'Arial',
	],
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];

#	array set product list
$styleListname = [
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];

#	array set product list
$styleListmoney = [
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];

#	array set font
$styleTop = [
	'alignment' => [
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
	]
];

#	array set border
$styleBorder = [
	'borders' => [
		'allBorders' => [
			'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
			'color' => ['argb' => '00000000'],
		],
	],
];
#	array set border
$styleBorderOut = [
	'borders' => [
		'outline' => [
			'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
			'color' => ['argb' => '00000000'],
		],
	],
];

#	set border
$spreadsheet->getActiveSheet()->getStyle($cell_start . ':' . $cell_end)->applyFromArray($styleBorder);

#	wrap text
$spreadsheet->getActiveSheet()->getStyle($cell_start . ':' . $cell_col_end)->getAlignment()->setWrapText(true);

#	set column width
$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(12);

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);

$index_col_invoice = array_search($textinvoice, $write_array[0]);
$spreadsheet->getActiveSheet()->getColumnDimension($columnname[$index_col_invoice])->setWidth(16);

$index_col_datevat = array_search($textdatevat, $write_array[0]);
$spreadsheet->getActiveSheet()->getColumnDimension($columnname[$index_col_datevat])->setWidth(16);

$index_col_order = array_search($textoerderid, $write_array[0]);
$spreadsheet->getActiveSheet()->getColumnDimension($columnname[$index_col_order])->setWidth(16);

$index_col_name = array_search($textname, $write_array[0]);
$spreadsheet->getActiveSheet()->getColumnDimension($columnname[$index_col_name])->setWidth(34);

$index_col_citizen = array_search($textcitizen, $write_array[0]);
$spreadsheet->getActiveSheet()->getColumnDimension($columnname[$index_col_citizen])->setWidth(20);

$index_col_typepay = array_search($typepay, $write_array[0]);
// $spreadsheet->getActiveSheet()->getFont($columnname[$index_col_typepay])->setSize(9);
$styletypepay = [
	'font' => [
		'size' => 9,
	],
];
$spreadsheet->getActiveSheet()->getStyle($columnname[$index_col_typepay] . "2:" . $columnname[$index_col_typepay] . $index_row_end)->applyFromArray($styletypepay);

$index_col_vat = array_search($textvat, $write_array[0]);
$index_col_code = array_search($textcode, $write_array[0]);
#	number format
$spreadsheet->getActiveSheet()->getStyle($columnname[$index_col_vat] . "2:" . $columnname[$index_col_vat] . $index_row_end)->getNumberFormat()
	->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
#
#	change style
$spreadsheet->getActiveSheet()->getStyle($columnname[$index_col_vat] . "2:" . $columnname[$index_col_vat] . $index_row_end)->applyFromArray($styleListmoney);
$spreadsheet->getActiveSheet()->getStyle($columnname[$index_col_code] . "2:" . $columnname[$index_col_code] . $index_row_end)->applyFromArray($styleLeft);

#
#  change type
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(21);
$spreadsheet->getActiveSheet()->getStyle($columnname[$index_col_code] . '2:' . $columnname[$index_col_code] . $index_row_end)
	->getNumberFormat()
	->setFormatCode('0');

#	=========================================
#	=========		RUN HEADER		=========
#	setting
$rowfirst = 1;
#	insert row
$spreadsheet->getActiveSheet()->insertNewRowBefore($rowfirst, 1);
#	creat text title
$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
$ritch_style = $richText->createTextRun($textheader);
$ritch_style->getFont()->setBold(true);
$ritch_style->getFont()->setSize(16);
#	mergeCells
$spreadsheet->getActiveSheet()->mergeCells($col_start . $rowfirst . ':' . $col_end . $rowfirst);
#	set height
$spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
#	insert text
$spreadsheet->getActiveSheet()->getCell($col_start . $rowfirst)->setValue($richText);
#	change style
$spreadsheet->getActiveSheet()->getStyle($col_start . $rowfirst)->applyFromArray($styleHead);

#	Date
#	setting
$rowfirst++;
#	insert row
$spreadsheet->getActiveSheet()->insertNewRowBefore($rowfirst, 1);
#	creat text title
$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
$ritch_style = $richText->createTextRun($textdate);
$ritch_style->getFont()->setBold(true);
#	mergeCells
$spreadsheet->getActiveSheet()->mergeCells($col_start . $rowfirst . ':' . $col_end . $rowfirst);
#	set height
$spreadsheet->getActiveSheet()->getRowDimension($rowfirst)->setRowHeight(14);
#	insert text
$spreadsheet->getActiveSheet()->getCell($col_start . $rowfirst)->setValue($richText);
#	change style
$spreadsheet->getActiveSheet()->getStyle($col_start . $rowfirst)->applyFromArray($styleListmoney);
#	=========		END HEADER		=========
#	=========================================

#	set sheet name
$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));

#	protection
$spreadsheet->getActiveSheet()->getProtection()->setSheet(false);

#	set break page
// $spreadsheet->getActiveSheet()->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

#	header & footer for print
/* $spreadsheet->getActiveSheet()->getHeaderFooter()
    ->setOddHeader('&C&HPlease treat this document as confidential!'); */
$spreadsheet->getActiveSheet()->getHeaderFooter()
	->setOddFooter('&L ' . date('Y-m-d H:i:s') . " by " . $this->session->userdata('useradminid') . "" . '&RPage &P of &N');
/*	// referance sheet name 
	$spreadsheet->getActiveSheet()->getHeaderFooter()
	->setOddFooter('&L&B '.$this->session->userdata('useradminname')."" . $spreadsheet->getProperties()->getTitle() . '&RPage &P of &N'); */

#	set print title
$spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);

#	page setup
$spreadsheet->getActiveSheet()->getPageSetup()
	->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->getActiveSheet()->getPageSetup()
	->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

//
//	setting
$filename = "rp_vatlist_" . date('Y-m-d') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: cache, must-revalidate');
header('Pragma: public');
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
//	for clear bug when export file error extension not valid
for ($i = 0; $i < ob_get_level(); $i++) {
	ob_end_flush();
}
ob_implicit_flush(1);
ob_clean();
//
$writer->save('php://output');
