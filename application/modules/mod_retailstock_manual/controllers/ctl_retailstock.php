<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_retailstock extends CI_Controller
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
		$this->load->model('mdl_retailstock');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_retailstock',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid'),
			'mainmenu'		        => 'retail',
			'submenu'		        => 'retailstock',
			'url_begin'		        => $this->uri->segment(1) . "/" . $this->uri->segment(2),
			'datenow'				=> date('Y-m-d')
			// 'datenow'				=> "2021-05-20"
			// 'datenow'				=> "2021-01-30"
			// 'datenow'				=> "2021-05-30"
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function stock()
	{
		if (chkPermiss() == 1) {
			redirect('mod_admin/ctl_login');
		}

		if ($this->input->get('date') && $this->input->get('date') < date('Y-m-d')) {
			$date = $this->input->get('date');
		} else {
			$date = date('Y-m-d');
		}

		//	check stock daily
		$sqlstock = $this->db->select('*')
			->from('retail_stock')
			->where('date(retail_stock.date_starts)', $date)
			->where('retail_stock.status', 1);
		$numstock = $sqlstock->count_all_results(null, false);
		$qstock = $sqlstock->get();

		//	check stock daily
		$sqlinsert = $this->db->select('*')
			->from('retail_productlist')
			->where('retail_productlist.promain_id not in(6,12)')
			->where('retail_productlist.status_view', 1)
			->where('retail_productlist.status', 1);
		$numinsert = $sqlinsert->count_all_results(null, false);
		$qinsert = $sqlinsert->get();

		//	check stock ถ้าไม่มี ให้สร้างใหม่
		if ($numstock == 0) {

			if ($numinsert) {
				foreach ($qinsert->result() as $row) {

					$start = 0;
					$total = 0;
					// find total เอาตัวเลข เหลือ ของเมื่อวาน มาสร้างเป็น คงคลัง ของวันนี้
					$sqltotal = $this->db->select('*')
						->from('retail_stock')
						->where('retail_stock.retail_productlist_id', $row->ID)
						->where('retail_stock.status', 1)
						->where('date(retail_stock.date_starts) = DATE_SUB("' . $date . '", INTERVAL 1 DAY)', null, false);
					$numtotal = $sqltotal->count_all_results(null, false);
					$qtotal = $sqltotal->get();
					if ($numtotal) {
						$rtotal = $qtotal->row();

						$start = $rtotal->TOTAL;
						$total = $rtotal->TOTAL;
					}

					$datainsert = array(
						'retail_productlist_id'		=> $row->ID,
						'start'				=> $start,
						'total'				=> $total,
						'date_starts'		=> date($date . ' H:i:s'),
						'user_starts'		=> $this->session->userdata('useradminid')
					);
					$this->db->insert('retail_stock', $datainsert);
				}
			}
		} else {		//	แต่ถ้ามีแล้วให้เช็คว่า สินค้าตัวไหนยังไม่ได้สร้าง

			if ($numinsert) {
				foreach ($qinsert->result() as $row) {

					$sqlstockcheck = $this->db->select('*')
						->from('retail_stock')
						->where('retail_stock.retail_productlist_id', $row->ID)
						->where('date(retail_stock.date_starts)', $date)
						->where('retail_stock.status', 1);
					$numstockcheck = $sqlstockcheck->count_all_results(null, false);
					$qstockcheck = $sqlstockcheck->get();

					if ($numstockcheck == 0) {
						$datainsert = array(
							'retail_productlist_id'		=> $row->ID,
							'start'				=> 0,
							'total'				=> 0,
							'date_starts'		=> date($date . ' H:i:s'),
							'user_starts'		=> $this->session->userdata('useradminid')
						);
						$this->db->insert('retail_stock', $datainsert);
					}
				}
			}
			//	end if
		}

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> 'stock'
		);
		($this->input->get('date') ? $getdate = $this->input->get('date') : $getdate = date('Y-m-d'));

		$resultdata = json_decode($this->mdl_retailstock->get_stockdata($getdate));

		$claim = json_decode($this->mdl_retailstock->get_staticClaim($getdate));
		$loss = json_decode($this->mdl_retailstock->get_staticLoss($getdate));
		$repack = json_decode($this->mdl_retailstock->get_staticRepack($getdate));
		$other = json_decode($this->mdl_retailstock->get_staticOther($getdate));

		$data['staticPull'] = $resultdata->data->pull;
		$data['staticCut'] = $resultdata->data->cut;

		$data['staticClaim'] = $claim->total;
		$data['staticLoss'] = $loss->total;
		$data['staticRepack'] = $repack->total;
		$data['staticOther'] = $other->total;
		$data['get_date'] = $getdate;
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('stock', $data);
	}

	public function stocksetting()
	{
		if (chkPermiss() == 1) {
			redirect('mod_admin/ctl_login');
		}

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> 'stock'
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('stocksetting', $data);
	}

	function fetch_product()
	{
		$fetch_data = $this->mdl_retailstock->make_datatables();
		$basepic = base_url() . BASE_PIC;
		$data = array();
		$index = 1;
		$status_bnt = '';

		$request = $_REQUEST;
		/* echo "<pre>";
		print_r($fetch_data);
		echo "</pre>"; */

		$arrayset = array(
			'date'	=> ($request['date'] ? $request['date'] : date('Y-m-d'))
		);
		$totalnumber = $this->mdl_retailstock->countTotalProductOrder($arrayset);
		/* echo "<pre>";
		print_r($arrayset);
		print_r($totalnumber);
		echo "</pre>";
		exit; */
		foreach ($fetch_data as $row) {

			$stockid = $row->id;
			$codemac = $row->codemac;
			$codeproduct = $row->p_codeproduct;


			$producttotal = 0;
			$p_l = array_keys(array_column($totalnumber, 'prolist_id'), $row->p_id);
			$l = array_keys(array_column($totalnumber, 'list_id'), $row->p_id);
			if ($p_l) {
				foreach ($p_l as $plkey) {
					$producttotal += $totalnumber[$plkey]['num'];
				}
			}
			if ($l) {
				foreach ($l as $lkey) {
					$producttotal += $totalnumber[$lkey]['num'];
				}
			}
			
			
		// echo "id=".$row->id." proid=".$row->p_id."|| num :".$numt;

			//	total
			$arrayparam = array(
				'stockid'		=> $stockid
			);

			$scoreother = $row->claim + $row->loss + $row->repack + $row->other;

			$btn = "";

			$btn = '<button id="callModal" name="callModal" data-id="' . $row->id . '" class="btn btn-sm btn-secondary" data-toggle="modal" data-target=".bs-example-modal-center" >+</button>';

			($row->total ? $total = $row->total : $total = 0);

			($row->p_codemac ? $textcodemac = " (" . $row->p_codemac . ")" : $textcodemac = "");
			($producttotal ? $textproducttotal = "<span> >>> " . $producttotal . "</span>": $textproducttotal = "");
			
			$sub_array = array();
			$sub_array[] = "<div class='text-right' data-id='" . $stockid . "' data-min='" . $row->rtst_min . "' >" . $index++ . "</div>";
			$sub_array[] = $row->p_id;
			$sub_array[] = $row->p_name . $textcodemac . $textproducttotal;
			$sub_array[] = $row->start;
			$sub_array[] = $row->cut;
			$sub_array[] = $row->pull;
			$sub_array[] = "<span>" . get_valueNullToNull($scoreother) . '</span>' . $btn;
			$sub_array[] = $total;


			$data[] = $sub_array;
		}
		$output = array(
			"draw"             	=>     intval($_POST["draw"]),
			"recordsTotal"      =>     $this->mdl_retailstock->get_all_data(),
			"recordsFiltered"   =>     $this->mdl_retailstock->get_filtered_data(),
			"data"              =>     $data
		);
		echo json_encode($output);
	}

	function fetch_setting()
	{
		$fetch_data = $this->mdl_retailstock->datasetting();
		$basepic = base_url() . BASE_PIC;
		$data = array();
		$index = 1;
		$status_bnt = '';
		/* echo "<pre>";
		print_r($fetch_data);
		echo "</pre>"; */
		foreach ($fetch_data as $row) {

			$stockid = $row->id;
			$codemac = $row->codemac;
			$codeproduct = $row->p_codeproduct;

			//	total
			$arrayparam = array(
				'stockid'		=> $stockid
			);

			$scoreother = $row->claim + $row->loss + $row->repack + $row->other;

			$btn = "";

			$btn = '<button id="callModal" name="callModal" data-id="' . $row->id . '" class="btn btn-sm btn-secondary" data-toggle="modal" data-target=".bs-example-modal-center" >+</button>';

			($row->total ? $total = $row->total : $total = 0);

			$sub_array = array();
			$sub_array[] = "<div class='text-right' data-id='" . $stockid . "'>" . $index++ . "</div>";
			$sub_array[] = $row->p_id;
			$sub_array[] = $row->p_name . ($row->p_codemac ? " (" . $row->p_codemac . ") " : "");
			$sub_array[] = $row->rtst_min;
			$sub_array[] = $row->rtst_max;
			$sub_array[] = ($row->staff_nameth ? $row->staff_nameth : $row->staff_name);

			$data[] = $sub_array;
		}
		$output = array(
			"draw"             	=>     intval($_POST["draw"]),
			"recordsTotal"      =>     $this->mdl_retailstock->get_all_data(),
			"recordsFiltered"   =>     $this->mdl_retailstock->get_filtered_data(),
			"data"              =>     $data
		);
		echo json_encode($output);
	}

	public function ajax_updatestock_setting()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_retailstock->ajax_updatestock_setting();
		}
	}

	public function ajax_updatestock()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_retailstock->ajax_updatestock();
		}
	}

	public function ajax_getDataOther()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$returns = $this->mdl_retailstock->ajax_getDataOther();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajax_frmOtherSubmit()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$returns = $this->mdl_retailstock->ajax_frmOtherSubmit();
			// $return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajax_staticReload()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$returns = $this->mdl_retailstock->ajax_staticReload();
			// $return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajax_report()
	{

		$array = array();
		$fetch_data = $this->mdl_retailstock->make_datatables();
		if ($fetch_data) {

			$request = $_REQUEST;
			$date = $request['date'];
			$datelast = date("Y-m-d", strtotime("-7 days", strtotime($date)));

			foreach ($fetch_data as $row) {

				//	get sum total 
				$arrayparam = array(
					'datestart'	=> $datelast,
					'dateend'	=> $date,
					'id'	=> $row->p_id
				);
				$week = $this->mdl_retailstock->get_sumDateToItem($arrayparam);

				$product_price = $row->p_price;
				$total_stockprice = $product_price * $row->total;


				$rstaff = get_WherePara('staff', 'code', $row->user_update);

				$array[$row->p_id]['p_id'] = $row->p_id;
				$array[$row->p_id]['p_name'] = $row->p_name;
				$array[$row->p_id]['start'] = $row->start;
				$array[$row->p_id]['cut'] = get_valueNullTozero($row->cut);
				$array[$row->p_id]['pull'] = get_valueNullTozero($row->pull);
				$array[$row->p_id]['claim'] = get_valueNullTozero($row->claim);
				$array[$row->p_id]['loss'] = get_valueNullTozero($row->loss);
				$array[$row->p_id]['repack'] = get_valueNullTozero($row->repack);
				$array[$row->p_id]['other'] = get_valueNullTozero($row->other);
				$array[$row->p_id]['total'] = get_valueNullTozero($row->total);
				$array[$row->p_id]['price'] = get_valueNullTozero($product_price);
				// $array[$row->p_id]['total_stockprice'] = (get_valueNullTozero($total_stockprice) ? number_format($total_stockprice) : 0);
				$array[$row->p_id]['total_stockprice'] = get_valueNullTozero($total_stockprice);
				$array[$row->p_id]['cut_week'] = get_valueNullTozero($week->cut);
				$array[$row->p_id]['other_remark'] = $row->other_remark;
				$array[$row->p_id]['date_update'] = $row->date_update;
				$array[$row->p_id]['user_update'] = ($rstaff ? $rstaff->NAME . " " . $rstaff->LASTNAME : null);
			}

			$data['array']	= $array;
			/* echo "<pre>";
			print_r($data);
			echo "</pre>";
			exit; */
			$this->load->view('doc_reportstock', $data);

			$error = 0;
			$txt = 'success';
		} else {

			$error = 1;
			$txt = 'data not found';
		}


		/* $data = array(
				'error_code'	=> $error,
				'txt'			=> $txt
			);
			$return = json_encode($data);
			echo $return; */
	}

	public function reRunStock()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$input = filter_input_array(INPUT_GET);
			$returns = $this->mdl_retailstock->reRunStock($input);
			// $return = json_decode($returns);
			echo $returns;
		}
	}

	public function chartrank()
	{
		$request = $_REQUEST;
		if ($request['date']) {
			$date = $request['date'];
		} else {
			$date = date('Y-m-d');
		}

		$sql = $this->db->select('*')
			->from('retail_stock')
			->where('total != ""')
			->where('date(date_starts)', $date)
			->where('status', 1);
		$q = $sql->get();
		$num = $q->num_rows();

		$result = "";
		if ($num) {
			foreach ($q->result() as $row) {
				// $rowarray = array();
				$rowarray['label'][] = $row->RETAIL_PRODUCTLIST_ID;
				$rowarray['data'][] = $row->TOTAL;
			}

			$result = json_encode($rowarray);
		}

		echo $result;
	}

	public function chartpullcut()
	{
		$request = $_REQUEST;
		if ($request['date']) {
			$date = $request['date'];
		} else {
			$date = date('Y-m-d');
		}

		$sql = $this->db->select('
			sum(cut) as cut,
			sum(pull) as pull
		')
			->from('retail_stock')
			->where('date(date_starts)', $date)
			->where('status', 1);
		$q = $sql->get();
		$num = $q->num_rows();

		$result = "";
		if ($num) {
			$row = $q->row();

			$rowarray['label'] = array('purchase', 'sales');
			$rowarray['data'] = array($row->pull, $row->cut);

			$result = json_encode($rowarray);
		}

		echo $result;
	}

	public function chartpullcut_week()
	{
		$request = $_REQUEST;
		if ($request['date']) {
			$date = $request['date'];
		} else {
			$date = date('Y-m-d');
		}

		$datelast = date("Y-m-d", strtotime("-7 days", strtotime($date)));

		$sql = $this->db->select('
			sum(cut) as cut,
			sum(pull) as pull,
			date(date_starts) as datestarts
		')
			->from('retail_stock')
			->where('date(date_starts) >', $datelast)
			->where('date(date_starts) <=', $date)
			->where('status', 1)
			->group_by('date(date_starts)');
		$q = $sql->get();
		$num = $q->num_rows();

		$result = "";
		if ($num) {

			foreach ($q->result() as $row) {
				$rowarray['label'][] = array($row->datestarts);
				$rowarray['data_pull'][] = array($row->pull);
				$rowarray['data_cut'][] = array($row->cut);
			}

			$result = json_encode($rowarray);
		}

		echo $result;
	}

	public function create_pullDetail()
	{
		$request = $_REQUEST;
		if ($request['date']) {
			$date = $request['date'];
		} else {
			$date = date('Y-m-d');
		}

		$sql = $this->db->select('
			retail_stock.pull as pull,
			retail_stock.total as total,
			retail_stock.date_starts as date_starts,
			retail_stock.date_update as date_update,
			retail_productlist.name_th as name
		')
			->from('retail_stock')
			->join('retail_productlist', 'retail_productlist.id=retail_stock.retail_productlist_id', 'left')
			->where('retail_stock.pull != ""')
			->where('date(retail_stock.date_starts)', $date)
			->where('retail_stock.status', 1);
		$q = $sql->get();
		$num = $q->num_rows();

		$result = "";
		if ($num) {
			foreach ($q->result() as $row) {
				// $rowarray = array();
				$rowarray['product_name'] = $row->name;
				$rowarray['pull'] = $row->pull;
				$rowarray['date'] = date('H:i:s', strtotime($row->date_update));

				$data[] = $rowarray;
			}

			$result = json_encode($data);
		}

		echo $result;
	}

	public function create_cutDetail()
	{
		$request = $_REQUEST;
		if ($request['date']) {
			$date = $request['date'];
		} else {
			$date = date('Y-m-d');
		}

		$sql = $this->db->select('
			retail_stock.cut as cut,
			retail_stock.total as total,
			retail_stock.date_starts as date_starts,
			retail_stock.date_update as date_update,
			retail_productlist.name_th as name
		')
			->from('retail_stock')
			->join('retail_productlist', 'retail_productlist.id=retail_stock.retail_productlist_id', 'left')
			->where('retail_stock.cut != ""')
			->where('date(retail_stock.date_starts)', $date)
			->where('retail_stock.status', 1);
		$q = $sql->get();
		$num = $q->num_rows();

		$result = "";
		if ($num) {
			foreach ($q->result() as $row) {
				// $rowarray = array();
				$rowarray['product_name'] = $row->name;
				$rowarray['cut'] = $row->cut;
				$rowarray['date'] = date('H:i:s', strtotime($row->date_update));

				$data[] = $rowarray;
			}

			$result = json_encode($data);
		}

		echo $result;
	}
}
