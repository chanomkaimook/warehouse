<?php

use phpDocumentor\Reflection\Types\Integer;

defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_supplier extends CI_Controller
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
		$this->load->model('mdl_supplier');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->library('supplier');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_supplier',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function supplier()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailsupplier'
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('supplier', $data);
	}

	public function getDataBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$total = $this->mdl_supplier->alldata();
			$sql = $this->mdl_supplier->makedata();

			$data = array();
			$subdata = array();

			//	sql creditnote
			if ($sql->result()) {
				$index = $request['start'] + 1;
				foreach ($sql->result() as $row) {

					//	quantity
					$qty = $row->cn_qty;
					$aftifact = "";	//	เหตุผิดปกติ


					// $textdisplay = "<font class='text-bold'>".$row->cn_code." <i class='fas fa-search text-muted'></i></font>";
					$textdisplay = "<a href='" . site_url('mod_retailsupplier/ctl_supplier/viewbill?id=' . $row->cn_id) . "' target=_blank class='text-bold text-secondary text-md' >" . $row->cn_code . " <i class='fas fa-search text-muted'></i></a>";

					//	สถานะบิล
					$bill_status = $this->supplier->get_dataComplete($row->cn_complete);
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

					$suppliername = (trim($row->cn_sup_name_th) ? trim($row->cn_sup_name_th) : trim($row->cn_sup_name));

					//	check aftifact
					$textsql2 = '
						retail_supplier.id as r_id,
						retail_supplierdetail.quantity as r_qty,

						retail_supplierdetail.promain_id as r_main_id,
						retail_supplierdetail.prolist_id as r_list_id,
						retail_supplierdetail.list_id as r_list
							';
					$queryprocheck2 = $this->db->select($textsql2)
						->from('retail_supplier')
						->join('retail_supplierdetail', 'retail_supplier.id=retail_supplierdetail.rt_sup_id', 'left')
						->where('retail_supplierdetail.status', 1)
						->where('retail_supplier.id', $row->cn_id)
						->where('retail_supplier.status', 1);
					$sql_productcheck2 = $queryprocheck2;
					$num_productcheck2 = $this->db->count_all_results(null, false);
					$q_productcheck2 = $sql_productcheck2->get();
					if ($num_productcheck2) {
						foreach ($q_productcheck2->result() as $rcheck2) {
							$textsql = '
								sum(retail_receivedetail.quantity) as r_qty
								';
							$queryprocheck = $this->db->select($textsql)
								->from('retail_receive')
								->join('retail_receivedetail', 'retail_receive.id=retail_receivedetail.receive_id', 'left')
								->join('staff', 'retail_receive.user_starts=staff.id', 'left')
								->where('retail_receivedetail.promain_id', $rcheck2->r_main_id)
								->where('retail_receivedetail.prolist_id', $rcheck2->r_list_id)
								->where('retail_receivedetail.list_id', $rcheck2->r_list)
								->where('retail_receivedetail.status', 1)
								->where('retail_receive.sp_bill_id', $rcheck2->r_id)
								->where('retail_receive.complete', 2)
								->where('retail_receive.status', 1);

							$sql_productcheck = $queryprocheck;
							$num_productcheck = $this->db->count_all_results(null, false);
							$q_productcheck = $sql_productcheck->get();
							if ($num_productcheck) {
								$rcheck = $q_productcheck->row();

								if($rcheck2->r_qty < $rcheck->r_qty){
									$aftifact = "ผิดปกติ";
								}
							}
						}
					}




					$rowarray = array();
					$rowarray['DT_RowId'] = $row->cn_id;	//	set row id
					$rowarray['id'] = $index;
					$rowarray['code'] = $textdisplay;
					$rowarray['ref'] = trim($row->cn_ref);
					$rowarray['supplier'] = $suppliername;
					$rowarray['complete'] = $statustext;
					$rowarray['aftifact'] = $aftifact;
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
			'submenu' 		=> 'retailsupplier'
		);

		$data['method'] = $this->uri->segment(3);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;

		$sql = $this->db->select('complete,approve,approve_store')
			->from('retail_supplier')
			->where('retail_supplier.id', $this->input->get('id'));
		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$r = $q->row();

			if ($r->complete == 0 || $r->complete == 1) {
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
			'submenu' 		=> 'retailsupplier'
		);

		$id = $this->input->get('id');
		$query = "";
		if ($id) {
			$sql = $this->db->select('*')
				->from('retail_supplier')
				->where('retail_supplier.id', $id);
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

			$q = $this->supplier->add_bill($request, $_FILES);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	update
	public function update_bill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->supplier->update_bill($request, $_FILES);

			$result = json_encode($q);
			echo $result;
		}
	}


	//	update bill approve from finance
	public function confirmFinance()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->supplier->confirmFinance($request);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	cancel bill
	public function cancelBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$q = $this->supplier->cancelBill($request);

			$result = json_encode($q);
			echo $result;
		}
	}

	//	download

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
				$q = $this->supplier->read_bill($text);

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
							'supplier'	=> trim($r->rtm_name),
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
			$$dataissuedetail = array();

			if ($text) {
				$q = $this->supplier->read_dataBill($text);


				$querycheck = $this->db->select('id')
					->from('retail_receive')
					->where('sp_bill_id', $text)
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

				if ($q) {
					foreach ($q->result() as $r) {

						//	สถานะบิล
						$statuscomplete = $this->supplier->get_dataComplete($r->cn_complete);
						switch ($r->cn_complete) {
							case 0:
								$complete = $statuscomplete['data'];
								break;
							case 1:
								$complete = $statuscomplete['data'];
								break;
							case 2:
								$complete = "<font class='text-success'>" . $statuscomplete['data'] . "</font>";
								break;
							case 3:
								$complete = "<font class='text-danger'>" . $statuscomplete['data'] . "</font>";
								break;
						}

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
								$waitetotal = $r->rtd_qty - $r_productcheck->total;

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
									->where('retail_receivedetail.promain_id', $r->rtd_productmain)
									->where('retail_receivedetail.prolist_id', $r->rtd_productid)
									->where('retail_receivedetail.list_id', $r->rtd_productlist)
									->where('retail_receivedetail.status', 1)
									->where('retail_receive.sp_bill_id', $text)
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
										'product_name'	=> $r->rtp_name,
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

						//	ค้นหาการเบิกคืน กรณีเป็นบิลสำหรับเบิกคืน
						if($r->cn_type){
							$textsql = '
								sum(retail_issue.quantity) as total,
								retail_issue.code as rc_code,
								retail_issue.date_starts as rc_date_starts,
								retail_issue.quantity as rc_qty,
								staff.name as st_name,
								staff.lastname as st_lastname,
								staff.name_th as st_name_th,
								staff.lastname_th as st_lastname_th,
							';
							$queryprocheck = $this->db->select($textsql)
								->from('retail_issue')

								->join('staff', 'retail_issue.user_starts=staff.id', 'left')
								->where('retail_issue.promain_id', $r->rtd_productmain)
								->where('retail_issue.prolist_id', $r->rtd_productid)
								->where('retail_issue.list_id', $r->rtd_productlist)
								->where('retail_issue.status', 1)
								->where('retail_issue.sp_bill_id', $text)
								->where('retail_issue.complete', 2)
								->where('retail_issue.status', 1);

							$sql_productcheck = $queryprocheck;
							$num_productcheck = $this->db->count_all_results(null, false);
							$q_productcheck = $sql_productcheck->get();
							if ($num_productcheck) {
								$r_productcheck = $q_productcheck->row();
								$totalissue = $r_productcheck->total;
								settype($r_productcheck->total, "integer");
								$waitetotalissue = $r->rtd_qty - $r_productcheck->total;

								//	แยกตามแถวบนฐานข้อมูล
								$textsql2 = '
									retail_issue.code as rc_code,
									retail_issue.date_starts as rc_date_starts,
									retail_issue.quantity as rc_qty,
									staff.name as st_name,
									staff.lastname as st_lastname,
									staff.name_th as st_name_th,
									staff.lastname_th as st_lastname_th,
								';
								$queryprocheck2 = $this->db->select($textsql2)
									->from('retail_issue')

									->join('staff', 'retail_issue.user_starts=staff.id', 'left')
									->where('retail_issue.promain_id', $r->rtd_productmain)
									->where('retail_issue.prolist_id', $r->rtd_productid)
									->where('retail_issue.list_id', $r->rtd_productlist)
									->where('retail_issue.status', 1)
									->where('retail_issue.sp_bill_id', $text)
									->where('retail_issue.complete', 2)
									->where('retail_issue.status', 1);
								$sql_productcheck = $queryprocheck2;
								$queryprocheck2 = $sql_productcheck->get();
								foreach ($queryprocheck2->result() as $r_productcheck2) {
									//	list order issue
									$dataissuedetail[]	= array(
										'codename'	=> $r_productcheck2->rc_code,
										'date_starts'	=> thai_date(date('Y-m-d', strtotime($r_productcheck2->rc_date_starts))),
										'by'		=> ($r_productcheck2->st_name_th ? trim($r_productcheck2->st_name_th) . " " . trim($r_productcheck2->st_lastname_th) : trim($r_productcheck2->st_name) . " " . trim($r_productcheck2->st_lastname)),
										'product_name'	=> $r->rtp_name,
										'product_qty'	=> $r_productcheck2->rc_qty,
									);
								}
							} else {
								$totalissue = "";
								$waitetotalissue = "";
							}
						}


						$data	= array(
							'id'	=> trim($r->rt_id),

							'code'	=> trim($r->rt_code),
							'ref'	=> trim($r->rt_ref),
							'name'	=> (trim($r->sup_name) ? trim($r->sup_name) : trim($r->sup_name_en)),
							'textcode'		=> (trim($r->rt_textcode) ? trim($r->rt_textcode) : trim($r->rt_ref)),

							'complete'		=> $complete,
							'complete_id'	=> $r->cn_complete,

							'check_rc'		=> $check_rc,

							'datecreate'		=> (trim($r->rt_date_starts) ? thai_date(date('Y-m-d', strtotime(trim($r->rt_date_starts)))) : ""),

							'staffcreate'		=> (trim($r->sf_name_th ? trim($r->sf_name_th) . " " . trim($r->sf_lastname_th) : trim($r->sf_name) . " " . trim($r->sf_lastname))),
							'remark'	=> trim($r->rt_remark),
							'remark_order'	=> trim($r->rt_remark_order)
						);

						if($r->rtd_id){
							$datadetail[]	= array(
								'product_rowid'	=> $r->rtd_id,
								'product_name'	=> $r->rtp_name,
								'product_qty'	=> $r->rtd_qty,
								'product_price'	=> $r->rtp_price,
								'product_totalprice'	=> $r->rtd_price,
	
								'product_receive'	=> $total,
								'product_receivewaite'	=> (!empty($waitetotal) ? $waitetotal : ""),

								'product_issue'		=> $totalissue,
								'product_issuewaite'	=> (!empty($waitetotalissue) ? $waitetotalissue : ""),
	
								'promain'	=> trim($r->rtd_productmain),
								'prolist'	=> trim($r->rtd_productid),
								'list'		=> trim($r->rtd_productlist)
							);
						}
						
					}

					$dataresult = array('data' => $data, 'datadetail' => $datadetail, 'datareceivedetail' => $datareceivedetail, 'dataissuedetail' => $dataissuedetail);
					$result = json_encode($dataresult);
				} else {
					$result = json_encode($result);
				}
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
				$r = $this->supplier->get_image($text);

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

	//	add product
	public function ajaxselectproductmain()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_supplier->ajaxselectproductmain();
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

	public function get_supplier() {
		$dataresult = array();

		$sql = $this->db->select('*')
		->from('supplier')
		->where('supplier.status',1);
		$q = $sql->get();
		$num = $q->num_rows();
		
		if($num){
			$dataresult = array(
				'data'	=> $q->result(),
				'num'	=> $num
			);
		}
		
		$result = json_encode($dataresult);

		echo $result;

	}

	public function add_supplier() {
		$dataresult = array();

		$error_code = 1;
		$txt = "ไม่สามารถทำรายการได้";

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$request = $_REQUEST;
			if(trim($request['suppliername'])){

				$datainsert = array(
					'name_th'	=> trim($request['suppliername']),
					'date_starts'	=> date('Y-m-d H:i:s'),
					'user_starts'	=> $this->session->userdata('useradminid')
				);

				$this->db->insert('supplier',$datainsert);
				$id = $this->db->insert_id();
				if($id){
					$error_code = 0;
					$txt = "ทำรายการสำเร็จ";
				}				
			}
		}

		$dataresult = array(
			'error_code' 	=> $error_code,
			'txt'			=> $txt
		);
		
		$result = json_encode($dataresult);

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
