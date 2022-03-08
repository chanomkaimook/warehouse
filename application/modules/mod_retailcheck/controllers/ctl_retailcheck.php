<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctl_retailcheck extends CI_Controller {

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
        $this->load->model('mdl_retailcheck');
		$this->load->library('session');
		$this->load->library('Permiss');
        $this->load->helper(array('form', 'url','myfunction_helper','sql_helper','permiss_helper'));
        
        $this->set	= array (
            'ctl_name'				=> 'ctl_retailcheck',
            'username_session'		=> $this->session->userdata('useradminname'),
            'userid_session'		=> $this->session->userdata('useradminid'),
            'mainmenu'		        => 'retail',
            'submenu'		        => 'retailcheck',
			'url_begin'		        => $this->uri->segment(1)."/".$this->uri->segment(2) ,
			'datenow'				=> date('Y-m-d')
        );
        if($this->session->userdata('useradminid') == ''){
        	redirect('mod_admin/ctl_login');
        }
    }
     
	public function checkorder() {

		if(chkPermiss() == 1){
			redirect('mod_admin/ctl_login');
		}
		
		if($this->input->post('select-order')){

			$arrays = $this->input->post('select-order');
		}else{
			$arrays = array();
		}
		asort($arrays);
		/* echo "<pre>";
		print_r($arrays);
		echo "</pre>"; */
		$data = array (
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> 'retailcheck',
			'billid'		=> $arrays
		);
		
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
 		$this->load->view('checkorder', $data);
	}

	function fetch_product(){  
		$this->load->library('order');

		$fetch_data = $this->mdl_retailstock->make_datatables();  
		$basepic = base_url().BASE_PIC;
        $data = array(); $index = 1;  $status_bnt = '';
		/* echo "<pre>";
		print_r($fetch_data);
		echo "</pre>"; */
		foreach($fetch_data as $row){  
			
            $prolistid = $row->p_id;
            $codemac = $row->codemac;
			$codeproduct = $row->p_codeproduct;
			
			//	total
			$arrayparam = array(
				'productid'		=> $prolistid
			);
			$data_stock = $this->order->informationStock($prolistid);

			//	bill success and transfer now
			$productpay = $data_stock['cut_stock'];

			//	bill success and waite transfer (date > now)
			$orderwaite_total = $data_stock['waite_stock'];

			//  total product in stock and stock add 
			$nettotal = $data_stock['net_totalstock'];

			//  total product in stock now  
			$stock_total = $data_stock['total_stock'];

			//	total order bill waite and success
			$productorder= $data_stock['cut_order'];

			//	total order in stock if cut bill
			$order_total= $data_stock['total_order'];

			//	total order outstanding in stock
			$orderout_total= $data_stock['out_stock'];

			$sub_array = array();
			$sub_array[] = "<div class='text-center' data-productid='".$prolistid."'>".$index++."</div>";
			$sub_array[] = $row->p_name;
			$sub_array[] = $row->p_typename;
			$sub_array[] = $nettotal;
			$sub_array[] = $stock_total;
			$sub_array[] = $productpay;
			$sub_array[] = $order_total;
			$sub_array[] = $productorder;
			$sub_array[] = $orderwaite_total;
			$sub_array[] = $orderout_total;
  	 
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
	
	public function searchOrderBarcode() {
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$returns = $this->mdl_retailcheck->searchOrderBarcode();
			$return = json_decode($returns);
			echo $returns;
		}
	}
	
}
