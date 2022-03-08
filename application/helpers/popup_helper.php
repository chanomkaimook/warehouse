<?php 
error_reporting(E_ALL & ~E_NOTICE);
	//
	//	popup helper
	function get_PopupData($franshine) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("popup");
		$ci->db->where("franshine",$franshine);
		$ci->db->where("status",1);
		$ci->db->where("date_starts >= now()");
		$ci->db->order_by("date_starts","desc");
		echo $ci->db->get_compiled_select(null,FALSE);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		
		return $r;
	}
?>