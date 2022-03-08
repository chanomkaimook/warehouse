<?php 
error_reporting(E_ALL & ~E_NOTICE);
	//
	//	status [0=off,1=on]
	//
	function report_statusOn($status) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		if($status == 1){
			$result = "on";
		}else{
			$result = "off";
		}
		
		return $result;
	}
	
	#
	#	auto code retail
	#	format YYYY-mm-dd
	#	new generate is "CH yy mm dd"
	#
	function gen_invretail($date) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$explode = explode("-",$date);
		
		#	yy
		$thaiyear = $explode[0]+543;
		$new_y = substr($thaiyear,2);
		
		#	mm
		$new_m = $explode[1];
		
		#	dd
		$new_d = $explode[2];
		
		$result = "CH".$new_y.$new_m.$new_d;
		
		return $result;
	}
?>