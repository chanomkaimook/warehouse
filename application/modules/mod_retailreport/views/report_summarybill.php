<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Writer;

$write_array[] = array(
	"ลำดับ",
	"วันทำรายการ",
	"เลขที่",

	"ชื่อสินค้า",
	"จำนวน",
	"เขต",
	"สาขา",
	"ผู้สั่ง",
	"ผู้ตรวจ",

	"คืนสินค้า",
);
//
foreach ($query as $row => $val) {
	//	number
	$row++;

	$q_staff_start = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $val->bill_user_starts);
	$q_staff_update = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $val->bill_user_update);
	$approve1 = ($q_staff_start->name_th ? $q_staff_start->name_th . " " . $q_staff_start->lastname_th : $q_staff_start->name . " " . $q_staff_start->lastname);
	$approve2 = ($q_staff_update->name_th ? $q_staff_update->name_th . " " . $q_staff_update->lastname_th : $q_staff_update->name . " " . $q_staff_update->lastname);

	$creditnote = 0;

	// check ยอดใบลดหนี้ หากมีให้นำมาลบยอดรวม
	$sqlt = $this->db->select('retail_creditnote.net_total as rtd_net')
		->from('retail_creditnote')
		->join('retail_creditnotedetail', 'retail_creditnote.id=retail_creditnotedetail.creditnote_id', 'left')
		->where('retail_creditnote.rt_id', $val->bill_id)
		->where('retail_creditnote.complete', 2)
		->where('retail_creditnote.status', 1);
	$qt = $sqlt->get();
	$numt = $qt->num_rows();
	if ($numt) {
		$rowt = $qt->row();
		$creditnote = number_format($rowt->rtd_net, 2);
		$nettotal = number_format($nettotal - $rowt->rtd_net);
	}
	//

	$write_array[] = array(
		$row,
		date('d-m-Y', strtotime($val->bill_datetime)),
		$val->bill_code,

		$val->product_name,
		$val->bill_qty,

		$val->bill_typedelivery,
		$val->bill_gateway,
		$approve1,
		$approve2,

		$text_qty
	);
}
// echo "<pre>";print_r($write_array);echo "</pre>";die();
$row++;
$last_row = $row;
// echo  count($write_array)."<br>"."<pre>";print_r($write_array);echo "</pre>";die();
//	
//	set style
$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

$spreadsheet = new Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->fromArray($write_array, NULL, 'A1');

#	set default excel
$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

#	creat text
$row_ritchtext = $last_row + 2;
$richText->createText('This invoice is ');
$payable = $richText->createTextRun('document to secret for Senior officer farmchokchai');
$payable->getFont()->setBold(true);
$payable->getFont()->setItalic(true);
$payable->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN));
$richText->createText(', Do not share.');
$spreadsheet->getActiveSheet()->getCell('A' . $row_ritchtext)->setValue($richText);
$spreadsheet->getActiveSheet()->getStyle('A' . $row_ritchtext)
	->getAlignment()
	->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

#	array set
$styleHead = [
	'font' => [
		'bold' => true,
		'name' => 'Arial',
		'size' => 8,
	],
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	],
	'fill' => [
		'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
		'rotation' => 90,
		'startColor' => [
			'argb' => 'ff75e0',
		],
		'endColor' => [
			'argb' => 'f8caee',
		],
	],
];
$spreadsheet->getActiveSheet()->getStyle('A1:R1')->applyFromArray($styleHead);

#	array set alignment
$styleAlign = [
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];
$spreadsheet->getActiveSheet()->getStyle('A2:R' . $last_row)->applyFromArray($styleAlign);

#	array set
$stylePrice = [
	'font' => [
		'bold' => false,
		'name' => 'Arial',
		'size' => 8,
	],
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];
$spreadsheet->getActiveSheet()->getStyle('E2:E' . $last_row)->applyFromArray($stylePrice);


#	array set border
$styleBorder = [
	'borders' => [
		'allBorders' => [
			'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
			'color' => ['argb' => '00000000'],
		],
	],
];
$spreadsheet->getActiveSheet()->getStyle('A1:J' . $last_row)->applyFromArray($styleBorder);

#	wraptext (show text non-over column width)
$spreadsheet->getActiveSheet()->getStyle('D4:D' . $last_row)->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('N4:N' . $last_row)->getAlignment()->setWrapText(true);

$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(36);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(60);

#	creat total
$nextrow1 = $last_row + 1;
$array_columntotal = array(
	'E'	=> "",
);
//	price
$styleTotalPrice = [
	'font' => [
		'bold' => true,
		'name' => 'Arial',
		'size' => 8,
	],
	'alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	]
];

foreach ($array_columntotal as $key => $val) {
	/* $totalText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
		$payable = $totalText->createTextRun($val);
		$payable->getFont()->setBold(true);
		$spreadsheet->getActiveSheet()->getCell(''.$key.''.$nextrow1)->setValue($totalText); */
	$spreadsheet->getActiveSheet()->getStyle('' . $key . '' . $nextrow1)
		->getAlignment()
		->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

	$spreadsheet->getActiveSheet()->getStyle('' . $key . '' . $nextrow1)
		->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
		->getStartColor()->setARGB('00FFFF00');

	$spreadsheet->getActiveSheet()
		->setCellValue(
			'' . $key . '' . $nextrow1,
			'=SUM(' . $key . '2:' . $key . '' . $last_row . ')'
		);

	$spreadsheet->getActiveSheet()->getStyle('' . $key . '' . $nextrow1)->applyFromArray($styleTotalPrice);

	//	number format
	$spreadsheet->getActiveSheet()->getStyle('' . $key . '' . $nextrow1)->getNumberFormat()
		->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

#	set sheet name
$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));

#	protection
$spreadsheet->getActiveSheet()->getProtection()->setSheet(false);

//
//	setting
$filename = "rp_billSales_" . date('Y-m-d') . ".xlsx";
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
