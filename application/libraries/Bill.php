<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bill
{
	function gencode()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$retail_bill = 'retail_bill';
		$retail_billdetail = 'retail_billdetail';

		$ci->db->select($retail_bill . '.CODE AS codemax');
		$ci->db->from($retail_bill);
		$ci->db->order_by($retail_bill . '.ID', 'DESC');
		$Query_Max = $ci->db->get();
		$num = $Query_Max->num_rows($Query_Max);
		$RowMax = $Query_Max->row();
		if ($num > 0) {
			$str = explode(" ", $RowMax->codemax);
			$Code = explode("_", $str[1]);
			$codeDB = '';
			$dateY = (date('Y') + 543);
			if ($Code[1] == $dateY) {
				$count = $Code[0] + 1;
				$codeDB = $str[0] . ' ' . $count . '_' . $Code[1];
			} else {
				$Code[0] = 0;
				$count = $Code[0] + 1;
				$codeDB = $str[0] . ' ' . $count . '_' . $dateY;
			}
		} else {
			$dateY = (date('Y') + 543);
			$codeDB = 'Jerky 1_' . $dateY;
		}

		return $codeDB;
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
	function add_BillNew($array, $arrayfile)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//


		$retail_bill = 'retail_bill';
		$retail_billdetail = 'retail_billdetail';

		$result = array();
		//
		//	array
		if ($array) {

			$bill_date = get_ValueNullToNull(trim($array['bill_date']));
			$bill_delivery = get_ValueNullToNull(trim($array['bill_delivery']));
			$bill_method = get_ValueNullToNull(trim($array['bill_method']));

			$billstatus = get_ValueNullToNull(trim($array['billstatus']));
			$statuscomplete = $array['statuscomplete'];
			$billnew = get_ValueNullToNull(trim($array['billnew']));

			$remark = trim($array['remark']);
			$bill_id = get_ValueNullToNull(trim($array['bill_id']));
			$bill_code = get_ValueNullToNull(trim($array['bill_code']));

			$cust_name = get_ValueNullToNull(trim($array['cust_name']));
			$cust_tel = trim($array['cust_tel']);
			$cust_zipcode = trim($array['cust_zipcode']);
			$cust_textcode = get_ValueNullToNull(trim($array['cust_textcode']));
			$cust_address = get_ValueNullToNull(trim($array['cust_address']));
			$cust_textnumber = get_ValueNullToNull(trim($array['cust_textnumber']));

			$item = $array['item'];

			//	check file image
			if (count($arrayfile)) {
				$checkfile = $ci->bill->check_img($arrayfile);
				if ($checkfile['error_code'] != 0) {
					$result = array(
						'error_code'	=> $checkfile['error_code'],
						'txt'			=> $checkfile['txt']
					);

					return $result;
				}
			}

			/* echo "<pre>";
			print_r($array);
			print_r($arrayfile);
			echo "</pre>";
			exit; */

			//	order date
			if (!$bill_date) {
				$result = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุวันสร้างบิล'
				);
				return $result;
			}

			//	delivery
			if (!$bill_delivery) {
				$result = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุช่องทางจัดส่ง'
				);
				return $result;
			}

			//	method
			if (!$bill_method) {
				$result = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุช่องทางรับรายการ'
				);
				return $result;
			}

			//	customer name
			if (!$cust_name) {
				$result = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุชื่อลูกค้า'
				);
				return $result;
			}

			//	customer tel
			// echo "tel : ".is_null($cust_tel);
			if (empty($cust_tel)) {
				$result = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุเบอร์ติดต่อลูกค้า'
				);
				return $result;
			}

			//	customer zipcode
			if (empty($cust_zipcode)) {
				$result = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุรหัสไปรษณีย์'
				);
				return $result;
			}

			//	customer address
			if (!$cust_address) {
				$result = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุที่อยู่'
				);
				return $result;
			}

			if (!$item) {
				$result = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุสินค้า'
				);
				return $result;
			}

			$ci->load->library('bill');

			$i = 0;
			if ($array) {
				//	generate code
				$code = $ci->bill->gencode();


				//	methodorder
				$methodorder_id = $bill_method;

				//	delivery
				$delivery_formid = $bill_delivery;

				//	customer
				$name = $cust_name;

				//	address
				$phone_number = $cust_tel;
				$address = $cust_address;
				$zipcode = $cust_zipcode;
				$textcode = $cust_textcode;

				//	price total
				$total_price = $array['bill_price'];
				$parcel_cost = get_ValueNullToNull(trim($array['bill_parcel']));
				$shor_money = get_ValueNullToNull(trim($array['bill_shor']));
				$tax = get_ValueNullToNull(trim($array['bill_tax']));
				$delivery_fee = get_ValueNullToNull(trim($array['bill_logis']));
				$discount_price = get_ValueNullToNull(trim($array['bill_discount']));
				$net_total = get_ValueNullToNull(trim($array['totalamount']));

				//	bank id
				$transfered_banik_id = get_ValueNullToNull(trim($array['bankid']));

				//	transfer date
				$transfered_date = null;
				if (get_ValueNullToNull(trim($array['transfereddate'])) && get_ValueNullToNull(trim($array['transferedtime']))) {
					$transfered_date = get_ValueNullToNull(trim($array['transfereddate'])) . " " . get_ValueNullToNull(trim($array['transferedtime']));
				}

				//	transfer amount
				$transfered_amount = get_ValueNullToNull(trim($array['amount']));

				//	transfer remark
				$transfered_remark = get_ValueNullToNull(trim($array['transferedremark']));

				$status_complete = $statuscomplete;
				$status_approve1 = 0;
				$status_approve2 = 0;

				if ($billstatus == 'F') {
					$status_approve1 = 2;
					$status_approve2 = 2;
				} else if ($billstatus == 'C') {
					$status_approve1 = 2;
				}

				$datainsert = array(
					'code'	=> $code,

					'delivery_formid'	=> $delivery_formid,
					'methodorder_id'	=> $methodorder_id,

					'name'			=> $name,
					'phone_number'		=> $phone_number,
					'address'		=> $address,
					'zipcode'		=> $zipcode,
					'text_number'	=> $cust_textnumber,
					'textCode'		=> $textcode,

					'total_price'		=> $total_price,
					'parcel_cost'		=> $parcel_cost,
					'delivery_fee'		=> $delivery_fee,
					'discount_price'	=> $discount_price,
					'shor_money'		=> $shor_money,
					'tax'				=> $tax,
					'net_total'			=> $net_total,

					'transfered_banik_id'	=> $transfered_banik_id,
					'transfered_daytime'	=> $transfered_date,
					'transfered_amount'	=> $transfered_amount,
					'transfered_remark'	=> $transfered_remark,

					'remark_order'		=> $remark,

					'status_approve1'	=> $status_approve1,
					'status_approve2'	=> $status_approve2,
					'status_complete'	=> $status_complete,
					'billstatus'	=> $billstatus,

					'billref_id'	=> $bill_id,
					'billref_code'	=> $bill_code,

					'date_starts'	=> $bill_date . " " . date('H:i:s'),
					'user_starts'	=> $ci->session->userdata('useradminid')
				);
				$ci->db->insert($retail_bill, $datainsert);
				$id = $ci->db->insert_id();
				if ($id) {
					$i++;	// count 
					foreach ($item as $keydetail => $subdetail) {

						$datainsertdetail = array(
							'code'	=> $code,
							'bill_id'	=> $id,

							'promain_id'	=> get_ValueNullToNull(trim($subdetail['promain'])),
							'prolist_id'	=> get_ValueNullToNull(trim($subdetail['prolist'])),
							'list_id'		=> get_ValueNullToNull(trim($subdetail['list'])),
							'quantity'		=> get_ValueNullToNull(trim($subdetail['qty'])),
							'total_price'	=> get_ValueNullToNull(trim($subdetail['totalprice'])),

							'date_starts'	=> date('Y-m-d H:i:s'),
							'user_starts'	=> $ci->session->userdata('useradminid')
						);
						$ci->db->insert($retail_billdetail, $datainsertdetail);
					}
				}
			}	/* END INSERT RETAIL_BILL */

			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Insert billnew Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
			$type = "Insert";
			$arraylog = array(
				'log_id'  		 => $last_id,
				'detail'  		 => $detail,
				'logquery'       => $log_query,
				'type'     	 	 => $type,
				'date_starts'    => date('Y-m-d H:i:s')
			);
			updateLog($arraylog);

			//	ดึงข้อมูลสลิปเก่า
			if ($array['bill_parcel']) {
				$sqlimg = $ci->db->select('*')
					->from('retail_billimg')
					->where('billid', $bill_id);
				$qimg = $sqlimg->get();
				$numimg = $qimg->num_rows();
				if ($numimg) {
					foreach ($qimg->result() as $rowimg) {
						if ($rowimg->BILLID) {
							$setdetail[] = array(
								'billid'		=> $id,
								'imgname'		=> $rowimg->IMGNAME,
								'date_starts'		=> date('Y-m-d H:i:s')
							);
						}
					}
					$dataupdateimg = $setdetail;
					if ($dataupdateimg) {
						$ci->db->insert_batch('retail_billimg', $dataupdateimg);

						// ============== Log_Detail ============== //
						$log_query = $ci->db->last_query();
						$last_id = $ci->session->userdata('log_id');
						$detail = "Insert billimg old Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
						$type = "Insert";
						$arraylog = array(
							'log_id'  		 => $last_id,
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
			$error = 1;
			if (count($arrayfile)) {
				$arraybill = array(
					'bill_id'	=> $id,
					'code'		=> $code
				);
				$uploadimage = $ci->bill->upload_img($arraybill, $arrayfile);
				//	upload error
				if ($uploadimage['error_code'] != 0) {
					$result = array(
						'error_code'	=> $error,
						'txt'			=> $uploadimage['txt']
					);

					return $result;
				}
			}


			/* echo "<pre>";
			print_r($array);
			print_r($arrayfile);
			echo "</pre>";
			exit; */

			$result = array(
				'error_code'	=> 0,
				'txt'			=> 'success',
				'total'	=> $i
			);
		}

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
		$uploadsDir = BASE_PIC . "front/retail/BillPaymentMultiple/";
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

							$explodeimg = explode("/", $check_upload['data']);
							$newimagename = $explodeimg[5];

							$datainsert = array(
								'billid '    =>  $arraybill['bill_id'],
								'imgname'      	 	=>  $newimagename,
								'date_starts'   	=>  date('Y-m-d H:i:s')
							);
							$ci->db->insert('retail_billimg', $datainsert);
							$new_id = $ci->db->insert_id();
							if (!$new_id) {
								$txterror = 'ข้อมูลไม่ถูกบันทึก ' . $targetPath;
							} else {
								// ============== Log_Detail ============== //
								$log_query = $ci->db->last_query();
								$last_id = $ci->session->userdata('log_id');
								$detail = "Insert retail_billimg name:" . $check_upload['data'] . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
		$uploadsDir =  FCPATH . BASE_PIC . "creditnote/";
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
}
