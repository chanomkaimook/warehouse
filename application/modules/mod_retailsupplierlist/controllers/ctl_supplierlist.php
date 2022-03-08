<?php

use phpDocumentor\Reflection\Types\Integer;

defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_supplierlist extends CI_Controller
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
		$this->load->model('mdl_supplierlist');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->library('Supplier');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_supplierlist',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function supplierlist()
	{

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'retailsupplierlist'
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('supplierlist', $data);
	}

	public function getDataBill()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$request = $_REQUEST;

			$total = $this->mdl_supplierlist->alldata();
			$sql = $this->mdl_supplierlist->makedata();

			$data = array();
			$subdata = array();

			//	sql creditnote
			if ($sql->result()) {
				$index = $request['start'] + 1;
				foreach ($sql->result() as $row) {

					// $textdisplay = "<font class='text-bold'>".$row->cn_code." <i class='fas fa-search text-muted'></i></font>";
					$textdisplay = "<a href='" . site_url('mod_retailsupplierlist/ctl_supplierlist/viewbill?id=' . $row->sp_id) . "' target=_blank class='text-bold text-secondary text-md' >" . $row->sp_name_th . " </a>";

					if($row->sp_user_update){
						$date = thai_date(date('Y-m-d',strtotime($row->sp_date_update)));
						$staff = $this->mdl_supplierlist->findUsernameByCode($row->sp_user_update);
					}else{
						$date = thai_date(date('Y-m-d',strtotime($row->sp_date_starts)));
						$staff = $this->mdl_supplierlist->findUsernameByCode($row->sp_user_starts);
					}

					$rowarray = array();
					$rowarray['DT_RowId'] = $row->sp_id;	//	set row id
					$rowarray['id'] = $index;
					$rowarray['name'] = $textdisplay;
					$rowarray['date'] = $date;
					$rowarray['user'] = $staff;

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
			'submenu' 		=> 'retailsupplierlist'
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
			'submenu' 		=> 'retailsupplierlist'
		);

		$id = $this->input->get('id');
		$data['method'] = $this->uri->segment(3);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('editbill', $data);
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

	public function update_supplier() {
		$dataresult = array();

		$error_code = 1;
		$txt = "ไม่สามารถทำรายการได้";

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$request = $_REQUEST;
			if(trim($request['suppliername']) && $request['bill_id']){

				$dataupdate = array(
					'name_th'	=> trim($request['suppliername']),
					'date_update'	=> date('Y-m-d H:i:s'),
					'user_update'	=> $this->session->userdata('useradminid')
				);

				$this->db->where('supplier.id',$request['bill_id']);
				$this->db->update('supplier',$dataupdate);

				$error_code = 0;
				$txt = "อัพเดตรายการสำเร็จ";			
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
			$datareceivedetail = array();

			if ($text) {
				$table = 'supplier';
				$tablestaff = 'staff';

				$sql = $this->db->select(
					$table . '.ID as sp_id,' .
					$table . '.NAME as sp_name,' .
					$table . '.NAME_TH as sp_name_th,' .
					$table . '.DATE_STARTS as sp_date_starts,' .
					$table . '.USER_STARTS as sp_user_starts,' .
					$table . '.DATE_UPDATE as sp_date_update,' .
					$table . '.USER_UPDATE as sp_user_update,'
				)
				->from('supplier')
				->where('supplier.id',$text)
				->where('supplier.status',1);
				$q = $sql->get();
				$num = $q->num_rows();

				if ($num) {
					foreach ($q->result() as $r) {
						$data	= array(
							'sp_id'	=> trim($r->sp_id),

							'sp_name'		=> (trim($r->sp_name_th) ? trim($r->sp_name_th) : trim($r->sp_name)),
							'sp_date_starts'	=> trim($r->sp_date_starts),
							'sp_user_starts'		=> (trim($r->sp_user_starts) ? $this->mdl_supplierlist->findUsernameByCode($r->sp_user_starts) : ""),
							'sp_date_update'		=> trim($r->sp_date_update),
							'sp_user_update'		=> (trim($r->sp_user_update) ? $this->mdl_supplierlist->findUsernameByCode($r->sp_user_update) : "")
						);
					}

					$dataresult = array('data' => $data);
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

				$this->db->where('supplier.id',$request['bill_id']);
				$this->db->update('supplier',$dataupdate);

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
	/**
	 * =====================================================================================================
	 * =====================================================================================================
	 * =====================================================================================================
	 */
	
}
