<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctl_retailbarcode extends CI_Controller {

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
        $this->load->model('mdl_retailbarcode');
		$this->load->model('mdl_sql');
		$this->load->library('session');
		$this->load->library('Permiss');
        $this->load->helper(array('form', 'url','myfunction_helper','sql_helper','permiss_helper'));
        
        $this->set	= array (
            'ctl_name'				=> 'ctl_retailbarcode',
            'username_session'		=> $this->session->userdata('useradminname'),
            'userid_session'		=> $this->session->userdata('useradminid'),
            'mainmenu'		        => 'retail',
            'submenu'		        => 'barcode'
        );
        if($this->session->userdata('useradminid') == ''){
        	redirect('mod_admin/ctl_login');
        }
    }
     
	public function barcode() {
		if(chkPermiss() == 1){
			redirect('mod_admin/ctl_login');
		}
		
		$data = array (
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);
		
		$data['Query_productmain'] = $this->mdl_sql->get_WhereParaqry('retail_productmain', 'status', 1);
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
 		$this->load->view('barcode', $data);
	}
	function fetch_products(){  
		$fetch_data = $this->mdl_retailbarcode->make_datatables();
		$data = array(); $index = 1;  $status_bnt = '';
		
		$arraypic = array();
		
		foreach($fetch_data as $row){  
			$prolistid = $row->RPL_ID;
            $codemac = $row->CODEMAC;
            $codeproduct = $row->CODEPRODUCT;
            $imagebarcode = "";
            $imagebarcodemac = "";
			
			if(isset($codemac)){
                if(array_search($codeproduct,$arraypic) !== false){
                    $imagebarcodemac = "<img src='".base_url('asset/images/barcode/'.$codemac.".png")."' >";
                }else{
                    $imagebarcodemac = $this->mdl_retailbarcode->barcode($codemac);
                }
            }
			
			//  creat button
            // $button_edit = '<button class="btn btn-sm btn-outline-success p-1"><i class="fas fa-pen"></i></button>';
            $button_edit = '<div class="text-success btn-edit" data-id="'.$prolistid.'" ><i class="fas fa-pen"></i></div>';
            // $button_del = '<div class="text-danger ml-4"><a><i class="fas fa-trash"></i></a></div>';
            $action = "<div class='d-flex'>". $button_edit ."</div>";

			$sub_array = array();  
			$sub_array[] = "<div class='text-center'>".$index++."</div>";
			$sub_array[] = $codemac;
			$sub_array[] = $codeproduct;
			$sub_array[] = $row->RPL_NAME_TH;
			$sub_array[] = $row->RPM_NAME_TH;
			$sub_array[] = $imagebarcodemac;
			$sub_array[] = "s";
			$sub_array[] = $action;
  	 
 			$data[] = $sub_array;  
		}
		$output = array(  
			"draw"             	=>     intval($_POST["draw"]),  
			"recordsTotal"      =>     $this->mdl_retailbarcode->get_all_data(),  
			"recordsFiltered"   =>     $this->mdl_retailbarcode->get_filtered_data(),  
			"data"              =>     $data  
		);  
		echo json_encode($output); 
	}
	function fetch_product(){  
		$fetch_data = $this->mdl_retailbarcode->make_datatables();  

		$basepic = base_url().BASE_PIC;
        $data = array(); $index = 1;  $status_bnt = '';
        
        $arraypic = array();
        //
        //  read file directory
        $objOpen = opendir('asset/images/barcode');
        while (($file = readdir($objOpen)) !== false)
        {
            $filename = "";
            $type = strchr($file,".");      //  check type file
            if($type == ".png"){
                $filename = explode(".",$file);
                array_push($arraypic,$filename[0]);
            }
        }
    // print_r($arraypic);
		foreach($fetch_data as $row){  

            $prolistid = $row->RPL_ID;
            $codemac = $row->CODEMAC;
            $codeproduct = $row->CODEPRODUCT;
            $imagebarcode = "";
            $imagebarcodemac = "";
            //  
            //  check image barcode
           if(isset($codeproduct)){
                if(array_search($codeproduct,$arraypic) !== false){
                    $imagebarcode = "<img src='".base_url('asset/images/barcode/'.$codeproduct.".png")."' >";
                }else{
                    $imagebarcode = $this->mdl_retailbarcode->barcode($codeproduct);
                }
			}
			if(isset($codemac)){
                if(array_search($codeproduct,$arraypic) !== false){
                    $imagebarcodemac = "<img src='".base_url('asset/images/barcode/'.$codemac.".png")."' >";
                }else{
                    $imagebarcodemac = $this->mdl_retailbarcode->barcode($codemac);
                }
            }

            //  creat button
            // $button_edit = '<button class="btn btn-sm btn-outline-success p-1"><i class="fas fa-pen"></i></button>';
            $button_edit = '<div class="text-success btn-edit" data-id="'.$prolistid.'" ><i class="fas fa-pen"></i></div>';
            // $button_del = '<div class="text-danger ml-4"><a><i class="fas fa-trash"></i></a></div>';
            $action = "<div class='d-flex'>". $button_edit ."</div>";

			$sub_array = array();  
			$sub_array[] = "<div class='text-center'>".$index++."</div>";
			$sub_array[] = $codemac;
			$sub_array[] = $codeproduct;
			$sub_array[] = $row->RPL_NAME_TH;
			$sub_array[] = $row->RPM_NAME_TH;
			$sub_array[] = $imagebarcodemac;
			$sub_array[] = $imagebarcode;
			$sub_array[] = $action;
  	 
 			$data[] = $sub_array;  
		}  
		$output = array(  
			"draw"             	=>     intval($_POST["draw"]),  
			"recordsTotal"      =>     $this->mdl_retailbarcode->get_all_data(),  
			"recordsFiltered"   =>     $this->mdl_retailbarcode->get_filtered_data(),  
			"data"              =>     $data  
		);  
		echo json_encode($output);  
   }  

	public function updateProductlist() {
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$returns = $this->mdl_retailbarcode->updateProductlist();
			$return = json_decode($returns);
			echo $returns;
		}
	}
 
}
