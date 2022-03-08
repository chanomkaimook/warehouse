<?php

use phpDocumentor\Reflection\Types\Integer;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Receive
{
	public function __construct()
	{
		// Assign the CodeIgniter super-object
		$this->TBMAIN = 'retail_receive';
		$this->TBSUB = 'retail_receivedetail';
		$this->DIR = 'receive';
	}

	function genCodeBill()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$table = $this->TBMAIN;
		$codebill = 'FA';

		$sql = $ci->db->from($table)
			->where($table . '.code is not null')
			->where($table . '.code like "%' . date('Ym') . '%"');	// Ex. 202111
		$number = $sql->count_all_results(null, false);
		$q = $sql->get();

		$numbernext = $number + 1;
		$new_number = str_pad($numbernext, 4, '0', STR_PAD_LEFT);

		$gencode = $codebill . date('Ym') . "_" . $new_number;
		$result = $gencode;

		return $result;
	}

	//	add bill
	//	@param array[item]		@array 
	//		promain		@int 	=> promain id
	//		prolist		@int 	=> prolist id
	//		list		@int 	=> list id
	// 		name		@text 	=> product name 
	// 		qty			@int 	=> product quantity
	// 		price		@float 	=> product price
	// 		totalprice	@float 	=> product*quantity 
	//	@param array[billtype]			@int = type document [1=supplier,2=bill,3=ยืม]
	//	@param array[remark]		@text = bill remark
	//	@param array[bill_id]		@int = retail_bill id
	//	@param array[bill_code]		@int = retail_bill code
	//	@param array[sp_bill_name]		@text = retail_supplier name
	//	@param arrayfile		@array = $_FILES
	//

	function add_bill($array, $arrayfile)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$dataresult = array();
		$error = 1;
		$txt = 'ไม่สามารถทำรายการได้';

		//	check array
		$bill_id = get_valueNullToNull($array['bill_id']);
		$bill_code = get_valueNullToNull($array['bill_code']);
		$billtype = get_valueNullToNull($array['billtype']);
		$remark = get_valueNullToNull($array['remark']);

		$sp_bill_name = get_valueNullToNull($array['sp_bill_name']);

		$bill_textcode = get_valueNullToNull($array['bill_textcode']);

		//	check bill supplier online
		if ($sp_bill_name) {
			$sqlsp = $ci->db->select('id')
				->from('retail_supplier')
				->where('retail_supplier.code', $bill_code)
				->where('retail_supplier.complete', 1)
				->where('retail_supplier.status', 1);
			$qsp = $sqlsp->get();
			$numsp = $qsp->num_rows();
			if (!$numsp) {

				$txt = 'สถานะใบหลักปิดการรับสินค้า';
				$result = array(
					'error_code'	=> $error,
					'txt'			=> $txt
				);

				return $result;
			}
		}

		if ($array['complete']) {
			$complete = get_valueNullToNull($array['complete']);
		} else {
			$complete = 0;
		}
		$approve_store = null;
		$apst_date = null;
		$apst_user = null;

		//	check file image
		if (count($arrayfile)) {
			$checkfile = $this->check_img($arrayfile);
			if ($checkfile['error_code'] != 0) {
				$result = array(
					'error_code'	=> $checkfile['error_code'],
					'txt'			=> $checkfile['txt']
				);

				return $result;
			}
		}

		//	check array item zero
		if (!$array['item']) {
			$result = array(
				'error_code'	=> 1,
				'txt'			=> 'กรุณาระบุสินค้า'
			);

			return $result;
		}

		//	check array item zero
		if ($array['item']) {
			foreach ($array['item'] as $key => $val) {
				$qty = get_valueNullToNull($val['qty']);

				if ($qty <= 0 || $qty == null) {
					$result = array(
						'error_code'	=> 1,
						'txt'			=> 'มีจำนวนสินค้าไม่ถูกต้อง'
					);

					return $result;
				}
			}
		}

		//	check complete bill
		if ($complete == 2) {
			$approve_store = 1;
			$apst_date = date('Y-m-d H:i:s');
			$apst_user = $ci->session->userdata('useradminid');
		}

		$bill_sp = null;
		if ($billtype == 1) {			//	ถ้าเป็น suppier ให้เก็บข้อมูลเลข ID บิลที่ sp_bill_id แทน rt_id
			$bill_sp = $bill_id;
			$bill_id = null;
		}

		//	check supplier complete
		if($bill_sp){
			$arraybillsup = array(
				'bill_id'		=> $bill_sp,
				'item'			=> $array['item']
			);

			$ci->load->library('supplier');
			$check_supplier = $ci->supplier->check_complete($arraybillsup);
		}

		//	check issue complete กรณียืม
		$bill_is = null;
		if($billtype == 4){	//	4	= ใบเบิกยืม (อิงจาก table retail_receive)
			$bill_is = $bill_id;
			$is_bill_name = $bill_textcode;
			$bill_id = null;
			$arraybillissue = array(
				'bill_id'		=> $bill_is,
				'item'			=> $array['item']
			);

			//	check bill issue online
			if ($bill_is) {
				$sqlsp = $ci->db->select('id')
					->from('retail_issue')
					->where('retail_issue.id', $bill_is)
					->where('retail_issue.complete in (0,1)')
					->where('retail_issue.status', 1);
				$qsp = $sqlsp->get();
				$numsp = $qsp->num_rows();
				if (!$numsp) {

					$txt = 'สถานะใบหลักปิดการรับสินค้า';
					$result = array(
						'error_code'	=> $error,
						'txt'			=> $txt
					);

					return $result;
				}
			}

			$ci->load->library('issue');
			$check_supplier = $ci->issue->check_complete($arraybillissue);
		}

		//	 generate code
		$gencode = $ci->receive->genCodeBill();

		$datainsert = array(
			'rt_id'				=> $bill_id,
			'rt_bill_code'		=> $bill_code,
			'sp_bill_id'		=> $bill_sp,
			'sp_bill_name'		=> $sp_bill_name,
			'is_bill_id'		=> $bill_is,
			'is_bill_name'		=> $is_bill_name,
			'code'				=> $gencode,
			'complete'			=> $complete,
			'approve_store'		=> $approve_store,
			'apst_date'			=> $apst_date,
			'apst_user'			=> $apst_user,
			'billtype'			=> $billtype,
			'remark'		=> $remark,
			'date_starts'		=> date('Y-m-d H:i:s'),
			'user_starts'		=> $ci->session->userdata('useradminid')
		);

		$ci->db->insert($this->TBMAIN, $datainsert);
		$new_id = $ci->db->insert_id();
		if ($new_id) {

			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Insert " . $this->TBMAIN . " id:" . $new_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
			$type = "Insert";
			$arraylog = array(
				'log_id'  	 	 => $last_id,
				'detail'  		 => $detail,
				'logquery'       => $log_query,
				'type'     	 	 => $type,
				'date_starts'    => date('Y-m-d H:i:s')
			);
			updateLog($arraylog);

			if ($array['item']) {
				foreach ($array['item'] as $key => $val) {
					$promain = get_valueNullToNull($val['promain']);
					$prolist = get_valueNullToNull($val['prolist']);
					$list = get_valueNullToNull($val['list']);
					$qty = get_valueNullToNull($val['qty']);
					$totalprice = get_valueNullToNull(str_replace(',', '', $val['totalprice']));

					if ($qty && $promain && $prolist) {
						$setdetail[] = array(
							'code'				=> $gencode,
							'receive_id'		=> $new_id,
							'promain_id'		=> $promain,
							'prolist_id'		=> $prolist,
							'list_id'			=> $list,
							'quantity'			=> $qty,
							'total_price'		=> $totalprice,
							'date_starts'		=> date('Y-m-d H:i:s'),
							'user_starts'		=> $ci->session->userdata('useradminid')
						);
					}
				}
				$datainsertdetail = $setdetail;

				$ci->db->insert_batch($this->TBSUB, $datainsertdetail);
				// ============== Log_Detail ============== //
				$log_query = $ci->db->last_query();
				$last_id = $ci->session->userdata('log_id');
				$detail = "Insert " . $this->TBSUB . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
				$type = "Insert";
				$arraylog = array(
					'log_id'  	 	 => $last_id,
					'detail'  		 => $detail,
					'logquery'       => $log_query,
					'type'     	 	 => $type,
					'date_starts'    => date('Y-m-d H:i:s')
				);
				updateLog($arraylog);
			}

			//
			//	upload image
			if (count($arrayfile)) {
				$arraybill = array(
					'bill_id'	=> $new_id,
					'code'		=> $gencode
				);
				$uploadimage = $ci->receive->upload_img($arraybill, $arrayfile);
				//	upload error
				if ($uploadimage['error_code'] != 0) {
					$result = array(
						'error_code'	=> $error,
						'txt'			=> $uploadimage['txt']
					);

					return $result;
				}
			}

			$error = 0;
			$txt = 'เพิ่มใบรับเข้าสำเร็จ';

			//***** */
			

		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt,
			'data'			=> array(
				'new_id'	=> $new_id
			)
		);

		return $result;
	}

	//	update bill
	//	@param array[item]		@array 
	//		promain		@int 	=> promain id
	//		prolist		@int 	=> prolist id
	//		list		@int 	=> list id
	// 		name		@text 	=> product name 
	// 		qty			@int 	=> product quantity
	// 		price		@float 	=> product price
	// 		totalprice	@float 	=> product*quantity 
	//	@param array[bill_price]	@float = bill price
	//	@param array[bill_parcel]	@float = bill parcel
	//	@param array[bill_logis]	@float = bill delivery
	//	@param array[bill_shor]		@float = bill shor
	//	@param array[bill_discount]	@float = bill discount
	//	@param array[bill_tax]		@float = bill tax
	//	@param array[totalamount]	@float = bill total amount
	//	@param array[loss]			@int = product return [0=return,1=loss]
	//	@param array[remark]		@text = bill remark
	//	@param array[bill_id]		@int = retail_bill id
	//	@param array[bill_code]		@int = retail_bill code
	//	@param arrayfile		@array = $_FILES
	//

	function update_bill($array, $arrayfile)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$dataresult = array();
		$error = 1;
		$txt = 'ไม่สามารถทำรายการได้';

		//	check array
		$bill_id = get_valueNullToNull($array['bill_id']);
		$bill_code = get_valueNullToNull($array['bill_code']);
		$totalamount = get_valueNullToNull(_toFloat($array['totalamount']));
		$loss = get_valueNullToNull($array['loss']);
		$remark = get_valueNullToNull($array['remark']);

		//	check complete
		//	if loss == 1 and net total = 0 will bill success auto (complete = 2)
		/* $complete = 0;
		if ($totalamount == 0 && $loss == 1) {
			$complete = 2;
		} */

		//	check file image
		if (count($arrayfile)) {
			$checkfile = $this->check_img($arrayfile);
			if ($checkfile['error_code'] != 0) {
				$result = array(
					'error_code'	=> $checkfile['error_code'],
					'txt'			=> $checkfile['txt']
				);

				return $result;
			}
		}

		//	check item
		if (!$array['item']) {
			$result = array(
				'error_code'	=> 1,
				'txt'			=> 'ไม่มีรายการ'
			);

			return $result;
		}
		/* echo "<pre>";
		print_r($array);
		echo "</pre>";
		exit; */
		$dataupdate = array(
			'remark'		=> $remark,
			'date_update'		=> date('Y-m-d H:i:s'),
			'user_update'		=> $ci->session->userdata('useradminid')
		);
		$ci->db->where('retail_receive.id', $bill_id);
		$ci->db->update('retail_receive', $dataupdate);

		// ============== Log_Detail ============== //
		$log_query = $ci->db->last_query();
		$last_id = $ci->session->userdata('log_id');
		$detail = "Update receive id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
		$type = "Update";
		$arraylog = array(
			'log_id'  	 	 => $last_id,
			'detail'  		 => $detail,
			'logquery'       => $log_query,
			'type'     	 	 => $type,
			'date_starts'    => date('Y-m-d H:i:s')
		);
		updateLog($arraylog);

		if ($bill_id) {
			//	call bill product old
			$sqlold = $ci->db->select('id')
				->from('retail_receivedetail')
				->where('retail_receivedetail.receive_id', $bill_id)
				->where('retail_receivedetail.status', 1);
			$qold = $sqlold->get();
			$numold = $qold->num_rows();
			if ($numold) {
				foreach ($qold->result() as $rold) {
					$compare[] = $rold->id;
				}
			}
		}

		//	update product detail
		if ($array['item']) {
			foreach ($array['item'] as $key => $val) {
				$find_inarray = array_keys($compare, $val['iddetail']);
				if (!is_null($find_inarray[0])) {
					unset($compare[$find_inarray[0]]);
				}

				$iddetail = get_valueNullToNull($val['iddetail']);
				$promain = get_valueNullToNull($val['promain']);
				$prolist = get_valueNullToNull($val['prolist']);
				$list = get_valueNullToNull($val['list']);
				$qty = get_valueNullToNull($val['qty']);
				$totalprice = get_valueNullToNull($val['totalprice']);
				$status = 1;

				if ($qty <= 0) {
					$status = 0;
				}

				if ($qty  && $promain && $prolist) {
					$setdetail = array(
						'promain_id'		=> $promain,
						'prolist_id'		=> $prolist,
						'list_id'			=> $list,
						'quantity'			=> $qty,
						'total_price'		=> $totalprice,
						'date_update'		=> date('Y-m-d H:i:s'),
						'user_update'		=> $ci->session->userdata('useradminid'),
						'status'			=> $status
					);

					$ci->db->where('retail_receivedetail.id', $iddetail);
					$ci->db->update('retail_receivedetail', $setdetail);

					// ============== Log_Detail ============== //
					$log_query = $ci->db->last_query();
					$last_id = $ci->session->userdata('log_id');
					$detail = "Update receivedetail id:" . $iddetail . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
					$type = "Update";
					$arraylog = array(
						'log_id'  	 	 => $last_id,
						'detail'  		 => $detail,
						'logquery'       => $log_query,
						'type'     	 	 => $type,
						'date_starts'    => date('Y-m-d H:i:s')
					);
					updateLog($arraylog);
				}
			}

			//	update status 0 for detail id not paramiter
			if (count($compare)) {
				foreach ($compare as $key => $val) {
					$setoff = array(
						'status'			=> 0
					);

					$ci->db->where('retail_receivedetail.id', $val);
					$ci->db->update('retail_receivedetail', $setoff);

					// ============== Log_Detail ============== //
					$log_query = $ci->db->last_query();
					$last_id = $ci->session->userdata('log_id');
					$detail = "Update receivedetail id:" . $val . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
					$type = "Update";
					$arraylog = array(
						'log_id'  	 	 => $last_id,
						'detail'  		 => $detail,
						'logquery'       => $log_query,
						'type'     	 	 => $type,
						'date_starts'    => date('Y-m-d H:i:s')
					);
					updateLog($arraylog);
				}
			}

			//
			//	update status 0 for image delete
			$imagedel = $array['imagedel'];
			if ($imagedel && count($imagedel)) {
				foreach ($imagedel as $key => $val) {
					$setoff = array(
						'status'			=> 0
					);

					$ci->db->where('retail_receiveimg.id', $val);
					$ci->db->update('retail_receiveimg', $setoff);

					// ============== Log_Detail ============== //
					$log_query = $ci->db->last_query();
					$last_id = $ci->session->userdata('log_id');
					$detail = "Update retail_receiveimg id:" . $val . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
					$type = "Update";
					$arraylog = array(
						'log_id'  	 	 => $last_id,
						'detail'  		 => $detail,
						'logquery'       => $log_query,
						'type'     	 	 => $type,
						'date_starts'    => date('Y-m-d H:i:s')
					);
					updateLog($arraylog);
				}
			}
		}

		//
		//	upload image
		if (count($arrayfile)) {
			$arraybill = array(
				'bill_id'	=> $bill_id,
				'code'		=> $bill_code
			);
			$uploadimage = $this->upload_img($arraybill, $arrayfile);
			//	upload error
			if ($uploadimage['error_code'] != 0) {
				$result = array(
					'error_code'	=> $error,
					'txt'			=> $uploadimage['txt']
				);

				return $result;
			}
		}

		$error = 0;
		$txt = 'อัพเดตข้อมูลใบรับเข้าสำเร็จ';

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	//	upload image
	//	@param arraybill		@array 
	//		bill_id		@int 	=> bill id reference
	//	@param arrayimage		@array = $_FILES
	//
	function upload_img($arraybill, $arrayimage)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$ci->load->library('image');
		//	setting
		$uploadsDir = BASE_PIC . $this->DIR . "/";
		$allowedFileType = array('jpg', 'png', 'jpeg', 'JPG', 'gif', 'GIF');

		$error = 1;
		$txt = 'ไม่สามารถบันทึกไฟล์ได้';

		//	run upload
		$count = count($arrayimage);
		if ($count && count($arraybill)) {
			$array = array();
			$file = $arrayimage['file'];
			foreach ($file['name'] as $key => $val) {

				//	check file for upload
				if ($file['name'][$key]) {
					$img_type = $file['type'][$key];
					$img_name = $file['name'][$key];
					$img_tmp = $file['tmp_name'][$key];

					$img_err = $file['error'][$key];
					$img_size = $file['size'][$key];

					// create array FILE
					$imagefile = array(
						'type'		=> $img_type,
						'name'		=> $img_name,
						'tmp_name'	=> $img_tmp,
						'error'		=> $img_err,
						'size'		=> $img_size
					);

					//	new name iamge
					$new_image_name = $arraybill['code'] . "_" . $key . '_' . time();

					$targetPath = $uploadsDir . $new_image_name . '.' . explode("/", $img_type)[1];
					$fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

					if (in_array($fileType, $allowedFileType)) {
						$check_upload = $ci->image->uploadimage($targetPath, $imagefile);

						$txterror = "";
						if ($check_upload['status'] == 1) {	//	1 = upload success
							$datainsert = array(
								'receive_id '    =>  $arraybill['bill_id'],
								'path'      	 	=>  $check_upload['data'],
								'date_starts'   	=>  date('Y-m-d H:i:s')
							);
							$ci->db->insert('retail_receiveimg', $datainsert);
							$new_id = $ci->db->insert_id();
							if (!$new_id) {
								$txterror = 'ข้อมูลไม่ถูกบันทึก ' . $targetPath;
							} else {
								// ============== Log_Detail ============== //
								$log_query = $ci->db->last_query();
								$last_id = $ci->session->userdata('log_id');
								$detail = "Insert retail_receiveimg name:" . $check_upload['data'] . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
								$type = "Insert";
								$arraylog = array(
									'log_id'  	 	 => $last_id,
									'detail'  		 => $detail,
									'logquery'       => $log_query,
									'type'     	 	 => $type,
									'date_starts'    => date('Y-m-d H:i:s')
								);
								updateLog($arraylog);
							}
						}
						$error = 0;
						$txt = 'ทำรายการบันทึกไฟล์สำเร็จ ' . $txterror;
					}
				}
			}
		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	//	check file upload
	//	@param arrayimage		@array = $_FILES
	//
	function check_img($arrayimage)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$uploadsDir =  FCPATH . BASE_PIC . $this->DIR . "/";
		$allowedFileType = array('jpg', 'png', 'jpeg', 'JPG', 'gif', 'GIF');

		$error = 1;
		$txt = 'ไม่สามารถบันทึกไฟล์ได้';

		//	run upload
		$count = count($arrayimage);
		if ($count && $arrayimage) {
			$array = array();
			$file = $arrayimage['file'];
			foreach ($file['name'] as $key => $val) {

				//	check file for upload
				if ($file['name'][$key]) {
					$img_type = $file['type'][$key];
					$img_name = $file['name'][$key];
					$img_tmp = $file['tmp_name'][$key];

					//	new name iamge
					$new_image_name = 'sss_' . time();

					$targetPath = $uploadsDir . $new_image_name . '.' . explode("/", $img_type)[1];
					$fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

					if (in_array($fileType, $allowedFileType)) {
						$error = 0;
						$txt = 'success';
					} else {	//	error
						$error = 1;
						$txt = 'ไม่ใช่ไฟล์รูปภาพ';
					}
				}
			}
		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	function read_dataBill($bill_id)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$dataresult = array();
		$text = get_valueNullToNull($bill_id);
		$subdata = array();
		$dataresult = array();
		$result = "";

		$q = $ci->receive->get_dataBillDetail($text);
		$num = $q->num_rows();

		if ($num) {

			$result = $q;
		}

		return $result;
	}

	function read_bill($bill_id)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$dataresult = array();
		$text = get_valueNullToNull($bill_id);
		$subdata = array();
		$dataresult = array();
		$result = "";

		$q = $this->get_retailBill($text);
		$num = $q->num_rows();

		if ($num) {

			$result = $q;
		}

		return $result;
	}

	function read_billSearch($textsearch)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$dataresult = array();
		$text = get_valueNullToNull($textsearch);
		$subdata = array();
		$dataresult = array();
		$result = "";

		$q = $this->get_retailSearchBill($text);
		$num = $q->num_rows();

		if ($num) {
			foreach ($q->result() as $r) {
				$subdata['id'] = $r->rt_id;
				$subdata['code'] = $r->rt_code;
				$subdata['name'] = $r->rt_name;
				$dataresult[] = $subdata;
			}

			$result = $dataresult;
		}

		return $result;
	}

	function read_SupplierSearch($textsearch)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$dataresult = array();
		$text = get_valueNullToNull($textsearch);

		//	find bill supplier
		$ci->load->library('supplier');
		$result = $ci->supplier->read_billSearch($text);

		return $result;
	}

	//	check store approve
	//	@param array		@array 
	//		bill_id		@int 	=> bill creditnote id
	//		approve		@int 	=> creditnote approve
	//
	function confirmStore($array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$bill_id = get_valueNullToNull($array['bill_id']);
		$approve = get_valueNullToNull($array['approve']);

		$error = 1;
		$txt = 'ไม่สามารถอนุมัติได้';

		$sql = $ci->db->select(
			$this->TBMAIN . ".cn_bill_id as cn_bill_id,"
		)
			->from($this->TBMAIN)
			->where($this->TBMAIN . '.id', $bill_id)
			->where($this->TBMAIN . '.status', 1);
		$q = $sql->get();
		$num = $q->num_rows();

		if ($bill_id && $num) {
			$row = $q->row();

			if (!$approve) {
				$complete = 0;
			} else {
				$complete = 2;
			}

			/* echo "<pre>";
			echo "<br>approve :".$approve;
			echo "<br>complete :".$complete;
			echo "<br>id :".$bill_id;
			echo "<br>cn id :".$row->cn_bill_id;
			print_r($array);
			echo "</pre>";exit; */

			//	run upload
			$txterror = "";
			$dataupdate = array(
				'approve_store'    	=>  $approve,
				'complete'    	=>  $complete,
				'apst_date'   	=>  date('Y-m-d H:i:s'),
				'apst_user'   	=>  $ci->session->userdata('useradminid')
			);
			$ci->db->where($this->TBMAIN . '.id', $bill_id);
			$ci->db->update($this->TBMAIN, $dataupdate);
			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Update " . $this->TBMAIN . " approve store id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
			$type = "Update";
			$arraylog = array(
				'log_id'  	 	 => $last_id,
				'detail'  		 => $detail,
				'logquery'       => $log_query,
				'type'     	 	 => $type,
				'date_starts'    => date('Y-m-d H:i:s')
			);
			updateLog($arraylog);

			// check update for create credit code report
			if ($num) {
				$cn_id = $row->cn_bill_id;

				$ci->load->library('creditnote');
				$setarray = array(
					'bill_id'	=> $cn_id,
					'approve'	=> $approve
				);
				$ci->creditnote->confirmStore($setarray);
			}

			$error = 0;
			$txt = 'ทำรายการสำเร็จ ';
		} else {
			$txt .= ' ไม่พบเลข Bill';
		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	//	cancel bill creditnote
	//	@param array		@array 
	//		bill_id		@int 	=> bill receive id
	//		remark_order		@text 	=> bill remark delete
	//
	function cancelBill($array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$bill_id = get_valueNullToNull($array['bill_id']);
		$remark_order = get_valueNullToNull($array['remark_order']);

		$error = 1;
		$txt = 'ไม่สามารถยกเลิกได้';

		if ($bill_id) {

			//	run upload
			$txterror = "";
			$dataupdate = array(
				'complete'    	=>  3,
				'remark_order'    	=>  $remark_order,
				'date_update'   	=>  date('Y-m-d H:i:s'),
				'user_update'   	=>  $ci->session->userdata('useradminid')
			);
			$ci->db->where($this->TBMAIN . '.id', $bill_id);
			$update = $ci->db->update($this->TBMAIN, $dataupdate);

			if($update){
				//	หา supplier id
				$sqlr = $ci->db->select(
					'retail_receive.sp_bill_id as sp_bill_id,'.
					'retail_receive.is_bill_id as is_bill_id'
				)
					->from('retail_receive')
					->join('retail_receivedetail','retail_receive.id=retail_receivedetail.receive_id','left')
					->where('retail_receive.id', $bill_id)
					->where('retail_receive.status', 1);
				$qr = $sqlr->get();
				$numr = $qr->num_rows();
				if ($numr) {
					$array_result = array();
					$rr = $qr->row();
					$bill_sp = $rr->sp_bill_id;

					$bill_is = $rr->is_bill_id;

					if($bill_sp){
						$arraybillsup = array(
							'bill_id'		=> $bill_sp,
							'item'			=> array()
						);
						$ci->load->library('supplier');
						$check_supplier = $ci->supplier->check_complete($arraybillsup);	
					}

					if($bill_is){
						$arraybillissue = array(
							'bill_id'		=> $bill_is,
							'item'			=> array()
						);
						$ci->load->library('issue');
						$check_issue = $ci->issue->check_complete($arraybillissue);	
					}
					
				}
			}
			

			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "cancel " . $this->TBMAIN . " id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
			$type = "Update";
			$arraylog = array(
				'log_id'  	 	 => $last_id,
				'detail'  		 => $detail,
				'logquery'       => $log_query,
				'type'     	 	 => $type,
				'date_starts'    => date('Y-m-d H:i:s')
			);
			updateLog($arraylog);

			$error = 0;
			$txt = 'ยกเลิกรายการสำเร็จ ';
		} else {
			$txt .= ' ไม่พบเลข Bill';
		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	//
	//	==================================================
	//		SQL 
	//	==================================================
	//
	//	find image
	function get_image($billid)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$table = "retail_receiveimg";
		$tablefield = "receive_id";

		//	check path image
		$sqlcheck = $ci->db->select($this->TBMAIN . '.cn_bill_id')
			->from($this->TBMAIN)
			->where($this->TBMAIN . '.id', $billid);
		$qcheck = $sqlcheck->get();
		$numqcheck = $qcheck->num_rows();
		if ($numqcheck) {
			$rowcheck = $qcheck->row();
			if ($rowcheck->cn_bill_id) {
				$table = "retail_creditnoteimg";
				$tablefield = "creditnote_id";

				$billid = $rowcheck->cn_bill_id;
			}
		}

		$ci->db->select(
			$table . ".id as cni_id," .
				$table . ".path as cni_path"
		);
		$ci->db->from($table);
		$ci->db->where($table . '.status', 1);
		$ci->db->where($table . '.' . $tablefield, $billid);
		$q = $ci->db->get();
		$num = $q->num_rows();

		if ($num) {
			$result = $q;
		} else {
			$result = "";
		}

		return $result;
	}

	function get_dataBillDetail($bill_id)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$arrayselect = array(
			$this->TBMAIN . '.id as cn_id',
			$this->TBMAIN . '.code as cn_code',
			$this->TBMAIN . '.rt_bill_code as cn_rt_bill_code',
			$this->TBMAIN . '.cn_bill_id as cn_bill_id',
			$this->TBMAIN . '.cn_bill_code as cn_bill_code',
			$this->TBMAIN . '.sp_bill_id as cn_supplier_id',
			$this->TBMAIN . '.sp_bill_name as cn_sp_bill_name',
			$this->TBMAIN . '.is_bill_id as is_bill_id',
			$this->TBMAIN . '.is_bill_name as is_bill_name',
			$this->TBMAIN . '.approve as cn_approve',
			$this->TBMAIN . '.approve_store as cn_approve_store',
			$this->TBMAIN . '.complete as cn_complete',
			$this->TBMAIN . '.billtype as cn_billtype',
			$this->TBMAIN . '.remark as cn_remark',
			$this->TBMAIN . '.remark_order as cn_remark_order',
			$this->TBMAIN . '.date_starts as cn_date_starts',
			$this->TBMAIN . '.user_starts as cn_user_starts',
			$this->TBMAIN . '.date_update as cn_date_update',
			$this->TBMAIN . '.user_update as cn_user_update',

			$this->TBMAIN . '.appr_date as cn_appr_date',
			$this->TBMAIN . '.appr_user as cn_appr_user',
			$this->TBMAIN . '.apst_date as cn_apst_date',
			$this->TBMAIN . '.apst_user as cn_apst_user',

			$this->TBSUB . '.id as cnd_id',
			$this->TBSUB . '.promain_id as cnd_productmain',
			$this->TBSUB . '.prolist_id as cnd_productid',
			$this->TBSUB . '.list_id as cnd_productlist',
			$this->TBSUB . '.quantity as cnd_qty',
			$this->TBSUB . '.total_price as cnd_price',

			'retail_bill.code as rt_code',
			'retail_bill.name as rt_name',
			'retail_bill.phone_number as rt_tel',
			'retail_bill.text_number as rt_citizen',
			'retail_bill.address as rt_address',
			'retail_bill.zipcode as rt_zipcode',

			'retail_productlist.name_th as rtp_name',
			'retail_productlist.price as rtp_price',

			'staff.name as sf_name',
			'staff.name_th as sf_nameth',
			'staff.lastname as sf_lastname',
			'staff.lastname_th as sf_lastnameth',
		);
		$arraywhere = array(
			$this->TBMAIN . '.id' 					=> $bill_id,
			$this->TBMAIN . '.status' 					=> 1
		);

		$q = $this->query_databilldetail($arrayselect, $arraywhere, null);
		$result = $q;

		return $result;
	}

	// query detail
	//	@param array[group_by]	@text = group by
	//	@param array[order_by]	@array = order by text 
	//	@param array[limit]		@array = [total[int] = total show , start[int] = start point on data]
	// function query_retailbill($arrayselect, $arraywhere, $arrayorderby, $arraylimit)
	function query_databilldetail($arrayselect, $arraywhere, $array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting

		$ci->db->select($arrayselect);

		$this->query_datadetail();

		$ci->db->where($arraywhere, null, FALSE);
		$ci->db->where('if(' . $this->TBSUB . '.receive_id is not null ,' . $this->TBSUB . '.status = 1,true)', null, FALSE);

		if ($array['order_by']) {
			foreach ($array['order_by'] as $val) {
				$ci->db->order_by($val);
			}
		} else {
			$ci->db->order_by($this->TBMAIN . '.complete', 'asc');
		}

		if ($array['limit']) {
			$ci->db->limit($array['limit']['total'], $array['limit']['start']);	//	total , start
		}


		$result = $ci->db->get();

		return $result;
	}

	//	query creditnote and detail
	function query_datadetail()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$ci->db->from($this->TBMAIN);
		$ci->db->join($this->TBSUB, 'if(' . $this->TBMAIN . '.ID = ' . $this->TBSUB . '.RECEIVE_ID,' . $this->TBMAIN . '.ID = ' . $this->TBSUB . '.RECEIVE_ID,' . $this->TBSUB . '.RECEIVE_ID is null) ', 'left', FALSE);
		$ci->db->join('retail_bill', $this->TBMAIN . '.RT_ID = retail_bill.ID ', 'left');
		// $ci->db->join('retail_productmain', 'retail_billdetail.PROMAIN_ID = retail_productmain.ID ', 'left');
		$ci->db->join('retail_productlist', $this->TBSUB . '.PROLIST_ID = retail_productlist.ID ', 'left');
		$ci->db->join('staff', $this->TBMAIN . '.USER_STARTS = staff.CODE ', 'left');
	}

	function get_creditnoteBill($textsearch)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$arrayselect = array(
			$this->TBMAIN . '.id as cn_id',
			$this->TBMAIN . '.code as cn_code',
			$this->TBMAIN . '.rt_bill_code as cn_rt_bill_code',
			$this->TBMAIN . '.sp_bill_id as cn_supplier_id',
			$this->TBMAIN . '.approve as cn_approve',
			$this->TBMAIN . '.approve_store as cn_approve_store',
			$this->TBMAIN . '.complete as cn_complete',
			$this->TBMAIN . '.remark as cn_remark',
			$this->TBMAIN . '.date_starts as cn_date_starts',
			$this->TBMAIN . '.user_starts as cn_user_starts'
		);
		$arraywhere = array(
			$this->TBMAIN . '.code like "%' . preg_replace("/_/", "\_", $textsearch) . '%"' 			=> null,
			$this->TBMAIN . '.status' 		=> 1
		);

		$array = array(
			'order_by'		=> array($this->TBMAIN . '.id desc'),
			'limit'			=> array(
				'total'	=> 25,
				'start'	=> 0
			),
		);


		$q = $this->query_creditnotebill($arrayselect, $arraywhere, $array);
		$result = $q;

		return $result;
	}

	//	query main
	//	@param array[group_by]	@text = group by
	//	@param array[order_by]	@array = order by text 
	//	@param array[limit]		@array = [total[int] = total show , start[int] = start point on data]
	// function query_retailbill($arrayselect, $arraywhere, $arrayorderby, $arraylimit)
	function query_creditnotebill($arrayselect, $arraywhere, $array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting

		$ci->db->select($arrayselect);

		$this->query_creditnote();

		$ci->db->where($arraywhere);

		if ($array['order_by']) {
			foreach ($array['order_by'] as $val) {
				$ci->db->order_by($val);
			}
		}

		if ($array['limit']) {
			$ci->db->limit($array['limit']['total'], $array['limit']['start']);	//	total , start
		}


		$result = $ci->db->get();

		return $result;
	}

	//	query creditnote
	function query_creditnote()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$ci->db->from($this->TBMAIN);
	}
	/**
	 * =====================================================================
	 * SQL RETAIL
	 * =====================================================================
	 */
	function get_retailBill($bill_id)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$arrayselect = array(
			'retail_bill.id as rt_id',
			'retail_bill.code as rt_code',
			'retail_bill.textcode as rt_textcode',
			'retail_bill.delivery_formid as rt_delivery_formid',
			'retail_bill.methodorder_id as rt_methodorder_id',
			'retail_bill.ref as rt_ref',
			'retail_bill.name as rt_name',
			'retail_bill.phone_number as rt_tel',
			'retail_bill.text_number as rt_citizen',
			'retail_bill.address as rt_address',
			'retail_bill.zipcode as rt_zipcode',
			'retail_bill.date_starts as rt_datestarts',
			'retail_bill.billstatus as rt_billstatus',
			'retail_bill.status_complete as rt_complete',
			'retail_bill.remark_order as rt_remark',

			'retail_bill.total_price as rt_total_price',
			'retail_bill.parcel_cost as rt_parcel_cost',
			'retail_bill.delivery_fee as rt_delivery_fee',
			'retail_bill.shor_money as rt_shor_money',
			'retail_bill.discount_price as rt_discount_price',
			'retail_bill.tax as rt_tax',
			'retail_bill.net_total as rt_net_total',

			'retail_bill.transfered_daytime as rt_bank_daytime',
			'retail_bill.transfered_amount as rt_bank_amount',
			'retail_bill.transfered_remark as rt_bank_remark',

			'retail_billdetail.promain_id as rtd_productmain',
			'retail_billdetail.prolist_id as rtd_productid',
			'retail_billdetail.list_id as rtd_productlist',
			'retail_billdetail.quantity as rtd_qty',
			'retail_billdetail.total_price as rtd_price',

			'retail_productlist.name_th as rtp_name',
			'retail_productlist.price as rtp_price',

			'retail_methodorder.topic as rtm_name',

			'delivery.name_th as rtd_name',

			'staff.name_th as sf_name',
			'staff.lastname_th as sf_lastname',
			'bank.name_th as b_name'
		);
		$arraywhere = array(
			'retail_bill.id' 		=> $bill_id,
		);

		$q = $this->query_retailbilldetail($arrayselect, $arraywhere, null);
		$result = $q;

		return $result;
	}

	function get_retailSearchBill($textsearch)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$arrayselect = array(
			'retail_bill.id as rt_id',
			'retail_bill.code as rt_code',
			'retail_bill.name as rt_name'
		);
		$arraywhere = array(
			'retail_bill.code like "%' . preg_replace("/_/", "\_", $textsearch) . '%"' 			=> null,
			'retail_bill.status' 		=> 1,
		);

		$array = array(
			'order_by'		=> array('retail_bill.id desc'),
			'limit'			=> array(
				'total'	=> 25,
				'start'	=> 0
			),
		);


		$q = $this->query_retailbill($arrayselect, $arraywhere, $array);
		$result = $q;

		return $result;
	}
	//	query main
	//	@param array[group_by]	@text = group by
	//	@param array[order_by]	@array = order by text 
	//	@param array[limit]		@array = [total[int] = total show , start[int] = start point on data]
	// function query_retailbill($arrayselect, $arraywhere, $arrayorderby, $arraylimit)
	function query_retailbill($arrayselect, $arraywhere, $array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting

		$ci->db->select($arrayselect);

		$this->query_retail();

		$ci->db->where($arraywhere);

		if ($array['order_by']) {
			foreach ($array['order_by'] as $val) {
				$ci->db->order_by($val);
			}
		}

		if ($array['limit']) {
			$ci->db->limit($array['limit']['total'], $array['limit']['start']);	//	total , start
		}


		$result = $ci->db->get();

		return $result;
	}

	// query detail
	//	@param array[group_by]	@text = group by
	//	@param array[order_by]	@array = order by text 
	//	@param array[limit]		@array = [total[int] = total show , start[int] = start point on data]
	// function query_retailbill($arrayselect, $arraywhere, $arrayorderby, $arraylimit)
	function query_retailbilldetail($arrayselect, $arraywhere, $array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting

		$ci->db->select($arrayselect);

		$this->query_retaildetail();

		$ci->db->where($arraywhere);

		if ($array['order_by']) {
			foreach ($array['order_by'] as $val) {
				$ci->db->order_by($val);
			}
		}

		if ($array['limit']) {
			$ci->db->limit($array['limit']['total'], $array['limit']['start']);	//	total , start
		}


		$result = $ci->db->get();

		return $result;
	}

	//	query retail_bill
	function query_retail()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$ci->db->from("retail_bill");
		// $ci->db->join('retail_billdetail', 'retail_bill.ID = retail_billdetail.BILL_ID ', 'left');
		// $ci->db->join('retail_productmain', 'retail_billdetail.PROMAIN_ID = retail_productmain.ID ', 'left');
		// $ci->db->join('retail_productlist', 'retail_billdetail.PROLIST_ID = retail_productlist.ID ', 'left');
		$ci->db->join('retail_methodorder', 'retail_bill.METHODORDER_ID = retail_methodorder.ID ', 'left');
		$ci->db->join('staff', 'retail_bill.USER_STARTS = staff.CODE ', 'left');
		$ci->db->join('bank', 'retail_bill.TRANSFERED_BANIK_ID = bank.ID ', 'left');
		$ci->db->join('retail_billimg', 'retail_bill.ID = retail_billimg.BILLID ', 'left');
	}

	//	query retail_bill and detail
	function query_retaildetail()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$ci->db->from("retail_bill");
		$ci->db->join('retail_billdetail', 'retail_bill.ID = retail_billdetail.BILL_ID ', 'left');
		// $ci->db->join('retail_productmain', 'retail_billdetail.PROMAIN_ID = retail_productmain.ID ', 'left');
		$ci->db->join('retail_productlist', 'retail_billdetail.PROLIST_ID = retail_productlist.ID ', 'left');
		$ci->db->join('retail_methodorder', 'retail_bill.METHODORDER_ID = retail_methodorder.ID ', 'left');
		$ci->db->join('delivery', 'retail_bill.DELIVERY_FORMID = delivery.ID ', 'left');
		$ci->db->join('staff', 'retail_bill.USER_STARTS = staff.CODE ', 'left');
		$ci->db->join('bank', 'retail_bill.TRANSFERED_BANIK_ID = bank.ID ', 'left');
		$ci->db->join('retail_billimg', 'retail_bill.ID = retail_billimg.BILLID ', 'left');
	}

	//
	//	==================================================
	//		FUNCTION 
	//	==================================================
	//
	//	status complete
	//	@param status	@int = complete bill (receive)
	function get_dataComplete($status)
	{
		switch ($status) {
			case 0:
				$statustext = "รอคลังรับสินค้า";
				break;
			case 1:
				$statustext = "รอคลังรับสินค้า";
				break;
			case 2:
				$statustext = "สำเร็จ";
				break;
			case 3:
				$statustext = "ยกเลิก";
				break;
		}

		$result = array(
			'data' => $statustext
		);

		return $result;
	}

	//
	//	bill type status
	//	@param status	@int = billtype bill (receive)
	function get_dataBilltype($status)
	{
		switch ($status) {
			case 1:
				$statustext = "supplier";
				break;
			case 2:
				$statustext = "บิลสั่งซื้อ";
				break;
			case 3:
				$statustext = "ใบลดหนี้";
				break;
		}

		$result = array(
			'data' => $statustext
		);

		return $result;
	}
}
