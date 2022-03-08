<?php

use phpDocumentor\Reflection\Types\Integer;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Supplier
{
	public function __construct()
	{
			// Assign the CodeIgniter super-object
			$this->TBMAIN = 'retail_supplier';
			$this->TBSUB = 'retail_supplierdetail';
			$this->TBIMG = 'retail_supplierimg';
			$this->DIR = 'supplier';
	}

	function genCodeBill()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$table = $this->TBMAIN;
		$codebill = 'SP';

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
		$type = ($array['type'] == 'true' ? 1 : null);
		$ref = get_valueNullToNull(trim($array['ref1']));
		$remark = get_valueNullToNull(trim($array['remark']));
		$complete = get_valueNullToNull($array['complete']);
		$supplier_id = get_valueNullToNull($array['selectsupplier']);

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

		//	check array item
		if (!$array['item'] || count($array['item']) == 0) {
			$result = array(
				'error_code'	=> 1,
				'txt'			=> 'โปรดระบุสินค้า'
			);

			return $result;
		}

		//	check supplier
		if (!$supplier_id) {

			$result = array(
				'error_code'	=> 1,
				'txt'			=> 'โปรดระบุ supplier'
			);

			return $result;
		}

		//	 generate code
		$gencode = $ci->supplier->genCodeBill();

		$datainsert = array(
			'code'				=> $gencode,
			'type'				=> $type,
			'ref'				=> $ref,
			'supplier_id'		=> $supplier_id,
			'complete'			=> $complete,
			'remark'			=> $remark,
			'date_starts'		=> date('Y-m-d H:i:s'),
			'user_starts'		=> $ci->session->userdata('useradminid')
		);

		$ci->db->insert($this->TBMAIN, $datainsert);
		$new_id = $ci->db->insert_id();
		if ($new_id) {

			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Insert ".$this->TBMAIN." id:" . $new_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
					$totalprice = 0;

					if ($qty && $promain && $prolist) {
						$setdetail[] = array(
							'code'				=> $gencode,
							'rt_sup_id'		=> $new_id,
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
				$detail = "Insert ".$this->TBSUB." Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
				$uploadimage = $ci->supplier->upload_img($arraybill, $arrayfile);
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
			$txt = 'เพิ่มใบ supplier สำเร็จ';
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
		$bill_textcode = get_valueNullToNull($array['bill_textcode']);
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
		print_r($array);
		echo "</pre>";
		exit; */
		$dataupdate = array(
			'ref'				=> $bill_textcode,
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

		if ($bill_id) {
			//	call bill product old
			$sqlold = $ci->db->select('id')
				->from($this->TBSUB)
				->where($this->TBSUB.'.rt_sup_id', $bill_id)
				->where($this->TBSUB.'.status', 1);
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

				//	call bill product old
				$sqlold = $ci->db->select($this->TBMAIN.'.id as rt_id')
					->from($this->TBMAIN)
					->join($this->TBSUB,$this->TBMAIN.'.id='.$this->TBSUB.'.rt_sup_id','left')
					->where($this->TBMAIN.'.id', $bill_id)
					->where($this->TBMAIN.'.status', 1)
					->where($this->TBSUB.'.prolist_id', $prolist)
					->where($this->TBSUB.'.status', 1);
				$qold = $sqlold->get();
				$numold = $qold->num_rows();
				if ($numold) {
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
	
						$ci->db->where($this->TBSUB.'.id', $iddetail);
						$ci->db->update($this->TBSUB, $setdetail);
	
						// ============== Log_Detail ============== //
						$log_query = $ci->db->last_query();
						$last_id = $ci->session->userdata('log_id');
						$detail = "Update ".$this->TBSUB." id:" . $iddetail . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
				}else{
					//	if new item
					if ($qty  && $promain && $prolist) {
						$setdetail = array(
							'code'				=> $bill_code,
							'rt_sup_id'			=> $bill_id,
							'promain_id'		=> $promain,
							'prolist_id'		=> $prolist,
							'list_id'			=> $list,
							'quantity'			=> $qty,
							'total_price'		=> 0,
							'date_starts'		=> date('Y-m-d H:i:s'),
							'user_starts'		=> $ci->session->userdata('useradminid'),
							'status'			=> $status
						);
						$ci->db->insert($this->TBSUB, $setdetail);
	
						// ============== Log_Detail ============== //
						$log_query = $ci->db->last_query();
						$last_id = $ci->session->userdata('log_id');
						$detail = "Insert ".$this->TBSUB." id:" . $iddetail . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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

				
			}

			//	update status 0 for detail id not paramiter
			if (count($compare)) {
				foreach ($compare as $key => $val) {
					$setoff = array(
						'status'			=> 0,
						'date_update'		=> date('Y-m-d H:i:s'),
						'user_update'		=> $ci->session->userdata('useradminid'),
					);

					$ci->db->where($this->TBSUB.'.id', $val);
					$ci->db->update($this->TBSUB, $setoff);

					// ============== Log_Detail ============== //
					$log_query = $ci->db->last_query();
					$last_id = $ci->session->userdata('log_id');
					$detail = "Update ".$this->TBSUB." id:" . $val . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
		$txt = 'อัพเดตข้อมูลใบซัพไพเออร์สำเร็จ';

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
								'rt_sup_id '    =>  $arraybill['bill_id'],
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

		$q = $this->get_retailBill($text);
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

	//	check finance approve
	//	@param array		@array
	//		bill_id		@int 	=> bill supplier id
	//		approve		@int 	=> supplier approve
	//
	function confirmFinance($array)
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

		if ($bill_id) {

			//	check bill approve
			$sqlold = $ci->db->select('id')
				->from($this->TBMAIN)
				->where($this->TBMAIN.'.id', $bill_id)
				->where($this->TBMAIN.'.status', 1);
			$qold = $sqlold->get();
			$numold = $qold->num_rows();
			if (!$numold) {

				$txt = 'ไม่พบเลข Bill';
				$result = array(
					'error_code'	=> $error,
					'txt'			=> $txt
				);

				return $result;
			}

			//	check product loss for set complete 1[waite] or 2[success]
			$sql = $ci->db->select('code')
				->from($this->TBMAIN)
				->where($this->TBMAIN.'.id', $bill_id);
			$q = $sql->get();
			$num = $q->num_rows();
			if ($num) {
				$r = $q->row();

				$complete = 2;

				//	หากดึง status กลับให้ complete เป็น 0
				if (!$approve) {
					$complete = 1;
				}
			}

			//	run upload
			$txterror = "";
			$dataupdate = array(
				'approve'    	=>  $approve,
				'complete'    	=>  $complete,
				'appr_date'   	=>  date('Y-m-d H:i:s'),
				'appr_user'   	=>  $ci->session->userdata('useradminid')
			);
			$ci->db->where($this->TBMAIN.'.id', $bill_id);
			$ci->db->update($this->TBMAIN, $dataupdate);
			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Update ".$this->TBMAIN." approve id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
		} else {
			$txt .= ' ไม่พบเลข Bill';
		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	//	cancel bill supplier
	//	@param array		@array 
	//		bill_id		@int 	=> bill supplier id
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
			$ci->db->where($this->TBMAIN.'.id', $bill_id);
			$ci->db->update($this->TBMAIN, $dataupdate);
			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "cancel ".$this->TBMAIN." id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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

	//	check complete
	//	@param array		@array 
	//		bill_id		@int 	=> bill receive id
	//
	function check_completeIssue($array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$bill_id = $array['bill_id'];

		$error = 1;
		$txt = 'ไม่สามารถทำรายการได้';

		if ($bill_id) {
			//	setting
			$array_sup_item = array();
			$array_rec_item = array();

			//	bill supplier 
			$sqlold = $ci->db->select(
				$this->TBSUB.'.prolist_id as sp_prolist_id,'.
				$this->TBSUB.'.quantity as sp_qty,'.
				$this->TBMAIN.'.type as sp_type'
			)
				->from($this->TBMAIN)
				->join($this->TBSUB,$this->TBMAIN.'.id='.$this->TBSUB.'.rt_sup_id','left')
				->where($this->TBMAIN.'.id', $bill_id)
				// ->where($this->TBMAIN.'.complete', 1)
				->where($this->TBMAIN.'.status', 1)
				->where($this->TBSUB.'.status', 1);
			$qold = $sqlold->get();
			$numold = $qold->num_rows();
			if ($numold) {
				foreach ($qold->result() as $rold) {
					$type = $rold->sp_type;
					$array_sup_item[$rold->sp_prolist_id] += $rold->sp_qty;
				}
			}

			//	bill issue ค้นหาจำนวนจากบิลที่คืนไปแล้ว
			$sqlr = $ci->db->select(
				'retail_issue.prolist_id as sp_prolist_id,'.
				'retail_issue.quantity as sp_qty'
			)
				->from('retail_issue')
				->where('retail_issue.sp_bill_id', $bill_id)
				->where('retail_issue.complete', 2)
				->where('retail_issue.status', 1);
			$qr = $sqlr->get();
			$numr = $qr->num_rows();
			if ($numr) {
				foreach ($qr->result() as $rr) {
					$array_rec_item[$rr->sp_prolist_id] += $rr->sp_qty;					
				}
			}

			$result_check = 0;
			if(count($array_sup_item) && count($array_rec_item)){
				foreach ($array_sup_item as $keyitem => $valitem) {
					if($array_rec_item[$keyitem] && $array_rec_item[$keyitem] >= $valitem){

					}else{
						$result_check = 1;
					}

					//	หากเป็น supplier ประเภทยืมเข้าคลัง ต้องตรวจสอบเอกสารเบิกคืนด้วย
					if($type == 1){	//	1 =	ยืมเข้าคลัง
						
						$sqlissue = $ci->db->select(
							'sum(retail_issue.quantity) as qty'
						)
							->from('retail_issue')
							->where('retail_issue.sp_bill_id', $bill_id)
							->where('if(retail_issue.list_id is not null , retail_issue.list_id='.$keyitem.' , retail_issue.prolist_id='.$keyitem.')',null,false)
							->where('retail_issue.status', 1);
						$qissue = $sqlissue->get();
						$numissue = $qissue->num_rows();
						if($numissue){
							$rissue = $qissue->row();
							$totalqty = $rissue->qty;

							if($totalqty >= $valitem){

							}else{
								$result_check = 1;
							}
						}else{
							$result_check = 1;
						}
					}
				}
			}else{
				$result_check = 1;
			}

		}else{
			$txt .= ' ไม่พบเลข Bill';
		}

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
				$this->TBSUB.'.prolist_id as sp_prolist_id,'.
				$this->TBSUB.'.quantity as sp_qty,'.
				$this->TBMAIN.'.type as sp_type'
			)
				->from($this->TBMAIN)
				->join($this->TBSUB,$this->TBMAIN.'.id='.$this->TBSUB.'.rt_sup_id','left')
				->where($this->TBMAIN.'.id', $bill_id)
				// ->where($this->TBMAIN.'.complete', 1)
				->where($this->TBMAIN.'.status', 1)
				->where($this->TBSUB.'.status', 1);
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
				->where('retail_receive.sp_bill_id', $bill_id)
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
			if(count($array_sup_item) && count($array_rec_item)){
				foreach ($array_sup_item as $keyitem => $valitem) {
					if($array_rec_item[$keyitem] && $array_rec_item[$keyitem] >= $valitem){

					}else{
						$result_check = 1;
					}

					//	หากเป็น supplier ประเภทยืมเข้าคลัง ต้องตรวจสอบเอกสารเบิกคืนด้วย
					if($type == 1){	//	1 =	ยืมเข้าคลัง
						
						$sqlissue = $ci->db->select(
							'sum(retail_issue.quantity) as qty'
						)
							->from('retail_issue')
							->where('retail_issue.sp_bill_id', $bill_id)
							->where('if(retail_issue.list_id is not null , retail_issue.list_id='.$keyitem.' , retail_issue.prolist_id='.$keyitem.')',null,false)
							->where('retail_issue.status', 1);
						$qissue = $sqlissue->get();
						$numissue = $qissue->num_rows();
						if($numissue){
							$rissue = $qissue->row();
							$totalqty = $rissue->qty;

							if($totalqty >= $valitem){

							}else{
								$result_check = 1;
							}
						}else{
							$result_check = 1;
						}
					}
				}
			}else{
				$result_check = 1;
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
					'complete'    	=>  1,
					'approve_store'   	=>  null,
					'apst_date'   		=>  date('Y-m-d H:i:s'),
					'apst_user'   		=>  $ci->session->userdata('useradminid')
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
		$table = "retail_supplierimg";
		$tablefield = "rt_sup_id";

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
			$this->TBMAIN.'.id as rt_id',
			$this->TBMAIN.'.code as rt_code',
			$this->TBMAIN.'.ref as rt_ref',
			$this->TBMAIN.'.supplier_id as rt_supplier_id',
			$this->TBMAIN.'.complete as cn_complete',
			$this->TBMAIN.'.type as cn_type',
			$this->TBMAIN.'.approve as rt_approve',
			$this->TBMAIN.'.approve_store as rt_approve_store',
			$this->TBMAIN.'.appr_date as rt_appr_date',
			$this->TBMAIN.'.appr_user as rt_appr_user',
			$this->TBMAIN.'.apst_date as rt_apst_date',
			$this->TBMAIN.'.apst_user as rt_apst_user',
			$this->TBMAIN.'.date_starts as rt_date_starts',
			$this->TBMAIN.'.user_starts as rt_user_starts',
			$this->TBMAIN.'.date_update as rt_date_update',
			$this->TBMAIN.'.user_update as rt_user_update',
			$this->TBMAIN.'.remark as rt_remark',
			$this->TBMAIN.'.remark_order as rt_remark_order',

			$this->TBSUB.'.id as rtd_id',
			$this->TBSUB.'.promain_id as rtd_productmain',
			$this->TBSUB.'.prolist_id as rtd_productid',
			$this->TBSUB.'.list_id as rtd_productlist',
			$this->TBSUB.'.quantity as rtd_qty',
			$this->TBSUB.'.total_price as rtd_price',

			'supplier.name_th as sup_name',
			'supplier.name as sup_name_en',

			'retail_productlist.name_th as rtp_name',
			'retail_productlist.price as rtp_price',

			'staff.name_th as sf_name_th',
			'staff.lastname_th as sf_lastname_th',
			'staff.name as sf_name',
			'staff.lastname as sf_lastname'
		);
		$arraywhere = array(
			$this->TBMAIN.'.id' 		=> $bill_id,
			$this->TBSUB.'.status' 		=> 1
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
			$this->TBMAIN.'.id as rt_id',
			$this->TBMAIN.'.code as rt_code',
			'supplier.name_th as rt_name'
		);
		$arraywhere = array(
			$this->TBMAIN.'.code like "%' . preg_replace("/_/", "\_", $textsearch) . '%"' 			=> null,
			$this->TBMAIN.'.complete' 		=> 1,
			$this->TBMAIN.'.status' 		=> 1
		);

		$array = array(
			'order_by'		=> array($this->TBMAIN.'.id desc'),
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

		if ($array['group']) {
			$ci->db->group_by($array['group']);
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

		$ci->db->from($this->TBMAIN);
		$ci->db->join('staff', $this->TBMAIN.'.USER_STARTS = staff.CODE ', 'left');
		$ci->db->join('supplier', $this->TBMAIN.'.SUPPLIER_ID = supplier.ID ', 'left');
	}

	//	query retail_bill and detail
	function query_retaildetail()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$ci->db->from($this->TBMAIN);
		$ci->db->join($this->TBSUB, $this->TBMAIN.'.ID = '.$this->TBSUB.'.RT_SUP_ID ', 'left');
		$ci->db->join('retail_productmain', $this->TBSUB.'.PROMAIN_ID = retail_productmain.ID ', 'left');
		$ci->db->join('retail_productlist', $this->TBSUB.'.PROLIST_ID = retail_productlist.ID ', 'left');
		$ci->db->join('staff', $this->TBMAIN.'.USER_STARTS = staff.CODE ', 'left');
		$ci->db->join('supplier', $this->TBMAIN.'.SUPPLIER_ID = supplier.ID ', 'left');

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
}
