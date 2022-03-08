<?php

use phpDocumentor\Reflection\Types\Integer;

defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_issue extends CI_Controller
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
		$this->load->model('mdl_issue');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->library('Issue');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_issue',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function issue()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailissue'
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('issue', $data);
	}

	public function getDataBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$total = $this->mdl_issue->alldata();
			$sql = $this->mdl_issue->makedata();

			$data = array();
			$subdata = array();

			//	sql creditnote
			if ($sql->result()) {
				$index = $request['start'] + 1;
				foreach ($sql->result() as $row) {

					// $textdisplay = "<font class='text-bold'>".$row->cn_code." <i class='fas fa-search text-muted'></i></font>";
					$textdisplay = "<a href='" . site_url('mod_retailissue/ctl_issue/viewbill?id=' . $row->tb_id) . "' target=_blank class='' >" . $row->pd_name_th . " </a>";

					if($row->sp_user_update){
						$date = thai_date(date('Y-m-d',strtotime($row->tb_date_update)));
						$staff = $this->mdl_issue->findUsernameByCode($row->tb_user_update);
					}else{
						$date = thai_date(date('Y-m-d',strtotime($row->tb_date_starts)));
						$staff = $this->mdl_issue->findUsernameByCode($row->tb_user_starts);
					}

					$rowarray = array();
					$rowarray['DT_RowId'] = $row->sp_id;	//	set row id
					$rowarray['id'] = $index;
					$rowarray['code'] = $row->tb_code;
					$rowarray['name'] = $textdisplay;
					$rowarray['qty'] = $row->tb_qty;
					$rowarray['remark'] = $row->tb_remark;
					$rowarray['type'] = status_issue($row->tb_type);
					$rowarray['date'] = $date;
					$rowarray['user'] = $staff;
					$rowarray['status'] = status_complete($row->tb_complete);

					$subdata[] = $rowarray;
					$index++;
				}
			}

			$data['draw'] = intval($request['draw']);
			$data['recordsTotal'] = $total;
			$data['recordsFiltered'] = $total;
			$data['data'] = $subdata;

			$result = json_encode($data);
			echo $result;
		}
	}

	public function viewbill()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailissue'
		);

		$id = $this->input->get('id');
		$data['method'] = $this->uri->segment(3);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('viewbill', $data);
	}

	public function editbill()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailissue'
		);

		$id = $this->input->get('id');
		$data['method'] = $this->uri->segment(3);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('editbill', $data);
	}

	public function genCodeBill()
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		//	setting
		$table = 'retail_issue';
		$codebill = 'IS';

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

	public function add_Bill() {
		$dataresult = array();

		$error_code = 1;
		$txt = "ไม่สามารถทำรายการได้";

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$request = $_REQUEST;

			if(!$this->input->post('bill_type')){
				$dataerror = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุรูปแบบ'
				);
				$result = json_encode($dataerror);
				echo $result;
				exit;
			}

			if(!$request['item']){
				$dataerror = array(
					'error_code'	=> 1,
					'txt'			=> 'โปรดระบุสินค้า'
				);

				$result = json_encode($dataerror);
				echo $result;
				exit;
			}

			//	check array item zero
			if ($request['item']) {
				foreach ($request['item'] as $key => $val) {
					$qty = get_valueNullToNull($val['qty']);

					if ($qty <= 0 || $qty == null) {
						$dataerror = array(
							'error_code'	=> 1,
							'txt'			=> 'มีจำนวนสินค้าไม่ถูกต้อง'
						);
						$result = json_encode($dataerror);

						echo $result;
						exit;
					}
				}
			}

			foreach($request['item'] as $key => $val){
				//
				$gencode = $this->genCodeBill();

				$billtype = trim($request['bill_type']);
				$remark = trim($request['remark']);
				$billto = get_valueNullToNull(trim($request['billto']));

				$complete = 2;
				if($billtype == 1){		//	ยืม
					$complete = 0;
				}

				if($billtype == 3){		//	แปลงสินค้า
					$complete = 0;
				}

				if($billtype == 1){
					if(!$billto){
						$dataerror = array(
							'error_code'	=> 1,
							'txt'			=> 'โปรดระบุชื่อผู้ยืม'
						);
						$result = json_encode($dataerror);

						echo $result;
						exit;
					}
				}
				$sp_bill_id = null;
				$sp_bill_name = null;
				if($request['sp_bill_id']){
					$sp_bill_id = $request['sp_bill_id'];
					$sp_bill_name = $request['sp_bill_name'];
				}

				$datainsert = array(
					'code'		=> $gencode,
					'sp_bill_id'	=> $sp_bill_id,
					'sp_bill_name'	=> $sp_bill_name,
					'billto'	=> $billto,
					'complete'	=> $complete,
					'type'		=> $billtype,
					'remark'	=> $remark,

					'promain_id'	=> $request['item'][$key]['promain'],
					'prolist_id'	=> $request['item'][$key]['prolist'],
					'list_id'		=> get_valueNullToNull($request['item'][$key]['list']),
					'quantity'		=> $request['item'][$key]['qty'],
					'total_price'	=> $request['item'][$key]['totalprice'],

					'date_starts'	=> date('Y-m-d H:i:s'),
					'user_starts'	=> $this->session->userdata('useradminid')
				);
				$this->db->insert('retail_issue',$datainsert);

				$id = $this->db->insert_id();
				if($id){
					

					// ============== Log_Detail ============== //
					$log_query = $this->db->last_query();
					$last_id = $this->session->userdata('log_id');
					$detail = "Insert retail_issue id:" . $id . " Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
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

			//	check update complete
			if($request['sp_bill_id']){
				$arraybillsup = array(
					'bill_id'		=> $request['sp_bill_id'],
					'item'			=> array()
				);
				$this->load->library('supplier');
				$check_supplier = $this->supplier->check_complete($arraybillsup);
			}
			

			$error_code = 0;
			$txt = "ทำรายการสำเร็จ";			
		}

		$dataresult = array(
			'error_code' 	=> $error_code,
			'txt'			=> $txt
		);
		
		$result = json_encode($dataresult);

		echo $result;
	}

	//	get data bill to add
	public function get_dataBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['bill_id']);

			$result = "";
			$data = array();
			$datadetail = array();
			$datareceivedetail = array();

			if ($text) {
				$querycheck = $this->db->select('id')
					->from('retail_receive')
					->where('is_bill_id', $text)
					->where('complete !=', 3)
					->where('status', 1);

				//	check order หากมีการรับเข้าเเล้วไม่ให้ยกเลิก
				$sqlcheck = $querycheck;
				$numcheck = $this->db->count_all_results(null, false);
				$qcheck = $sqlcheck->get();

				if ($numcheck) {
					$rowcheck = $qcheck->row();

					$check_rc = 1;
					$receive_id = $rowcheck->id;
				} else {
					$check_rc = "";
				}

				$table = 'retail_issue';
				$tablestaff = 'staff';

				$sql = $this->db->select(
					$table . '.ID as tb_id,' .
					$table . '.CODE as tb_code,' .
					$table . '.SP_BILL_ID as tb_sp_bill_id,' .
            		$table . '.SP_BILL_NAME as tb_sp_bill_name,' .
					$table . '.BILLTO as tb_billto,' .
					$table . '.COMPLETE as tb_complete,' .
					$table . '.TYPE as tb_type,' .
					$table . '.REMARK as tb_remark,' .

					$table . '.PROMAIN_ID as tb_promain_id,' .
					$table . '.PROLIST_ID as tb_prolist_id,' .
					$table . '.LIST_ID as tb_list_id,' .
					$table . '.QUANTITY as tb_qty,' .

					$table . '.DATE_STARTS as tb_date_starts,' .
					$table . '.USER_STARTS as tb_user_starts,' .
					$table . '.DATE_UPDATE as tb_date_update,' .
					$table . '.USER_UPDATE as tb_user_update,' .
					'retail_productlist.name_th as tb_product_name,'
				)
				->from($table)
				->join('retail_productlist','if('.$table.'.LIST_ID,'.$table.'.LIST_ID=retail_productlist.id,'.$table.'.PROLIST_ID=retail_productlist.id)',null,false)
				->where($table.'.id',$text)
				->where($table.'.status',1);
				$q = $sql->get();
				$num = $q->num_rows();

				if ($num) {
					foreach ($q->result() as $r) {

						//	check total product
						if ($numcheck) {
							$textsql = '
								sum(retail_receivedetail.quantity) as total,
								retail_receive.code as rc_code,
								retail_receive.date_starts as rc_date_starts,
								retail_receivedetail.quantity as rc_qty,
								staff.name as st_name,
								staff.lastname as st_lastname,
								staff.name_th as st_name_th,
								staff.lastname_th as st_lastname_th,
							';
							$queryprocheck = $this->db->select($textsql)
								->from('retail_receive')
								->join('retail_receivedetail', 'retail_receive.id=retail_receivedetail.receive_id', 'left')
								->join('staff', 'retail_receive.user_starts=staff.id', 'left')
								->where('retail_receivedetail.promain_id', $r->tb_promain_id)
								->where('retail_receivedetail.prolist_id', $r->tb_prolist_id)
								->where('retail_receivedetail.list_id', $r->tb_list_id)
								->where('retail_receivedetail.status', 1)
								->where('retail_receive.is_bill_id', $text)
								->where('retail_receive.complete', 2)
								->where('retail_receive.status', 1);

							$sql_productcheck = $queryprocheck;
							$num_productcheck = $this->db->count_all_results(null, false);
							$q_productcheck = $sql_productcheck->get();
							if ($num_productcheck) {
								$r_productcheck = $q_productcheck->row();
								$total = $r_productcheck->total;
								settype($r_productcheck->total, "integer");
								$waitetotal = $r->tb_qty - $r_productcheck->total;

								//	แยกตามแถวบนฐานข้อมูล
								$textsql2 = '
									retail_receive.code as rc_code,
									retail_receive.date_starts as rc_date_starts,
									retail_receivedetail.quantity as rc_qty,
									staff.name as st_name,
									staff.lastname as st_lastname,
									staff.name_th as st_name_th,
									staff.lastname_th as st_lastname_th,
								';
								$queryprocheck2 = $this->db->select($textsql2)
									->from('retail_receive')
									->join('retail_receivedetail', 'retail_receive.id=retail_receivedetail.receive_id', 'left')
									->join('staff', 'retail_receive.user_starts=staff.id', 'left')
									->where('retail_receivedetail.promain_id', $r->tb_promain_id)
									->where('retail_receivedetail.prolist_id', $r->tb_prolist_id)
									->where('retail_receivedetail.list_id', $r->tb_list_id)
									->where('retail_receivedetail.status', 1)
									->where('retail_receive.is_bill_id', $text)
									->where('retail_receive.complete', 2)
									->where('retail_receive.status', 1);
								$sql_productcheck = $queryprocheck2;
								$queryprocheck2 = $sql_productcheck->get();
								foreach ($queryprocheck2->result() as $r_productcheck2) {
									//	list order receive
									$datareceivedetail[]	= array(
										'codename'	=> $r_productcheck2->rc_code,
										'date_starts'	=> thai_date(date('Y-m-d', strtotime($r_productcheck2->rc_date_starts))),
										'by'		=> ($r_productcheck2->st_name_th ? trim($r_productcheck2->st_name_th) . " " . trim($r_productcheck2->st_lastname_th) : trim($r_productcheck2->st_name) . " " . trim($r_productcheck2->st_lastname)),
										'product_name'	=> $r->tb_product_name,
										'product_qty'	=> $r_productcheck2->rc_qty,
									);
								}
							} else {
								$total = "";
								$waitetotal = "";
							}
						} else {
							$total = "";
							$waitetotal = "";
						}

						switch ($r->tb_complete) {
							case 0:
								$statustext = 'รอสินค้า';
								break;
							case 1:
								$statustext = 'รอสินค้า';
								break;
							case 2:
								$statustext = "<font class='text-success'>สำเร็จ</font>";
								break;
							case 3:
								$statustext = "<font class='text-danger'>ยกเลิก</font>";
								break;
						}

						$data	= array(
							'tb_id'	=> trim($r->tb_id),
							'check_rc'		=> $check_rc,

							'tb_code'		=> trim($r->tb_code),
							'tb_sp_bill_name'		=> trim($r->tb_sp_bill_name),
							'tb_billto'		=> trim($r->tb_billto),
							'tb_complete'		=> $statustext,
							'tb_typeid'		=> $r->tb_type,
							'tb_type'		=> status_issue($r->tb_type),
							'tb_remark'		=> trim($r->tb_remark),
							'tb_date_starts'	=> trim($r->tb_date_starts),
							'tb_user_starts'		=> (trim($r->tb_user_starts) ? $this->mdl_issue->findUsernameByCode($r->tb_user_starts) : ""),
							'tb_date_update'		=> trim($r->tb_date_update),
							'tb_user_update'		=> (trim($r->tb_user_update) ? $this->mdl_issue->findUsernameByCode($r->tb_user_update) : "")
						);
					}

					if($r->tb_prolist_id){
						$datadetail[]	= array(
							'product_rowid'	=> "",
							'product_name'	=> $r->tb_product_name,
							'product_qty'	=> $r->tb_qty,
							'product_price'	=> 0,
							'product_totalprice'	=> 0,

							'product_receive'	=> $total,
							'product_receivewaite'	=> (!empty($waitetotal) ? $waitetotal : ""),

							'promain'	=> trim($r->tb_promain_id),
							'prolist'	=> trim($r->tb_prolist_id),
							'list'		=> trim($r->tb_list_id)
						);
					}

					$dataresult = array('data' => $data, 'datadetail' => $datadetail, 'datareceivedetail' => $datareceivedetail);
					$result = json_encode($dataresult);
				} else {
					$result = json_encode($result);
				}
			}

			echo $result;
		}
	}

	//	cancel bill
	public function cancelBill()
	{
		$dataresult = array();

		$error_code = 1;
		$txt = "ไม่สามารถยกเลิกรายการได้";

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$request = $_REQUEST;
			if($request['bill_id']){

				$dataupdate = array(
					'status'	=> 0,
					'date_update'	=> date('Y-m-d H:i:s'),
					'user_update'	=> $this->session->userdata('useradminid')
				);

				$this->db->where('retail_issue.id',$request['bill_id']);
				$this->db->update('retail_issue',$dataupdate);

				$error_code = 0;
				$txt = "ยกเลิกรายการสำเร็จ";			
			}
		}

		$dataresult = array(
			'error_code' 	=> $error_code,
			'txt'			=> $txt
		);
		
		$result = json_encode($dataresult);

		echo $result;
	}

	//	add product
	public function ajaxselectproductmain()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_issue->ajaxselectproductmain();
			$return = json_decode($returns);
			echo $returns;
		}
	}
	//	get product data information
	public function get_product()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$data = "";
			$request = $_REQUEST;
			$billid = $request['billid'];
			$pid = $request['pid'];
			$pqty = $request['pqty'];

			$sql = $this->db->select('*')
				->from('retail_productlist')
				->where('id', $pid);
				// ->where('status', 1);
			$q = $sql->get();
			$num = $q->num_rows();

			//	check duplicate product
			$numold = 0;
			if($billid){
				$sqlold = $this->db->select('retail_supplier.id as rt_id')
				->from('retail_supplier')
				->join('retail_supplierdetail','retail_supplier.id=retail_supplierdetail.rt_sup_id','left')
				->where('retail_supplier.id', $billid)
				->where('retail_supplier.status', 1)
				->where('retail_supplierdetail.prolist_id', $pid)
				->where('retail_supplierdetail.status', 1);
				$qold = $sqlold->get();
				$numold = $qold->num_rows();
			}

			if ($num && $numold < 1) {
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
				$result = json_encode($data);
			}

			echo $result;
		}
	}
	//	get product data information
	public function get_productReturn()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$data = array();
			$request = $_REQUEST;
			$billid = $request['billid'];
			$pid = $request['pid'];
			$pqty = $request['pqty'];

			/* echo "<pre>";
			print_r($request);
			echo "</pre>"; */

			$numold = 0;
			if($billid){
				$sqlold = $this->db->select('
					retail_supplier.id as rt_id,
					retail_supplierdetail.quantity as rtd_qty,

					retail_productlist.name_th as rtp_name_th,
					retail_productlist.price as rtp_price,
					retail_productlist.id as rtp_id,
					retail_productlist.promain_id as rtp_mainid,
					retail_productlist.list_id as rtp_listid,
					
				')
				->from('retail_supplier')
				->join('retail_supplierdetail','retail_supplier.id=retail_supplierdetail.rt_sup_id','left')
				->join('retail_productlist','if(retail_supplierdetail.list_id is not null, retail_supplierdetail.list_id=retail_productlist.id, retail_supplierdetail.prolist_id=retail_productlist.id)','left',false)
				->where('retail_supplier.id', $billid)
				->where('retail_supplier.complete in (0,1)')
				->where('retail_supplier.status', 1)
				->where('retail_supplierdetail.status', 1)
				->order_by('retail_supplierdetail.id', 'asc');
				$qold = $sqlold->get();
				$numold = $qold->num_rows();
			}

			if ($numold) {

				foreach($qold->result() as $r){

					//	check issue (เบิกคืนแล้วหรือยัง)
					$sqlissue = $this->db->select('sum(retail_issue.quantity) as rts_qty')
					->from('retail_issue')
					->where('retail_issue.sp_bill_id',$r->rt_id)
					->where('if(retail_issue.list_id is not null,retail_issue.list_id = '.$r->rtp_id.',retail_issue.prolist_id = '.$r->rtp_id.')',null,false)
					->where('retail_issue.complete',2)
					->where('retail_issue.status',1);
					$qissue = $sqlissue->get();
					$numissue = $qissue->num_rows();
					if($numissue){
						$rowissue = $qissue->row();
						$totalissue = $rowissue->rts_qty;
						/* if($rowissue->rts_qty >= $row->rtd_qty){

						} */
					}

					$data[] = array(
						'name_th'	=> $r->rtp_name_th,
						'price'	=> $r->rtp_price,
						'qty'	=> $r->rtd_qty,
						'main'	=> $r->rtp_mainid,
						'list'	=> $r->rtp_listid ? $r->rtp_listid  : "" ,
						'id'	=> $r->rtp_id,
						'sqty'	=> $totalissue
					);
				}

				$result = json_encode($data);
			} else {
				$result = json_encode($data);
			}

			echo $result;
		}
	}
	//	update
	public function update_bill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->issue->update_bill($request, $_FILES);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	get data bill image
	public function get_billImg()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['id']);

			$result = "";

			if ($text) {
				$r = $this->issue->get_image($text);

				if ($r) {
					foreach ($r->result() as $row) {
						$subdata['id'] = $row->cni_id;
						$subdata['path'] = site_url() . $row->cni_path;

						$data[] = $subdata;
					}
				}

				$dataresult = array(
					'data'	=> $data
				);

				$result = json_encode($dataresult);
			}

			echo $result;
		}
	}
	/**
	 * =====================================================================================================
	 * =====================================================================================================
	 * =====================================================================================================
	 */
	//	get product data information
	public function get_dataConvert()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$data = array();
			$request = $_REQUEST;
			$billid = $request['id'];

			$sql = $this->db->select('
			retail_receive.code as code,
			retail_receive.user_starts as user_starts,
			retail_receive.date_starts as date_starts,
			retail_receivedetail.prolist_id as prolist_id,
			retail_receivedetail.list_id as list_id,
			retail_receivedetail.quantity as qty,
			retail_productlist.name_th as name_th,
			')
				->from('retail_receive')
				->join('retail_receivedetail','retail_receive.id=retail_receivedetail.receive_id','left')
				->join('retail_productlist','if(retail_receivedetail.list_id is not null,retail_receivedetail.list_id=retail_productlist.id,retail_receivedetail.prolist_id=retail_productlist.id)','left',false)
				->where('retail_receive.is_bill_id', $billid)
				->where('retail_receive.complete', 2)
				->where('retail_receive.status', 1);
			$q = $sql->get();
			$num = $q->num_rows();

			if ($num) {
				$r = $q->row();
				foreach($q->result() as $r){

					/* let index = val + 1;
					let codename = key.codename;
					let date_starts = key.date_starts;
					let product_name = key.product_name;
					let product_qty = key.product_qty;
					let receivetotal = key.product_receive;
					let by = key.by;

					hmtl += '<tr data-row="' + index + '">';
					hmtl += '<td ></td>';
					hmtl += '<td>' + codename + '</td>';
					hmtl += '<td>' + date_starts + '</td>';
					hmtl += '<td>';
					hmtl += '<span class="text-right" >' + product_name + '</span>';
					hmtl += '</td>';
					hmtl += '<td>' + product_qty + '</td>';
					hmtl += '<td>' + by + '</td>';
					hmtl += '</tr>'; */

					$date = thai_date(date('Y-m-d',strtotime($r->date_starts)));
					$staff = $this->mdl_issue->findUsernameByCode($r->user_starts);

					$data[] = array(
						'codename'				=> $r->code,
						'date_starts'			=> $date,
						'product_name'			=> $r->name_th,
						'product_qty'			=> $r->qty,
						'product_receive'		=> "",
						'by'					=> $staff
					);
				}

				$result = json_encode($data);
			} else {
				$result = json_encode($data);
			}

			echo $result;
		}
	}

	//	add product
	public function get_supplierWaite()
	{
		$data = "";

		if ($this->input->server('REQUEST_METHOD')) {
			$this->load->library('supplier');
			
			$tbmain = 'retail_supplier';
			$tbsub = 'retail_supplierdetail';

			//	setting
			$arrayselect = array(
				$tbmain.'.id as rt_id',
				$tbmain.'.code as rt_code',
				$tbmain.'.ref as rt_ref',
				$tbmain.'.supplier_id as rt_supplier_id',
				$tbmain.'.complete as cn_complete',
				$tbmain.'.approve as rt_approve',
				$tbmain.'.approve_store as rt_approve_store',
				$tbmain.'.appr_date as rt_appr_date',
				$tbmain.'.appr_user as rt_appr_user',
				$tbmain.'.apst_date as rt_apst_date',
				$tbmain.'.apst_user as rt_apst_user',
				$tbmain.'.date_starts as rt_date_starts',
				$tbmain.'.user_starts as rt_user_starts',
				$tbmain.'.date_update as rt_date_update',
				$tbmain.'.user_update as rt_user_update',
				$tbmain.'.remark as rt_remark',
				$tbmain.'.remark_order as rt_remark_order',

				$tbsub.'.id as rtd_id',
				$tbsub.'.promain_id as rtd_productmain',
				$tbsub.'.prolist_id as rtd_productid',
				$tbsub.'.list_id as rtd_productlist',
				$tbsub.'.quantity as rtd_qty',
				$tbsub.'.total_price as rtd_price',

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
				$tbmain.'.type' 				=> 1,
				$tbmain.'.complete in (0,1)' 	=> null,
				$tbsub.'.status' 				=> 1
			);

			$array = array(
				"group"	=> $tbmain.'.code'
			);

			$q = $this->supplier->query_retailbilldetail($arrayselect, $arraywhere, $array);

			$data = array(
				'query'			=> $q->result()
			);
		}

		$result = json_encode($data);

		echo $result;
	}

	function get_countMenu(){
		$dataresult = get_countMenu();

		$data = array(
			'data'			=> $dataresult
		);
		$result = json_encode($data);

		echo $result;
	}
	
}
