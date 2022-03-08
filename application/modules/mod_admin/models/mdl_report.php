<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_report extends CI_Model {
	 
	function report1(){
		$this->db->select('SUM(crm_point.POINT) as sumpoint');
		$this->db->from('crm_point');
		$this->db->where('crm_point.status', 1);
		$this->db->where('crm_point.CRM_METHOD_CODE', '02');
		$this->db->where('crm_point.CRM_STATUS_CODE', "01");
		$this->db->where('(crm_point.DATE_STARTS like "%'.date('Y-m-d').'%")');  
 		$Query = $this->db->get();
		$result = $Query->row();

		$data = $result;
 		return $data;
	}

	function report2(){
		$this->db->select('COUNT(crm_customer.id) as countcus');
		$this->db->from('crm_customer');
		$this->db->where('crm_customer.status', 1);
		$this->db->where('crm_customer.DATE_STARTS BETWEEN "'.date('Y-m-d'). ' 00:00:00" and "'.date('Y-m-d').'  23:59:59"');  
		 
 		$Query = $this->db->get();
		$result = $Query->row();

		$data = $result;
 		return $data;
	}

	function report4(){
		$this->db->select('SUM(crm_point.id) as sumpoint');
		$this->db->from('crm_point');
		$this->db->where('crm_point.status', 1);
		$this->db->where('crm_point.CRM_STATUS_CODE', "01");
		$this->db->where('crm_point.CRM_METHOD_CODE', '02');
  		$Query = $this->db->get();
		$result = $Query->row();

		$data = $result;
 		return $data;
	}

	function report6(){
		$this->db->select('crm_promotion.PRO_START as PRO_START, crm_promotion.PRO_END as PRO_END');
		$this->db->from('crm_promotion');
		// $this->db->where('crm_promotion.status', 1);
		// echo $this->db->get_compiled_select();
   		$Query = $this->db->get();
		 
		   $items = [];
		   foreach($Query->result() As $key => $row){
			   	 
					$date1 = $row->PRO_START;
					$date2 = date('Y-m-d H:i:s');
					$datetime1 = new DateTime($date1);
					$datetime2 = new DateTime($date2);
					$interval = $datetime1->diff($datetime2);
					
						// echo   date('Y-m-d')." -- ".date('Y-m-d',strtotime($row->PRO_START)).'<br>';
						if(date('Y-m-d') <= date('Y-m-d',strtotime($row->PRO_START))){
							$items[$key]['PRO_START'] = $row->PRO_START;
							$items[$key]['date_D14'] = $diff_result = $interval->format('%d');
							$items[$key]['date_M14'] = $diff_result = $interval->format('%m');
							$items[$key]['date_Y14'] = $diff_result = $interval->format('%y');
							$items[$key]['PRO_END'] = "NO";
						}
			 
						if(date('Y-m-d') >= date('Y-m-d',strtotime($row->PRO_END))){
							$items[$key]['PRO_END'] = date('Y-m-d',strtotime($row->PRO_END));
						}
		   }
		// echo '<pre>'; print_r($items); exit;
		$data = $items;
 		return $data;
	}

	function report10(){
		$this->db->select('SUM(crm_point.POINT) as sumpoint');
		$this->db->from('crm_point');
		$this->db->where('crm_point.status', 1);
		$this->db->where('crm_point.CRM_METHOD_CODE', '01');
		$this->db->where('crm_point.CRM_STATUS_CODE', "01");
		$this->db->where('crm_point.DATE_STARTS BETWEEN "'.date('Y-m-d'). ' 00:00:00" and "'.date('Y-m-d').'  23:59:59"');  
		 
 		$Query = $this->db->get();
		$result = $Query->row();

		$data = $result;
 		return $data;
	}

	function report11(){
		$this->db->select('SUM(crm_point.POINT) as sumpoint');
		$this->db->from('crm_point');
		$this->db->where('crm_point.status', 1);
		$this->db->where('crm_point.CRM_STATUS_CODE', "01");
		$this->db->where('crm_point.CRM_METHOD_CODE', '01');
  		$Query = $this->db->get();
		$result = $Query->row();

		$data = $result;
 		return $data;
	}

	function report12(){
		$this->db->select('SUM(crm_point.POINT) as sumpoint');
		$this->db->from('crm_point');
		$this->db->where('crm_point.status', 1);
		$this->db->where('crm_point.CRM_METHOD_CODE', '03');
		$this->db->where('crm_point.CRM_STATUS_CODE', "01");
 		$this->db->where('crm_point.DATE_STARTS BETWEEN "'.date('Y-m-d'). ' 00:00:00" and "'.date('Y-m-d').'  23:59:59"');  
 		$Query = $this->db->get();
		$result = $Query->row();

		$data = $result;
 		return $data;
	}

	function report13(){
		$this->db->select('SUM(crm_point.POINT) as sumpoint');
		$this->db->from('crm_point');
		$this->db->where('crm_point.status', 1);
		$this->db->where('crm_point.CRM_STATUS_CODE', "01");
		$this->db->where('crm_point.CRM_METHOD_CODE', '03');
  		$Query = $this->db->get();
		$result = $Query->row();

		$data = $result;
 		return $data;
	}
  
	 //---------------------------- DATATABLE Report 8 ----------------------------//
	 var $order_column = array("CPid", "CPpoint", "CS_name");  
 	 function make_query() {  
		 
		$this->db->select('crm_point.ID as CPid, SUM(crm_point.POINT) as CPpoint, 
		crm_customer.id as CS_id, crm_customer.firstname as CS_name, crm_customer.name as name, crm_customer.picture as CS_picture');
		$this->db->from('crm_point');
		$this->db->join('crm_customer', "crm_point.CUSTOMER_ID = crm_customer.id", "right");
		$this->db->where('crm_customer.status', 1);
		$this->db->where('crm_point.CRM_STATUS_CODE', "01");
		$this->db->where('crm_point.CRM_METHOD_CODE', '02');
		  
		if(!empty($_POST["valdate"]) && !empty($_POST["valdateTo"])) {  
			$this->db->where('crm_point.DATE_STARTS BETWEEN "'.$_POST["valdate"]. ' 00:00:00" and "'.$_POST["valdateTo"].'  23:59:59"');  
		} else if(!empty($_POST["valdate"])) {  
			$this->db->where('(crm_point.DATE_STARTS like "%'.$_POST["valdate"].'%")');  
		} 

		if(!empty($_POST["search"]["value"])) {  
			$this->db->like("crm_customer.firstname", $_POST["search"]["value"]);  
			$this->db->or_like("crm_customer.name", $_POST["search"]["value"]);  
  		}  
		if(!empty($_POST["order"])) {  
			$this->db->order_by($this->order_column_9[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
		} else {  
			$this->db->order_by('SUM(crm_point.POINT)', 'DESC');
			// $this->db->order_by('crm_point.DATE_STARTS', 'DESC');
		}  
		$this->db->group_by('crm_customer.id');
 		 
	 }  
	 function make_datatables(){  
		 $this->make_query();  
		 if($_POST["length"] != -1) {  
			 $this->db->limit($_POST['length'], $_POST['start']);  
		 }  
		 
		 // echo $this->db->get_compiled_select();
		$query = $this->db->get();  
		$items = [];
		foreach($query->result() as $row){
			$items[$row->CS_id]['CS_id'] = $row->CS_id;
			$items[$row->CS_id]['CS_name'] = $row->CS_name;
			$items[$row->CS_id]['name'] = $row->name;
			$items[$row->CS_id]['CS_picture'] = $row->CS_picture;
			$items[$row->CS_id]['CPpoint'] = $row->CPpoint;
		}
		return $items;  
	 }  
	 function get_filtered_data(){  
		 $this->make_query();  
		 $query = $this->db->get();  
		 return $query->num_rows();  
	 }       
	 function get_all_data()  
	 {  
		 $this->db->select("*");  
		 $this->db->from('crm_customer');  
		 return $this->db->count_all_results();  
	 }   

	  //---------------------------- DATATABLE Report 9 ----------------------------//
	var $order_column_9 = array("CPid", "CPpoint", "CS_name");  
 	function make_query_9() {  
		 
		$this->db->select('crm_customer.id as CS_id, crm_customer.firstname as CS_name, crm_customer.name as name, crm_customer.POINT as CPpoint,
		 COUNT(crm_customer.id) as countcus, crm_point.DATE_STARTS as DATE_STARTS');
		$this->db->from('crm_point');
		$this->db->join('crm_customer', "crm_point.CUSTOMER_ID = crm_customer.id", "right");
		$this->db->where('crm_customer.status', 1);
		$this->db->where('crm_point.CRM_METHOD_CODE', "02");
		$this->db->where('crm_point.CRM_STATUS_CODE', "01");
		

		// if(!empty($_POST["valdate"])) {  
		// 	$dateTo = date('Y-m-d');
        // 	$valdate = date("Y-m-d",strtotime("-".$_POST["valdate"]." days",strtotime($dateTo)));  
		// 	$this->db->where('crm_point.DATE_STARTS BETWEEN "'.$valdate. ' 00:00:00" and "'.date('Y-m-d').'  23:59:59"');  
		// }  

		if(!empty($_POST["search"]["value"])) {  
			$this->db->like("crm_customer.firstname", $_POST["search"]["value"]);  
			$this->db->or_like("crm_customer.name", $_POST["search"]["value"]);  
			$this->db->or_like("COUNT(crm_customer.id)", $_POST["search"]["value"]);  
  		}  
		if(!empty($_POST["order"])) {  
			$this->db->order_by($this->order_column_9[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
		} else {  
			$this->db->order_by('crm_point.DATE_STARTS', 'DESC');
 		}  
		    
		$this->db->group_by('crm_customer.id');
		$this->db->group_by('crm_customer.firstname');
		$this->db->group_by('crm_customer.name');
		$this->db->group_by('crm_customer.POINT');
		 
	}  
	function make_datatables_9(){  
		 $this->make_query_9();  
		 if($_POST["length"] != -1) {  
			 $this->db->limit($_POST['length'], $_POST['start']);  
		 }  
		 
		//  echo $this->db->get_compiled_select();
		$query = $this->db->get();  
		return $query->result();  
	}  
	function get_filtered_data_9(){  
		$this->make_query_9();  
		$query = $this->db->get();  
		return $query->num_rows();  
	}       
	function get_all_data_9()  
	{  
		$this->db->select("*");  
		$this->db->from('crm_customer');  
		return $this->db->count_all_results();  
	}   
	//---------------------------- DATATABLE Report Prorangking ----------------------------//
	var $order_column_Prorangking = array("crm_promotion.ID", "crm_promotion.NAME_TH", "SUM(crm_point.POINT)");  
	function make_query_Prorangking() {  
		  
		 $this->db->select('crm_promotion.ID as Proid, crm_promotion.NAME_TH as Proname, crm_point.ID as CID, SUM(crm_point.POINT) as Cpoint, 
		 COUNT(crm_promotion.ID) as count');
		 $this->db->from('crm_point');
		 $this->db->join('crm_pointdetail', "crm_point.ID = crm_pointdetail.CRM_POINT_ID", "left");
		 $this->db->join('crm_promotion', "crm_pointdetail.CRM_PROMOTION_ID = crm_promotion.id", "left");
		 $this->db->where('crm_point.status', 1);
		 $this->db->where('crm_pointdetail.CRM_PROMOTION_ID != ""');
		 $this->db->where('crm_point.CRM_STATUS_CODE', "01");
		if(!empty($_POST["valdate"]) && !empty($_POST["valdateTo"])) {  
		 	$this->db->where('crm_point.DATE_STARTS BETWEEN "'.$_POST["valdate"]. ' 00:00:00" and "'.$_POST["valdateTo"].'  23:59:59"');  
		} else if(!empty($_POST["valdate"])) {  
			$this->db->where('(crm_point.DATE_STARTS like "%'.$_POST["valdate"].'%")');  
		} else {
			$this->db->where('crm_point.DATE_STARTS BETWEEN "'.date('Y-m').'-01 00:00:00" and "'.date('Y-m').'-30  23:59:59"');
		}
 
		if(!empty($_POST["search"]["value"])) {  
			$this->db->like("crm_promotion.NAME_TH", $_POST["search"]["value"]); 
			$this->db->or_like("crm_promotion.NAME_US", $_POST["search"]["value"]);  
		}  
		if(!empty($_POST["order"])) {  
		 	$this->db->order_by($this->order_column_Prorangking[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
		} else {  
			$this->db->order_by('crm_point.POINT', 'DESC');
		}  
		$this->db->group_by('crm_promotion.ID');
		$this->db->group_by('crm_promotion.NAME_TH');
		 
 	 }  
	 function make_datatables_Prorangking(){  
		  $this->make_query_Prorangking();  
		  if($_POST["length"] != -1) {  
			  $this->db->limit($_POST['length'], $_POST['start']);  
		  }  
		  
		//   echo $this->db->get_compiled_select();
		 $query = $this->db->get();  
		
		 return $query->result();  
	 }  
	 function get_filtered_data_Prorangking(){  
		 $this->make_query();  
		 $query = $this->db->get();  
		 return $query->num_rows();  
	 }       
	 function get_all_data_Prorangking()  
	 {  
		 $this->db->select("*");  
		 $this->db->from('crm_promotion');  
		 return $this->db->count_all_results();  
	 }  
	// ================================================ //
	function ajaxtextmonth(){
		$valdate = thai_date_month($this->input->post('valdate')); 
		$valdateTo = thai_date_month($this->input->post('valdateTo'));

		$data = array(
			"valdate" => $valdate,
			"valdateTo" => $valdateTo
		);

		$data = json_encode($data);
 		return $data;
	}
	
	function report_pro($valdate, $valdateTo){
		$this->db->select('crm_promotion.ID as Proid, crm_promotion.NAME_TH as Proname, crm_point.ID as CID, SUM(crm_point.POINT) as Cpoint, 
		 COUNT(crm_promotion.ID) as count');
		 $this->db->from('crm_point');
		 $this->db->join('crm_pointdetail', "crm_point.ID = crm_pointdetail.CRM_POINT_ID", "left");
		 $this->db->join('crm_promotion', "crm_pointdetail.CRM_PROMOTION_ID = crm_promotion.id", "left");
		 $this->db->where('crm_point.status', 1);
		 $this->db->where('crm_pointdetail.CRM_PROMOTION_ID != ""');
		 $this->db->where('crm_point.CRM_STATUS_CODE', "01");
		if($valdate != ''  && $valdateTo != '') {  
		 	$this->db->where('crm_point.DATE_STARTS BETWEEN "'.$valdate. ' 00:00:00" and "'.$valdateTo.'  23:59:59"');  
		} else if($valdate != '') {  
			$this->db->where('(crm_point.DATE_STARTS like "%'.$valdate.'%")');  
		} else {
			$this->db->where('crm_point.DATE_STARTS BETWEEN "'.date('Y-m').'-01 00:00:00" and "'.date('Y-m').'-30  23:59:59"');
		}
		$this->db->order_by('crm_point.POINT', 'DESC');
		$this->db->group_by('crm_promotion.ID');
		$this->db->group_by('crm_promotion.NAME_TH');
	 
		$query = $this->db->get();
 		$data = $query->result();
 		return $data;
	}
}
?>