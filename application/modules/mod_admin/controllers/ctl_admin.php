<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctl_admin extends CI_Controller {

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
        $this->load->model('mdl_getway');
		$this->load->model('mdl_sql');
		$this->load->model('mdl_report');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->helper(array('form', 'url','myfunction_helper'));
	}
	 
	 
	public function gateway() {	
		$data = array (
			'mainmenu' 		=> 'Gateway',
			'submenu' 		=> 'Gateway List'
		);
		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
  		$this->load->view('gateway', $data);
	}
	function report1(){
		$data = array (
			'mainmenu' 		=> 'Gateway',
			'submenu' 		=> 'Gateway List'
		);
	 
		$data['report_pro'] = $this->mdl_report->report_pro($this->input->get('valdate'), $this->input->get('valdateTo')); 
		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
  		$this->load->view('report1', $data);
	}

	public function backend_main()
	{
		if($this->session->userdata('useradminid') == ''){
        	redirect('mod_admin/ctl_login');
        }
		$data = array (
			'mainmenu' 		=> 'main',
			'submenu' 		=> 'backend main'
		);
		
		$count1 = 0; $count2 = 0;
		$result = $this->mdl_report->report6();
		foreach($result as $row){
			if($row['date_Y14'] == 0){
				if($row['date_M14'] == 0){
					if($row['date_D14'] <= 15){
						if($row['PRO_END'] == 'NO'){
							 $count1 += 1;
						}
					}
				}
			}
			if($row['PRO_END'] != 'NO'){
				$count2 += 1;
			}
		}
		// echo $count2; 
		$data['report1'] = $this->mdl_report->report1();
		$data['report4'] = $this->mdl_report->report4();
		$data['report2'] = $this->mdl_report->report2();
		$data['report3'] = $this->mdl_sql->get_CountTableNumWhere('crm_customer', 'status', 1);
		$data['report5'] = $this->mdl_sql->get_CountTableNumWhere('crm_promotion', 'status', 1);
		$data['report6'] = $count1;
		$data['report7'] = $count2;
		$data['report10'] = $this->mdl_report->report10();
		$data['report11'] = $this->mdl_report->report11();
		$data['report12'] = $this->mdl_report->report12();
		$data['report13'] = $this->mdl_report->report13();
		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		// echo '<pre>'; print_r($data['report8']); exit;
		$this->load->view('backend_main', $data);
	}

	function fetch_report8(){  
			$fetch_data = $this->mdl_report->make_datatables();  
			$data = array(); $index = 1;  
 			foreach($fetch_data as $row){  
				if($row['CS_name'] != ''){ $Fname = $row['CS_name']; } else { $Fname = $row['name']; }
				$sub_array = array();  
				$sub_array[] = "<div class='text-center'>".$index++."</div>";
				$sub_array[] = $Fname;
				$sub_array[] = "<div class='text-center'>".$row['CPpoint']."</div>";  
				
				$data[] = $sub_array;  
			}  
			$output = array(  
				"draw"             	=>     intval($_POST["draw"]),  
				"recordsTotal"      =>     $this->mdl_report->get_all_data(),  
				"recordsFiltered"   =>     $this->mdl_report->get_filtered_data(),  
				"data"              =>     $data  
			);  
			
			echo json_encode($output);  
	}  

   	function fetch_report9(){  
		$fetch_data = $this->data_report9();
		$data = array(); $index = 1;  
		
		foreach($fetch_data as $row){  
			 
			$sub_array = array();  
			$sub_array[] = "<div class='text-center'>".$index++."</div>";
			$sub_array[] = $row['Fname'];
			$sub_array[] = "<div class='text-left'>วันที่ทำรายการล่าสุด<br> - ".$row['DATE_STARTS'].'<hr>คะแนนรวม '.$row['Point'].'<br>จำนวนรายการ '.$row['Countcus']." ครั้ง</div>";  
 			$data[] = $sub_array;  
		}  
		$output = array(  
			"draw"             	=>     intval($_POST["draw"]),  
			"recordsTotal"      =>     $this->mdl_report->get_all_data_9(),  
			"recordsFiltered"   =>     $this->mdl_report->get_filtered_data_9(),  
			"data"              =>     $data  
		);  
		
		echo json_encode($output);  
	}  

	function data_report9(){
		// if(!empty($_POST["valdate"])) {  
		// 	$dateTo = date('Y-m-d');
        // 	echo $valdate = date("Y-m-d",strtotime("-".$_POST["valdate"]." days",strtotime($dateTo)));  exit;
		// }  
		$result = $this->mdl_report->make_datatables_9(); 
		$items = [];
			foreach($result as $key => $row){
				if($row->CS_name != ''){ $Fname = $row->CS_name; } else { $Fname = $row->name; }
					$date1 = $row->DATE_STARTS;
					$date2 = date('Y-m-d H:i:s');
					$datetime1 = new DateTime($date1);
					$datetime2 = new DateTime($date2);
					$interval = $datetime1->diff($datetime2);
					if(!empty($_POST["valdate"])) { 
						$date_max = ($_POST["valdate"] + 29);
						if($interval->days >= $_POST["valdate"] && $interval->days <= $date_max){
							$items[$key]['Fname'] = $Fname;
							$items[$key]['Point'] = $row->CPpoint;
							$items[$key]['Countcus'] = $row->countcus;
							$items[$key]['DATE_STARTS'] = $row->DATE_STARTS;
							$items[$key]['Days'] = $interval->days;
						}
					} else {
						if($interval->days >= '30' && $interval->days <= '59'){
							$items[$key]['Fname'] = $Fname;
							$items[$key]['Point'] = $row->CPpoint;
							$items[$key]['Countcus'] = $row->countcus;
							$items[$key]['DATE_STARTS'] = $row->DATE_STARTS;
							$items[$key]['Days'] = $interval->days;
						}
					}
 
 			}
		// echo '<pre>'; print_r($items); exit;
		$data = $items;
		return $data;
	}

	function fetch_reportProrangking(){  
		$fetch_data = $this->mdl_report->make_datatables_Prorangking();  
		$data = array(); $index = 1;  
		foreach($fetch_data as $row){  
		
			$sub_array = array();  
			$sub_array[] = "<div class='text-center'>".$index++."</div>";
			$sub_array[] = $row->Proname;
			$sub_array[] = "<div class='text-center'>".$row->Cpoint."</div>";  
			$sub_array[] = "<div class='text-center'>".$row->count."</div>";  
			$data[] = $sub_array;  
		}  
		$output = array(  
			"draw"             	=>     intval($_POST["draw"]),  
			"recordsTotal"      =>     $this->mdl_report->get_all_data_Prorangking(),  
			"recordsFiltered"   =>     $this->mdl_report->get_filtered_data_Prorangking(),  
			"data"              =>     $data  
		);  
		
		echo json_encode($output);  
	}  
  
	public function ajaxtextmonth() {
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$returns = $this->mdl_report->ajaxtextmonth();
			$return = json_decode($returns);
			echo $returns;
		}
	}
	 
}
