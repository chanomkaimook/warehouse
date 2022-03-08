<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sql extends CI_Model {
	function thai_date($time){
		// ถ้ามีการเก็บวันที่และเวลาในรูปแบบ timestamp
		// สามารถนำมาใช้งานในฟังก์ชันได้ดังนี้
		// $eng=1224562026; กรณีเป็น timestamp
		//$eng_date=time(); // แสดงวันที่ปัจจุบัน
		//echo thai_date($eng_date);
		
		// ถ้ามีการเก็บวันที่และเวลาในรูปแบบ date
		// คือ 2008-10-31 ต้องเปลี่ยนเป็น timestamp
		// ด้วยคำส่ง strtotime("2008-10-31");
		// แสดงวันที่ 31 เดือน ตุลาคม ปี 2008 แบบภาษาไทย
		//$eng_date=strtotime("2008-10-31"); 
		//echo thai_date($eng_date);
		
		$this->thai_day_arr=array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
		$this->thai_month_arr=array(
			"00"=>"",
			"01"=>"มกราคม",
			"02"=>"กุมภาพันธ์",
			"03"=>"มีนาคม",
			"04"=>"เมษายน",
			"05"=>"พฤษภาคม",
			"06"=>"มิถุนายน", 
			"07"=>"กรกฎาคม",
			"08"=>"สิงหาคม",
			"09"=>"กันยายน",
			"10"=>"ตุลาคม",
			"11"=>"พฤศจิกายน",
			"12"=>"ธันวาคม"                 
		);
		//global $thai_day_arr,$thai_month_arr;
		$time_day = date("w",$time);
		$time_month = date("m",$time);
		$thai_date_return="วัน".$this->thai_day_arr[$time_day];
		$thai_date_return.= "ที่ ".date("j",$time);
		$thai_date_return.=" เดือน".$this->thai_month_arr[$time_month];
		$thai_date_return.= " พ.ศ.".(date("Y",$time)+543);
		//$thai_date_return.= "  ".date("H:i",$time)." น.";
		return $thai_date_return;
	}
	
	function chkPermiss() {
		$permisspage = $this->mdl_sql->get_WherePara('permiss','id',$this->session->userdata('permiss'));
		$page = $this->uri->segment(3);		// Call ctl_/....
		$pagepermiss = explode(",",$permisspage->PERMISS_PAGE);
		$numpage = count($pagepermiss);
		$result = 1;
		for($i=0;$i<$numpage;$i++){
			if($pagepermiss[$i]==$page){
				$result = 0;
			}
		}
		if($permisspage->PERMISS_PAGE == 'all'){
			$result = 0;
		}
		return $result;
	}
	function get_CustomerLimit($limit) {
		$this->db->select('*');
		$this->db->from('customer_mail');
		$this->db->where('transfered','');
		$this->db->where('status','1');
		$this->db->limit($limit);
		$q = $this->db->get();
		return $q;
	}
	function get_CountTableNum($table) {
		$q = $this->db->get($table);
		$num = $q->num_rows($q);
		return $num;
	}
	function get_CountTableNumWhere($table,$where,$para) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where."",$para);
		$q = $this->db->get();
		$num = $q->num_rows($q);
		return $num;
	}
	function get_CountTableNumWhere2($table,$where1,$para1,$where2,$para2) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$q = $this->db->get();
		$num = $q->num_rows($q);
		return $num;
	}
	function get_CountTableNumWhere3($table,$where1,$para1,$where2,$para2,$where3,$para3) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$this->db->where("".$where3."",$para3);
		$q = $this->db->get();
		$num = $q->num_rows($q);
		return $num;
	}
	function update_TableWhere($table,$column,$para,$id) {
		$data = array(
               $column		 	=> $para
            );
		$this->db->where('id', $id);
		$this->db->update($table, $data); 
	}
	function get_WherePara($table,$where,$para1) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where."",$para1);
		$q = $this->db->get();
		$r = $q->row();
		return $r;
	}
	function get_Where2Para($table,$where1,$para1,$where2,$para2) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$q = $this->db->get();
		$r = $q->row();
		return $r;
	}
	function get_Where2ParaQry($table,$where1,$para1,$where2,$para2) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$q = $this->db->get();
		return $q;
	}
	function get_WhereQry2ParaSort($table,$where1,$para1,$where2,$para2,$column,$sort) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$this->db->order_by($column,$sort);
		$q = $this->db->get();
		return $q;
	}
	function get_Where2ParaSort($table,$where1,$para1,$where2,$para2,$column,$sort) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$this->db->order_by($column,$sort);
		$q = $this->db->get();
		$r = $q->row();
		return $r;
	}
	function get_WhereQry2ParaSortGroupby($table,$where1,$para1,$where2,$para2,$column,$sort,$group) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$this->db->order_by($column,$sort);
		$this->db->group_by("".$group."");
		$q = $this->db->get();
		return $q;
	}
	function get_Where3Para($table,$where1,$para1,$where2,$para2,$where3,$para3) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$this->db->where("".$where3."",$para3);
		$q = $this->db->get();
		$r = $q->row();
		return $r;
	}
	function get_Where3ParaSort($table,$where1,$para1,$where2,$para2,$where3,$para3,$column,$sort) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$this->db->where("".$where3."",$para3);
		$this->db->order_by($column,$sort);
		$q = $this->db->get();
		$r = $q->row();
		return $r;
	}
	function get_WhereQry3ParaSort($table,$where1,$para1,$where2,$para2,$where3,$para3,$column,$sort) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$this->db->where("".$where3."",$para3);
		$this->db->order_by($column,$sort);
		$q = $this->db->get();
		return $q;
	}
	function get_WhereParaqry($table,$where,$para1) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where."",$para1);
		$q = $this->db->get();
		return $q;
	}
	function get_WhereParaSort($table,$where,$para1,$column,$sort) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where."",$para1);
		$this->db->order_by($column,$sort);
		$q = $this->db->get();
		return $q;
	}
	function get_WhereParaQrySort($table,$where,$para1,$column,$sort) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where."",$para1);
		$this->db->order_by($column,$sort);
		$q = $this->db->get();
		$r = $q->row();
		return $r;
	}
	function get_WhereTable($table) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$q = $this->db->get();
		return $q;
	}
	function get_WhereTableNum($table) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$q = $this->db->get();
		$num = $q->num_rows($q);
		return $num;
	}
	function get_WhereTableSort($table,$column,$sort) {
		$this->db->select('*');
		$this->db->from($table);
		$this->db->order_by($column,$sort);
		$q = $this->db->get();
		return $q;
	}
	function get_MAX($table,$where,$para1,$column) {
		$this->db->select('max('.$table.'.'.$column.') AS countmax');
		$this->db->from("".$table."");
		$this->db->where("".$where."",$para1);
		// $this->db->group_by($table.'.'.$column);
		$q = $this->db->get();
		$r = $q->row();
		return $r;
	}
	function get_WhereTableNum2($table,$where1,$para1,$where2,$para2) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$q = $this->db->get();
		$num = $q->num_rows($q);
		return $num;
	}
}
?>