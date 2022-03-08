<?php

use phpDocumentor\Reflection\Types\Integer;

ini_set('max_execution_time', 0);
ini_set('memory_limit', "100M");

defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_report extends CI_Controller
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
		$this->load->model(array('mdl_report'));
		$this->load->library('session');
		$this->load->library(array('Permiss'));
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper', 'array_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_report',
			'mainmenu'		        => 'retail',
			'submenu'		        => 'report',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function table()
	{
		// if(chkPermiss() == 1){
		// redirect('mod_admin/ctl_login');
		// }

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('table', $data);
	}

	public function retailreport()
	{
		// if(chkPermiss() == 1){
		// redirect('mod_admin/ctl_login');
		// }

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('retailreport', $data);
	}

	function query_data()
	{
		//
		//	setting
		$array = array();
		$getalldata = "";
		$getfilterdata = "";
		$data = "";
		// $table = $this->input->post('table');
		$table = $_REQUEST['table'];

		$requestData = $_REQUEST;

		$valdate = $requestData['valdate'];
		$valdateTo = $requestData['valdateto'];
		$searchfromdate = $requestData['valsearchfromdate'];
		//
		// echo $table."*************";
		if ($table) {

			$fetch_data = $this->mdl_report->make_datatables();

			$getalldata = $this->mdl_report->get_all_data();
			// $getfilterdata = $this->mdl_report->get_filtered_data();
			$getfilterdata = $fetch_data['numberfilter'];

			$basepic = base_url() . BASE_PIC;
			$data = array();
			$index = $fetch_data['startno'] + 1;

			foreach ($fetch_data['query'] as $r) {
				array_push($array, $r);
			}
		}

		// echo "<pre>";print_r($table);echo "</pre>";die();
		switch ($table) {
			case 'bill_report':
				foreach ($array as $row) {
					$sub_array = array();

					$delivery_name = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->delivery_name . "\" >" . $row->delivery_name . "</p>";
					$method_name = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->method_name . "\" >" . $row->method_name . "</p>";

					$datacreate = "<p>" . date('d-m-Y', strtotime($row->bill_datetime)) . "</p>";
					$billid = $row->billid;
					$billcode = $row->bill_code;

					$q_staff_start = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $row->bill_user_starts);
					$q_staff_update = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $row->bill_user_update);
					$approve1 = "<p class=\"textoverflow\">" . ($q_staff_start->name_th ? $q_staff_start->name_th . " " . $q_staff_start->lastname_th : $q_staff_start->name . " " . $q_staff_start->lastname) . "</p>";
					$approve2 = "<p class=\"textoverflow\">" . ($q_staff_update->name_th ? $q_staff_update->name_th . " " . $q_staff_update->lastname_th : $q_staff_update->name . " " . $q_staff_update->lastname) . "</p>";

					$sub_array[] = $index;
					$sub_array[] = $datacreate;
					$sub_array[] = "<a href='" . site_url('mod_retailcreateorder/ctl_createorder/viwecreatebill?id=' . $billid . '&mdl=mdl_createorder') . "' target='blank' >" . $billcode . "</a>";
					$sub_array[] = $row->rtd_sum." รายการ";
					$sub_array[] = $delivery_name;
					$sub_array[] = $method_name;
					$sub_array[] = $approve1;
					$sub_array[] = $approve2;


					$data[] = $sub_array;
					$index++;
				}
				break;
			case 'bill_summary':
				foreach ($array as $row) {

					$sub_array = array();

					$delivery_name = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->bill_typedelivery . "\" >" . $row->bill_typedelivery . "</p>";
					$method_name = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->bill_gateway . "\" >" . $row->bill_gateway . "</p>";

					$datacreate = "<p>" . date('d-m-Y', strtotime($row->bill_datetime)) . "</p>";
					$billcode = $row->bill_code;

					$q_staff_start = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $row->bill_user_starts);
					$q_staff_update = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $row->bill_user_update);
					$approve1 = "<p class=\"textoverflow\">" . ($q_staff_start->name_th ? $q_staff_start->name_th . " " . $q_staff_start->lastname_th : $q_staff_start->name . " " . $q_staff_start->lastname) . "</p>";
					$approve2 = "<p class=\"textoverflow\">" . ($q_staff_update->name_th ? $q_staff_update->name_th . " " . $q_staff_update->lastname_th : $q_staff_update->name . " " . $q_staff_update->lastname) . "</p>";

					$sub_array[] = $index;
					$sub_array[] = $datacreate;
					$sub_array[] = $billcode;
					$sub_array[] = $row->product_name;
					$sub_array[] = $row->bill_qty;
					$sub_array[] = $delivery_name;
					$sub_array[] = $method_name;
					$sub_array[] = $approve1;
					$sub_array[] = $approve2;


					$data[] = $sub_array;
					$index++;
				}
				break;
			case 'bill_store':
				$inarray = array();
				$subinarray = array();

				$prolistkey = array();

				foreach ($array as $row) {
					$data_array = array();
					$datacreate = "<p>" . date('d-m-Y', strtotime($row->bill_datetime)) . "</p>";

					$billcode = $row->bill_code;
					$mainid = $row->mainid;
					$listid = $row->listid;
					$quantity = $row->quantity;
					$billstatus = $row->billstatus; // T=ปกติ,C=ปลายทาง,F=อื่นๆ

					// check ยอดรับเข้า หากมีให้นำมาลบยอดรวม
					$sqlt = $this->db->select('retail_receivedetail.quantity as rtd_qty')
						->from('retail_receive')
						->join('retail_receivedetail', 'retail_receive.id=retail_receivedetail.receive_id', 'left')
						->where('retail_receive.rt_bill_code', $billcode)
						->where('retail_receive.complete', 2)
						->where('retail_receive.status', 1);
					$qt = $sqlt->get();
					$numt = $qt->num_rows();
					if ($numt) {
						$rowt = $qt->row();
						$quantity = $quantity - $rowt->rtd_qty;
					}

					/* array_push($inarray,array(
										'datestarts' 	=> $datacreate,
										'promainid' 	=> $mainid,
										'prolistid' 	=> $listid,
										'qty' 			=> $quantity
							)); */

					$inarray[$datacreate][$listid][$billstatus] += $quantity;
				}

				// echo "<pre>by inarray:".count($inarray)."=" . print_r($inarray) . "</pre>";
				$byGroup_date = array_keys($inarray);

				//	check column
				$column_query = $this->mdl_report->columnProduct();
				$r = json_decode($column_query);

				if (isset($byGroup_date[0])) {	//	find date
					foreach ($byGroup_date as $keydate => $date) {
						$sub_array = array();
						$sub_array[] = $index;
						$sub_array[] = $date;

						if ($r->error_code == 0) {
							foreach ($r->query as $column) {
								$price = $inarray[$date][$column->product_id]['T'] + $inarray[$date][$column->product_id]['C'];
								$free = $inarray[$date][$column->product_id]['F'];
								if ($free > 0) {
									$newprice = $price . " / <span class='text-danger'>" . $free . "</span>";
								} else {
									$newprice = $price;
								}

								$sub_array[] = $newprice;
							}
						}

						$data[] = $sub_array;
						$index++;
					}
				}
				break;
			case 'ranking_sale':

				// $arr_name = array_unique($array['bill_name']);
				// $arr_name = array_keys($array, "bill_name");
				$arr_name = array_column($array, "bill_name");

				$arr_groupname = array_unique($arr_name);
				// $arr_groupname = array_values(array_unique($arr_name));

				/* echo "<pre>";
				// print_r($array);
				// print_r($arr_name);
				// print_r($array[0]->bill_totalprice);
				echo "</pre>"; */
				// exit;
				foreach ($arr_groupname as $rowname => $valname) {
					$arr_setname[$rowname] = array_keys(array_column($array, 'bill_name'), $valname);
				}

				unset($arr_name, $arr_groupname);

				foreach ($arr_setname as $key => $keyarray) {
					$totalprice[$key] = 0;
					foreach ($keyarray as $value) {
						$totalprice[$key] += $array[$value]->bill_totalprice;
					}

					$array[$key]->bill_totalprice = $totalprice[$key];
				}
				asort($totalprice);
				/* // echo "arr_setname<pre>";
				// print_r($arr_setname);
				echo "totalprice<pre>";
				print_r($totalprice);
				echo "</pre>";
				exit; */

				foreach ($totalprice as $key => $keyarray) {

					$sub_array = array();

					$this->db->select('
									COUNT(retail_bill.id) as totalbill
								');
					$this->db->from('retail_bill');
					$this->db->where('retail_bill.status_complete = 2');
					$this->db->where('retail_bill.status = 1');
					$this->db->where('retail_bill.name = "' . $array[$key]->bill_name . '"');

					if ($valdate != '' && $valdateTo == '') {
						$this->db->where('date(retail_bill.date_starts) ', $valdate);
					} else if ($valdate != '' && $valdateTo != '') {
						$this->db->where('date(retail_bill.date_starts) >=', $valdate);
						$this->db->where('date(retail_bill.date_starts) <=', $valdateTo);
					} else if ($valdateTo != '') {
						$this->db->where('date(retail_bill.date_starts) <=', $valdateTo);
					}

					$q = $this->db->get();
					$r = $q->row();

					$username = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $array[$key]->bill_name . "\" >" . $array[$key]->bill_name . "</p>";
					$phone = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $array[$key]->bill_phone . "\" >" . $array[$key]->bill_phone . "</p>";

					$totalbill = $r->totalbill;

					$datacreate = "<p>" . date('d-m-Y', strtotime($array[$key]->bill_datetime)) . "</p>";
					$billcode = $array[$key]->bill_code;
					$price = $array[$key]->bill_totalprice;

					$sub_array[] = $index;
					$sub_array[] = $username;
					$sub_array[] = $phone;
					$sub_array[] = $price;
					$sub_array[] = "<p class='text-right'>" . $totalbill . "</p>";

					$data[] = $sub_array;
					$index++;
				}
				break;
		}
		//
		//
		$output = array(
			"draw"             	=>     intval($_POST["draw"]),
			"recordsTotal"      =>     $getalldata,
			"recordsFiltered"   =>     $getfilterdata,
			"query"             =>     $array,
			"data"              =>     $data
		);

		return $output;
	}
	function fetch_data()
	{
		$output = $this->query_data();
		echo json_encode($output);
	}

	function query_dataMKTProduct()
	{
		//
		//	setting
		$array = array();
		$getalldata = "";
		$getfilterdata = "";
		$data = "";
		// $table = $this->input->post('table');
		$table = $this->input->get('table');
		//
		// echo $this->input->post('table')."*************";
		if ($table) {
			$getalldata = $this->mdl_report->get_all_data();
			$getfilterdata = $this->mdl_report->get_filtered_data();
			$fetch_data = $this->mdl_report->make_datatablesProduct();

			$basepic = base_url() . BASE_PIC;
			$data = array();
			$index = 1;
			foreach ($fetch_data as $r) {
				array_push($array, $r);
			}

			foreach ($array as $row) {
				$productid = "";
				$sub_array = array();

				$username = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->bill_name . "\" >" . $row->bill_name . "</p>";
				$phone = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->bill_phone . "\" >" . $row->bill_phone . "</p>";

				$datacreate = "<p>" . date('d-m-Y', strtotime($row->bill_datetime)) . "</p>";
				$billcode = $row->bill_code;

				$qty = $row->bill_qty;
				$text_qty = $qty;

				$productid = $row->product_id;
				$gateway = $row->methodtopic;

				// check ยอดรับเข้า หากมีให้นำมาลบยอดรวม
				$sqlt = $this->db->select('retail_receivedetail.quantity as rtd_qty')
					->from('retail_receive')
					->join('retail_receivedetail', 'retail_receive.id=retail_receivedetail.receive_id', 'left')
					->where('retail_receive.rt_bill_code', $billcode)
					->where('retail_receive.complete', 2)
					->where('retail_receive.status', 1);
				$qt = $sqlt->get();
				$numt = $qt->num_rows();
				if ($numt) {
					$rowt = $qt->row();
					$quantity = $qty - $rowt->rtd_qty;

					$text_qty = $quantity . " ($qty - $rowt->rtd_qty)";
				}


				if ($productid) {
					$product = get_WhereParaSelect('code,name_th', 'retail_productlist', 'id', $productid);
					$productname = $product->name_th . " - (" . $product->code . ")";
				} else {
					$productname = "";
				}

				$delivery_name = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->bill_typedelivery . "\" >" . $row->bill_typedelivery . "</p>";
				$method_name = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->methodtopic . "\" >" . $row->methodtopic . "</p>";

				$q_staff_start = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $row->bill_user_starts);
				$q_staff_update = get_WhereParaSelect('name_th,lastname_th,name,lastname', 'staff', 'code', $row->bill_user_update);
				$approve1 = "<p class=\"textoverflow\">" . ($q_staff_start->name_th ? $q_staff_start->name_th . " " . $q_staff_start->lastname_th : $q_staff_start->name . " " . $q_staff_start->lastname) . "</p>";
				$approve2 = "<p class=\"textoverflow\">" . ($q_staff_update->name_th ? $q_staff_update->name_th . " " . $q_staff_update->lastname_th : $q_staff_update->name . " " . $q_staff_update->lastname) . "</p>";

				$sub_array[] = $index;
				$sub_array[] = $datacreate;
				$sub_array[] = $billcode;
				$sub_array[] = $productname;
				$sub_array[] = $text_qty;
				$sub_array[] = $delivery_name;
				$sub_array[] = $method_name;
				$sub_array[] = $approve1;
				$sub_array[] = $approve2;


				$data[] = $sub_array;
				$index++;
			}
		}
		//
		//
		$output = array(
			"draw"             	=>     intval($_POST["draw"]),
			"recordsTotal"      =>     $getalldata,
			"recordsFiltered"   =>     $getfilterdata,
			"query"             =>     $array,
			"data"              =>     $data
		);
		return $output;
	}

	function fetch_dataMKT()
	{
		$output = $this->query_dataMKTProduct();
		echo json_encode($output);
	}

	public function report()
	{
		if (chkPermiss() == 1) {
			redirect(site_url(varglobal('permitexpire')));
		}

		$this->load->helper('report');

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);

		$table = $this->input->get('table');
		$subtable = $this->input->get('subtable');

		//	for load report detail in tab data table
		if ($subtable) {
			switch ($subtable) {
				case 'report_summaryproduct':
					$output = $this->query_dataMKTProduct();
					break;
			}
		} else {
			$output = $this->query_data();
		}
		// echo "<pre>";print_r($output['recordsFiltered']);echo "</pre>";exit;
		// echo $table." : tablename";exit;
		//
		switch ($table) {
			case 'bill_report':
				$this->load->view('report_bill', $output);
				break;
			case 'bill_summary':
				if ($subtable == "report_summaryproduct") {
					$this->load->view('report_summaryproduct', $output);
				} else {
					$this->load->view('report_summarybill', $output);
				}
				break;
			case 'bill_store':
				$this->load->view('report_store', $output);
				break;
			case 'ranking_sale':
				$this->load->view('report_ranking', $output);
				break;
				#
				#	from button
			case 'bill_vat':
				$this->load->view('report_billvat', $output);
				break;
			case 'bill_receipt':
				$this->load->view('report_receipt', $output);
				break;
			case 'bill_vatlistmonth':
				$this->load->view('report_billvatlistmonth', $output);
				break;
			case 'bill_listorders':
				$this->load->view('report_listorders', $output);
				break;
		}
	}

	public function ajaxSlip()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$billcode = $this->input->post('id');
			$returns = $this->mdl_report->ajaxSlip($billcode);
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function columnProduct()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_report->columnProduct();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajaxListbill()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_report->ajaxListbill();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajaxListmonth()
	{
		if ($this->input->server('REQUEST_METHOD') == 'GET') {
			//	load helper report
			$this->load->helper('report');

			//	call query array
			$output = $this->query_data();

			$returns = $this->mdl_report->ajaxListmonth($output['query']);
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajaxProductList()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$returns = $this->mdl_report->ajaxProductList();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function post()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			// $returns = $this->mdl_report->ajaxPost();
			$this->load->view('post');
			// return $returns;
			// $output = array(  
			// "draw"             	=>     intval($_POST["draw"]),  
			// "recordsTotal"      =>     intval($returns['recordsTotal']),  
			// "recordsFiltered"   =>     intval($returns['recordsFiltered']),  
			// "data"              =>     $returns['data']
			// ); 
			// echo json_encode($output);  
		}
	}
}
