<?php
	require 'vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Writer;
	
	#
	#	setting
	#
	$filename = "rp_form_FLASH_".date('Y-m-d').".xlsx";
	
	$fontsize = 10;
	$fontfamily = 'Arial';
	$set_cl_width = 15;
	$set_rw_height = 15;
	$startrow = 1;
	
	/* $data_array[] = array(
							"OrderCode",
							"OrderDate",
							"ShipperCode",
							"ShipperName",
							"ShipperAddress",
							"ShipperZipcode",
							"ShipperTel",
							"DeliveryDate",
							"CustomerCode",
							"CustomerName",
							"DeliveryAddress",
							"Zipcode",
							"ContactName",
							"Tel",
							"Note",
							"Total Boxes",
							"Weight Kgs",
							"Total CBM",
							"COD Amount",
							"TrackingNumber"
						); */
	
	$data_array[] = array(
							"Customer_order_number",
							"Consignee_name",
							"Address",
							"Postal_code",
							"Phone_number",
							"Phone_number2",
							"COD",
							"Item_type",
							"Weight_kg",
							"Length",
							"Width",
							"Height",
							"Freight_insurance",
							"Value_insurance",
							"Declared_value",
							"Speed_service",
							"Remark1",
							"Remark2",
							"Remark3"
						);
	
	foreach($query as $row => $val){
		#	datetime
		$datetime = date_indent(date('Y-m-d',strtotime($val->bill_datetime)),"/");
		$deliverydate = $datetime;
		
		# cal zipcode
		if($val->bill_zipcode){
			$zipcode = $val->bill_zipcode;
		}else{
			$zipcode = substr($val->bill_address,-5);
		}
		
		
		#	setting form
		$shipcode = "100951";
		$shipname = "Chokchai international Co.,LTD.";
		$shipaddress = "บริษัท โชคชัยอินเตอร์เนชั่นแนล จำกัด  294 หมู่ 8 ถนนวิภาวดี-รังสิต ตำบลคูคต อำเภอลำลูกกา จังหวัดปทุมธานี 12130";
		$shipzipcode = "12130";
		$shiptel = "0-2532-2846-8";
		$totalbox = 1;

		if($val->billstatus == "C"){
			$cod = $val->bill_nettotal;
		}else{
			$cod = "";
		}

		$tracking = "";
		
		#
		#	product list
		// $productcode = $this->mdl_sentformems->get_codeProduct($val->bill_prolist);
		$product = $this->mdl_sentformems->get_codeProduct($val->bill_id);
		// $product = $productcode."=".$val->bill_qty;
	
		$write_array[] = array(
							"code"				=> $val->bill_code,
							"name"				=> $val->bill_name,
							"address"			=> $val->bill_address,
							"zipcode"			=> $zipcode,
							"tel"				=> $val->bill_phone,
							"tel2"				=> "",
							"amount"			=> $cod,
							"type"				=> "",
							"weight"			=> $totalbox,
							"length"			=> "",
							"width"				=> "",
							"height"			=> "",
							"Freight_insurance"			=> "",
							"Value_insurance"			=> "",
							"Declared_value"			=> "",
							"Speed_service"			=> "",
							"product"			=> $product,
							"Remark2"			=> "",
							"Remark3"			=> ""
							
						);
	}
	
	#	group order
	$order = unique_multidim_array($write_array,'code');
	
	#	find product in order
	foreach($order as $row => $val){
		$arr_groupcode[] = array_keys(array_column($write_array, 'code'),$val['code']);
	}
	
	#	new index
	#	order
	$code = array();
	foreach($order as $row => $val){
		array_push($code,$val);
	}
	
	#
	#	push data to data array
	foreach($code as $row => $val){
		#
		#	merce array product code
		$productcodelist = "";
		foreach($arr_groupcode[$row] as $key => $vals){

			$productcodelist .= $write_array[$vals]['product'];
			if($key != count($arr_groupcode[$row])-1){
				$productcodelist .= ",";
			}
		}
		
		$data_array[] = array(
							$val['code'],
							$val['name'],
							$val['address'],
							$val['zipcode'],
							$val['tel'],
							$val['tel2'],
							$val['amount'],
							$val['type'],
							$val['weight'],
							$val['length'],
							$val['width'],
							$val['height'],
							$val['Freight_insurance'],
							$val['Value_insurance'],
							$val['Declared_value'],
							$val['Speed_service'],
							$productcodelist,
							$val['Remark2'],
							$val['Remark3']

						);
	}
	
	/* echo "<pre>";
	print_r($arr_groupcode);
	echo "</pre>";die(); */
	
	
	#	set call header excel
	$spreadsheet = new Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	
	#	set default excel
	$spreadsheet->getDefaultStyle()->getFont()->setName($fontfamily);
	$spreadsheet->getDefaultStyle()->getFont()->setSize(10);
	
	$spreadsheet->getActiveSheet()->fromArray($data_array,NULL,'A1');
	
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
	
	#	set row
	// $nextrow1 = $last_row+1;
	
	#	set sheet name
	$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));
	
	#	protection
	$spreadsheet->getActiveSheet()->getProtection()->setSheet(false);
	
	//
	//	to export excel
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