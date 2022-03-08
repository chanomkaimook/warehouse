<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Promotion {
	////////////////////////////
	////	setting
	public function __construct()
    {
		//
		//	setting
		$this->commingday = "3 day";
		//
		//
		//$this->comm = $table;
	}
	////////////////////////////
	////////////////////////////
	function get_PromotionCountuserpick($promotionid,$userid){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$sqlcount = $ci->db->select('*')
		->from('crm_point')
		->join('crm_pointdetail','crm_point.id=crm_pointdetail.crm_point_id','left')
		->where('crm_point.crm_method_code','02')
		->where('crm_point.crm_status_code','01')
		->where('crm_point.customer_id',$userid)
		->where('crm_point.status',1)
		->where('crm_pointdetail.status',1)
		->where('crm_pointdetail.crm_promotion_id',$promotionid);
		$totalcount = $ci->db->count_all_results(null,false);
		$rcount = $sqlcount->get();
		
		return $totalcount;
	}
	function get_PromotionCountpick($promotionid){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$sqlcount = $ci->db->select('*')
		->from('crm_point')
		->join('crm_pointdetail','crm_point.id=crm_pointdetail.crm_point_id','left')
		->where('crm_point.crm_method_code','02')
		->where('crm_point.crm_status_code','01')
		->where('crm_point.status',1)
		->where('crm_pointdetail.status',1)
		->where('crm_pointdetail.crm_promotion_id',$promotionid);
		$totalcount = $ci->db->count_all_results(null,false);
		$rcount = $sqlcount->get();
		
		return $totalcount;
	}
	function get_PromotionShow(){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$sql = $ci->db->select('*')
        ->from('crm_promotion')
		->where('
				( CASE WHEN date(pro_end) is not null and date(pro_start) is not null THEN date(pro_start) <= curdate() and date(pro_end) >= curdate()
				WHEN date(pro_end) is null and date(pro_start) is not null THEN date(pro_start) <= curdate()
				WHEN date(pro_end) is not null and date(pro_start) is null THEN date(pro_end) >= curdate()
				ELSE null
				END)
			')
		->where('status',1)
		->where('viewstatus',1);

		$total = $ci->db->count_all_results(null,false);
		$r = $sql->get();
		$array = array();
		if($total){
			foreach($r->result() as $row){
				$countpick = $row->COUNTPICK;
				if($countpick){
					//
					//	check promotion countpick
					$totalcount = $this->get_PromotionCountpick($row->ID);
					if($totalcount < $countpick){
						array_push($array,$row);
					}
				}else{
					array_push($array,$row);
				}
			}
		}
		
		$result = array(
						'count'	=> count($array),
						'query'	=> $array
					);
		
		return $result;
	}
	function get_PromotionOnline(){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$sql = $ci->db->select('*')
        ->from('crm_promotion')
		->where('
				( CASE WHEN date(pro_end) is not null and date(pro_start) is not null THEN date(pro_start) <= curdate() and date(pro_end) >= curdate()
				WHEN date(pro_end) is null and date(pro_start) is not null THEN date(pro_start) <= curdate()
				WHEN date(pro_end) is not null and date(pro_start) is null THEN date(pro_end) >= curdate()
				ELSE null
				END)
			')
		->where('status',1)
		->where('viewstatus',1);

		$total = $ci->db->count_all_results(null,false);
		$r = $sql->get();
		if($total){
			$r = $r;
		}else{
			$r = "";
		}
		$result = array(
						'count'	=> $total,
						'query'	=> $r
					);
		
		return $result;
	}
	function get_PromotionComming(){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$sql = $ci->db->select('*')
        ->from('crm_promotion')
		->where('
				( CASE 
				WHEN date(pro_start) is not null THEN date(pro_start) > curdate() and date(pro_start) <= date_add(curdate(),interval '.$this->commingday.')
				WHEN date(pro_end) is not null and date(pro_start) is null THEN date(pro_end) > curdate() and date(pro_end) <= date_add(curdate(),interval '.$this->commingday.')
				ELSE null
				END)
			')

		->where('status',1)
		->where('viewstatus',1);

		$total = $ci->db->count_all_results(null,false);
		$r = $sql->get();
		if($total){
			$r = $r;
		}else{
			$result = "";
		}
		$result = array(
						'count'	=> $total,
						'query'	=> $r
					);
		
		return $result;
	}
}

?>