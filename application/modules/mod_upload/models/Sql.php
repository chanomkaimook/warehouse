<?php 
class Sql extends CI_Model {
	function get_table($table) {
		$this->db->select('*');
		$this->db->from("".$table."");
		$q = $this->db->get();
		$r = $q->row();
		return $r;
	}
	function get_Where2ParaTrans($table,$where1,$para1,$where2,$para2,$lang) {	
		$this->db->select('*');
		$this->db->from("".$table."");
		$this->db->where("".$where1."",$para1);
		$this->db->where("".$where2."",$para2);
		$q = $this->db->get();
		$r = $q->row();
		$rs = $r->$lang;
		return $rs;
	}
	function menuActive($namepage) {
		$crt_page = $this->uri->segment(2);
		if($namepage == $crt_page){
			$r = "active";
		}else{
			$r = "";
		}
		if($namepage == "#home"){		//if page home
			$r = "active";
		}
		return $r;
	}
	function callPromotionPopup() {
		$txt = "";
		$promotion_id	= $this->input->post('id');
		$qry_promotion = get_WherePara('promotion','id',$promotion_id);
		$img = "<img src=\"".base_url('asset/images/front/promotion')."/".$qry_promotion->PIC."\" alt=\"steak\">";

		$txt .=		"<div class=\"img col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center\">";
		$txt .=		$img;
		$txt .=		"</div>";
		$txt .=		"<div class=\"col-lg-12 col-md-12 col-sm-12 col-xs-12\" style=\"margin-top:auto;margin-bottom:auto;padding-top:15px\">";
		$txt .=		"<p style='margin:0px;'><b>".get_valueLang($qry_promotion->NAME_TH,$qry_promotion->NAME_US)."</b></p>";
		$txt .=		"<p>".get_valueLang($qry_promotion->DETAIL_TH,$qry_promotion->DETAIL_US)."</p>
					<p style=\"text-align:center;color:#BF9B7A\">".get_PromotionDate($qry_promotion->ID)."</p>
					<p style=\"text-align:center;color:#fff\">".get_Franshine($qry_promotion->ID)."</p>
					";
		$txt .=		"</div>";
		
		$code = 0;
		$data = array(
				"error_code" 	=> $code ,
				"txt"			=> $txt
		);
		$data = json_encode($data);
		return $data;
	}
	function callPic() {
		$txt = "";
		$pathpic	= $this->input->post('path');
		$img = "<img src=\"".$pathpic."\" alt=\"berger\" class=\"center-block\">";
		
		$code = 0;
		$data = array(
				"error_code" 	=> $code ,
				"txt"			=> $img
		);
		$data = json_encode($data);
		return $data;
	}
} 	
?>