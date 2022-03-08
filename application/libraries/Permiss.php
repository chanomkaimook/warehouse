<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Permiss {
	////////////////////////////
	////	setting
	
	////////////////////////////
	////////////////////////////
	
	function gen_CodePermiss($page){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('id,name,permiss_type_id,owner');
		$ci->db->from("permiss_page");
		$ci->db->where("name",$page);
		$ci->db->where("status",1);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
			$id = $r->id;
			$type = $r->permiss_type_id;
			($r->owner != '' ? $owner = $r->owner : $owner = 0);

			###
			$code_own = "";
			$code_type = "";
			$code_id = "";
			$code_text = "";
			
			$coderef = ""; 
			###
			if($page){
				$code_text = substr($page,0,1);
			}
			if($id){
				$code_id = $id;
			}
			if($type){
				$code_type = $type;
			}
			if($owner){
				$code_own = $owner;
			}
			###
			$coderef .= $code_text;
			$coderef .= $code_id;
			$coderef .= "-".$code_type; 
			$coderef .= "-".$owner; 
			
			$result = $coderef;
		}else{
			$result = "";
		}
		
		return $result;
	}
	function convert_CodePermiss($setcode){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$page = "";
		$arrayset = explode(',',$setcode);
		// echo "<pre>";print_r($arrayset);echo "</pre>";
		foreach($arrayset as $code){
			$codeset = explode('-',$code);			///	array codeset will have 3 slot
			
			$text = substr($codeset[0],0,1);
			$id = substr($codeset[0],1);
			
			($codeset[2] != 0 ? $owner = $codeset[2] : $owner = null);
			$ci->db->select('name');
			$ci->db->from("permiss_page");
			$ci->db->where("id",$id);
			$ci->db->where("name like '".$text."%'");
			$ci->db->where("permiss_type_id",$codeset[1]);
			$ci->db->where("owner",$owner);
			$ci->db->where("status",1);
			$qry_pm_p = $ci->db->get();

			$num_pm_p = $qry_pm_p->num_rows();
			
			if($num_pm_p > 0){
				$row_pm_p = $qry_pm_p->row();
				$page .= $row_pm_p->name;
			}
			if(end($arrayset) != $code){
			// if(end($arrayset)){
				$page .= ",";
			}
		}

		$result = $page;
		
		return $result;
	}
	function convert_CodePermissTH($setcode){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$page = "";
		$arrayset = explode(',',$setcode);
		// echo "<pre>";print_r($arrayset);echo "</pre>";
		foreach($arrayset as $code){
			$codeset = explode('-',$code);			///	array codeset will have 3 slot
			
			$text = substr($codeset[0],0,1);
			$id = substr($codeset[0],1);
			($codeset[2] != 0 ? $owner = $codeset[2] : $owner = null);
			$ci->db->select('name,name_th');
			$ci->db->from("permiss_page");
			$ci->db->where("id",$id);
			$ci->db->where("name like '".$text."%'");
			$ci->db->where("permiss_type_id",$codeset[1]);
			$ci->db->where("owner",$owner);
			$ci->db->where("status",1);
			$qry_pm_p = $ci->db->get();

			$num_pm_p = $qry_pm_p->num_rows();
			if($num_pm_p > 0){
				$row_pm_p = $qry_pm_p->row();
				if($row_pm_p->name_th){
					$page .= $row_pm_p->name_th;
				}else{
					$page .= $row_pm_p->name;
				}
				
			}
			if(end($arrayset) != $code){
			// if(end($arrayset)){
				$page .= ",";
			}
		}

		$result = $page;
		
		return $result;
	}
	function get_Permiss($staffidcode){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$result = "";
		
		$ci->db->select('PERMISS_SET_ID,PERMISS_PAGE_CODE');
		$ci->db->from("permiss_control");
		$ci->db->where("staff_id",$staffidcode);
		$ci->db->where("status",1);
		$ci->db->where("permiss_set_id !=''");
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$r = $q->row();
			$ci->db->select('PERMISS_NAME,PERMISS_PAGE');
			$ci->db->from("permiss_set");
			$ci->db->where("id",$r->PERMISS_SET_ID);
			$qry_pm_s = $ci->db->get();
			$num_pm_s = $qry_pm_s->num_rows();
			if($num_pm_s > 0){
				$row_pm_s = $qry_pm_s->row();
				$result = array(
							"permiss_name"		=> $row_pm_s->PERMISS_NAME,
							"permiss_page"		=> $row_pm_s->PERMISS_PAGE
							);
			}
		}else{
			$result = array(
						"permiss_name"		=> "",
						"permiss_page"		=> ""
						);
		}
		
		return $result;
	}
	function get_PagePermiss($staffidcode){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
		//===================//
		$ci->db->select('PERMISS_SET_ID,PERMISS_PAGE_CODE');
		$ci->db->from("permiss_control");
		$ci->db->where("staff_id",$staffidcode);
		$ci->db->where("status",1);
		$q = $ci->db->get();
		$num = $q->num_rows();
		if($num > 0){
			$page = array();
			foreach($q->result() as $r){
				////	start if permiss_set
				$permiss_set = $r->PERMISS_SET_ID;
				if($permiss_set != ""){
					$ci->db->select('PERMISS_PAGE');
					$ci->db->from("permiss_set");
					$ci->db->where("id",$permiss_set);
					$ci->db->where("permiss_page !=","all");
					$ci->db->where("status",1);
					$qry_pm_s = $ci->db->get();
					$num_pm_s = $qry_pm_s->num_rows();
					if($num_pm_s > 0){
						$row_pm_s = $qry_pm_s->row();
						$pagename = $ci->permiss->convert_CodePermiss($row_pm_s->PERMISS_PAGE);
						// echo "main=======".$row_pm_s->PERMISS_PAGE." = ".$pagename;
						array_push($page,$pagename);
					}else{
						$pagename = "all";
						array_push($page,$pagename);
					}
					////	end if 
				}
				////	end if  permiss_set
				
				////	start if permiss_page
				$permiss_page = $r->PERMISS_PAGE_CODE;
				if($permiss_page != ""){
					$pagename = $ci->permiss->convert_CodePermiss($r->PERMISS_PAGE_CODE);
					array_push($page,$pagename);
				}
				////	end if  permiss_page
			}
			$result_page = implode(',',$page);
			
			$result = $result_page;

		}else{
			$result = "";
		}

		return $result;
	}
}

?>