<?php
ini_set('max_execution_time',0);
ini_set('memory_limit',"100M");

defined('BASEPATH') OR exit('No direct script access allowed');

class Ctl_dashboard extends CI_Controller {

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
        $this->load->model(array('mdl_dashboard'));
		$this->load->library('session');
		$this->load->library(array('Permiss'));
        $this->load->helper(array('form', 'url','myfunction_helper','sql_helper','permiss_helper','array_helper'));
        
        $this->set	= array (
            'ctl_name'				=> 'ctl_report',
            'username_session'		=> $this->session->userdata('useradminname'),
            'userid_session'		=> $this->session->userdata('useradminid')
        );
        if($this->session->userdata('useradminid') == ''){
        	redirect('mod_admin/ctl_login');
        }
    }
    
	public function dashboard() {
		// if(chkPermiss() == 1){
			// redirect('mod_admin/ctl_login');
		// }
		
		$data = array (
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'dashboard'
		);
		 
 		$data['base_bn'] = base_url().BASE_BN;
		$data['basepic'] = base_url().BASE_PIC;
		$this->load->view('dashboard', $data);
	}
	
}
