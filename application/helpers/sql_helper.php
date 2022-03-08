<?php 
error_reporting(E_ALL & ~E_NOTICE);
///		====================================================================================================================	///
///		=================================			    		SQL							================================	///
///		====================================================================================================================	///
	function get_WherePara($table,$where,$para1) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where."",$para1);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		
		return $r;
	}
	function get_WhereParaSelect($select,$table,$where,$para1) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select($select);
		$ci->db->from("".$table."");
		$ci->db->where("".$where."",$para1);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		
		return $r;
	}
	function get_WhereParaQry($table,$where,$para1) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where."",$para1);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		
		return $r;
	}
	function get_WhereParaQrySelect($select,$table,$where,$para1) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select($select);
		$ci->db->from("".$table."");
		$ci->db->where("".$where."",$para1);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		
		return $r;
	}
	function get_WhereParaNum($table,$where,$para1) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where."",$para1);
		$q = $ci->db->get();
		$num = $q->num_rows();
		
		return $num;
	}
	function get_WhereParaQryOrderby($table,$where,$para1,$culumn,$order) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where."",$para1);
		$ci->db->order_by("".$culumn."",$order);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		
		return $r;
	}
	function get_Where2ParaSelect($select,$table,$where1,$para1,$where2,$para2) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select($select);
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where2Para($table,$where1,$para1,$where2,$para2) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where2ParaNum($table,$where1,$para1,$where2,$para2) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$q = $ci->db->get();
		$num = $q->num_rows();

		return $num;
	}
	function get_CountTableNumWhere2Para($table,$where1,$para1,$where2,$para2) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$q = $ci->db->get();
		$num = $q->num_rows($q);
		return $num;
	}
	function get_Where2ParaQrySelect($select,$table,$where1,$para1,$where2,$para2) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select($select);
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where2ParaQryOrderby($table,$where1,$para1,$where2,$para2,$column,$order) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->order_by("".$column."",$order);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where2ParaQryGroupby($table,$where1,$para1,$where2,$para2,$groupby) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->group_by($groupby);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where2ParaGroupby($table,$where1,$para1,$where2,$para2,$groupby) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->group_by($groupby);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where3Para($table,$where1,$para1,$where2,$para2,$where3,$para3) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where3ParaSelect($select,$table,$where1,$para1,$where2,$para2,$where3,$para3) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select($select);
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where3ParaQrySelect($select,$table,$where1,$para1,$where2,$para2,$where3,$para3) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select($select);
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where3ParaQryOrderby($table,$where1,$para1,$where2,$para2,$where3,$para3,$column,$order) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$ci->db->order_by("".$column."",$order);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Count3Para($table,$where1,$para1,$where2,$para2,$where3,$para3){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->from($table);
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$num = $ci->db->count_all_results();
		return $num;
	}
	function get_Where3ParaNum($table,$where1,$para1,$where2,$para2,$where3,$para3) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$q = $ci->db->get();
		$num = $q->num_rows();

		return $num;
	}
	function get_Where4ParaQryOrderby($table,$where1,$para1,$where2,$para2,$where3,$para3,$where4,$para4,$column,$order) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$ci->db->where("".$where4."",$para4);
		$ci->db->order_by("".$column."",$order);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q;
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where4Para($table,$where1,$para1,$where2,$para2,$where3,$para3,$where4,$para4) {	
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$ci->db->where("".$where4."",$para4);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
		}else{
			$r = "";
		}
		return $r;
	}
	function get_Where4ParaNum($table,$where1,$para1,$where2,$para2,$where3,$para3,$where4,$para4) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('*');
		$ci->db->from("".$table."");
		$ci->db->where("".$where1."",$para1);
		$ci->db->where("".$where2."",$para2);
		$ci->db->where("".$where3."",$para3);
		$ci->db->where("".$where4."",$para4);
		$q = $ci->db->get();
		$num = $q->num_rows();

		return $num;
	}
	function get_CountTable($table,$arraywhere) {
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->from($table);
		if($arraywhere){
			foreach($arraywhere as $key){
				$ci->db->where($key);
			}	
		}
		$total_result = $ci->db->count_all_results();
		$num = $total_result;

		return $num;
	}
?>