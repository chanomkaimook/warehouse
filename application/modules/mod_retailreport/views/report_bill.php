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
							"จำนวน(รายการ)",
							"เขต",
							"สาขา",
							"ผู้สั่ง",
							"ผู้ตรวจ",
							"หมายเหตุ"
						);
	foreach($query as $row => $val){
		//	number
		$row++;
		//
		$q_staff_start = get_WhereParaSelect('name_th,lastname_th,name,lastname','staff','code',$val->bill_user_starts);
		$q_staff_update = get_WhereParaSelect('name_th,lastname_th,name,lastname','staff','code',$val->bill_user_update);
		$approve1 = ($q_staff_start->name_th ? $q_staff_start->name_th." ".$q_staff_start->lastname_th : $q_staff_start->name." ".$q_staff_start->lastname); 
		$approve2 = ($q_staff_update->name_th ? $q_staff_update->name_th." ".$q_staff_update->lastname_th : $q_staff_update->name." ".$q_staff_update->lastname); 
		
		$write_array[] = array(
							$row,
							date('d-m-Y',strtotime($val->bill_datetime)),
							$val->bill_code,
							$val->rtd_sum,

							$val->delivery_name,
							$val->method_name,

							$approve1,
							$approve2,

							$val->bill_remark
						);
	}
	/* echo $this->db->last_query();
	echo "<pre>";print_r($query);echo "</pre>";die(); */
	$row++;
	$last_row = $row;
	$insertrow = 1;
	// echo  count($write_array)."<br>"."<pre>";print_r($write_array);echo "</pre>";die();
	//	
	//	set style
	$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	
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
	
	#	creat text
	$row_ritchtext = $last_row+1;
	$richText->createText('This invoice is ');
	$payable = $richText->createTextRun('document to secret for Senior officer farmchokchai');
	$payable->getFont()->setBold(true);
	$payable->getFont()->setItalic(true);
	$payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN ) );
	$richText->createText(', Do not share.');
	
	#	mergeCells
	$spreadsheet->getActiveSheet()->mergeCells('A'.$row_ritchtext.':M'.$row_ritchtext);
	
	$spreadsheet->getActiveSheet()->getCell('A'.$row_ritchtext)->setValue($richText);
	
	#	array set font
	$styleHead = [
		'font' => [
			'bold' => true,
			'name' => 'Arial',
			'size' => 8,
		],
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
		]
	];
	$spreadsheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleHead);
	
	#	array set alignment
	$styleAlign = [
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
		]
	];
	$spreadsheet->getActiveSheet()->getStyle('B2:L'.$last_row)->applyFromArray($styleAlign);
	
	
	#	wraptext (show text non-over column width)
	$spreadsheet->getActiveSheet()->getStyle('E2:E'.$last_row)->getAlignment()->setWrapText(true);
	$spreadsheet->getActiveSheet()->getStyle('G2:G'.$last_row)->getAlignment()->setWrapText(true);
	
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(36);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(48);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(25);

	#	set sheet name
	$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));
	
	#	protection
	$spreadsheet->getActiveSheet()->getProtection()->setSheet(false);
	
	//
	//	setting
	$filename = "rp_bill_".date('Y-m-d').".xlsx";
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