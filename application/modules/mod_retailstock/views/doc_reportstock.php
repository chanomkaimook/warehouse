<?php
	require 'vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Writer;
	
	$write_array[] = array(
							"#",
							"ID",
							"สินค้า",
							"คงคลัง",
							"จำหน่าย",
							"รับเข้า",
							"เคลม",
							"สูญเสีย",
							"รีเเพ็ค",
							"อื่นๆ",
							"เหลือ",
							"ราคา",
							"ราคารวมคงเหลือ",
							"ขาย-7 วันล่าสุด",
							"หมายเหตุ",
							"วันอัพเดต",
							"ผู้อัพเดต"
						);
	
	$row = 1;
	if($array){
		foreach($array as $key => $val){

			$write_array[] = array(
				$row,
				$array[$key]['p_id'],
				$array[$key]['p_name'],
				$array[$key]['start'],
				$array[$key]['cut'],
				$array[$key]['pull'],
				$array[$key]['claim'],
				$array[$key]['loss'],
				$array[$key]['repack'],
				$array[$key]['other'],
				$array[$key]['total'],
				$array[$key]['price'],
				$array[$key]['total_stockprice'],
				$array[$key]['cut_week'],
				$array[$key]['other_remark'],
				$array[$key]['date_update'],
				$array[$key]['user_update']
			);
			
			$row++;
		}
	}

	$spreadsheet = new Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	$spreadsheet->getActiveSheet()->fromArray($write_array,NULL,'A1');
	
	#	number format(#,###.00)
	$spreadsheet->getActiveSheet()->getStyle('M1:M'.$row)->getNumberFormat()
	->setFormatCode('#,##0');

	#	set sheet name
	$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));
	
	#	protection
	$spreadsheet->getActiveSheet()->getProtection()->setSheet(false);
	
	
	//
	//	setting
	$filename = "rp_stock_".date('Y-m-d').".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0');
	// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: cache, must-revalidate');
	header('Pragma: public');
	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		// for clear bug when export file error extension not valid
	for ($i = 0; $i < ob_get_level(); $i++) {
	   ob_end_flush();
	}
	ob_implicit_flush(1);
	ob_clean();
	
	$writer->save('php://output'); 
?>