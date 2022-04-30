<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_createorder extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('mdl_createorder');
		$this->load->model('mdl_sql');
		$this->load->model('mdl_uplode');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->library('creditnote');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'max_upload_image'		=> 1000000,		// 1 k = 1000
			'max_size_image'		=> 1920,
			'ctl_name'				=> 'ctl_createorder',
			'mainmenu'		        => 'retail',
			'submenu'		        => 'bill',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	// public function updateBill() {

	// 	$this->db->select('*');
	// 	$this->db->from('retail_bill');
	// 	$this->db->where('STATUS_COMPLETE', 2);
	// 	$this->db->where('TRANSFERED_DAYTIME', NULL);
	// 	$this->db->where('STATUS', 1);
	// 	$Query = $this->db->get();
	// 	foreach($Query->result() AS $row){
	// 		$data = array( 'TRANSFERED_DAYTIME' => $row->DATE_STARTS );
	// 		$this->db->where('ID', $row->ID);
	//     	$this->db->update('retail_bill', $data);
	// 	}
	// 	$data = array(
	// 		'status' => $status,
	// 		'massage' => $massage
	// 	);
	// 	$this->load->view('updateBill', $data);
	// }

	public function bill()
	{
		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> 'createbill'
		);
		// $data['Query_methodorder'] = $this->mdl_sql->get_WhereParaqry('retail_methodorder', 'status', 1);
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('bill', $data);
	}

	public function get_retailMethod()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$dataresult = array();
	
			$request = $_REQUEST;
			$sql = $this->db->select('ID,TOPIC')
				->from('retail_methodorder')
				->where('delivery_id', $request['method_id'])
				->where('status', 1);
			$q = $sql->get();
			$number = $q->num_rows();
			if ($number) {
				$text = '';
				foreach($q->result() as $r){
					$text .= '<option value="'.$r->ID.'" > '.$r->TOPIC.' </option>';
				}

				$dataresult = array(
					'error_code'	=> 0,
					'txt'			=> '',
					'data'			=> $text
				);
			}

			$result = json_encode($dataresult);

			echo $result;
		}
		
	}

	public function createbill()
	{

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
		$data['Query_bank'] = $this->mdl_sql->get_WhereParaqry('bank', 'status', 1);
		if($this->session->userdata('franshine')){
			$data['Query_methodorder'] = $this->mdl_sql->get_Where2Paraqry('retail_methodorder', 'status', 1,'id', $this->session->userdata('franshine'));
		}else{
			$data['Query_methodorder'] = $this->mdl_sql->get_WhereParaqry('retail_methodorder', 'status', 1);
		}
		$data['Query_productmain'] = $this->mdl_sql->get_WhereParaqry('retail_productmain', 'status', 1);
		$data['Query_productlist'] = $this->mdl_sql->get_WhereParaqry('retail_productlist', 'status', 1);
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('createbill', $data);
	}

	public function createorder_update()
	{

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);

		$id = $this->input->get('id');
		$data['Query_bank'] = $this->mdl_sql->get_WhereParaqry('bank', 'status', 1);
		if($this->session->userdata('franshine')){
			$data['Query_methodorder'] = $this->mdl_sql->get_Where2Paraqry('retail_methodorder', 'status', 1,'id', $this->session->userdata('franshine'));
		}else{
			$data['Query_methodorder'] = $this->mdl_sql->get_WhereParaqry('retail_methodorder', 'status', 1);
		}
		$data['Query_billdetil'] = $this->mdl_createorder->datebilldetail($id);
		$data['Query_productmain'] = $this->mdl_sql->get_WhereParaqry('retail_productmain', 'status', 1);
		$data['Query_productlist'] = $this->mdl_sql->get_WhereParaqry('retail_productlist', 'status', 1);
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		// echo '<pre>'; print_r($data['Query_billdetil']); exit;
		$this->load->view('createorder_update', $data);
	}

	public function claimorder_update()
	{

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);

		$id = $this->input->post('hdfclaimorder');
		$data['Query_bank'] = $this->mdl_sql->get_WhereParaqry('bank', 'status', 1);
		$data['Query_methodorder'] = $this->mdl_sql->get_WhereParaqry('retail_methodorder', 'status', 1);
		$data['Query_billdetil'] = $this->mdl_createorder->datebilldetail($id);
		$data['Query_productmain'] = $this->mdl_sql->get_WhereParaqry('retail_productmain', 'status', 1);
		$data['Query_productlist'] = $this->mdl_sql->get_WhereParaqry('retail_productlist', 'status', 1);
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		// echo '<pre>'; print_r($data['Query_billdetil']); exit;
		$this->load->view('claimorder_update', $data);
	}

	public function viwecreatebill()
	{

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);

		$id = $this->input->get('id');
		$mdl = $this->input->get('mdl');
		if ($mdl) {
			$mdl = $mdl;
		} else {
			$mdl = 'mdl_createorder';
		}
		$data['Query_billdetil'] = $this->$mdl->datebilldetail($id);
		$data['billnewnumber'] = find_Billnew($id);
		$data['creditnotenumber'] = find_Creditnote($id);
		$data['receivenumber'] = find_Receive($id);
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		// echo '<pre>'; print_r($data['Query_billdetil']); exit;
		$this->load->view('viwecreatebill', $data);
	}

	public function excel()
	{

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
		$id = $this->input->get('id');
		$mdl = $this->input->get('mdl');
		if ($mdl) {
			$mdl = $mdl;
		} else {
			$mdl = 'mdl_createorder';
		}

		$data['Query_billdetil'] = $this->$mdl->datebilldetail($id);
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('excel', $data);
	}

	public function sentems()
	{

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
		$id = $this->input->get('id');
		$data['Query_billdetil'] = $this->mdl_createorder->datebilldetail($id);
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('sentems', $data);
	}


	public function ajaxeditstatus()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->ajaxeditstatus();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function statusapprove()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->statusapprove();
			$return = json_decode($returns);
			echo $returns;
		}
	}


	function fetch_createorder()
	{

		$mdl = 'mdl_createorder';

		$fetch_data = $this->$mdl->make_datatables();
		$basepic = base_url() . BASE_PIC;
		$data = array();
		$index = 1;
		$status_bnt = '';
		$remark = '';
		foreach ($fetch_data as $row) {
			//	find bill new
			$billnewnumber = find_Billnew($row->ID);
			if($billnewnumber['num']){
				$rbillnew = $billnewnumber['query']->row();
				$bill_new = '<div class="badge badge-info">บิลใหม่</div>';
			}else{
				$bill_new = "";
			}

			//	find bill creditnote
			$creditnumber = find_Creditnote($row->ID);
			if($creditnumber['num']){
				$rcreditnote = $creditnumber['query']->row();
				$bill_creditnote = '<div class="badge badge-secondary">ใบลดหนี้</div>';
			}else{
				$bill_creditnote = "";
			}

			//	find bill receive
			$receivenumber = find_Receive($row->ID);
			if($receivenumber['num']){
				$rreceive = $receivenumber['query']->row();
				$bill_store = '<div class="badge badge-warning">ใบรับสินค้า</div>';
			}else{
				$bill_store = "";
			}

			if ($row->STATUS_COMPLETE == 0) {
				$STATUS_COMPLETE = '<span class="span-Status-001"> รอการอนุมัติรายการ </span>';
			} else if ($row->STATUS_COMPLETE == 1) {
				$STATUS_COMPLETE = '<span class="span-Status-001"> รอการอนุมัติรายการ </span>';
			} else if ($row->STATUS_COMPLETE == 2) {
				if ($row->BILLSTATUS == "C" && $row->TRANSFERED_DAYTIME == null) {
					$STATUS_COMPLETE = '<span class="span-Status-002"> รอการอนุมัติรายการ <small> (เก็บเงินที่หลัง) </small> </span>';
				} else {
					$STATUS_COMPLETE = '<span style="font-weight: 300;color: #28a745 ;"> อนุมัติสำเร็จ </span>';
				}
			} else if ($row->STATUS_COMPLETE == 3) {
				$STATUS_COMPLETE = '<span style="font-weight: 300;color: #f44336 ;"> ยกเลิกรายการ </span>';
				if ($row->REMARK != '') {
					$remark = '<b>หมายเหตุ : </b>' . $row->REMARK . '<br>';
				}
			} 
			if ($row->STATUS_APPROVE1 == 1) {
				$APPROVE1 = '<span style="color: #28a745 ;"> <i class="fa fa-check-circle" aria-hidden="true"></i> </span>';
			} else {
				$APPROVE1 = '<span style="color: #ffc107 ;"> <i class="fa fa-clock-o" aria-hidden="true"></i> </span>';
			}
			if ($row->STATUS_APPROVE2 == 1) {
				$APPROVE2 = '<span style="color: #28a745 ;"> <i class="fa fa-check-circle" aria-hidden="true"></i> </span>';
			} else {
				$APPROVE2 = '<span style="color: #ffc107 ;"> <i class="fa fa-clock-o" aria-hidden="true"></i> </span>';
			}
			$disabled = '';
			$claim = 'style="display: none;" ';
			if ($row->STATUS_COMPLETE == 3) {
				$disabled = 'style="display: none;" ';
			} else if ($row->STATUS_COMPLETE == 2) {
				$disabled = 'style="display: none;" ';
				$claim = 'style="display: block;" ';
			}

			//	new query
			$sql = $this->db->select('NAME_US')
				->from('delivery')
				->where('id', $row->DELIVERY_FORMID)
				->get();
			$numdelevery = $sql->num_rows();
			if ($numdelevery > 0) {
				$r = $sql->row();
				$DELIVERYFORMID = $r->NAME_US;
			}
			$sql = $this->db->select('TOPIC')
				->from('retail_methodorder')
				->where('id', $row->METHODORDER_ID)
				->get();
			$numdelevery = $sql->num_rows();
			if ($numdelevery > 0) {
				$r = $sql->row();
				$METHODORDER_ID = $r->TOPIC;
			}

			$style_claim = '';
			$rowID = $row->ID;
			$STATUSCLAIM = '';
			if ($row->STATUS_COMPLETE != 4) {
				$content = '<b> สาขา : ' . $APPROVE1 . '</b><br>';
				$content .= '<b> เขต : ' . $APPROVE2 . '</b><br>';
				$content .= '<b> สถานะ : ' . $STATUS_COMPLETE . '</b><br>';
			} 

			//	button
			$bill_cancel = chkPermissPage('btn_billcancel');
			if ($bill_cancel == 1) {
				$btn_cancel = '<button type="button" ' . $disabled . ' class="btn btn-app3 btn-block bg-danger" data-toggle="modal" data-target="#exampleModalCenter" id="deleteorder" value="' . $rowID . '"> <li class="fa fa-trash-o"> </li>  ยกเลิกออเดอร์ </button>';
			}
			$bill_claim = chkPermissPage('btn_billclaim');
			if ($bill_claim == 1) {
				$btn_claim = '<button type="button" ' . $claim . ' class="btn btn-app3 btn-block bg-info" data-toggle="modal" data-target="#modalclaim" id="modalclaim-btn" value="' . $rowID . '"> <li class="fa fa-archive"> </li>  เคลม </button>';
			}
			//	cancel claim
			$btn_claim = "";
			$textcode = "";
			($row->TextCode ? $textcode = "(" . $row->TextCode . ")" : false);
			($row->REF ? $textcode = "(" . $row->REF . ")" : false);

			//	check หากมีการเปิดบิลหน้าที่เกี่ยวข้องกับบิลนี้ ห้ามยกเลิก
			$countBillreceive = countBillreceive_search($row->ID);
			$countBillCreditnote = countBillCreditnote_search($row->ID);
			if($countBillreceive || $countBillCreditnote){
				$btn_cancel = "";
			}
	
			$sub_array = array();
			$sub_array[] = "<div class='text-center'>" . $index++ . "</div>";
			$sub_array[] =  '	
							<div class="row ' . $style_claim . '">
								<div class="col-sm-5">
									<div class="list-CA001">
											<b>รหัสออเดอร์ : ' . $row->CODE . '</b> <br>
											วันที่ : ' . thai_date($row->DATE_STARTS) . ' เวลา : ' . date('H:i:s', strtotime($row->DATE_STARTS)) . ' น. <br>
											รูปแบบการส่ง : ' . $DELIVERYFORMID . ' | สาขา : ' . $METHODORDER_ID .'
									</div>
								</div>
								<div class="col-sm-5">
									<div class="status-CA001">
										' . $content . '
										' . $remark . '
										' . $STATUSCLAIM . '
										' . $bill_new . '
										' . $bill_creditnote . '
										' . $bill_store . '
									</div>
								</div>
								<div class="col-sm-2">
									<div class="bnt-CA001" >
											<a target="_blank" href="' . site_url('mod_retailcreateorder/ctl_createorder/viwecreatebill?id=' . $rowID . '&mdl=' . $mdl) . '" class="btn btn-app3 btn-block"> <i class="fa fa-eye" aria-hidden="true"></i> ตรวจสอบ </a>
											' . $btn_cancel . '' . $btn_claim . '
 									</div>
								</div>
							</div>
							 
							';
			$data[] = $sub_array;
		}
		$output = array(
			"draw"             	=>     intval($_POST["draw"]),
			"recordsTotal"      =>     $this->$mdl->get_all_data(),
			"recordsFiltered"   =>     $this->$mdl->get_filtered_data(),
			"data"              =>     $data
		);

		echo json_encode($output);
	}

	public function ajaxaddrowtable()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->ajaxaddrowtable();
			$return = json_decode($returns);
			echo $returns;
		}
	}
	public function ajaxselectproductmain()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->ajaxselectproductmain();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajaxdataform()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->ajaxdataform();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function checkFile()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_uplode->checkFile();
			$return = json_decode($returns);
			echo $returns;
		}
	}
	public function deleteorder()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->deleteorder();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function claimorder()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->claimorder();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajaximg()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->ajaximg();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function claimDelivery()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_createorder->claimDelivery();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajaxchecktextcode()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$num = 0;
			if (!empty($this->input->post('textcode'))) {
				$this->db->select('*');
				$this->db->from('retail_bill');
				$this->db->where('retail_bill.TextCode', trim($this->input->post('textcode')));
				$this->db->where('retail_bill.status_complete !=', 3);
				$q = $this->db->get();
				$num = $q->num_rows($q);
			}
			$data = json_encode($num);
			echo $data;
		}
	}

	//	find history
	public function findHistory()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$request = $_REQUEST;
			/* echo "<pre>";
			print_r($request);
			echo "</pre>"; */

			$bill_id = $request['bill_id'];
			$func_findCreditnote = find_Creditnote($bill_id);

			$datarow = array();
			if ($func_findCreditnote['query']) {
				foreach ($func_findCreditnote['query']->result() as $row) {
					$subrow['id'] = $row->ID;
					$subrow['code'] = $row->CODE;
					$subrow['net'] = $row->NET_TOTAL;
					$subrow['date'] = date('Y-m-d', strtotime($row->DATE_STARTS));
					$subrow['time'] = date('H:i:s', strtotime($row->DATE_STARTS));
					$subrow['type'] = ($row->LOSS ? "สูญเสีย" : "");
					$datarow[] = $subrow;
				}
			}

			$dataresult = array(
				'error_code' 	=> 0,
				'txt' 			=> 'success',
				'data' 			=> array(
					'listorder'	=> $datarow,
					'bill_id'	=> $bill_id,
					'num'		=> $func_findCreditnote['num']
				)
			);
			$result = json_encode($dataresult);
			echo $result;
		}
	}

	//	find bill new
	public function findBillNew()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$request = $_REQUEST;
			$bill_id = $request['bill_id'];

			$sql = $this->db->select('ID,CODE,NET_TOTAL,DATE_STARTS,BILLSTATUS')
				->from('retail_bill')
				->where('billref_id', $bill_id)
				->where('status_complete', 2)
				->where('status', 1);
			$q = $sql->get();
			$number = $q->num_rows();
			if ($number) {
				foreach ($q->result() as $row) {

					switch ($row->BILLSTATUS) {
						case 'T':
							$type = 'โอนเงิน';
							break;
						case 'C':
							$type = 'ปลายทาง';
							break;
						case 'F':
							$type = 'อื่นๆ';
							break;
					}

					$subrow['id'] = $row->ID;
					$subrow['code'] = $row->CODE;
					$subrow['net'] = $row->NET_TOTAL;
					$subrow['date'] = date('Y-m-d', strtotime($row->DATE_STARTS));
					$subrow['time'] = date('H:i:s', strtotime($row->DATE_STARTS));
					$subrow['type'] = $type;
					$datarow[] = $subrow;
				}
			}

			$dataresult = array(
				'error_code' 	=> 0,
				'txt' 			=> 'success',
				'data' 			=> array(
					'listorder'	=> $datarow,
					'bill_id'	=> $bill_id,
					'num'		=> $number
				)
			);
			$result = json_encode($dataresult);
			echo $result;
		}
	}

	//	find bill ref
	public function findBillRef()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$request = $_REQUEST;

			$bill_id = $request['bill_id'];

			$sql = $this->db->select('ID,BILLREF_ID')
				->from('retail_bill')
				->where('id', $bill_id)
				->where('status', 1);
			$q = $sql->get();
			$number = $q->num_rows();
			if ($number) {

				$r = $q->row();
				if ($r->BILLREF_ID) {

					$sqlref = $this->db->select('ID,CODE,NET_TOTAL,DATE_STARTS,BILLSTATUS')
						->from('retail_bill')
						->where('id', $r->BILLREF_ID);
					$qref = $sqlref->get();
					$numberref = $qref->num_rows();
					if ($numberref) {
						foreach ($qref->result() as $row) {

							switch ($row->BILLSTATUS) {
								case 'T':
									$type = 'โอนเงิน';
									break;
								case 'C':
									$type = 'ปลายทาง';
									break;
								case 'F':
									$type = 'อื่นๆ';
									break;
							}

							$subrow['id'] = $row->ID;
							$subrow['code'] = $row->CODE;
							$subrow['net'] = $row->NET_TOTAL;
							$subrow['date'] = date('Y-m-d', strtotime($row->DATE_STARTS));
							$subrow['time'] = date('H:i:s', strtotime($row->DATE_STARTS));
							$subrow['type'] = $type;
							$datarow[] = $subrow;
						}
					}
				}
			}

			$dataresult = array(
				'error_code' 	=> 0,
				'txt' 			=> 'success',
				'data' 			=> array(
					'listorder'	=> $datarow,
					'bill_id'	=> $bill_id,
					'num'		=> $number
				)
			);
			$result = json_encode($dataresult);
			echo $result;
		}
	}

	//	find bill receive
	public function findReceive()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$request = $_REQUEST;
			$bill_id = $request['bill_id'];
			$funcbill = find_Receive($bill_id);

			$datarow = array();
			if ($funcbill['query']) {
				foreach ($funcbill['query']->result() as $row) {
					$subrow['id'] = $row->ID;
					$subrow['code'] = $row->CODE;
					$subrow['date'] = date('Y-m-d', strtotime($row->DATE_STARTS));
					$subrow['time'] = date('H:i:s', strtotime($row->DATE_STARTS));
					$datarow[] = $subrow;
				}
			}

			$dataresult = array(
				'error_code' 	=> 0,
				'txt' 			=> 'success',
				'data' 			=> array(
					'listorder'	=> $datarow,
					'bill_id'	=> $bill_id,
					'num'		=> $funcbill['num']
				)
			);
			$result = json_encode($dataresult);
			echo $result;
		}
	}
	/**
	 * ========================================================================================
	 * ส่วนการทำงานของปุ่มเอกสารเพิ่มเติม
	 * ========================================================================================
	 */
	//	get data bill to add
	public function get_orderBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['bill_id']);

			$result = "";
			$data = array();
			$datadetail = array();

			if ($text) {
				$q = $this->creditnote->read_bill($text);

				if ($q->result()) {
					foreach ($q->result() as $r) {

						//	ชำระ
						if ($r->rt_billstatus == 'T') {
							$billstatus = 'โอนเงิน';
						} else if ($r->rt_billstatus == 'C') {
							$billstatus = 'เก็บปลายทาง';
						} else {
							$billstatus = 'ฟรี';
						}

						//	สถานะบิล
						if ($r->rt_complete == '2' || $r->rt_complete == '5') {
							$complete = 'อนุมัติ';
						} else {
							$complete = 'รออนุมัติ';
						}

						$data	= array(
							'id'	=> trim($r->rt_id),

							'name'	=> trim($r->rt_name),
							'tel'	=> trim($r->rt_tel),
							'citizen'	=> trim($r->rt_citizen),
							'address'		=> trim($r->rt_address),
							'zipcode'		=> trim($r->rt_zipcode),

							'code'	=> trim($r->rt_code),
							'receive_id'	=> trim($r->rt_delivery_formid),
							'delivery_id'	=> trim($r->rt_methodorder_id),
							'receive'	=> trim($r->rtm_name),
							'delivery'	=> trim($r->rtd_name),
							'textcode'		=> (trim($r->rt_textcode) ? trim($r->rt_textcode) : trim($r->rt_ref)),
							'billstatus'	=> trim($billstatus),
							'complete'		=> trim($complete),

							'price'		=> trim($r->rt_total_price),
							'parcel'		=> trim($r->rt_parcel_cost),
							'logis'		=> trim($r->rt_delivery_fee),
							'shor'		=> trim($r->rt_shor_money),
							'discount'		=> trim($r->rt_discount_price),
							'tax'		=> trim($r->rt_tax),
							'net'		=> trim($r->rt_net_total),

							'bank'		=> trim($r->b_name),
							'bank_daytime'		=> (trim($r->rt_bank_daytime) ? thai_date(date('Y-m-d', strtotime(trim($r->rt_bank_daytime)))) . " - " . date('H:i', strtotime(trim($r->rt_bank_daytime))) : ""),
							'bank_amount'		=> trim($r->rt_bank_amount),
							'bank_remark'		=> trim($r->rt_bank_remark),

							'datecreate'		=> (trim($r->rt_datestarts) ? thai_date(date('Y-m-d', strtotime(trim($r->rt_datestarts)))) : ""),

							'staffcreate'		=> trim($r->sf_name) . " " . trim($r->sf_lastname),
							'remark'	=> trim($r->rt_remark)
						);

						$datadetail[]	= array(
							'product_name'	=> $r->rtp_name,
							'product_qty'	=> $r->rtd_qty,
							'product_price'	=> $r->rtp_price,
							'product_totalprice'	=> $r->rtd_price,

							'promain'	=> trim($r->rtd_productmain),
							'prolist'	=> trim($r->rtd_productid),
							'list'		=> trim($r->rtd_productlist)
						);
					}

					$dataresult = array('data' => $data, 'datadetail' => $datadetail);
				}

				$result = json_encode($dataresult);
			}

			echo $result;
		}
	}

	//	add
	public function add_Creditnote()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->creditnote->add_bill($request, $_FILES);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	get data bill to add
	public function countcreditnote()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$count = countCreditnote();
			$dataresult = array(
				'error_code' => 0,
				'txt' => 'success',
				'data' => array('count' => $count)
			);
			$result = json_encode($dataresult);

			echo $result;
		}
	}
	//	get data bill to add
	public function countReceive()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$count = countBillreceive();
			$dataresult = array(
				'error_code' => 0,
				'txt' => 'success',
				'data' => array('count' => $count)
			);
			$result = json_encode($dataresult);

			echo $result;
		}
	}

	//	add
	public function add_BillNew()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$this->load->library('Bill');
			$request = $_REQUEST;

			$q = $this->bill->add_BillNew($request, $_FILES);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	get product data information
	public function get_product()
	{
		if ($this->input->server('REQUEST_METHOD')) {

			$request = $_REQUEST;
			$pid = $request['pid'];
			$pqty = $request['pqty'];

			$sql = $this->db->select('*')
				->from('retail_productlist')
				->where('id', $pid)
				->where('status', 1);
			$q = $sql->get();
			$num = $q->num_rows();

			if ($num) {
				$r = $q->row();
				$data = array(
					'name_th'	=> $r->NAME_TH,
					'price'	=> $r->PRICE,
					'qty'	=> $pqty,
					'main'	=> $r->PROMAIN_ID,
					'list'	=> $r->LIST_ID ? $r->LIST_ID  : "" ,
					'id'	=> $pid
				);

				$result = json_encode($data);
			} else {
				$result = "";
			}

			echo $result;
		}
	}

	//	add
	public function add_Store()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$this->load->library('Receive');
			$request = $_REQUEST;

			$q = $this->receive->add_bill($request, $_FILES);

			$result = json_encode($q);
			echo $result;
		}
	}
}
