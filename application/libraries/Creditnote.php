<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Creditnote
{

	function genCodeBill()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$table = 'retail_creditnote';
		$codebill = 'FC';

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

	function genCodeCreditnote()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$table = 'retail_creditnote';
		$codebill = 'CN';

		//	นับจำนวนบิลในวันนั้น
		$sql = $ci->db->from($table)
			->where($table . '.codereport is not null')
			->where($table . '.date_starts is not null')
			->where('date(' . $table . '.date_starts)', date('Y-m-d'))
			->where($table . '.status', 1);
		$number = $sql->count_all_results(null, false);
		$q = $sql->get();

		$numbernext = $number + 1;
		$new_number = str_pad($numbernext, 3, '0', STR_PAD_LEFT);

		$date = date('Y-m-d');
		$explode = explode("-", $date);

		#	yy
		$thaiyear = $explode[0] + 543;
		$new_y = substr($thaiyear, 2);
		$newcode = $new_y . "" . $explode[1] . "" . $explode[2];

		$gencode = $codebill . $newcode . "-" . $new_number;
		$result = $gencode;

		return $result;
	}

	// update code creditnote report id
	//	@param array		@array 
	//		bill_id		@int 	=> creditnote id
	//
	function update_codeCreditnote($array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$bill_id = get_valueNullToNull($array['bill_id']);
		$error = 1;
		$txt = "ไม่สามารถออกเลข code ได้";

		$newcode = $ci->creditnote->genCodeCreditnote();
		if ($newcode) {

			$dataupdate = array(
				'codereport'		=> $newcode
			);
			$ci->db->where('retail_creditnote.id', $bill_id);
			$ci->db->update('retail_creditnote', $dataupdate);

			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Update creditnote codereport id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
			$txt = "success";
		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	//	add bill receive
	//	@param array		@array 
	//		bill_id		@int 	=> creditnote id
	//

	function create_billReceive($array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$dataresult = array();
		$new_id = "";
		$error = 1;
		$txt = 'ไม่สามารถทำรายการได้';

		//	check array
		$bill_id = get_valueNullToNull($array['bill_id']);
		$bill_rtid = "";
		$bill_code = "";
		$gencode = "";
		$complete = "";
		$approve = "";
		$appr_date = "";
		$appr_user = "";
		$billtype = "";
		$remark = "";
		$datestarts = "";
		$userstarts = "";

		if ($bill_id) {
			//	get data bill creditnote 
			$sqlold = $ci->db->select('
				retail_creditnote.id as cn_id,
				retail_creditnote.rt_id as cn_rt_id,
				retail_creditnote.rt_bill_code as cn_rt_bill_code,
				retail_creditnote.code as cn_code,
				retail_creditnote.complete as cn_complete,
				retail_creditnote.remark as cn_remark,
				retail_creditnote.date_starts as cn_date_starts,
				retail_creditnote.user_starts as cn_user_starts,

				retail_creditnote.approve as cn_approve,
				retail_creditnote.approve_store as cn_approve_store,
				retail_creditnote.appr_date as cn_appr_date,
				retail_creditnote.appr_user as cn_appr_user,
				retail_creditnote.apst_date as cn_apst_date,
				retail_creditnote.apst_user as cn_apst_user,

				retail_creditnotedetail.code as cnd_code,
				retail_creditnotedetail.promain_id as cnd_promain_id,
				retail_creditnotedetail.prolist_id as cnd_prolist_id,
				retail_creditnotedetail.list_id as cnd_list_id,
				retail_creditnotedetail.quantity as cnd_quantity,
				retail_creditnotedetail.total_price as cnd_total_price
			')
				->from('retail_creditnote')
				->join('retail_creditnotedetail', 'if(retail_creditnote.ID = retail_creditnotedetail.CREDITNOTE_ID,retail_creditnote.ID = retail_creditnotedetail.CREDITNOTE_ID,retail_creditnotedetail.CREDITNOTE_ID is null) ', 'left', FALSE)
				->where('retail_creditnote.id', $bill_id)
				->where('retail_creditnotedetail.status', 1);
			$qold = $sqlold->get();
			$numold = $qold->num_rows();
			
			if ($numold) {
				
				$arrayold = array();
				foreach ($qold->result() as $rold) {

					$arrayold[] = array(
						'bill_rtid'	=> $rold->cn_rt_id,
						'bill_code'	=> $rold->cn_rt_bill_code,
						'code'	=> $rold->cn_code,
						'complete'	=> $rold->cn_complete,
						'remark'	=> $rold->cn_remark,

						'approve'	=> $rold->cn_approve,
						'approve_store'	=> $rold->cn_approve_store,
						'appr_date'	=> $rold->cn_appr_date,
						'appr_user'	=> $rold->cn_appr_user,
						'apst_date'	=> $rold->cn_apst_date,
						'apst_user'	=> $rold->cn_apst_user,

						'promain'	=> $rold->cnd_promain_id,
						'prolist'	=> $rold->cnd_prolist_id,
						'list'	=> $rold->cnd_list_id,
						'qty'	=> $rold->cnd_quantity,
						'totalprice'	=> $rold->cnd_total_price
					);
				}

				// set start
				$bill_rtid = $arrayold[0]['bill_rtid'];
				$bill_code = $arrayold[0]['bill_code'];
				$gencode = $arrayold[0]['code'];
				$complete = $arrayold[0]['complete'];
				$billtype = 3;	//	set number bill type from creditnote
				$remark = $arrayold[0]['remark'];

				$approve = $arrayold[0]['approve'];
				$appr_date = $arrayold[0]['appr_date'];
				$appr_user = $arrayold[0]['appr_user'];

				//	set paramiter for create receive
				$array = array(
					'bill_id'		=> $bill_rtid,
					'bill_code'		=> $bill_code,
					'billtype'		=> $billtype,
					'remark'		=> $remark
				);
				$array['item'] = $arrayold;
				$arrayfile = array();

				//	create receive
				$ci->load->library('receive');
				$createreceive = $ci->receive->add_bill($array, $arrayfile);

				if ($createreceive['error_code'] == 0) {
					$dataupdate = array(
						'cn_bill_id'		=> $bill_id,
						'cn_bill_code'		=> $gencode,
						'complete'			=> $complete,
						'approve'			=> $approve,
						'appr_date'			=> $appr_date,
						'appr_user'			=> $appr_user,
					);
					$ci->db->where('retail_receive.id', $createreceive['data']['new_id']);
					$ci->db->update('retail_receive', $dataupdate);
				}
			}

			$error = 0;
			$txt = 'เพิ่มใบรับของสำเร็จ';
		}

		/* echo "<pre>----";
		print_r($array);
		print_r($arrayfile);
		echo "</pre>";
		exit; */

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

		return $result;
	}

	//	delete bill receive
	//	@param array		@array 
	//		bill_id		@int 	=> creditnote id
	//

	function delete_billReceive($array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$dataresult = array();
		$new_id = "";
		$error = 1;
		$txt = 'ไม่สามารถทำรายการได้';

		//	check array
		$bill_id = get_valueNullToNull($array['bill_id']);

		if ($bill_id) {

			//	call bill product old
			$sqlold = $ci->db->select('id')
				->from('retail_receive')
				->where('retail_receive.cn_bill_id', $bill_id)
				->where('retail_receive.complete !=', 3)
				->where('retail_receive.status', 1);
			$qold = $sqlold->get();
			$numold = $qold->num_rows();
			if ($numold) {
				$dataupdate = array(
					'status'		=> 0,
					'date_update'		=> date('Y-m-d H:i:s'),
					'user_update'		=> $ci->session->userdata('useradminid')
				);
				$ci->db->where('retail_receive.cn_bill_id', $bill_id);
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
			}

			$error = 0;
			$txt = "ทำรายการสำเร็จ";
		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
		);

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
		$bill_price = get_valueNullToNull(_toFloat($array['bill_price']));
		$bill_parcel = get_valueNullToNull(_toFloat($array['bill_parcel']));
		$bill_logis = get_valueNullToNull(_toFloat($array['bill_logis']));
		$bill_shor = get_valueNullToNull(_toFloat($array['bill_shor']));
		$bill_discount = get_valueNullToNull(_toFloat($array['bill_discount']));
		$bill_tax = get_valueNullToNull(_toFloat($array['bill_tax']));
		$totalamount = get_valueNullToNull(_toFloat($array['totalamount']));
		$loss = get_valueNullToNull($array['loss']);
		$remark = get_valueNullToNull($array['remark']);

		/* echo "<pre>----";
		print_r($array);
		echo "==shor==";
		echo $bill_shor;
		echo "</pre>";
		exit; */

		//	check complete
		//	if loss == 1 and net total = 0 will bill success auto (complete = 2)
		$complete = 0;
		if ($totalamount == 0 && $loss == 1) {
			$complete = 2;
		}

		//	check file image
		if (count($arrayfile)) {
			$checkfile = $ci->creditnote->check_img($arrayfile);
			if ($checkfile['error_code'] != 0) {
				$result = array(
					'error_code'	=> $checkfile['error_code'],
					'txt'			=> $checkfile['txt']
				);

				return $result;
			}
		}

		//	 generate code
		$gencode = $ci->creditnote->genCodeBill();

		$datainsert = array(
			'rt_id'				=> $bill_id,
			'rt_bill_code'		=> $bill_code,
			'code'				=> $gencode,
			'total_price'		=> $bill_price,
			'parcel_cost'		=> $bill_parcel,
			'delivery_fee'		=> $bill_logis,
			'shor_money'		=> $bill_shor,
			'discount_price'	=> $bill_discount,
			'tax'				=> $bill_tax,
			'net_total'			=> $totalamount,
			'complete'			=> $complete,
			'loss'			=> $loss,
			'remark'		=> $remark,
			'date_starts'		=> date('Y-m-d H:i:s'),
			'user_starts'		=> $ci->session->userdata('useradminid')
		);
		$ci->db->insert('retail_creditnote', $datainsert);
		$new_id = $ci->db->insert_id();
		if ($new_id) {

			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Insert retail_creditnote id:" . $new_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
					$totalprice = get_valueNullToNull(str_replace(',','',$val['totalprice']));

					if ($qty && $promain && $prolist) {
						$setdetail[] = array(
							'code'				=> $gencode,
							'creditnote_id'		=> $new_id,
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

				$ci->db->insert_batch('retail_creditnotedetail', $datainsertdetail);
				// ============== Log_Detail ============== //
				$log_query = $ci->db->last_query();
				$last_id = $ci->session->userdata('log_id');
				$detail = "Insert retail_creditnote Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
				$uploadimage = $ci->creditnote->upload_img($arraybill, $arrayfile);
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
			$txt = 'เพิ่มใบลดหนี้สำเร็จ';
		}

		if ($complete == 2 && $totalamount > 0) {
			$setarray = array(
				'bill_id'	=> $new_id
			);
			$ci->creditnote->update_codeCreditnote($setarray);
		}

		$result = array(
			'error_code'	=> $error,
			'txt'			=> $txt
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
		$bill_price = get_valueNullToNull(_toFloat($array['bill_price']));
		$bill_parcel = get_valueNullToNull(_toFloat($array['bill_parcel']));
		$bill_logis = get_valueNullToNull(_toFloat($array['bill_logis']));
		$bill_shor = get_valueNullToNull(_toFloat($array['bill_shor']));
		$bill_discount = get_valueNullToNull(_toFloat($array['bill_discount']));
		$bill_tax = get_valueNullToNull(_toFloat($array['bill_tax']));
		$totalamount = get_valueNullToNull(_toFloat($array['totalamount']));
		$loss = get_valueNullToNull($array['loss']);
		$remark = get_valueNullToNull($array['remark']);

		//	check complete
		//	if loss == 1 and net total = 0 will bill success auto (complete = 2)
		$complete = 0;
		if ($totalamount == 0 && $loss == 1) {
			$complete = 2;
		}

		//	check file image
		if (count($arrayfile)) {
			$checkfile = $ci->creditnote->check_img($arrayfile);
			if ($checkfile['error_code'] != 0) {
				$result = array(
					'error_code'	=> $checkfile['error_code'],
					'txt'			=> $checkfile['txt']
				);

				return $result;
			}
		}

		$dataupdate = array(
			'total_price'		=> $bill_price,
			'parcel_cost'		=> $bill_parcel,
			'delivery_fee'		=> $bill_logis,
			'shor_money'		=> $bill_shor,
			'discount_price'	=> $bill_discount,
			'tax'				=> $bill_tax,
			'net_total'			=> $totalamount,
			'complete'			=> $complete,
			'loss'			=> $loss,
			'remark'		=> $remark,
			'date_update'		=> date('Y-m-d H:i:s'),
			'user_update'		=> $ci->session->userdata('useradminid')
		);
		$ci->db->where('retail_creditnote.id', $bill_id);
		$ci->db->update('retail_creditnote', $dataupdate);

		if ($complete == 2 && $totalamount > 0) {
			$setarray = array(
				'bill_id'	=> $bill_id
			);
			$ci->creditnote->update_codeCreditnote($setarray);
		}

		// ============== Log_Detail ============== //
		$log_query = $ci->db->last_query();
		$last_id = $ci->session->userdata('log_id');
		$detail = "Update creditnote id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
				->from('retail_creditnotedetail')
				->where('retail_creditnotedetail.creditnote_id', $bill_id)
				->where('retail_creditnotedetail.status', 1);
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

					$ci->db->where('retail_creditnotedetail.id', $iddetail);
					$ci->db->update('retail_creditnotedetail', $setdetail);

					// ============== Log_Detail ============== //
					$log_query = $ci->db->last_query();
					$last_id = $ci->session->userdata('log_id');
					$detail = "Update creditnotedetail id:" . $iddetail . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
						'status'			=> 0,
						'date_update'		=> date('Y-m-d H:i:s'),
						'user_update'		=> $ci->session->userdata('useradminid')
					);

					$ci->db->where('retail_creditnotedetail.id', $val);
					$ci->db->update('retail_creditnotedetail', $setoff);

					// ============== Log_Detail ============== //
					$log_query = $ci->db->last_query();
					$last_id = $ci->session->userdata('log_id');
					$detail = "Update creditnotedetail id:" . $val . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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

					$ci->db->where('retail_creditnoteimg.id', $val);
					$ci->db->update('retail_creditnoteimg', $setoff);

					// ============== Log_Detail ============== //
					$log_query = $ci->db->last_query();
					$last_id = $ci->session->userdata('log_id');
					$detail = "Update retail_creditnoteimg id:" . $val . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
			$uploadimage = $ci->creditnote->upload_img($arraybill, $arrayfile);
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
		$txt = 'อัพเดตข้อมูลใบลดหนี้สำเร็จ';

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
		$uploadsDir = BASE_PIC . "creditnote/";
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
								'creditnote_id '    =>  $arraybill['bill_id'],
								'path'      	 	=>  $check_upload['data'],
								'date_starts'   	=>  date('Y-m-d H:i:s')
							);
							$ci->db->insert('retail_creditnoteimg', $datainsert);
							$new_id = $ci->db->insert_id();
							if (!$new_id) {
								$txterror = 'ข้อมูลไม่ถูกบันทึก ' . $targetPath;
							} else {
								// ============== Log_Detail ============== //
								$log_query = $ci->db->last_query();
								$last_id = $ci->session->userdata('log_id');
								$detail = "Insert retail_creditnoteimg name:" . $check_upload['data'] . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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



					/* //	code file upload original
					if (in_array($fileType, $allowedFileType)) {
						if (move_uploaded_file($img_tmp, $targetPath)) {
							$datainsert = array(
								'creditnote_id '    =>  $arraybill['bill_id'],
								'path'      	 	=>  $targetPath,
								'date_starts'   	=>  date('Y-m-d H:i:s')
							);
							$ci->db->insert('retail_creditnoteimg', $datainsert);
							$new_id = $ci->db->insert_id();
							if ($new_id) {
								$error = 0;
								$txt = 'ทำรายการบันทึกไฟล์สำเร็จ';
							}
						}
					} else {	//	error

						$txt = 'ไม่สามารถบันทึกไฟล์ชนิดนี้ได้';
					} */
				}
			}

			/* if(count($check_upload)){
				
			}else{
				$txt = 'ไม่สามารถบันทึกไฟล์ได้ [checkupload]';
			} */
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

	function read_creditnoteBill($bill_id)
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

		$q = $ci->creditnote->get_creditnoteBillDetail($text);
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

		$q = $ci->creditnote->get_retailBill($text);
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

		$q = $ci->creditnote->get_retailSearchBill($text);
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
	//		bill_id		@int 	=> bill creditnote id
	//		approve		@int 	=> creditnote approve
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
				->from('retail_creditnote')
				->where('retail_creditnote.codereport is not null')
				->where('retail_creditnote.id', $bill_id)
				->where('retail_creditnote.status', 1);
			$qold = $sqlold->get();
			$numold = $qold->num_rows();
			if ($numold) {

				$txt = 'ไม่สามารถยกเลิกได้ รายการสำเร็จครบแล้ว';
				$result = array(
					'error_code'	=> $error,
					'txt'			=> $txt
				);

				return $result;
			}

			//	check product loss for set complete 1[waite] or 2[success]
			$sql = $ci->db->select('code,loss')
				->from('retail_creditnote')
				->where('retail_creditnote.id', $bill_id);
			$q = $sql->get();
			$num = $q->num_rows();
			if ($num) {
				$r = $q->row();

				if ($r->loss) {
					$complete = 2;
				} else {
					//	check detail
					$sqlcheck = $ci->db->select('id')
						->from('retail_creditnotedetail')
						->where('retail_creditnotedetail.creditnote_id', $bill_id);
					$qcheck = $sqlcheck->get();
					$numcheck = $qcheck->num_rows();
					if ($numcheck) {
						$complete = 1;
					}else{
						$complete = 2;
					}
				}

				//	หากดึง status กลับให้ complete เป็น 0
				if (!$approve) {
					$complete = 0;

					//	ลบ temp ของ retail_receive
					$setarray = array(
						'bill_id'	=> $bill_id
					);
					$ci->creditnote->delete_billReceive($setarray);
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
			$ci->db->where('retail_creditnote.id', $bill_id);
			$ci->db->update('retail_creditnote', $dataupdate);
			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Update creditnote approve id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
			if ($complete == 2) {
				$setarray = array(
					'bill_id'	=> $bill_id
				);
				$ci->creditnote->update_codeCreditnote($setarray);
			}

			// หากมีการรับของให้สร้าง temp ที่ retail_receive
			if ($complete == 1) {
				$setarray = array(
					'bill_id'	=> $bill_id
				);
				$ci->creditnote->create_billReceive($setarray);
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

		if ($bill_id) {

			if (!$approve) {
				$complete = 0;
			} else {
				$complete = 2;
			}

			//	run upload
			$txterror = "";
			$dataupdate = array(
				'approve_store'    	=>  $approve,
				'complete'    	=>  $complete,
				'apst_date'   	=>  date('Y-m-d H:i:s'),
				'apst_user'   	=>  $ci->session->userdata('useradminid')
			);
			$ci->db->where('retail_creditnote.id', $bill_id);
			$ci->db->update('retail_creditnote', $dataupdate);
			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "Update creditnote approve store id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
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
			if ($complete == 2) {
				$setarray = array(
					'bill_id'	=> $bill_id
				);
				$ci->creditnote->update_codeCreditnote($setarray);
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
	//		bill_id		@int 	=> bill creditnote id
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

			//	check bill approve
			$sqlold = $ci->db->select('id')
				->from('retail_creditnote')
				->where('retail_creditnote.codereport is not null')
				->where('retail_creditnote.id', $bill_id)
				->where('retail_creditnote.status', 1);
			$qold = $sqlold->get();
			$numold = $qold->num_rows();
			if ($numold) {

				$txt = 'ไม่สามารถยกเลิกได้ รายการสำเร็จครบแล้ว';
				$result = array(
					'error_code'	=> $error,
					'txt'			=> $txt
				);

				return $result;
			}

			//	run upload
			$txterror = "";
			$dataupdate = array(
				'complete'    	=>  3,
				'remark_order'    	=>  $remark_order,
				'date_update'   	=>  date('Y-m-d H:i:s'),
				'user_update'   	=>  $ci->session->userdata('useradminid')
			);
			$ci->db->where('retail_creditnote.id', $bill_id);
			$ci->db->update('retail_creditnote', $dataupdate);
			// ============== Log_Detail ============== //
			$log_query = $ci->db->last_query();
			$last_id = $ci->session->userdata('log_id');
			$detail = "cancel creditnote id:" . $bill_id . " Code : " . $ci->session->userdata('useradminid') . " Name : " . $ci->session->userdata('useradminname');
			$type = "Update";
			$arraylog = array(
				'log_id'  	 	 => $last_id,
				'detail'  		 => $detail,
				'logquery'       => $log_query,
				'type'     	 	 => $type,
				'date_starts'    => date('Y-m-d H:i:s')
			);
			updateLog($arraylog);

			//	ลบ temp ของ retail_receive
			$setarray = array(
				'bill_id'	=> $bill_id
			);
			$ci->creditnote->delete_billReceive($setarray);

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

		$ci->db->select("
			retail_creditnoteimg.id as cni_id,
			retail_creditnoteimg.path as cni_path
		");
		$ci->db->from("retail_creditnoteimg");
		$ci->db->join('retail_creditnote', 'retail_creditnote.ID = retail_creditnoteimg.CREDITNOTE_ID ', 'left');
		$ci->db->where('retail_creditnoteimg.status', 1);
		$ci->db->where('retail_creditnoteimg.creditnote_id', $billid);
		$q = $ci->db->get();
		$num = $q->num_rows();

		if ($num) {
			$result = $q;
		} else {
			$result = "";
		}

		return $result;
	}

	function get_creditnoteBillDetail($bill_id)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$arrayselect = array(
			'retail_creditnote.id as cn_id',
			'retail_creditnote.code as cn_code',
			'retail_creditnote.codereport as cn_codereport',
			'retail_creditnote.rt_bill_code as cn_rt_bill_code',
			'retail_creditnote.total_price as cn_total_price',
			'retail_creditnote.parcel_cost as cn_parcel_cost',
			'retail_creditnote.delivery_fee as cn_delivery_fee',
			'retail_creditnote.discount_price as cn_discount_price',
			'retail_creditnote.shor_money as cn_shor_money',
			'retail_creditnote.tax as cn_tax',
			'retail_creditnote.net_total as cn_net_total',
			'retail_creditnote.approve as cn_approve',
			'retail_creditnote.approve_store as cn_approve_store',
			'retail_creditnote.complete as cn_complete',
			'retail_creditnote.remark as cn_remark',
			'retail_creditnote.remark_order as cn_remark_order',
			'retail_creditnote.loss as cn_loss',
			'retail_creditnote.date_starts as cn_date_starts',
			'retail_creditnote.user_starts as cn_user_starts',
			'retail_creditnote.date_update as cn_date_update',
			'retail_creditnote.user_update as cn_user_update',

			'retail_creditnote.appr_date as cn_appr_date',
			'retail_creditnote.appr_user as cn_appr_user',
			'retail_creditnote.apst_date as cn_apst_date',
			'retail_creditnote.apst_user as cn_apst_user',

			'retail_creditnotedetail.id as cnd_id',
			'retail_creditnotedetail.promain_id as cnd_productmain',
			'retail_creditnotedetail.prolist_id as cnd_productid',
			'retail_creditnotedetail.list_id as cnd_productlist',
			'retail_creditnotedetail.quantity as cnd_qty',
			'retail_creditnotedetail.total_price as cnd_price',

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
			'retail_creditnote.id' 					=> $bill_id
		);

		$q = $ci->creditnote->query_creditnotebilldetail($arrayselect, $arraywhere, null);
		$result = $q;

		return $result;
	}

	// query detail
	//	@param array[group_by]	@text = group by
	//	@param array[order_by]	@array = order by text 
	//	@param array[limit]		@array = [total[int] = total show , start[int] = start point on data]
	// function query_retailbill($arrayselect, $arraywhere, $arrayorderby, $arraylimit)
	function query_creditnotebilldetail($arrayselect, $arraywhere, $array)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting

		$ci->db->select($arrayselect);

		$ci->creditnote->query_creditnotedetail();

		$ci->db->where($arraywhere, null, FALSE);
		$ci->db->where('if(retail_creditnotedetail.creditnote_id is not null ,retail_creditnotedetail.status = 1,true)', null, FALSE);

		if ($array['order_by']) {
			foreach ($array['order_by'] as $val) {
				$ci->db->order_by($val);
			}
		} else {
			$ci->db->order_by('retail_creditnote.complete', 'asc');
		}

		if ($array['limit']) {
			$ci->db->limit($array['limit']['total'], $array['limit']['start']);	//	total , start
		}


		$result = $ci->db->get();

		return $result;
	}

	//	query creditnote and detail
	function query_creditnotedetail()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$ci->db->from("retail_creditnote");
		$ci->db->join('retail_creditnotedetail', 'if(retail_creditnote.ID = retail_creditnotedetail.CREDITNOTE_ID,retail_creditnote.ID = retail_creditnotedetail.CREDITNOTE_ID,retail_creditnotedetail.CREDITNOTE_ID is null) ', 'left', FALSE);
		$ci->db->join('retail_bill', 'retail_creditnote.RT_ID = retail_bill.ID ', 'left');
		// $ci->db->join('retail_productmain', 'retail_billdetail.PROMAIN_ID = retail_productmain.ID ', 'left');
		$ci->db->join('retail_productlist', 'retail_creditnotedetail.PROLIST_ID = retail_productlist.ID ', 'left');
		$ci->db->join('staff', 'retail_creditnote.USER_STARTS = staff.CODE ', 'left');
	}

	function get_creditnoteBill($textsearch)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		//	setting
		$arrayselect = array(
			'retail_creditnote.id as cn_id',
			'retail_creditnote.code as cn_code',
			'retail_creditnote.rt_bill_code as cn_rt_bill_code',
			'retail_creditnote.total_price as cn_total_price',
			'retail_creditnote.parcel_cost as cn_parcel_cost',
			'retail_creditnote.delivery_fee as cn_delivery_fee',
			'retail_creditnote.discount_price as cn_discount_price',
			'retail_creditnote.shor_money as cn_shor_money',
			'retail_creditnote.tax as cn_tax',
			'retail_creditnote.net_total as cn_net_total',
			'retail_creditnote.approve as cn_approve',
			'retail_creditnote.approve_store as cn_approve_store',
			'retail_creditnote.complete as cn_complete',
			'retail_creditnote.remark as cn_remark',
			'retail_creditnote.loss as cn_loss',
			'retail_creditnote.date_starts as cn_date_starts',
			'retail_creditnote.user_starts as cn_user_starts'
		);
		$arraywhere = array(
			'retail_creditnote.code like "%' . preg_replace("/_/", "\_", $textsearch) . '%"' 			=> null,
			'retail_creditnote.status' 		=> 1
		);

		$array = array(
			'order_by'		=> array('retail_creditnote.id desc'),
			'limit'			=> array(
				'total'	=> 25,
				'start'	=> 0
			),
		);


		$q = $ci->creditnote->query_creditnotebill($arrayselect, $arraywhere, $array);
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

		$ci->creditnote->query_creditnote();

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

		$ci->db->from("retail_creditnote");
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

		$q = $ci->creditnote->query_retailbilldetail($arrayselect, $arraywhere, null);
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


		$q = $ci->creditnote->query_retailbill($arrayselect, $arraywhere, $array);
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

		$ci->creditnote->query_retail();

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

		$ci->creditnote->query_retaildetail();

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
	// query detail
	//	@param status	@int = complete bill (creditnote)
	function get_creditnoteComplete($status)
	{
		switch ($status) {
			case 0:
				$statustext = "รอตรวจสอบ";
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
	// query detail
	//	@param id	@int = loss bill (creditnote)
	function get_creditnoteLoss($id)
	{
		switch ($id) {
			case 0:
				$statustext = "";
				break;
			case 1:
				$statustext = "สินค้าสูญเสีย";
				break;
		}

		$result = array(
			'data' => $statustext
		);

		return $result;
	}
}
