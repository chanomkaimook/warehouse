<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Writer;

#
#	Setting
#
$fontsize = 10;
$fontfamily = 'Arial';
$set_cl_width = 20;
$set_rw_height = 15;
$startrow = 1;

/* $write_array[] = array(
							"ลำดับ",
							"วันทำรายการ",
							"เลขที่",
							"ชื่อ-นามสกุล",
							"เบอร์ติดต่อ",
							"รหัสสินค้า",
							"ชื่อสินค้า",
							"จำนวน",
							"ราคา",
							"คืนสินค้า",
							"ลดหนี้",
							"ช่องทาง"
						); */
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
	//	product name
	$productcode = "";
	$productname = "";

	if ($productname == "") {
		$sqlproduct = $this->db->select('
						retail_productlist.code as productcode,
						retail_productlist.name_th as productname,
					')
			->from('retail_productlist')
			->where('retail_productlist.id', $val->product_id)
			->get();
		$r = $sqlproduct->row();

		$productcode = $r->productcode;
		$productname = $r->productname;
	}

	$text_qty = 0;
	// check ยอดคืนสินค้า หากมีให้นำมาลบยอดรวม
	$sqlt = $this->db->select('retail_receivedetail.quantity as rtd_qty')
		->from('retail_receive')
		->join('retail_receivedetail', 'retail_receive.id=retail_receivedetail.receive_id', 'left')
		->where('retail_receive.rt_bill_code', $val->bill_code)
		->where('retail_receive.complete', 2)
		->where('retail_receive.status', 1);
	$qt = $sqlt->get();
	$numt = $qt->num_rows();
	if ($numt) {
		$rowt = $qt->row();

		$text_qty = $rowt->rtd_qty;
	}

	$creditnote = 0;
	// check ยอดใบลดหนี้ หากมีให้นำมาลบยอดรวม
	$sqlc = $this->db->select('retail_creditnote.net_total as rtd_net')
		->from('retail_creditnote')
		->join('retail_creditnotedetail', 'retail_creditnote.id=retail_creditnotedetail.creditnote_id', 'left')
		->where('retail_creditnote.rt_bill_code', $val->bill_code)
		->where('retail_creditnote.complete', 2)
		->where('retail_creditnote.status', 1);
	$qc = $sqlc->get();
	$numc = $qc->num_rows();
	if ($numc) {
		$rowc = $qc->row();
		$creditnote = number_format($rowc->rtd_net, 2);
	}

	//	number
	$row++;

	$q_staff_start = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $val->bill_user_starts);
	$q_staff_update = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $val->bill_user_update);
	$approve1 = ($q_staff_start->name_th ? $q_staff_start->name_th . " " . $q_staff_start->lastname_th : $q_staff_start->name . " " . $q_staff_start->lastname);
	$approve2 = ($q_staff_update->name_th ? $q_staff_update->name_th . " " . $q_staff_update->lastname_th : $q_staff_update->name . " " . $q_staff_update->lastname);

	$write_array[] = array(
		$row,
		date('d-m-Y', strtotime($val->bill_datetime)),
		$val->bill_code,

		$productname,
		$val->bill_qty,

		$val->bill_typedelivery,
		$val->methodtopic,
		$approve1,
		$approve2,

		$text_qty
	);
}

$lastrow = count($write_array);
$nextrow = count($write_array) + 1;

// echo "<pre>";print_r($write_array);echo "</pre>";die();
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

#	array set font
$styleBold = [
	'font' => [
		'bold' => true,
		'name' => 'Arial'
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
$column = $spreadsheet->getActiveSheet()->getStyle('A1:G1');
$column->applyFromArray($styleBold);
$column->getAlignment()->setWrapText(true);
#	wraptext (show text non-over column width)
// $spreadsheet->getActiveSheet()->getStyle('D4:D'.$last_row)->getAlignment()->setWrapText(true);

#	creat total
$spreadsheet->getActiveSheet()
	->setCellValue(
		'E' . $nextrow,
		'=SUM(E2:E' . $lastrow . ')'
	);
$spreadsheet->getActiveSheet()->getStyle('E' . $nextrow)
	->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
	->getStartColor()->setARGB('00FFFF00');

#	number format
$spreadsheet->getActiveSheet()->getStyle('I2:I' . $lastrow)->getNumberFormat()
	->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$spreadsheet->getActiveSheet()->getStyle('K2:K' . $lastrow)->getNumberFormat()
	->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

//
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth($set_cl_width);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth($set_cl_width);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth($set_cl_width);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth($set_cl_width);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth($set_cl_width);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth($set_cl_width);
//
//	setting
$filename = "rp_billproduct_" . date('Y-m-d') . ".xlsx";
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
