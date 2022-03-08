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
							"ชื่อ-นามสกุล",
							"เบอร์ติดต่อ",
							"ยอดขาย",
							"ค่าพัศดุ",
							"ค่าจัดส่ง",
							"ยอดขายสุทธิ"
						);
	// echo phpinfo();
	// echo  count($query)."<br>"."<pre>";print_r($query);echo "</pre>";die();
	foreach($query as $row => $val){
		//	number
		$row++;
		//	status
		$user_status = report_statusOn($val->STATUS);
		
		$write_array[] = array(
							$row,
							date('d-m-Y',strtotime($val->bill_datetime)),
							$val->bill_code,
							$val->NAME,
							$val->PHONE_NUMBER,
							$val->TOTAL_PRICE,
							$val->PARCEL_COST,
							$val->DELIVERY_FEE,
							$val->NET_TOTAL
						);
	}
	$row++;
	$last_row = $row;
	// echo  count($write_array)."<br>"."<pre>";print_r($write_array);echo "</pre>";die();
	//	
	//	set style
	$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	
	$filename = "rp_bill_".date('Y-m-d').".xlsx";
	$spreadsheet = new Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	$spreadsheet->getActiveSheet()->fromArray($write_array,NULL,'A1');
	
	#	set default excel
	$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	
	#	alignment
	$spreadsheet->getActiveSheet()->getStyle('A2:A'.$last_row)
	->getAlignment()
	->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	
	#	number format(#,###.00)
	#	:FORMAT_CURRENCY_USD_SIMPLE		$xxx.00
	#	:FORMAT_CURRENCY_EUR			xxx e (Euro)
	#	setFormatCode('#,##0');			xxx e (Euro)
	$spreadsheet->getActiveSheet()->getStyle('I2:I'.$last_row)->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	
	#	creat text
	$row_ritchtext = $last_row+1;
	$richText->createText('This invoice is ');
	$payable = $richText->createTextRun('document to secret for Senior officer farmchokchai');
	$payable->getFont()->setBold(true);
	$payable->getFont()->setItalic(true);
	$payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN ) );
	$richText->createText(', Do not share.');
	
	#	mergeCells
	$spreadsheet->getActiveSheet()->mergeCells('A'.$row_ritchtext.':I'.$row_ritchtext);
	
	#	unmergeCells
	// $spreadsheet->getActiveSheet()->unmergeCells('A18:E22');
	
	
	$spreadsheet->getActiveSheet()->getCell('A'.$row_ritchtext)->setValue($richText);
	
	#	print break referance row
	// $spreadsheet->getActiveSheet()->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
	
	#	print break referance column
	// $spreadsheet->getActiveSheet()->setBreak('D10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN);
	
	#	auto column size
	// $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	
	#	array set font
	$stylePrice = [
		'font' => [
			'bold' => true,
			'name' => 'Arial',
			'size' => 8,
		]
	];
	$spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($stylePrice);
	
	#	array set alignment
	$styleAlign = [
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
		]
	];
	$spreadsheet->getActiveSheet()->getStyle('B2:I'.$last_row)->applyFromArray($styleAlign);
	
	#	array set border
	$styleBorder = [
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => '00000000'],
			],
		],
	];
	$spreadsheet->getActiveSheet()->getStyle('A1:I'.$last_row)->applyFromArray($styleBorder);
	
	#	wraptext (show text non-over column width)
	$spreadsheet->getActiveSheet()->getStyle('D2:D'.$last_row)->getAlignment()->setWrapText(true);
	
	#	set value compress in column (font to smaller)
	//$spreadsheet->getActiveSheet()->getStyle('D1:D26')->getAlignment()->setShrinkToFit(true);
	
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(36);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(8);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(8);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(14);
	
	#	set sheet name
	$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));
	
	#	protection
	$spreadsheet->getActiveSheet()->getProtection()->setSheet(false);

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$fileName.'"');
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
?>