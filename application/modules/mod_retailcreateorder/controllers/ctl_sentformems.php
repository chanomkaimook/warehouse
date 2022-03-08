<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctl_sentformems extends CI_Controller {

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
        $this->load->model('mdl_sentformems');
		$this->load->model('mdl_sql');
		$this->load->model('mdl_uplode');
		$this->load->library('session');
		$this->load->library('Permiss');
        $this->load->helper(array('form', 'url','myfunction_helper','sql_helper','permiss_helper','array_helper'));
        
        $this->set	= array (
            'ctl_name'				=> 'ctl_sentformems',
			'mainmenu'		        => 'retail',
			'submenu'		        => 'sentformems',
            'username_session'		=> $this->session->userdata('useradminname'),
            'userid_session'		=> $this->session->userdata('useradminid')
        );
        if($this->session->userdata('useradminid') == ''){
        	redirect('mod_admin/ctl_login');
        }
    }
     
	public function index() {
		
		$data = array (
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		
		$this->load->view('index', $data);
	}
	 
	public function report_scg() {
		
		$data = array (
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
		
		$data['query'] = $this->mdl_sentformems->dataems($this->input->post('select-order'));
		// echo $this->mdl_sentformems->dataems($this->input->post('select-order'))."xxxxxxxxxxxxxxxxxxxx";
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		$this->load->view('report_scg', $data);
	}
	
	public function report_flash() {
		
		$data = array (
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
		
		$data['query'] = $this->mdl_sentformems->dataems($this->input->post('select-order'));
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		$this->load->view('report_flash', $data);
	}
	
	public function report_express() {
		
		$data = array (
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
		
		$data['query'] = $this->mdl_sentformems->dataems($this->input->post('select-order'));
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		$this->load->view('report_express', $data);
	}
	 
	public function sentformems() {
 	
		$data = array (
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		$this->load->view('sentformems', $data);
	}

	public function sentformems_print() {
		 
		$data = array (
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
		$data['Query'] = $this->mdl_sentformems->dataprintems($this->input->post('select-order'));
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		// echo '<pre>'; print_r($data['Query']); exit;
		$this->load->view('sentformems_print', $data);
	}

	function fetch_sentformems(){  
	
		$fetch_data = $this->mdl_sentformems->make_datatables();  
		$basepic = base_url().BASE_PIC;
		$data = array(); $index = 1;  $status_bnt = ''; $remark = '';
		/* echo "<pre>";
		print_r($fetch_data);
		echo "</pre>"; */
		if($fetch_data){

			foreach($fetch_data as $row){  
				 
				if($row->STATUS_COMPLETE == 0){
					$STATUS_COMPLETE = '<span style="color: #ffc107 ;"> รอการอนุมัติ </span>';
				} else if($row->STATUS_COMPLETE == 1){
					$STATUS_COMPLETE = '<span style="color: #ffc107 ;"> รอการอนุมัติ </span>';
				} else if($row->STATUS_COMPLETE == 2){
					$STATUS_COMPLETE = '<span style="color: #28a745 ;"> อนุมัติสำเร็จ </span>';
				} else if($row->STATUS_COMPLETE == 3){
					$STATUS_COMPLETE = '<span style="color: #f44336 ;"> ยกเลิกรายการ </span>';
					if($row->REMARK != ''){
						$remark = '<b>หมายเหตุ : </b>'.$row->REMARK;
					}
				}	

				if($row->STATUS_APPROVE1 == 1){
					$APPROVE1 = '<span style="color: #28a745 ;"> <i class="fa fa-check-circle" aria-hidden="true"></i> </span>';
				} else {
					$APPROVE1 = '<span style="color: #ffc107 ;"> <i class="fa fa-clock-o" aria-hidden="true"></i> </span>';
				}
				if($row->STATUS_APPROVE2 == 1){
					$APPROVE2 = '<span style="color: #28a745 ;"> <i class="fa fa-check-circle" aria-hidden="true"></i> </span>';
				} else {
					$APPROVE2 = '<span style="color: #ffc107 ;"> <i class="fa fa-clock-o" aria-hidden="true"></i> </span>';
				}
				
				$sql = $this->db->select('NAME_US')
				->from('delivery')
				->where('id',$row->DELIVERY_FORMID)
				->get();
				$numdelevery = $sql->num_rows();
				if($numdelevery > 0){
					$r = $sql->row();
					$DELIVERYFORMID = $r->NAME_US;
				}
			
				
				$sub_array = array();  
				$sub_array[] = "<div class='text-center'>
									<div class='form-group'>
										<div class='custom-control custom-checkbox'>
											<input class='custom-control-input' type='checkbox' id='select-order-".$row->ID."' name='select-order[]' value='".$row->ID."'>
											<label for='select-order-".$row->ID."' class='custom-control-label'> </label>
										</div>
									</div>
								</div>";
				$sub_array[] = "<div class='text-left'> 
									<b>รหัสออเดอร์ : ".$row->CODE."</b> | <b>  สถานะ : ".$STATUS_COMPLETE."</b><br>
									<b>ชื่อ-นามสกุล :</b> ".$row->NAME." <br> 
								</div>";
				$sub_array[] = "<div class='text-left'> 
									<b>วันที่ : </b>".thai_date($row->DATE_STARTS)." เวลา : ".date('H:i:s',strtotime($row->DATE_STARTS))." | <b>รูปแบบการส่ง : </b>".$DELIVERYFORMID."<br>  
									<b>ที่อยู่ :</b> ".$row->ADDRESS." <b>รหัสไปรษณีย์</b> ".$row->ZIPCODE." <br> 
								</div>";
								
				// $sub_array[] = $row->CODE;
				$data[] = $sub_array;  
			}  
			
			$output = array(  
				"draw"             	=>     intval($_POST["draw"]),  
				"recordsTotal"      =>     $this->mdl_sentformems->get_all_data(),  
				"recordsFiltered"   =>     $this->mdl_sentformems->get_filtered_data(),  
				"data"              =>     $data  
			);  
		}  else{

			$output = array(  
				"draw"             	=>     0,  
				"recordsTotal"      =>     $this->mdl_sentformems->get_all_data(),  
				"recordsFiltered"   =>     $this->mdl_sentformems->get_filtered_data(),  
				"data"              =>     array()  
			);
		}
		
		
		 
		echo json_encode($output);  
	}  
	
	// public function ajaxprintems() {
	// 	if($this->input->server('REQUEST_METHOD') == 'POST'){
	// 		$returns = $this->mdl_sentformems->ajaxprintems();
	// 		$return = json_decode($returns);
	// 		echo $returns;
	// 	}
	// }
	    
}
