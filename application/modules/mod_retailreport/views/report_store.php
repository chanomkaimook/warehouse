<?php
	require 'vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Writer;
	
	$this->load->helper('excel_helper');

	//
	//	find total product column 
	$column_query = $this->mdl_report->columnProduct();
	$query_column = json_decode($column_query);
	//

	$write_array = array();
	$writenotprice_array = array();
	$arraycolumn = array();
	$arraycolumnid = array();
	$inarray = array();
	
	array_push($arraycolumn,"ลำดับ");
	array_push($arraycolumn,"วันทำรายการ");
	
	// foreach($query_column->result() as $subrow){
	foreach($query_column->query as $subrow){
		array_push($arraycolumn,$subrow->product_list);
		array_push($arraycolumn,"-1");
		array_push($arraycolumn,"-2");
		array_push($arraycolumnid,$subrow->product_id);
	}
	$row++;
	array_push($write_array,$arraycolumn);

	//
	//
	foreach($query as $resultrow => $val){
		array_push($inarray,array(
									'datestarts' 	=> date('d-m-Y',strtotime($val->bill_datetime)),
									'bill_code' 	=> $val->bill_code,
									'promainid' 	=> $val->mainid,
									'prolistid' 	=> $val->listid,
									'billstatus' 	=> $val->billstatus,
									'price' 		=> $val->price,
									'qty' 			=> $val->quantity
							)
					);
	}

	//
	//	list detail data
	$byGroup = array_group_by("datestarts", $inarray);
	foreach($byGroup as $key => $val){
		$i++;
		$arraydata = array();
		$arraydatanotprice = array();
		$arraydataprice = array();
		// echo $key."=";
		$arraydata[] = $i;
		$arraydata[] = $key;
		//	column
		foreach($arraycolumnid as $key1 => $val1){
			
			$total = 0;
			$total_process = 0;	//	calculate product
			$totalnotprice = 0;
			$price = 0;
			$pricecal = 0;
			foreach($val as $key2 => $val2){	// is array
				
				// echo $val1." v1---".$val2['prolistid']." price:".$val2['price']." V2<br>";
				//
				//	for item have price 
				// if($val1 == $val2['prolistid'] && $val2['price'] > 0){
				if($val1 == $val2['prolistid']){
					$total += $val2['qty'];
					$total_process += $val2['qty'];

					// check ยอดรับเข้า หากมีให้นำมาลบยอดรวม
					$sqlt = $this->db->select('retail_receivedetail.quantity as rtd_qty')
					->from('retail_receive')
					->join('retail_receivedetail','retail_receive.id=retail_receivedetail.receive_id','left')
					->where('retail_receive.rt_bill_code',$val2['bill_code'])
					->where('retail_receive.complete',2)
					->where('retail_receive.status',1)
					->where('retail_receivedetail.prolist_id',$val2['prolistid'])
					->where('retail_receivedetail.status',1);
					$qt = $sqlt->get();
					$numt = $qt->num_rows();
					// echo $val2['bill_code'].":".$val2['prolistid'].":::".$numt."<br>";
					if($numt){
						$rowt = $qt->row();
						$quantity = $total - $rowt->rtd_qty;
						// $total = $quantity." (".$total." - ".$rowt->rtd_qty.")"; 
						$total = $quantity; 
					}
				}
				
			}


			$pricecal = "";


			array_push($arraydata,get_valueNullToNull($total));

			array_push($arraydata,get_valueNullToNull($totalnotprice));
			
			array_push($arraydata,$pricecal);

		}

		array_push($write_array,$arraydata);

		array_push($writenotprice_array,$arraydatanotprice);
		/* echo "<pre>";
		print_r($arraydata);
		echo $key;
		echo "</pre>"; */

		$row++;
	}

	/* echo "<pre>";
	print_r($write_array);
	echo "</pre>";
	exit; */
	
	//
	//	setting
	//	* 0=A, 1=B, 2=C...
	$totalarray = count($arraydata) - 1;
	$column_startsum = 2;
	$totalarray_sum = $totalarray-$column_startsum;
	$columnfirst = get_columnExcelNameFromNumber(0);
	$columnlast = get_columnExcelNameFromNumber($totalarray);

	$last_row = $row;
	$insertrow = 0;
	$last_row = $last_row + $insertrow;
	/* echo  count($write_array)."<br> last row:".$last_row."<br>"."<pre>";
	print_r($write_array);echo "</pre>";die(); */
	//	
	//	set style
	$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
	
	$spreadsheet = new Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	$spreadsheet->getActiveSheet()->fromArray($write_array,NULL,$columnfirst.'1');

	//
	//	insert row on sheet excel
	$spreadsheet->getActiveSheet()->insertNewRowBefore(2, 1);
	$last_row = $last_row+1;

	//
	//	insert column on sheet excel
	$totalarrayaftermerce = $totalarray*3;	//	*3 because add 3 column on product
	$totalarrayaftermerce = $totalarray_sum+2;
	$columnlastmerce = get_columnExcelNameFromNumber($totalarrayaftermerce);
	
	for($col=$column_startsum;$col<=$totalarrayaftermerce;$col++){
		$nextcol = $col + 1;
		$col1 = $col;
		$col2 = $col+1;
		$col3 = $col+2;
		$col1_set = get_columnExcelNameFromNumber($col);
		$col2_set = get_columnExcelNameFromNumber($col2);
		$col3_set = get_columnExcelNameFromNumber($col3);
		#
		#	insert cell
		#	because add before
		// $spreadsheet->getActiveSheet()->insertNewColumnBefore(get_columnExcelNameFromNumber($nextcol), 2);

		$nextcol2 = $col + 2;
		$colmerce_start = get_columnExcelNameFromNumber($col);
		$colmerce_end = get_columnExcelNameFromNumber($nextcol2);

		#	
		#	creat text
		$titleText_normal = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
		$titleText_free = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
		$titleText_price = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
		$titleText_normal->createText('ปกติ');
		$spreadsheet->getActiveSheet()->getCell($col1_set.'2')->setValue($titleText_normal);
		$titleText_free->createText('Free');
		$spreadsheet->getActiveSheet()->getCell($col2_set.'2')->setValue($titleText_free);
		$titleText_price->createText('ราคา');
		$spreadsheet->getActiveSheet()->getCell($col3_set.'2')->setValue($titleText_price);

		#
		#	mergeCells
		$spreadsheet->getActiveSheet()->mergeCells($colmerce_start.'1:'.$colmerce_end.'1');

		$col = $col+2;
	}
	
	
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
	$spreadsheet->getActiveSheet()->getStyle($columnfirst.'1:'.$columnlastmerce.'1')->applyFromArray($styleHead);
	// $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	
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
	$spreadsheet->getActiveSheet()->getStyle($columnfirst.'1:'.$columnlastmerce.$last_row)->applyFromArray($styleBorder);
	
	#	wraptext (show text non-over column width)
	$spreadsheet->getActiveSheet()->getStyle('A1:'.$columnlastmerce.$last_row)->getAlignment()->setWrapText(true);
	
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
	/* $nextrow1 = $last_row+1;
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
		->setFormatCode('#,##0');
		
		$spreadsheet->getActiveSheet()->getStyle($keystart.''.$nextrow1)->applyFromArray($styleBorder);
	} */
	
	#	set sheet name
	$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));
	
	#	protection
	$spreadsheet->getActiveSheet()->getProtection()->setSheet(false);
	
	#	dimention
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);
	
	//
	//	setting
	$filename = "rp_Store_".date('Y-m-d').".xlsx";
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