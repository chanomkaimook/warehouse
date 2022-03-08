<?php
	require 'vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	
	$write_array[] = array(
							"id",
							"name_th",
							"detail_th",
							"point",
							"จำกัดจำนวนครั้ง",
							"จำกัดจำนวนครั้ง(ต่อคน)",
							"วันเริ่ม",
							"วันหมด",
							"remark",
							"creat",
							"staffcreat",
							"status"
						);
	// echo  count($query)."<br>"."<pre>";print_r($query);echo "</pre>";die();
	foreach($query as $row => $val){
		//	number
		$row++;
		//	status
		$user_status = report_statusOn($val->STATUS);
		
		$write_array[] = array(
							$row,
							$val->NAME_TH,
							$val->DETAIL_TH,
							$val->POINT,
							$val->COUNTPICK,
							$val->COUNTPICKUSER,
							$val->PRO_START,
							$val->PRO_END,
							$val->REMARK_TH,
							$val->DATE_STARTS,
							$val->USER_STARTS,
							$user_status
						);
	}
	// echo  count($write_array)."<br>"."<pre>";print_r($write_array);echo "</pre>";die();
	//	get print report
	$filename = "rp_promotion_".date('Y-m-d').".xlsx";

		$fileName = $filename;
		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0);
		$spreadsheet->getActiveSheet()->fromArray($write_array,NULL,'A1');
		$spreadsheet->getActiveSheet()->setTitle(date('Y-m-d'));

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$fileName.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	

?>