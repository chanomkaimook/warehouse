<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_receive extends CI_Controller
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
		$this->load->model('mdl_receive');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->library('receive');
		$this->load->library('supplier');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_receive',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function receive()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailreceive'
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('receive', $data);
	}

	public function getDataBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$total = $this->mdl_receive->alldata();
			$sql = $this->mdl_receive->makedata();

			$data = array();
			$subdata = array();

			//	sql creditnote
			if ($sql->result()) {
				$index = $request['start'] + 1;
				foreach ($sql->result() as $row) {

					// $textdisplay = "<font class='text-bold'>".$row->cn_code." <i class='fas fa-search text-muted'></i></font>";
					$textdisplay = "<a href='" . site_url('mod_retailreceive/ctl_receive/viewbill?id=' . $row->cn_id) . "' target=_blank class='text-bold text-secondary text-md' >" . $row->cn_code . " <i class='fas fa-search text-muted'></i></a>";

					$billtype = $this->receive->get_dataBilltype($row->billtype);

					//	สถานะบิล
					$bill_status = $this->receive->get_dataComplete($row->cn_complete);
					switch ($row->cn_complete) {
						case 0:
							$statustext = $bill_status['data'];
							break;
						case 1:
							$statustext = $bill_status['data'];
							break;
						case 2:
							$statustext = "<font class='text-success'>" . $bill_status['data'] . "</font>";
							break;
						case 3:
							$statustext = "<font class='text-danger'>" . $bill_status['data'] . "</font>";
							break;
					}

					$textdisplay .= "<span class='text-xs'>";
					$textdisplay .= "<br>เมื่อ " . thai_date_indent(date('Y-m-d', strtotime($row->cn_date_starts))) . " " . date('H:i:s', strtotime($row->cn_date_starts));

					($row->cn_name_th ? $user = $row->cn_name_th . " " . $row->cn_lastname_th : $user = $row->cn_name . " " . $row->cn_lastname);
					$textdisplay .= "<br>โดย " . $user;
					$textdisplay .= "</span>";

					//	code ref
					if ($row->cn_rt_bill_code) {
						$bill_ref = "<a href='" . site_url('mod_retailcreateorder/ctl_createorder/viwecreatebill?id=' . $row->cn_rt_id . '&mdl=mdl_createorder') . "' target=_blank class='' >อิงจาก " . $row->cn_rt_bill_code . "</a>";
						$textdisplay .= "<br>" . $bill_ref;
					} else {
						$bill_ref = "";
					}

					$suppliername = $row->cn_sp_bill_name;

					$rowarray = array();
					$rowarray['DT_RowId'] = $row->cn_id;	//	set row id
					$rowarray['id'] = $index;
					$rowarray['code'] = $textdisplay;
					$rowarray['typebill'] = $billtype['data'];
					$rowarray['supplier'] = $suppliername;
					$rowarray['complete'] = $statustext;
					$rowarray['date'] = $row->cn_date_starts;

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

	public function editbill()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailreceive'
		);

		$data['method'] = $this->uri->segment(3);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;

		$sql = $this->db->select('complete,approve,approve_store')
			->from('retail_receive')
			->where('retail_receive.id', $this->input->get('id'));
		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$r = $q->row();

			if ($r->complete == 0) {
				$this->load->view('editbill', $data);
			} else {
				redirect('mod_admin/ctl_login/pathadmin');
			}
		}
	}
	/**
	 * =====================================================================================================
	 * =====================================================================================================
	 * =====================================================================================================
	 */


	public function viewbill()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailreceive'
		);

		$id = $this->input->get('id');
		$query = "";
		if ($id) {
			$sql = $this->db->select('*')
				->from('retail_receive')
				->where('retail_receive.id', $id);
			$q = $sql->get();
			$num = $q->num_rows();
			if ($num) {
				$query = $q->row();
			}
		}

		$data['method'] = $this->uri->segment(3);
		$data['query'] = $query;

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('viewbill', $data);
	}



	//	add
	public function add_Bill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->receive->add_bill($request, $_FILES);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	update
	public function update_bill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->receive->update_bill($request, $_FILES);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	update bill approve from store
	public function confirmStore()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->receive->confirmStore($request);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	update bill approve from finance
	public function confirmFinance()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->receive->confirmFinance($request);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	cancel bill
	public function cancelBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->receive->cancelBill($request);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	download
	public function doc_receive()
	{
		$this->load->helper(array('array', 'report'));

		$data = array(
			'get_id'	=> $this->input->get('id'),
			'query'		=> $this->receive->get_dataBillDetail($this->input->get('id'))
		);

		$data['form'] = $this->load->view('viewbill', $data,true);

		$this->load->view('doc_receive', $data);
	}

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
				$q = $this->receive->read_bill($text);

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
							'receive'	=> trim($r->rtm_name),
							'delivery'	=> trim($r->rtd_name),
							'textcode'		=> (trim($r->rt_textcode) ? trim($r->rt_textcode) : trim($r->rt_ref)),
							'billstatus'	=> trim($billstatus),
							'complete'		=> trim($complete),
							'complete_id'		=> 2,

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

	//	get data bill to supplier
	public function get_orderSupplierBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['bill_id']);

			$result = "";
			$data = array();
			$datadetail = array();
			

			if ($text) {
				$q = $this->supplier->read_bill($text);

				if ($q->result()) {
					foreach ($q->result() as $r) {
						$total = 0;
						$waitetotal = "";
						$waitetotal = $r->rtd_qty;
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

						//	find receive total
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
							->where('retail_receivedetail.promain_id', $r->rtd_productmain)
							->where('retail_receivedetail.prolist_id', $r->rtd_productid)
							->where('retail_receivedetail.list_id', $r->rtd_productlist)
							->where('retail_receivedetail.status', 1)
							->where('retail_receive.sp_bill_id', $text)
							->where('retail_receive.complete', 2)
							->where('retail_receive.status', 1);

						$sql_productcheck = $queryprocheck;
						$num_productcheck = $this->db->count_all_results(null, false);
						$q_productcheck = $sql_productcheck->get();
						if ($num_productcheck) {
							$r_productcheck = $q_productcheck->row();
							$total = $r_productcheck->total;
							settype($r_productcheck->total, "integer");

							if($total){
								$waitetotal = $r->rtd_qty - $r_productcheck->total;
							}
						}

						$data	= array(
							'id'	=> trim($r->rt_id),

							'code'	=> trim($r->rt_code),
							'ref'	=> trim($r->rt_ref),
							'name'	=> trim($r->sup_name),
							'textcode'		=> (trim($r->rt_textcode) ? trim($r->rt_textcode) : trim($r->rt_ref)),

							'complete_id'		=> 2,

							'datecreate'		=> (trim($r->rt_date_starts) ? thai_date(date('Y-m-d', strtotime(trim($r->rt_date_starts)))) : ""),

							'staffcreate'		=> (trim($r->sf_name_th ? trim($r->sf_name_th) . " " . trim($r->sf_lastname_th) : trim($r->sf_name) . " " . trim($r->sf_lastname))),
							'remark'	=> trim($r->rt_remark)
						);

						$datadetail[]	= array(
							'product_name'	=> $r->rtp_name,
							'product_qty'	=> $r->rtd_qty,
							'product_price'	=> $r->rtp_price,
							'product_totalprice'	=> $r->rtd_price,

							'product_receive'	=> $total,
							'product_receivewaite'	=> $waitetotal,
							// 'product_receivewaite'	=> $waitetotal,

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

	//	get data bill to issue
	public function get_orderIssueBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['bill_id']);

			$result = "";
			$data = array();
			$datadetail = array();
			$waitetotal = "";

			if ($text) {
				$tbmain = 'retail_issue';

				$query = $this->db->select(
					$tbmain.'.id as rt_id,'.
					$tbmain.'.code as rt_code,'.
					$tbmain.'.billto as rt_ref,'.
					$tbmain.'.complete as cn_complete,'.
					$tbmain.'.type as cn_type,'.

					$tbmain.'.id as rtd_id,'.
					$tbmain.'.promain_id as rtd_productmain,'.
					$tbmain.'.prolist_id as rtd_productid,'.
					$tbmain.'.list_id as rtd_productlist,'.
					$tbmain.'.quantity as rtd_qty,'.
					$tbmain.'.total_price as rtd_price,'.

					$tbmain.'.date_starts as rt_date_starts,'.
					$tbmain.'.user_starts as rt_user_starts,'.
					$tbmain.'.date_update as rt_date_update,'.
					$tbmain.'.user_update as rt_user_update,'.
					$tbmain.'.remark as rt_remark,'.

					'retail_productlist.name_th as rtp_name,'.
					'retail_productlist.price as rtp_price,'.

					'staff.name_th as sf_name_th,'.
					'staff.lastname_th as sf_lastname_th,'.
					'staff.name as sf_name,'.
					'staff.lastname as sf_lastname'
				)
					->from($tbmain)
					->join('retail_productmain', $tbmain.'.PROMAIN_ID = retail_productmain.ID ', 'left')
					->join('retail_productlist', $tbmain.'.PROLIST_ID = retail_productlist.ID ', 'left')
					->join('staff', $tbmain.'.USER_STARTS = staff.CODE ', 'left')
					->where($tbmain.'.id', $text)
					->where($tbmain.'.status', 1);

				$q = $query->get();

				if ($q->result()) {
					foreach ($q->result() as $r) {

						//	find receive total
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
							->where('retail_receivedetail.promain_id', $r->rtd_productmain)
							->where('retail_receivedetail.prolist_id', $r->rtd_productid)
							->where('retail_receivedetail.list_id', $r->rtd_productlist)
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

							if($total){
								$waitetotal = $r->rtd_qty - $r_productcheck->total;
							}
						}

						$data	= array(
							'id'	=> trim($r->rt_id),

							'code'	=> trim($r->rt_code),
							'ref'	=> trim($r->rt_ref),
							'name'	=> trim($r->sup_name),
							'textcode'		=> (trim($r->rt_textcode) ? trim($r->rt_textcode) : trim($r->rt_ref)),

							'type'				=> $r->cn_type,
							'complete_id'		=> 2,

							'datecreate'		=> (trim($r->rt_date_starts) ? thai_date(date('Y-m-d', strtotime(trim($r->rt_date_starts)))) : ""),

							'staffcreate'		=> (trim($r->sf_name_th ? trim($r->sf_name_th) . " " . trim($r->sf_lastname_th) : trim($r->sf_name) . " " . trim($r->sf_lastname))),
							'remark'	=> trim($r->rt_remark)
						);

						$datadetail[]	= array(
							'product_name'	=> $r->rtp_name,
							'product_qty'	=> $r->rtd_qty,
							'product_price'	=> $r->rtp_price,
							'product_totalprice'	=> $r->rtd_price,

							'product_receive'	=> $total,
							'product_receivewaite'	=> $waitetotal,
							// 'product_receivewaite'	=> $waitetotal,

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

	//	get data bill to add
	public function get_dataBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['bill_id']);

			$result = "";
			$data = array();
			$datadetail = array();

			if ($text) {
				$q = $this->receive->read_dataBill($text);

				if ($q) {
					foreach ($q->result() as $r) {

						//	สถานะบิล
						$complete = $this->receive->get_dataComplete($r->cn_complete);
						switch ($r->cn_complete) {
							case 0:
								$statustext = $complete['data'];
								break;
							case 1:
								$statustext = $complete['data'];
								break;
							case 2:
								$statustext = "<font class='text-success'>" . $complete['data'] . "</font>";
								break;
							case 3:
								$statustext = "<font class='text-danger'>" . $complete['data'] . "</font>";
								break;
						}
						$appr_username = "";
						$apst_username = "";

						if (trim($r->cn_appr_user)) {
							$appr_username = $this->mdl_receive->findUsernameByCode(trim($r->cn_appr_user));
						}

						if (trim($r->cn_apst_user)) {
							$apst_username = $this->mdl_receive->findUsernameByCode(trim($r->cn_apst_user));
						}

						$userupdate = "";
						if (trim($r->cn_user_update)) {

							$sqluser = $this->db->select('name,name_th,lastname,lastname_th')
								->from('staff')
								->where('code', trim($r->cn_user_update));
							$quser = $sqluser->get();
							$numuser = $quser->num_rows();
							if ($numuser) {
								$rowuser = $quser->row();
								($rowuser->name_th ? $userupdate = $rowuser->name_th . " " . $rowuser->lastname_th : $userupdate = $rowuser->name . " " . $rowuser->lastname);
							} else {
								$userupdate = "ไม่มีชื่อ";
							}
						}

						(trim($r->cn_date_update) ? $dateupdate = "(" . thai_date(date('Y-m-d', strtotime(trim($r->cn_date_update)))) . " " . date('H:i:s', strtotime(trim($r->cn_date_update))) . " น.)" : $dateupdate = "");

						$userupdate .= "<br>" . $dateupdate;

						//	supplier
						if ($r->cn_supplier_id) {
							$sqlsup = $this->db->select('*')
								->from('supplier')
								->where('supplier.id', $r->cn_supplier_id);
							$qsup = $sqlsup->get();
							$numsup = $qsup->num_rows();

							if ($numsup) {
								$rsup = $qsup->row();
								$sup_name = (trim($rsup->NAME_TH) ? trim($rsup->NAME_TH) : trim($rsup->NAME));
							}
						}

						$data	= array(
							'id'	=> trim($r->cn_id),

							'retail_code'	=> trim($r->cn_rt_bill_code),
							'sup_name'	=> trim($r->cn_sp_bill_name),
							'name'	=> trim($r->rt_name),
							'tel'	=> trim($r->rt_tel),
							'citizen'	=> trim($r->rt_citizen),
							'address'		=> trim($r->rt_address),
							'zipcode'		=> trim($r->rt_zipcode),

							'code'	=> trim($r->cn_code),
							'billtype'	=> trim($r->cn_billtype),

							'cn_bill_id'	=> trim($r->cn_bill_id),
							'cn_bill_code'	=> trim($r->cn_bill_code),

							'is_bill_id'	=> trim($r->is_bill_id),
							'is_bill_name'	=> trim($r->is_bill_name),

							'codereport'		=> trim($r->cn_codereport),
							'complete'		=> $statustext,
							'codecomplete'		=> trim($r->cn_complete),
							'loss'		=> trim($r->cn_loss),

							'price'		=> trim($r->cn_total_price),
							'parcel'		=> trim($r->cn_parcel_cost),
							'logis'		=> trim($r->cn_delivery_fee),
							'shor'		=> trim($r->cn_shor_money),
							'discount'		=> trim($r->cn_discount_price),
							'tax'		=> trim($r->cn_tax),
							'net'		=> trim($r->cn_net_total),

							'approve'		=> trim($r->cn_approve),
							'approve_store'	=> trim($r->cn_approve_store),
							'appr_date'	=> trim($r->cn_appr_date),
							'appr_user'	=> trim($r->cn_appr_user),
							'apst_date'	=> trim($r->cn_apst_date),
							'apst_user'	=> trim($r->cn_apst_user),
							'appr_username'	=> $appr_username,
							'apst_username'	=> $apst_username,

							'datecreate'		=> (trim($r->cn_date_starts) ? thai_date(date('Y-m-d', strtotime(trim($r->cn_date_starts)))) : ""),
							'staffcreate'		=> ($r->sf_nameth ? $user = $r->sf_nameth . " " . $r->sf_lastnameth : $user = $r->sf_name . " " . $r->sf_lastname),

							'dateupdate'		=> $dateupdate,
							'staffupdate'		=> $userupdate,

							'remark'	=> trim($r->cn_remark),
							'remark_order'	=> trim($r->cn_remark_order)
						);

						if ($r->cnd_id) {
							$datadetail[]	= array(
								'product_rowid'	=> $r->cnd_id,
								'product_name'	=> $r->rtp_name,
								'product_qty'	=> $r->cnd_qty,
								'product_price'	=> $r->rtp_price,
								'product_totalprice'	=> $r->cnd_price,

								'promain'	=> trim($r->cnd_productmain),
								'prolist'	=> trim($r->cnd_productid),
								'list'		=> trim($r->cnd_productlist)
							);
						}
					}

					$dataresult = array('data' => $data, 'datadetail' => $datadetail);
					$result = json_encode($dataresult);
				} else {
					$result = json_encode($result);
				}
			}

			echo $result;
		}
	}

	//	get data bill to add
	public function get_orderSearchBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['searchorder']);

			$result = "";

			if ($text) {
				$r = $this->receive->read_billSearch($text);

				$dataresult = array(
					'data'	=> $r
				);

				$result = json_encode($dataresult);
			}

			echo $result;
		}
	}

	//	get data supplier bill to add
	public function get_orderSearchSubbill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['searchorder']);

			$result = "";

			if ($text) {
				$r = $this->receive->read_SupplierSearch($text);

				$dataresult = array(
					'data'	=> $r
				);

				$result = json_encode($dataresult);
			}

			echo $result;
		}
	}

	//	add product
	public function ajaxselectproductmain()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_receive->ajaxselectproductmain();
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
				->where('id', $pid)
				->where('status', 1);
			$q = $sql->get();
			$num = $q->num_rows();

			//	check duplicate product
			$numold = 0;
			if($billid){
				$sqlold = $this->db->select('retail_receive.id as rt_id')
				->from('retail_receive')
				->join('retail_receivedetail','retail_receive.id=retail_receivedetail.rt_sup_id','left')
				->where('retail_receive.id', $billid)
				->where('retail_receive.status', 1)
				->where('retail_receivedetail.prolist_id', $pid)
				->where('retail_receivedetail.status', 1);
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

	//	get data issue bill to add
	public function get_orderSearchIssue()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;
			$text = trim($request['searchorder']);

			$result = "";

			if ($text) {
				//	setting
				$table = 'retail_issue';

				$sql = $this->db->select(
					$table.'.id as ID,'.
					$table.'.code as CODE,'.
					$table.'.billto as BILLTO,'.
					$table.'.type as TYPE,'.
					$table.'.quantity as QUANTITY,'.
					'retail_productlist.name_th as PRODUCTNAME,'
				)
					->from($table)
					->join('retail_productlist',$table.'.prolist_id=retail_productlist.id','left')
					->where($table . '.complete in(0,1)')
					->where($table . '.code is not null')
					->where('('.$table . '.code like "%' . $text . '%" or '.$table . '.billto like "%'.$text .'%")',null,false);
				$number = $sql->count_all_results(null, false);
				$q = $sql->get();
				if ($number) {
					foreach ($q->result() as $r) {
						$subdata['id'] = $r->ID;
						$subdata['code'] = $r->CODE;
						$subdata['name'] = $r->BILLTO;
						$subdata['product'] = $r->PRODUCTNAME;
						$subdata['qty'] = $r->QUANTITY;
						$subdata['type'] = status_issue($r->TYPE);
						$dataresult[] = $subdata;
					}
		
					$r = $dataresult;
				}else{
					$r = "";
				}

				$dataresult = array(
					'data'	=> $r
				);

				$result = json_encode($dataresult);
			}

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
				$r = $this->receive->get_image($text);

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

	//	get data bill to add
	public function countbill()
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

	function get_countMenu(){
		$dataresult = get_countMenu();

		$data = array(
			'data'			=> $dataresult
		);
		$result = json_encode($data);

		echo $result;
	}
}
