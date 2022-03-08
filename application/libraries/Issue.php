<?php

use phpDocumentor\Reflection\Types\Integer;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Issue
{
	public function __construct()
	{
			// Assign the CodeIgniter super-object
			$this->TBMAIN = 'retail_issue';
			$this->TBIMG = 'retail_issueimg';
			$this->DIR = 'issue';
	}

	//	update bill
	//	@param array[item]		@array 
	//		promain		@int 	=> promain id
	//		prolist		@int 	=> prolist id
	//		list		@int 	=> list id
	// 		name		@text 	=> product name 
	// 		qty			@int 	=> product quantity
	// 		price		@float 	=> product price [default set 0]
	// 		totalprice	@float 	=> product*quantity [default set 0]
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

		/* echo "<pre>";
		echo "data information=====";
		print_r($array);
		echo "data image======";
		print_r($arrayfile);
		echo "</pre>";
		exit; */

		//	check array
		$bill_id = get_valueNullToNull($array['bill_id']);
		$bill_code = get_valueNullToNull($array['bill_code']);
		$remark = get_valueNullToNull($array['remark']);

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

		//	check array item zero
		if ($array['item']) {
			foreach ($array['item'] as $key => $val) {
				$qty = get_valueNullToNull($val['qty']);

				if($qty <= 0 || $qty == null){
					$result = array(
						'error_code'	=> 1,
						'txt'			=> 'มีจำนวนสินค้าไม่ถูกต้อง'
					);
	
					return $result;
				}
			}
		}
		/* echo "<pre>";
		echo get_valueNullToNull($array['item'][0]['list']);
		echo "</pre>";
		exit; */
		$dataupdate = array(
			'promain_id'		=> $array['item'][0]['promain'],
			'prolist_id'		=> $array['item'][0]['prolist'],
			'list_id'			=> get_valueNullToNull($array['item'][0]['list']),
			'quantity'			=> $array['item'][0]['qty'],
			'remark'			=> $remark,
			'date_update'		=> date('Y-m-d H:i:s'),
			'user_update'		=> $ci->session->userdata('useradminid')
		);
		$ci->db->where($this->TBMAIN.'.id', $bill_id);
		$ci->db->update($this->TBMAIN, $dataupdate);

		// ============== Log_Detail ============== //
		$log_query = $ci->db->last_query();
		$last_id = $ci->session->userdata('log_id');
		$detail = "Update ".$this->TBMAIN." id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
		$type = "Update";
		$arraylog = array(
			'log_id'  	 	 => $last_id,
			'detail'  		 => $detail,
			'logquery'       => $log_query,
			'type'     	 	 => $type,
			'date_starts'    => date('Y-m-d H:i:s')
		);
		updateLog($arraylog);

		//	check bill complete
		if($bill_id){
			$arraybillsup = array(
				'bill_id'		=> $bill_id,
				'item'			=> array()
			);
			$check_supplier = $this->check_complete($arraybillsup);
		}
		
		//
		//	update status 0 for image delete
		$imagedel = $array['imagedel'];
		if ($imagedel && count($imagedel)) {
			foreach ($imagedel as $key => $val) {
				$setoff = array(
					'status'			=> 0
				);

				$ci->db->where($this->TBIMG.'.id', $val);
				$ci->db->update($this->TBIMG, $setoff);

				// ============== Log_Detail ============== //
				$log_query = $ci->db->last_query();
				$last_id = $ci->session->userdata('log_id');
				$detail = "Update ".$this->TBIMG." id:" . $val . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
		$txt = 'อัพเดตข้อมูลใบเบิกสำเร็จ';

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	//	check complete
	//	@param array		@array 
	//		bill_id		@int 	=> bill receive id
	//	@param array[item]		@array 
	//		promain		@int 	=> promain id
	//		prolist		@int 	=> prolist id
	//		list		@int 	=> list id
	// 		name		@text 	=> product name 
	// 		qty			@int 	=> product quantity
	// 		price		@float 	=> product price
	// 		totalprice	@float 	=> product*quantity 
	//
	function check_complete($array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$bill_id = $array['bill_id'];
		$item = $array['item'];

		$error = 1;
		$txt = 'ไม่สามารถทำรายการได้';

		if ($bill_id) {
			//	setting
			$array_sup_item = array();
			$array_rec_item = array();

			//	bill supplier 
			$sqlold = $ci->db->select(
				$this->TBMAIN.'.prolist_id as sp_prolist_id,'.
				$this->TBMAIN.'.quantity as sp_qty,'.
				$this->TBMAIN.'.type as sp_type'
			)
				->from($this->TBMAIN)
				->where($this->TBMAIN.'.id', $bill_id)
				// ->where($this->TBMAIN.'.complete', 1)
				->where($this->TBMAIN.'.status', 1);
			$qold = $sqlold->get();
			$numold = $qold->num_rows();
			if ($numold) {
				foreach ($qold->result() as $rold) {
					$type = $rold->sp_type;
					$array_sup_item[$rold->sp_prolist_id] += $rold->sp_qty;
				}
			}

			//	bill received ค้นหาจำนวนจากบิลที่รับแล้ว 
			$sqlr = $ci->db->select(
				'retail_receivedetail.prolist_id as sp_prolist_id,
				retail_receivedetail.quantity as sp_qty'
			)
				->from('retail_receive')
				->join('retail_receivedetail','retail_receive.id=retail_receivedetail.receive_id','left')
				->where('retail_receive.is_bill_id', $bill_id)
				->where('retail_receive.complete', 2)
				->where('retail_receive.status', 1)
				->where('retail_receivedetail.status', 1);
			$qr = $sqlr->get();
			$numr = $qr->num_rows();
			if ($numr) {
				foreach ($qr->result() as $rr) {
					$array_rec_item[$rr->sp_prolist_id] += $rr->sp_qty;					
				}
			}

			if(count($item)){
				foreach($item as $key => $value){
					$array_rec_item[$value['prolist']] += $value['qty'];	
				}
			}

			$result_check = 0;
			if($type == 1){	//	ยืม
				if(count($array_sup_item) && count($array_rec_item)){
					foreach ($array_sup_item as $keyitem => $valitem) {
						if($array_rec_item[$keyitem] && $array_rec_item[$keyitem] >= $valitem){
	
						}else{
							$result_check = 1;
						}
					}
				}else{
					$result_check = 1;
				}
			}
			
			//	check result == 0 go to update complete bill (complete = 2)
			if($result_check === 0){

				//	run upload
				$txterror = "";
				$dataupdate = array(
					'complete'    	=>  2,
					'approve_store'   	=>  1,
					'apst_date'   		=>  date('Y-m-d H:i:s'),
					'apst_user'   		=>  $ci->session->userdata('useradminid')
				);
				$ci->db->where($this->TBMAIN.'.id', $bill_id);
				$ci->db->update($this->TBMAIN, $dataupdate);
				// ============== Log_Detail ============== //
				$log_query = $ci->db->last_query();
				$last_id = $ci->session->userdata('log_id');
				$detail = "update approve store ".$this->TBMAIN." id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
				$txt = 'ทำรายการสำเร็จ ';
			}else{
				//	run upload
				$txterror = "";
				$dataupdate = array(
					'complete'    	=>  0
					/* 'approve_store'   	=>  null,
					'apst_date'   		=>  date('Y-m-d H:i:s'),
					'apst_user'   		=>  $ci->session->userdata('useradminid') */
				);
				$ci->db->where($this->TBMAIN.'.id', $bill_id);
				$ci->db->update($this->TBMAIN, $dataupdate);
				// ============== Log_Detail ============== //
				$log_query = $ci->db->last_query();
				$last_id = $ci->session->userdata('log_id');
				$detail = "update cancel approve store ".$this->TBMAIN." id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
				$txt = 'ทำรายการสำเร็จ ';
			}

		} else {
			$txt .= ' ไม่พบเลข Bill';
		}

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
		$uploadsDir = BASE_PIC . $this->DIR."/";
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
								'rt_issue_id '    =>  $arraybill['bill_id'],
								'path'      	 	=>  $check_upload['data'],
								'date_starts'   	=>  date('Y-m-d H:i:s')
							);
							$ci->db->insert($this->TBIMG, $datainsert);
							$new_id = $ci->db->insert_id();
							if (!$new_id) {
								$txterror = 'ข้อมูลไม่ถูกบันทึก ' . $targetPath;
							} else {
								// ============== Log_Detail ============== //
								$log_query = $ci->db->last_query();
								$last_id = $ci->session->userdata('log_id');
								$detail = "Insert ".$this->TBIMG." name:" . $check_upload['data'] . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
		$uploadsDir =  FCPATH . BASE_PIC . $this->DIR."/";
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
	//
	//	==================================================
	//		Image 
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
		$table = "retail_issueimg";
		$tablefield = "rt_issue_id";

		$ci->db->select(
			$table.".id as cni_id,".
			$table.".path as cni_path"
		);
		$ci->db->from($table);
		$ci->db->where($table.'.status', 1);
		$ci->db->where($table.'.'.$tablefield, $billid);
		$q = $ci->db->get();
		$num = $q->num_rows();

		if ($num) {
			$result = $q;
		} else {
			$result = "";
		}

		return $result;
	}
}
