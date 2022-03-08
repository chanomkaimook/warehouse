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
	$set_cl_width = 11;
	$set_rw_height = 15;
	$startrow = 1;
	$cl_s = "A";
	$cl_e = "H";
	$cl_1_s = "A";
	$cl_1_e = "E";
	$cl_2_s = "F";
	$cl_2_e = "H";
	//	list orders
	$adcl_1_s = "A";
	$adcl_2_s = "B";
	$adcl_2_e = "E";
	$adcl_3_s = "F";
	$adcl_4_s = "G";
	$adcl_5_s = "H";

	//	text
	$textheader = "รายการสั่งซื้อ บริษัท โชคชัยอินเตอร์เนชั่นแนล จำกัด";
	$textrecipient = "ผู้ลงรายการ : ";
	$textdate = "ลงเมื่อ ";
	$textcustomers = "ลูกค้า : ";
	//	text list orders 
	$or_number = "No.";
	$or_list = "รายการ";
	$or_qty = "จำนวน";
	$or_qprice = "ราคา:หน่วย";
	$or_price = "ราคารวม";
	$delivery = "ค่าจัดส่ง";
	$totalprice = "ราคารวมทั้งหมด";

	foreach($query as $row => $val){
		//	number
		$row++;
		//
		//	for bill claim price will be 0
		if($val->bill_claimtotalprice){
			$price = $val->bill_claimtotalprice;
			$nettotal = $val->bill_claimnettotal; 
		}else{
			$price = $val->bill_totalprice;
			$nettotal = $val->bill_nettotal; 
		}
		//
		
		/* $write_array[] = array(
							$row,
							date('d-m-Y',strtotime($val->bill_datetime)),
							$val->bill_code,
							$val->bill_name,
							$val->bill_promain,
							$val->bill_prolist,
							$val->bill_qty,
							$val->bill_totalprice,
							$val->bill_phone,
							$val->bill_address,
							$val->bill_textnumber,
							$price,
							$val->bill_parcel,
							$val->bill_delivery,
							$nettotal,
							$val->bill_remark
						); */
		$write_array[] = array(
							"id"		=> $row,
							"code"		=> $val->bill_code,
							"name"		=> $val->bill_name,
							"address"		=> $val->bill_address,
							"citizen"		=> $val->bill_textnumber,
							"product"		=> $val->product,
							"qty"			=> $val->bill_qty,
							"qproduct"		=> $val->product_qty,
							"price"			=> $val->product_price,
							"totalprice"	=> $val->bill_totalprice,
							"delivery"		=> $val->bill_delivery,
							"netprice"		=> $val->bill_nettotal,
							"recive"		=> $val->recive,
							"date"			=> $val->bill_datetime
						);
	}

	
	#	group order
	$order = unique_multidim_array($write_array,'code');
	
	#	find product in order
	foreach($order as $row => $val){
		$arr_groupcode[] = array_keys(array_column($write_array, 'code'),$val['code']);
	}
	#
	#	new index
	#	code
	$code = array();
	foreach($order as $row => $val){
		array_push($code,$val);
	}
	
	/* foreach($arr_group as $row => $val){
		echo "<br>";
		echo $row."---";
		foreach($val as $key){
			echo "[".$key."],";
		}
	}
	die(); */
	/* echo "<pre>";
	print_r($write_array);
	echo "</pre>";die(); */
	$spreadsheet = new Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	// $spreadsheet->getActiveSheet()->fromArray($write_array,NULL,'A1');
	
	#	set dimention
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth($set_cl_width);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth($set_cl_width);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth($set_cl_width);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth($set_cl_width);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth($set_cl_width);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth($set_cl_width);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth($set_cl_width);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth($set_cl_width);
	
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
	$styleTitle = [
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
	$nextrow = 0;
	
	foreach($arr_groupcode as $row => $val){
		
	#
	#	date bill start
	// $datetime = date('Y-m-d',strtotime($code[$val]['date']));
	
	#
	#	invoice
	$invoice = $code[$row]['code'];
	
	#	customer
	$customer = $code[$row]['name'];
	
	#	recive
	$recive = $code[$row]['recive'];
	
	#	recivea date
	$date = $code[$row]['date'];
	#--------------------------------------------------------------------------#
	#	---	---	BEGIN HEADER	---	---
	#	title
	#	next row
	$nextrow = $nextrow+1;
	/* #	creat text title
	$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	$ritch_style = $richText->createTextRun($textheader);
	$ritch_style->getFont()->setBold(true);
	$ritch_style->getFont()->setSize($fontsize);
	#
	#	mergeCells
	$spreadsheet->getActiveSheet()->mergeCells($cl_s.$nextrow.':'.$cl_e.$nextrow);
	#
	#	set height
	$spreadsheet->getActiveSheet()->getRowDimension($nextrow)->setRowHeight($set_rw_height);
	#
	#	insert text
	$spreadsheet->getActiveSheet()->getCell($cl_1_s.$nextrow)->setValue($richText);
	#
	#	change style
	$spreadsheet->getActiveSheet()->getStyle($cl_1_s.$nextrow)->applyFromArray($styleHead); */
	
	#	next row
	$nextrow = $nextrow+1;
	$begin = $nextrow;
	#	
	#	creat text customer name
	$cusText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	$cus_style = $cusText->createTextRun($textcustomers);
	$cus_style->getFont()->setBold(true);
	$cus_style->getFont()->setSize($fontsize);
	$cusText->createText($customer);
	#	creat text customer name
	$cusInv = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	$cus_style = $cusInv->createTextRun($invoice);
	$cus_style->getFont()->setSize($fontsize);
	#
	#	mergeCells
	$spreadsheet->getActiveSheet()->mergeCells($cl_1_s.$nextrow.':'.$cl_1_e.$nextrow);
	$spreadsheet->getActiveSheet()->mergeCells($cl_2_s.$nextrow.':'.$cl_2_e.$nextrow);
	#
	#	insert text
	$spreadsheet->getActiveSheet()->getCell($cl_1_s.$nextrow)->setValue($cusText);
	$spreadsheet->getActiveSheet()->getCell($cl_2_s.$nextrow)->setValue($cusInv);
	#
	#	set alignment
	$spreadsheet->getActiveSheet()->getStyle($cl_2_s.$nextrow)
		->getAlignment()
		->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	
	#	next row
	$nextrow = $nextrow+1;
	#	
	#	creat text recive name
	$rcText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	$rc_style = $rcText->createTextRun($textrecipient);
	$rc_style->getFont()->setBold(true);
	$rc_style->getFont()->setSize($fontsize);
	$rcText->createText($recive);
	#	creat text recive date
	$rcInv = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	$rc_style = $rcInv->createTextRun($textdate);
	$rc_style->getFont()->setBold(true);
	$rc_style->getFont()->setSize($fontsize);
	$rcInv->createText(thai_date($date));
	#
	#	mergeCells
	$spreadsheet->getActiveSheet()->mergeCells($cl_1_s.$nextrow.':'.$cl_1_e.$nextrow);
	$spreadsheet->getActiveSheet()->mergeCells($cl_2_s.$nextrow.':'.$cl_2_e.$nextrow);
	#
	#	insert text
	$spreadsheet->getActiveSheet()->getCell($cl_1_s.$nextrow)->setValue($rcText);
	$spreadsheet->getActiveSheet()->getCell($cl_2_s.$nextrow)->setValue($rcInv);
	#
	#	set alignment
	$spreadsheet->getActiveSheet()->getStyle($cl_2_s.$nextrow)
		->getAlignment()
		->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	
	#
	#	set border
	$spreadsheet->getActiveSheet()->getStyle($cl_s.$begin.':'.$cl_e.$nextrow)->applyFromArray($styleBorder);
	#--------------------------------------------------------------------------#
	#	---	---	BEGIN LIST	---	---
	#	list product
	#	next row
	$nextrow = $nextrow+1;
	$begin_list = $nextrow;
	#
	#	mergeCells
	$spreadsheet->getActiveSheet()->mergeCells($adcl_2_s.$nextrow.':'.$adcl_2_e.$nextrow);
	#
	#	insert text
	$spreadsheet->getActiveSheet()->getCell($adcl_1_s.$nextrow)->setValue($or_number);
	$spreadsheet->getActiveSheet()->getCell($adcl_2_s.$nextrow)->setValue($or_list);
	$spreadsheet->getActiveSheet()->getCell($adcl_3_s.$nextrow)->setValue($or_qty);
	$spreadsheet->getActiveSheet()->getCell($adcl_4_s.$nextrow)->setValue($or_qprice);
	$spreadsheet->getActiveSheet()->getCell($adcl_5_s.$nextrow)->setValue($or_price);
	#
	#	set style
	$spreadsheet->getActiveSheet()->getStyle($cl_s.$nextrow.':'.$cl_e.$nextrow)->applyFromArray($styleTitle);
		$no = 1;
		foreach($val as $root => $key){
			#	list product
			#	next row
			$nextrow = $nextrow+1;
			#
			#	mergeCells
			$spreadsheet->getActiveSheet()->mergeCells($adcl_2_s.$nextrow.':'.$adcl_2_e.$nextrow);
			#
			#	insert text
			$spreadsheet->getActiveSheet()->getCell($adcl_1_s.$nextrow)->setValue($no);
			$spreadsheet->getActiveSheet()->getCell($adcl_2_s.$nextrow)->setValue($write_array[$key]['product']);
			$spreadsheet->getActiveSheet()->getCell($adcl_3_s.$nextrow)->setValue($write_array[$key]['qty']);
			$spreadsheet->getActiveSheet()->getCell($adcl_4_s.$nextrow)->setValue($write_array[$key]['qproduct']);
			$spreadsheet->getActiveSheet()->getCell($adcl_5_s.$nextrow)->setValue($write_array[$key]['price']);
			#
			#	set alignment
			$spreadsheet->getActiveSheet()->getStyle($adcl_1_s.$nextrow)
				->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$no++;
		}
	#	list delivery
	#	next row
	$nextrow = $nextrow+1;
	#
	#	mergeCells
	$spreadsheet->getActiveSheet()->mergeCells($adcl_1_s.$nextrow.':'.$adcl_2_e.$nextrow);
	$spreadsheet->getActiveSheet()->mergeCells($adcl_3_s.$nextrow.':'.$adcl_5_s.$nextrow);
	#
	#	insert text
	$spreadsheet->getActiveSheet()->getCell($adcl_1_s.$nextrow)->setValue($delivery);
	$spreadsheet->getActiveSheet()->getCell($adcl_3_s.$nextrow)->setValue($code[$row]['delivery']);
	#
	#	set style
	$spreadsheet->getActiveSheet()->getStyle($cl_s.$nextrow.':'.$cl_e.$nextrow)->applyFromArray($styleListmoney);
	#	list product total
	#	next row
	$nextrow = $nextrow+1;
	#
	#	mergeCells
	$spreadsheet->getActiveSheet()->mergeCells($adcl_1_s.$nextrow.':'.$adcl_2_e.$nextrow);
	$spreadsheet->getActiveSheet()->mergeCells($adcl_3_s.$nextrow.':'.$adcl_5_s.$nextrow);
	#
	#	insert text
	$spreadsheet->getActiveSheet()->getCell($adcl_1_s.$nextrow)->setValue($totalprice);
	$spreadsheet->getActiveSheet()->getCell($adcl_3_s.$nextrow)->setValue($code[$row]['netprice']);
	#
	#	set style
	$spreadsheet->getActiveSheet()->getStyle($cl_s.$nextrow.':'.$cl_e.$nextrow)->applyFromArray($styleListmoney);
	#
	#	set border
	$spreadsheet->getActiveSheet()->getStyle($cl_s.$begin_list.':'.$cl_e.$nextrow)->applyFromArray($styleBorder);
	#--------------------------------------------------------------------------#
	#	---	---	END CUST	---	---
	#	space
	#	next row
	$nextrow = $nextrow+1;
	#
	#	set border
	$spreadsheet->getActiveSheet()->getStyle($cl_s.$begin_list.':'.$cl_e.$nextrow)->applyFromArray($styleBorderOut);
	#	set break page
	// $spreadsheet->getActiveSheet()->setBreak($cl_s.$nextrow, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
	}

	
	#
	#--------------------------------------------------------------------------#
	#	---	---	END LIST	---	---
	
	#	set default row dimension
	// $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
	
	#	set sheet name
	$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));
	
	#	protection
	$spreadsheet->getActiveSheet()->getProtection()->setSheet(false);
	
	#	set break page
	// $spreadsheet->getActiveSheet()->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
	
	//
	//	setting
	$filename = "rp_billvat_".date('Y-m-d').".xlsx";
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