<?php
	require 'vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	
	$write_array[] = array(
							"id",
							"username",
							"activity",
							"point",
							"remark",
							"status",
							"runtime"
						);
	// echo  count($query)."<br>"."<pre>";print_r($query);echo "</pre>";die();
	foreach($query as $row => $val){
		//	number
		$row++;
		//	status
		$user_status = report_statusOn($val->STATUS);
		
		$write_array[] = array(
							$row,
							$val->username,
							$val->transectionmethod,
							$val->transectionpoint,
							$val->remark,
							$val->transectionstatus,
							$val->trans_datetime
						);
	}
	// echo  count($write_array)."<br>"."<pre>";print_r($write_array);echo "</pre>";die();
	//	get print report
	$filename = "rp_act_".date('Y-m-d').".xlsx";

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