<?php 
error_reporting(E_ALL & ~E_NOTICE);
	//
	//	check permiss for btn page = controller name return 0 or 1
	//
	function chkPermissPage($page){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->load->library('permiss');
		
		$arrayset = array();
		$chkpermiss = 0;
		//	check permiss id
		$chkpage = $ci->permiss->get_PagePermiss($ci->session->userdata('useradmin'));
		
		//	check mod
		$chkmod = $ci->uri->segment(1);
		$qrymod = get_Where2Para('permiss_page','name',$page,'status',1);
		$mod = $qrymod->MODNAME;
		
		$prearrayset = explode(',',$chkpage);
		foreach($prearrayset as $key => $val){
			if($val != ""){
				array_push($arrayset,$val);
			}
		}

		foreach($arrayset as $code){
			if(($code == $page || $chkpage == 'all')){
				if($chkpage != 'all' && $chkmod == $mod){
					$chkpermiss = 1;
				}else{
					$chkpermiss = 1;
				}
			}
		}
		
		$result = $chkpermiss;
		
		return $result;
	}
	//
	//	check permiss for btn (use with controller) return 0 or 1
	//
	function chkPermiss() {		
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->load->library('permiss');
		
		$permisspage = $ci->permiss->get_PagePermiss($ci->session->userdata('useradmin'));

		$page = $ci->uri->segment(3);		// Call ctl_/....
		$pagepermiss = explode(",",$permisspage);
		$numpage = count($pagepermiss);
		$result = 1;

		for($i=0;$i<$numpage;$i++){
			if($pagepermiss[$i]==$page){
				$result = 0;
			}
		}
		if($permisspage == 'all'){
			$result = 0;
		}
		return $result;
	}
	//
	//	check permiss admin enter value for use only
	//	value 	[
	//				- (permiss_name) 	= value on database table permiss_set (master_admin,admin,admin_web)
	//				- all				= all permiss for master_admin and admin only
	//			]
	//
	function chkAdmin() {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->load->library('permiss');
		
		$result = 1;
		$permiss = $ci->permiss->get_Permiss($ci->session->userdata('useradminid'));
		if($permiss['permiss_name'] == 'master_admin' || $permiss['permiss_name'] == 'admin'){
			$result = 0;
		}
		
		return $result;
	}
?>