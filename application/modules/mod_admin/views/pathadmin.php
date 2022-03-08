<?php
// echo $this->session->userdata('useradminid')."---".$permiss;die();
	if($this->session->userdata('useradminid') != ''){
		$userpermiss = $permiss;
		/* if($userpermiss == 'master_admin'){
			redirect('mod_admin/ctl_login/backend_main');
		}else if($userpermiss == 'admin'){
 			redirect('mod_admin/ctl_login/backend_main');
		}else{
			$url = site_url('mod_admin/ctl_login');
			header('Location:'.$url);
			exit(0); 
		} */
		redirect('mod_retaildashboard/ctl_dashboard/dashboard');
	}else{
		$url = site_url('mod_admin/ctl_login');
		header('Location:'.$url);
		exit(0);
	}
?>