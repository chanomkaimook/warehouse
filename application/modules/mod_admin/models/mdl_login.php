<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_login extends CI_Model {
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
	function ajaxCheckLogin() {
		$user = $this->input->post('id'); 
		$password = md5($this->input->post('passw'));
		$number = $this->mdl_sql->get_CountTableNumWhere3('staff','username',$user,'password',$password,'status',1);
		// echo $user." --- ".$this->input->post('passw')." number :".$number;
		
		if($number > 0){
			$qry = get_Where3Para('staff','username',$user,'password',$password,'status',1);
			 $sensitive = strnatcmp($user,$qry->USERNAME);
			if($sensitive == 0){
				$qry = $this->mdl_sql->get_Where3Para('staff','username',$user,'password',$password,'status',1);
					$qrypermiss = $this->mdl_sql->get_Where2Para('permiss_control','staff_id',$qry->ID,'status',1);
							$data = array(
								'user_id' 		=> $qry->CODE ,
								'user_ip' 		=> $this->input->ip_address() ,
								'status_user'   => $qry->PERMISS,
								'date_starts' 	=> date('Y-m-d H:i:s')
							);
							$this->db->insert('log', $data);
							$log_query = $this->db->last_query();
							$last_id = $this->db->insert_id();
							$detail = "Login admin Code : ".$qry->CODE." Name : ".$qry->NAME." ".$qry->LASTNAME;
							$type = "Login";
							$arraylog = array(
								'log_id'  	 	 => $last_id,
								'detail'  		 => $detail,
								'logquery'       => $log_query,
								'type'     	 	 => $type,
								'date_starts'    => date('Y-m-d H:i:s')
							);
							updateLog($arraylog);
							$newdata = array(
								'useradmin'  	 => $qry->ID,
								'useradminid'  	 => $qry->CODE,
								'useradminname'  => $qry->NAME." ".$qry->LASTNAME,
								'permiss'   	 => $qrypermiss->PERMISS_SET_ID,
								'franshine'   	 => $qry->FRANSHINE_ID,
								'log_id'     	 => $last_id
								);
							$this->session->set_userdata($newdata);
					$code = '0';
					$txt = '';
			}else{
				$code = 1;
				$txt = 'user or password incorrect';
			}
		}else{
			$code = 2;
			$txt = 'user or password incorrect';
		}

		$data = array(
				'error_code' 		=> $code ,
				'txt' 				=> $txt
		);
		// print_r($newdata);
		// echo $this->session->userdata('useradmin')." ------";die;
		$data = json_encode($data);
	 
		return $data;
	}
}
?>