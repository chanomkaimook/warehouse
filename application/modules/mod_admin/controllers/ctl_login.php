<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctl_login extends CI_Controller {

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
        $this->load->model('mdl_login');
		$this->load->model('mdl_sql');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->helper(array('form', 'url','myfunction_helper','sql_helper','permiss_helper'));
	}
	public function index()
	{
		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		$this->load->view('login', $data);
	}

	public function backend_main()
	{
	 	
		$data = array (
			'mainmenu' 		=> 'main',
			'submenu' 		=> 'backend main'
		);
	   		
		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		
		$this->load->view('backend_main', $data);
	}
	
	public function ajaxCheckLogin() {
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$returns = $this->mdl_login->ajaxCheckLogin();
			$return = json_decode($returns);
			echo $returns;
		}
	}
	public function pathadmin()
	{
		// echo $this->session->userdata('useradmin')." : session";die();
		// $permiss = $this->mdl_sql->get_WherePara('permiss','id',$this->session->userdata('permiss'));
		$permiss = $this->permiss->get_Permiss($this->session->userdata('useradmin'));
		$data['permiss'] = $permiss['permiss_name'];
		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		 
		$this->load->view('pathadmin', $data);
	}
	public function logout()
	{
		session_destroy();
		if($this->session->userdata('useradminid')!='') {
			$qryLast = $this->mdl_sql->get_WhereParaQrySort('log','user_id',$this->session->userdata('useradminid'),'id','desc');
			$data = array(
						   'date_logout' => date('Y-m-d H:i:s')
						);
				$this->db->where('id', $qryLast->ID);
				$this->db->update('log', $data); 
			// ============== Log_Detail ============== //
			$log_query = $this->db->last_query();
			$last_id = $this->session->userdata('log_id');
			$detail = "Logout Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
			$type = "Logout";
  			$arraylog = array(
					'log_id'  	 	 => $last_id,
					'detail'  		 => $detail,
					'logquery'       => $log_query,
					'type'     	 	 => $type,
					'date_starts'    => date('Y-m-d H:i:s')
			);
			updateLog($arraylog);
			// ============== unset_userdata ============== //
			$array_items = array('useradminid','useradminname','permiss');
			$this->session->unset_userdata($array_items);
			
			$data['base_bn'] = base_url().BASE_BN;
			$data['basepic'] = base_url().BASE_PIC;
			$this->load->view('pathadmin', $data);
		}else{
			$data['base_bn'] = base_url().BASE_BN;
			$data['basepic'] = base_url().BASE_PIC;
			$this->load->view('pathadmin', $data);
		}
	}
}
