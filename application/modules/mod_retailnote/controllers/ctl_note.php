<?php

use phpDocumentor\Reflection\Types\Integer;

defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_note extends CI_Controller
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
		$this->load->model('mdl_note');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_note',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function note()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailnote'
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('note', $data);
	}

	public function getDataBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$total = $this->mdl_note->alldata();
			$sql = $this->mdl_note->makedata();

			$data = array();
			$subdata = array();

			//	sql creditnote
			if ($sql->result()) {
				$detailarray = array();

				foreach ($sql->result() as $row) {
					$arraycode[] = "'".$row->tb_code."'";
				}
				if($arraycode){
					$sqlcode = implode(',',$arraycode);
					$sqllist = $this->db->select('retail_note.code as n_code,retail_productlist.name_th as p_name')
					->from('retail_note')
					->join('retail_notedetail','retail_note.id=retail_notedetail.rt_note_id','left')
					->join('retail_productlist','if(retail_notedetail.list_id,retail_notedetail.list_id=retail_productlist.id,retail_notedetail.prolist_id=retail_productlist.id)','left',false)
					->where('retail_note.code in('.$sqlcode.')')
					->where('retail_notedetail.status',1);
					$qlist = $sqllist->get();
					$numlist = $qlist->num_rows();
					if($numlist){
						foreach($qlist->result() as $rowlist){
							$detailarray[$rowlist->n_code][] = $rowlist->p_name;
						}
					}
				}

				/* echo "<pre>";
				print_r($detailarray);
				echo "</pre>"; */

				$index = $request['start'] + 1;
				foreach ($sql->result() as $row) {
					// echo $key."---".$row;
					if($detailarray[$row->tb_code]){
						$textdisplay = "<a href='" . site_url('mod_retailnote/ctl_note/viewbill?id=' . $row->tb_id) . "' target=_blank class='' >".implode(',<br>',$detailarray[$row->tb_code])." </a>";
					}else{
						$textdisplay = "<a href='" . site_url('mod_retailnote/ctl_note/viewbill?id=' . $row->tb_id) . "' target=_blank class='' >" . $row->pd_name_th . " </a>";
					}
					// $textdisplay = "<font class='text-bold'>".$row->cn_code." <i class='fas fa-search text-muted'></i></font>";
					// $textdisplay = "<a href='" . site_url('mod_retailnote/ctl_note/viewbill?id=' . $row->DT_RowId) . "' target=_blank class='' >" . $row->pd_name_th . " </a>";

					if($row->sp_user_update){
						$date = thai_date(date('Y-m-d',strtotime($row->tb_date_update)));
						$staff = $this->mdl_note->findUsernameByCode($row->tb_user_update);
					}else{
						$date = thai_date(date('Y-m-d',strtotime($row->tb_date_starts)));
						$staff = $this->mdl_note->findUsernameByCode($row->tb_user_starts);
					}

					$rowarray = array();
					$rowarray['DT_RowId'] = $row->tb_id;	//	set row id
					$rowarray['id'] = $index;
					$rowarray['code'] = "<a href='" . site_url('mod_retailnote/ctl_note/viewbill?id=' . $row->tb_id) . "' target=_blank class='' >".$row->tb_code." </a>";
					$rowarray['name'] = $textdisplay;
					$rowarray['remark'] = $row->tb_remark;
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
			'submenu' 		=> 'retailnote'
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
			'submenu' 		=> 'retailnote'
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
		$table = 'retail_note';
		$codebill = 'NT';

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

	public function add_Bill()
	{
		$dataresult = array();

		$error_code = 1;
		$txt = "ไม่สามารถทำรายการได้";

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$request = $_REQUEST;

			if (!$request['item']) {
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

			//
			$gencode = $this->genCodeBill();

			$remark = trim($request['remark']);

			$datainsert = array(
				'code'		=> $gencode,

				'complete'	=> 1,

				'remark'	=> $remark,

				'date_starts'	=> date('Y-m-d H:i:s'),
				'user_starts'	=> $this->session->userdata('useradminid')
			);

			$this->db->insert('retail_note', $datainsert);
			$id = $this->db->insert_id();
			if ($id) {
				// ============== Log_Detail ============== //
				$log_query = $this->db->last_query();
				$last_id = $this->session->userdata('log_id');
				$detail = "Insert retail_note id:" . $id . " Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
				$type = "Insert";
				$arraylog = array(
					'log_id'  	 	 => $last_id,
					'detail'  		 => $detail,
					'logquery'       => $log_query,
					'type'     	 	 => $type,
					'date_starts'    => date('Y-m-d H:i:s')
				);
				updateLog($arraylog);

				if ($request['item']) {
					foreach ($request['item'] as $key => $val) {
						$promain = get_valueNullToNull($val['promain']);
						$prolist = get_valueNullToNull($val['prolist']);
						$list = get_valueNullToNull($val['list']);
						$qty = get_valueNullToNull($val['qty']);
						$totalprice = 0;

						if ($qty && $promain && $prolist) {
							$setdetail[] = array(
								'code'				=> $gencode,
								'rt_note_id'		=> $id,
								'promain_id'		=> $promain,
								'prolist_id'		=> $prolist,
								'list_id'			=> $list,
								'quantity'			=> $qty,
								'total_price'		=> $totalprice,
								'date_starts'		=> date('Y-m-d H:i:s'),
								'user_starts'		=> $this->session->userdata('useradminid')
							);
						}
					}
					$datainsertdetail = $setdetail;

					$this->db->insert_batch('retail_notedetail', $datainsertdetail);
					// ============== Log_Detail ============== //
					$log_query = $this->db->last_query();
					$last_id = $this->session->userdata('log_id');
					$detail = "Insert retail_notedetail Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
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

				$error_code = 0;
				$txt = "ทำรายการสำเร็จ";
			}
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

			if ($text) {

				$table = 'retail_note';
				$tabledetail = 'retail_notedetail';

				$sql = $this->db->select(
					$table . '.ID as tb_id,' .
						$table . '.CODE as tb_code,' .
						$table . '.APPROVE_STORE as tb_approve_store,' .
						$table . '.APST_DATE as tb_apst_date,' .
						$table . '.APST_USER as tb_apst_user,' .
						$table . '.COMPLETE as tb_complete,' .
						$table . '.REMARK as tb_remark,' .

						$tabledetail . '.ID as tb_pro_id,' .
						$tabledetail . '.PROMAIN_ID as tb_promain_id,' .
						$tabledetail . '.PROLIST_ID as tb_prolist_id,' .
						$tabledetail . '.LIST_ID as tb_list_id,' .
						$tabledetail . '.QUANTITY as tb_qty,' .

						$table . '.DATE_STARTS as tb_date_starts,' .
						$table . '.USER_STARTS as tb_user_starts,' .
						$table . '.DATE_UPDATE as tb_date_update,' .
						$table . '.USER_UPDATE as tb_user_update,' .
						'retail_productlist.name_th as tb_product_name,'
				)
					->from($table)
					->join($tabledetail, $table . '.id=' . $tabledetail . '.rt_note_id', 'left')
					->join('retail_productlist', 'if(' . $tabledetail . '.LIST_ID,' . $tabledetail . '.LIST_ID=retail_productlist.id,' . $tabledetail . '.PROLIST_ID=retail_productlist.id)', null, false)
					->where($table . '.id', $text)
					->where($table . '.status', 1)
					->where($tabledetail . '.status', 1);
				$q = $sql->get();
				$num = $q->num_rows();

				if ($num) {
					foreach ($q->result() as $r) {


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

							'tb_code'		=> trim($r->tb_code),
							'tb_complete'		=> $statustext,
							'tb_complete_id'	=> $r->tb_complete,
							'tb_remark'		=> trim($r->tb_remark),
							'tb_approve'		=> (trim($r->tb_approve_store) ? $this->mdl_note->findUsernameByCode($r->tb_apst_user) : ""),
							'tb_date_starts'	=> trim($r->tb_date_starts),
							'tb_user_starts'		=> (trim($r->tb_user_starts) ? $this->mdl_note->findUsernameByCode($r->tb_user_starts) : ""),
							'tb_date_update'		=> trim($r->tb_date_update),
							'tb_user_update'		=> (trim($r->tb_user_update) ? $this->mdl_note->findUsernameByCode($r->tb_user_update) : "")
						);

						if ($r->tb_prolist_id) {
							$datadetail[]	= array(
								'product_rowid'	=> $r->tb_pro_id,
								'product_name'	=> $r->tb_product_name,
								'product_qty'	=> $r->tb_qty,
								'product_price'	=> 0,
								'product_totalprice'	=> 0,

								'promain'	=> trim($r->tb_promain_id),
								'prolist'	=> trim($r->tb_prolist_id),
								'list'		=> trim($r->tb_list_id)
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

	//	update bill approve from store
	public function confirmStore()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			//	setting
			$bill_id = get_valueNullToNull($request['bill_id']);
			$approve = get_valueNullToNull($request['approve']);

			$error = 1;
			$txt = 'ไม่สามารถอนุมัติได้';

			$sql = $this->db->select(
				"retail_note.id as tb_bill_id,"
			)
				->from('retail_note')
				->where('retail_note.id', $bill_id)
				->where('retail_note.status', 1);
			$q = $sql->get();
			$num = $q->num_rows();

			if ($bill_id && $num) {
				$row = $q->row();

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
					'apst_user'   	=>  $this->session->userdata('useradminid')
				);
				$this->db->where('retail_note.id', $bill_id);
				$this->db->update('retail_note', $dataupdate);
				// ============== Log_Detail ============== //
				$log_query = $this->db->last_query();
				$last_id = $this->session->userdata('log_id');
				$detail = "Update " . $this->TBMAIN . " approve store id:" . $bill_id . " Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
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

			$dataresult = array(
				'error_code'	=> $error,
				'txt'			=> $txt
			);

			$result = json_encode($dataresult);

			echo $result;
			exit;
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
			if ($request['bill_id']) {

				$dataupdate = array(
					'status'	=> 0,
					'date_update'	=> date('Y-m-d H:i:s'),
					'user_update'	=> $this->session->userdata('useradminid')
				);

				$this->db->where('retail_note.id', $request['bill_id']);
				$this->db->update('retail_note', $dataupdate);

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
			$returns = $this->mdl_note->ajaxselectproductmain();
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
			if ($billid) {
				$sqlold = $this->db->select('retail_note.id as rt_id')
					->from('retail_note')
					->join('retail_notedetail', 'retail_note.id=retail_notedetail.rt_note_id', 'left')
					->where('retail_note.id', $billid)
					->where('retail_note.status', 1)
					->where('retail_notedetail.prolist_id', $pid)
					->where('retail_notedetail.status', 1);
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
					'list'	=> $r->LIST_ID ? $r->LIST_ID  : "",
					'id'	=> $pid
				);

				$result = json_encode($data);
			} else {
				$result = json_encode($data);
			}

			echo $result;
		}
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
	//
	public function update_bill()
	{
		/* echo "<pre>";
		print_r($_REQUEST);
		echo "</pre>";
		exit; */
		if ($this->input->server('REQUEST_METHOD')) {
			$dataresult = array();

			$error_code = 1;
			$txt = "ไม่สามารถทำรายการได้";

			if ($this->input->server('REQUEST_METHOD') == 'POST') {
				$request = $_REQUEST;

				if (!$request['item']) {
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

				$bill_id = get_valueNullToNull($request['bill_id']);
				$bill_code = get_valueNullToNull($request['bill_code']);
				$remark = trim($request['remark']);

				$datainsert = array(
					'remark'	=> $remark,

					'date_update'	=> date('Y-m-d H:i:s'),
					'user_update'	=> $this->session->userdata('useradminid')
				);

				$this->db->where('retail_note.id', $bill_id);
				$this->db->update('retail_note', $datainsert);
				// ============== Log_Detail ============== //
				$log_query = $this->db->last_query();
				$last_id = $this->session->userdata('log_id');
				$detail = "Update retail_note id:" . $bill_id . " Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
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
					$sqlold = $this->db->select('id')
						->from('retail_notedetail')
						->where('retail_notedetail.rt_note_id', $bill_id)
						->where('retail_notedetail.status', 1);
					$qold = $sqlold->get();
					$numold = $qold->num_rows();
					if ($numold) {
						foreach ($qold->result() as $rold) {
							$compare[] = $rold->id;
						}
					}
				}
				
				//	update product detail
				if ($request['item']) {
					foreach ($request['item'] as $key => $val) {
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
						$sqlold = $this->db->select('retail_note.id as rt_id')
							->from('retail_note')
							->join('retail_notedetail', 'retail_note.id=retail_notedetail.rt_note_id', 'left')
							->where('retail_note.id', $bill_id)
							->where('retail_note.status', 1)
							->where('retail_notedetail.prolist_id', $prolist)
							->where('retail_notedetail.status', 1);
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
									'user_update'		=> $this->session->userdata('useradminid'),
									'status'			=> $status
								);

								$this->db->where('retail_notedetail.id', $iddetail);
								$this->db->update('retail_notedetail', $setdetail);

								// ============== Log_Detail ============== //
								$log_query = $this->db->last_query();
								$last_id = $this->session->userdata('log_id');
								$detail = "Update retail_notedetail id:" . $iddetail . " Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
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
						} else {
							//	if new item
							if ($qty  && $promain && $prolist) {
								$setdetail = array(
									'code'				=> $bill_code,
									'rt_note_id'		=> $bill_id,
									'promain_id'		=> $promain,
									'prolist_id'		=> $prolist,
									'list_id'			=> $list,
									'quantity'			=> $qty,
									'total_price'		=> 0,
									'date_starts'		=> date('Y-m-d H:i:s'),
									'user_starts'		=> $this->session->userdata('useradminid'),
									'status'			=> $status
								);
								$this->db->insert('retail_notedetail', $setdetail);

								// ============== Log_Detail ============== //
								$log_query = $this->db->last_query();
								$last_id = $this->session->userdata('log_id');
								$detail = "Insert retail_notedetail id:" . $iddetail . " Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
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
					}		//	foreach


					//	update status 0 for detail id not paramiter
					if (count($compare)) {
						foreach ($compare as $key => $val) {
							$setoff = array(
								'status'			=> 0,
								'date_update'		=> date('Y-m-d H:i:s'),
								'user_update'		=> $this->session->userdata('useradminid'),
							);

							$this->db->where('retail_notedetail.id', $val);
							$this->db->update('retail_notedetail', $setoff);

							// ============== Log_Detail ============== //
							$log_query = $this->db->last_query();
							$last_id = $this->session->userdata('log_id');
							$detail = "Update retail_notdetail id:" . $val . " Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
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

				$error_code = 0;
				$txt = 'อัพเดตข้อมูล Note สำเร็จ';
			}

			$dataresult = array(
				'error_code' 	=> $error_code,
				'txt'			=> $txt
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

	/**
	 * =====================================================================================================
	 * =====================================================================================================
	 * =====================================================================================================
	 */
}
