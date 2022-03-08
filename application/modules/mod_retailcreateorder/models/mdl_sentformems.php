<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sentformems extends CI_Model {
   
    //---------------------------- CREATEORDER ----------------------------//
    var $order_column = array("CODE", "NAME", "TEXT_NUMBER", "NET_TOTAL", "DELIVERY_FORMID");  
    function make_query() {  
        
        $this->db->select('*');  
        $this->db->from('retail_bill'); 
        $this->db->where('(retail_bill.STATUS_COMPLETE != 3 AND retail_bill.STATUS_COMPLETE != 4 and retail_bill.STATUS = 1 )');
        if(!empty($_POST["statuscomplete"])){
            if($_POST["statuscomplete"] == 1){
				$this->db->where('(retail_bill.STATUS_COMPLETE = 0 OR retail_bill.STATUS_COMPLETE = 1)');
			} else {
				$this->db->where('retail_bill.STATUS_COMPLETE', $_POST["statuscomplete"]);
			}
        } else {
            if(!empty($_POST["deliveryid"]) || !empty($_POST["valdate"]) || !empty($_POST["valdateTo"]) || !empty($_POST["search"]["value"]) || !empty($_POST["order"])){
                // ------------------------ // 
            } else {
                $this->db->where('(retail_bill.STATUS_COMPLETE = 0 OR retail_bill.STATUS_COMPLETE = 1)');
            }
        }

        if(!empty($_POST["search"]["value"])) {  
           $this->db->like("retail_bill.CODE", $_POST["search"]["value"]);  
           $this->db->or_like("retail_bill.NAME", $_POST["search"]["value"]);  
           $this->db->or_like("retail_bill.TEXT_NUMBER", $_POST["search"]["value"]);  
           $this->db->or_like("retail_bill.PHONE_NUMBER", $_POST["search"]["value"]);  
        }  
        if(!empty($_POST["order"])) {  
             $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
        } else {  
             $this->db->order_by('retail_bill.ID', 'DESC');  
        }  
          
        if(!empty($_POST["valdate"]) && !empty($_POST["valdateTo"])) {  
            $this->db->where('retail_bill.DATE_STARTS BETWEEN "'.$_POST["valdate"]. ' 00:00:00" and "'.$_POST["valdateTo"].' 23:59:59"');  
        } else if(!empty($_POST["valdate"]) && empty($_POST["valdateTo"])) {  
            $this->db->where('retail_bill.DATE_STARTS BETWEEN "'.$_POST["valdate"]. ' 00:00:00" and "'.$_POST["valdate"].' 23:59:59"');  
        }else{
			$this->db->where('date(retail_bill.DATE_STARTS)',date("Y-m-d"));  
		} 

        if(!empty($_POST["deliveryid"])){
            $this->db->where('retail_bill.DELIVERY_FORMID', $_POST["deliveryid"]); 
        }
 
    }  
    function make_datatables(){  
        $this->make_query();  
        if($_POST["length"] != -1) {  
            $this->db->limit($_POST['length'], $_POST['start']);  
        }  
        
        // echo $this->db->get_compiled_select();
        $query = $this->db->get();  
        return $query->result();  
    }  
    function get_filtered_data(){  
        $this->make_query();  
        $query = $this->db->get();  
        return $query->num_rows();  
    }       
    function get_all_data()  
    {  
        $this->db->select("*");  
        $this->db->from('retail_bill');  
        return $this->db->count_all_results();  
    }   
    // ============================================================= //

    function dataprintems($val){
           
        if($val){
            $items = '';
            foreach($val AS $key => $row){
                if($key == count($val)-1){
                    $items .= $row;
                } else {
                    $items .= $row.',';
                }
                
            }
            
            $this->db->select('retail_bill.ID AS ID, 
			retail_bill.NAME AS NAME, 
			retail_bill.CODE AS CODE, 
			retail_bill.PHONE_NUMBER AS PHONENUMBER, 
			retail_bill.ADDRESS AS ADDRESS, retail_bill.ZIPCODE AS ZIPCODE');  
            $this->db->from('retail_bill'); 
            $this->db->where('retail_bill.STATUS_COMPLETE != 3');
            $this->db->where('retail_bill.STATUS', 1);
            $this->db->where('retail_bill.ID in ('.$items.')');
			
            $query = $this->db->get(); 
            $data = $query->result();
        } else {
            $data = '';
        }

        $data = $data;
        return $data;  
    }
 
	function dataems($val){

        if($val){
			$items = '';
            foreach($val AS $key => $row){
                if($key == count($val)-1){
                    $items .= $row;
                } else {
                    $items .= $row.',';
                }
                
            }
	
			$this->db->select('
					retail_bill.id as bill_id,
					retail_bill.code as bill_code,
					retail_bill.name as bill_name,
                    retail_bill.address as bill_address,
                    retail_bill.zipcode as bill_zipcode,
					retail_bill.text_number as bill_textnumber,
					retail_bill.phone_number as bill_phone,
					retail_bill.parcel_cost as bill_parcel,
					retail_bill.delivery_fee as bill_delivery,
					retail_bill.discount_price as bill_discount,
					retail_bill.net_total as bill_nettotal,
					retail_bill.pic_payment as bill_pic,
					retail_bill.pic_payment2 as bill_pic2,
					retail_bill.total_price as bill_totalprice,
                    retail_bill.billstatus as billstatus,
					retail_bill.date_starts as bill_datetime,
					retail_billdetail.promain_id as bill_promain,
					retail_billdetail.prolist_id as bill_prolist,
					retail_billdetail.quantity as bill_qty
				');
				$this->db->from('retail_bill');
				$this->db->join('retail_billdetail','retail_bill.id=retail_billdetail.bill_id','left');
				$this->db->where('retail_bill.status_complete != 3');
				$this->db->where('retail_bill.status_complete != 4');
				$this->db->where('retail_bill.status = 1');
				$this->db->where('retail_bill.id in ('.$items.')');					
				$this->db->group_by('retail_bill.id');					

			$query = $this->db->get(); 
            $data = $query->result();
        } else {
            $data = '';
        }

        $data = $data;
        return $data;  
    }
	
	//	id bill
	function get_codeProduct($id){
		
		$result = "---";
		
		$this->db->select(
		'retail_billdetail.prolist_id as prolist_id,
		retail_billdetail.quantity as qty'
		);  
		$this->db->from('retail_bill'); 
		$this->db->join('retail_billdetail','retail_bill.id=retail_billdetail.bill_id','left');
		$this->db->where('retail_bill.id',$id);
		$querybill = $this->db->get(); 
		$numbill = $querybill->num_rows();
		if($numbill > 0){
			$text = "";
			foreach($querybill->result() as $rbill){
				
				$this->db->select('code');  
				$this->db->from('retail_productlist'); 
				$this->db->where('retail_productlist.id',$rbill->prolist_id);
				// $this->db->where('retail_productlist.code is not null');
				$query = $this->db->get(); 
				$num = $query->num_rows();
				if($num > 0){
					$r = $query->row();
					if($r->code != "" ){
						$text .= $r->code."=".$rbill->qty." ";
					}else{
						$text .= "--- =".$rbill->qty." ";
					}
					
				}
			}
		}
		
		if(!is_null($text)){
			$result = $text;
		}
		
		return $result;  
	}
}
?>