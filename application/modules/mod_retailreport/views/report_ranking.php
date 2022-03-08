<?php
	ini_set('max_execution_time',0);
	ini_set('memory_limit',1);
	ini_set('memory_limit',"10M");
	
	require 'vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	
	$this->load->helper('excel_helper');
	
	$write_array = array();
	$arraycolumn = array();
	$data_arraycolumn = array(
							"ลำดับ",
							"ชื่อ-นามสกุล",
							"เบอร์ติดต่อ",
							"ยอดขาย",
							"จำนวนบิล"
						);
	foreach($data_arraycolumn as $subrow){
		array_push($arraycolumn,$subrow);
	}
	array_push($write_array,$arraycolumn);
	// echo  count($query)."<br>"."<pre>";print_r($query);echo "</pre>";die();
	
	$requestData= $_REQUEST;

	$valdate = $requestData['valdate'];
	$valdateTo = $requestData['valdateto'];
	
	foreach($query as $row => $val){
		$inarray = array();
		//	number
		$row++;
		//	query
		$this->db->select('
					COUNT(retail_bill.name) as totalbill
				');
		$this->db->from('retail_bill');
		$this->db->where('retail_bill.status_complete = 2');
		$this->db->where('retail_bill.status = 1');
		$this->db->where('retail_bill.name = "'.$val->bill_name.'"');
		
		if($valdate != '' && $valdateTo == ''){
			$this->db->where('date(retail_bill.date_starts) ',$valdate);
		}
		else if($valdate != '' && $valdateTo != ''){
			$this->db->where('date(retail_bill.date_starts) >=',$valdate);
			$this->db->where('date(retail_bill.date_starts) <=',$valdateTo);
		}
		else if($valdateTo != ''){
			$this->db->where('date(retail_bill.date_starts) <=',$valdateTo);
		}
		
		$q = $this->db->get();
		$r = $q->row();
		//
		array_push($inarray,$row);
		array_push($inarray,$val->bill_name);
		array_push($inarray,$val->bill_phone);
		array_push($inarray,$val->bill_totalprice);
		array_push($inarray,$r->totalbill);
		
		array_push($write_array,$inarray);
	}
	//
	//	setting
	//	* 0=A, 1=B, 2=C...
	$totalarray = count($arraycolumn) - 1;
	$column_startsum = 3;
	$totalarray_sum = $totalarray-$column_startsum;
	$columnfirst = get_columnExcelNameFromNumber(0);
	$columnlast = get_columnExcelNameFromNumber($totalarray);
	//
	$last_row = $row;
	$insertrow = 1;
	$last_row = $last_row + $insertrow;
	
	// echo  count($write_array)."<br>"."<pre>";print_r($write_array);echo "</pre>";die();
	//
	//	set style
	$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	
	$spreadsheet = new Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	$spreadsheet->getActiveSheet()->fromArray($write_array,NULL,$columnfirst.'1');
	
	#	set default excel
	$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	
	#	alignment
	$spreadsheet->getActiveSheet()->getStyle($columnfirst.'2:'.$columnfirst.''.$last_row)
	->getAlignment()
	->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	
	#	creat text
	$row_ritchtext = $last_row+2;
	$richText->createText('This invoice is ');
	$payable = $richText->createTextRun('document to secret for Senior officer farmchokchai');
	$payable->getFont()->setBold(true);
	$payable->getFont()->setItalic(true);
	$payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN ) );
	$richText->createText(', Do not share.');
	$spreadsheet->getActiveSheet()->getCell('A'.$row_ritchtext)->setValue($richText);
	$spreadsheet->getActiveSheet()->getStyle('A'.$row_ritchtext)
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
	$spreadsheet->getActiveSheet()->getStyle($columnfirst.'1:'.$columnlast.'1')->applyFromArray($styleHead);
	
	#	array set alignment
	$styleAlign = [
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
		]
	];
	$spreadsheet->getActiveSheet()->getStyle('B1:B'.$last_row)->applyFromArray($styleAlign);
	
	#	array set border
	$styleBorder = [
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => '00000000'],
			],
		],
	];
	$spreadsheet->getActiveSheet()->getStyle($columnfirst.'1:'.$columnlast.$last_row)->applyFromArray($styleBorder);
	
	#	wraptext (show text non-over column width)
	$spreadsheet->getActiveSheet()->getStyle('A1:'.$columnlast.$last_row)->getAlignment()->setWrapText(true);
	
	#	creat total
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
	
	//	
	$nextrow1 = $last_row+1;
	for($i=0;$i<=$totalarray_sum;$i++){
		//	find column
		$column = $column_startsum + $i;
		$keystart = get_columnExcelNameFromNumber($column);
		
		$spreadsheet->getActiveSheet()->getStyle(''.$keystart.''.$nextrow1)
		->getAlignment()
		->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		
		$spreadsheet->getActiveSheet()->getStyle(''.$keystart.''.$nextrow1)
		->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
		->getStartColor()->setARGB('00FFFF00');
		
		$spreadsheet->getActiveSheet()
		->setCellValue(
			''.$keystart.''.$nextrow1,
			'=SUM('.$keystart.'2:'.$keystart.''.$last_row.')'
		);
		$spreadsheet->getActiveSheet()->getStyle(''.$keystart.''.$nextrow1)->applyFromArray($styleTotalPrice);
		
		//	number format
		$spreadsheet->getActiveSheet()->getStyle(''.$keystart.''.$nextrow1)->getNumberFormat()
		->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		
		$spreadsheet->getActiveSheet()->getStyle($keystart.''.$nextrow1)->applyFromArray($styleBorder);
	}
	
	#	number format(#,###.00)
	$spreadsheet->getActiveSheet()->getStyle('D2:D'.$last_row)->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	
	#	set sheet name
	$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));
	
	#	dimention
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(26);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
	
	//	setting
	$filename = "rp_userSales_".date('Y-m-d').".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
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