<?php
ini_set('max_execution_time',0);
ini_set('memory_limit',"100M");

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_dashboard extends CI_Model {
	public function __construct()
    {

	}
	
	function countresult($usercode){
		$ci =& get_instance();
        $ci->load->database();
		
		$result = "";
		
		$sqlcount = $ci->db->select(
			'status_complete'
		)
		->from('retail_bill')
		->where('date(date_starts)',date('Y-m-d'))
		->where('user_starts',$usercode);
		$total = $sqlcount->count_all_results(null,false);
		$querycount = $sqlcount->get();
		
		$balance = 0;
		$total_approve = 0;
		$total_cancel = 0;
		
		if($total > 0){
			foreach($querycount->result() as $row){
				
				
				if($row->status_complete == 2){
					$total_approve++;
				}else if($row->status_complete == 3){
					$total_cancel++;
				}else{
					$balance++;
				}
			}
		}
		
		$result = array(
				'total'			=> $total,
				'approve'		=> $total_approve,
				'cancel'		=> $total_cancel,
				'balance'		=> $balance
		);
		
		return $result;
	}
	
	function count_allbill_result(){
		$ci =& get_instance();
        $ci->load->database();
		
		$result = "";
		
		$sqlcount = $ci->db->select(
			'status_complete'
		)
		->from('retail_bill')
		->where('date(date_starts)',date('Y-m-d'));
		$total = $sqlcount->count_all_results(null,false);
		$querycount = $sqlcount->get();
		
		$balance = 0;
		$total_approve = 0;
		$total_cancel = 0;
		
		if($total > 0){
			foreach($querycount->result() as $row){
				
				
				if($row->status_complete == 2){
					$total_approve++;
				}else if($row->status_complete == 3){
					$total_cancel++;
				}else{
					$balance++;
				}
			}
		}
		
		$result = array(
				'total'			=> $total,
				'approve'		=> $total_approve,
				'cancel'		=> $total_cancel,
				'balance'		=> $balance
		);
		
		return $result;
	}


}
?>