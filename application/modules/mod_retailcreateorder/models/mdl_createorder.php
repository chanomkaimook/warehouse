<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_createorder extends CI_Model {
  
    //---------------------------- CREATEORDER ----------------------------//
    var $order_column = array("CODE", "NAME", "TEXT_NUMBER", "NET_TOTAL", "DELIVERY_FORMID");  
    function make_query() {  
        
        $this->db->select('*');  
        $this->db->from('retail_bill'); 
        // $this->db->where('status',1); 
        
        if(!empty($_POST["statuscomplete"])){
            if(!empty($_POST["deliveryid"]) || !empty($_POST['methodorder']) || !empty($_POST["valdate"]) || !empty($_POST["valdateTo"]) || !empty($_POST["search"]["value"]) || !empty($_POST["order"])){
                 // ------------------------ // 
            } else {
                if($_POST["statuscomplete"] == 1){
                    $this->db->where('retail_bill.STATUS_COMPLETE in (0,1,5)');
                } else if($_POST["statuscomplete"] == 2){
                    $this->db->where('retail_bill.STATUS_COMPLETE', $_POST["statuscomplete"]);
                    $this->db->where('retail_bill.TRANSFERED_DAYTIME != ""');
                } else if($_POST["statuscomplete"] == 5){
                    $this->db->where('retail_bill.STATUS_COMPLETE = 2');
                    $this->db->where('retail_bill.BILLSTATUS', 'C');
                    $this->db->where('retail_bill.TRANSFERED_DAYTIME', null);
                } else {
                    $this->db->where('retail_bill.STATUS_COMPLETE', $_POST["statuscomplete"]);
                }
            }
        } else {
            if(!empty($_POST["deliveryid"]) || !empty($_POST['methodorder']) || !empty($_POST["valdate"]) || !empty($_POST["valdateTo"]) || !empty($_POST["search"]["value"]) || !empty($_POST["order"])){
                // ------------------------ // 
            } else {
                $this->db->where('retail_bill.STATUS_COMPLETE in (0,1,5)');
            }
        }
		
		if($this->session->userdata('franshine')){
			$this->db->where('retail_bill.METHODORDER_ID', $this->session->userdata('franshine')); 
		}

        if(!empty($_POST["search"]["value"])) {  
           $this->db->like("retail_bill.CODE", $_POST["search"]["value"]);  
           $this->db->or_like("retail_bill.NAME", $_POST["search"]["value"]);  
           $this->db->or_like("retail_bill.TEXT_NUMBER", $_POST["search"]["value"]);  
           $this->db->or_like("retail_bill.PHONE_NUMBER", $_POST["search"]["value"]);  
           $this->db->or_like("retail_bill.REMARK_ORDER", $_POST["search"]["value"]);  
           $this->db->or_like("retail_bill.TextCode", $_POST["search"]["value"]);  
        //    $this->db->where('status',0); 
        }  
        if(!empty($_POST["order"])) {  
             $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
        } else {  
            if($_POST["statuscomplete"] == 5){
                $this->db->order_by('retail_bill.DATE_STARTS', 'ASC'); 
            } else {
                $this->db->order_by('retail_bill.DATE_UPDATE', 'DESC'); 
            }
        }  
          
        if(!empty($_POST["valdate"]) && !empty($_POST["valdateTo"])) {  
            $this->db->where('retail_bill.DATE_STARTS BETWEEN "'.$_POST["valdate"]. ' 00:00:00" and "'.$_POST["valdateTo"].' 23:59:59"');  
        } else if(!empty($_POST["valdate"]) && empty($_POST["valdateTo"])) {  
            $this->db->where('retail_bill.DATE_STARTS BETWEEN "'.$_POST["valdate"]. ' 00:00:00" and "'.$_POST["valdate"].' 23:59:59"');  
        } 

        if(!empty($_POST["deliveryid"])){
            $this->db->where('retail_bill.DELIVERY_FORMID', $_POST["deliveryid"]); 
        }

        if(!empty($_POST["methodorder"])){
            $this->db->where('retail_bill.METHODORDER_ID', $_POST["methodorder"]); 
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

    // ================================================ //
    function ajaxeditstatus() {
        $id = $this->input->post('id');
        $status_chk = '';
        $this->db->select('*');
        $this->db->from('retail_bill');
        $this->db->where('retail_bill.ID', $id);
        // echo $this->db->get_compiled_select();
        $Query  = $this->db->get();
        $row = $Query->row();

        if($row->STATUS == 1){
            $data = array( 'STATUS_COMPLETE' => 3, 'status' => 0 );
        } else {
            if($row->STATUS_APPROVE1 == 1 && $row->STATUS_APPROVE2 == 1){
                $data = array( 'STATUS_COMPLETE' => 2, 'status' => 1 );
            } else if($row->STATUS_APPROVE1 == 1 && $row->STATUS_APPROVE2 == 0){
                $data = array( 'STATUS_COMPLETE' => 1, 'status' => 1 );
            } else if($row->STATUS_APPROVE1 == 0 && $row->STATUS_APPROVE2 == 1){
                $data = array( 'STATUS_COMPLETE' => 1, 'status' => 1 );
            } else {
                $data = array( 'STATUS_COMPLETE' => 0, 'status' => 1 );
            }
            
        }
        $status = $data['status']; $statustxt = '';
        if($status == 1){ $statustxt = 'Open';} else { $statustxt = 'Off';}
         
        $this->db->where('id', $id);
        $this->db->update('retail_bill', $data);
        
        // ============== Log_Detail ============== //
        $log_query = $this->db->last_query();
        $last_id = $this->session->userdata('log_id');
        $detail = "Update Status Bill    To ".$statustxt." Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
        $type = "Update";
        $arraylog = array(
            'log_id'  	 	 => $last_id,
            'detail'  		 => $detail,
            'logquery'       => $log_query,
            'type'     	 	 => $type,
            'date_starts'    => date('Y-m-d H:i:s')
        );
        updateLog($arraylog);
        $code = 0;
        $txt = "";

        $data = array(
            "error_code" 		=> "" ,
            "txt" 				=> $statustxt
        );
        $data = json_encode($data);
        return $data;
    }
    
    function ajaxaddrowtable(){
        $PromainID = $this->input->post('PromainID');
        $ProlistID = $this->input->post('ProlistID');
        $qty = $this->input->post('qty');
        $this->db->select('*');
        $this->db->from('retail_productlist');
        $this->db->where('retail_productlist.STATUS', 1);
        $this->db->where('retail_productlist.ID', $ProlistID);
        $Query  = $this->db->get();
        $row = $Query->row();
        $items = [];
        
        $items['PromainID'] = $row->PROMAIN_ID;
        $items['ProlistID'] = $row->ID;
        $items['prolist_name'] = $row->NAME_TH;
        $items['prolist_price'] = number_format($row->PRICE, 2);
        $items['prolist_qty'] = ($qty);
        $items['total_price'] = ($row->PRICE * $qty);
        $items['NF_total_price'] = number_format($row->PRICE * $qty, 2);

        $data = json_encode($items);
        return $data;
    }
 
    function ajaxselectproductmain(){
        $val = $this->input->post('val');
 		$this->db->select('retail_productlist.NAME_TH AS NAME_TH, retail_productlist.ID AS ID');
		$this->db->from("retail_productlist");
		$this->db->join('retail_productmain','retail_productlist.PROMAIN_ID = retail_productmain.ID ','left'); 
        $this->db->where("retail_productmain.status", 1);
        $this->db->where("retail_productlist.status", 1);
        $this->db->where("retail_productlist.STATUS_VIEW", 1);
        if($val != null){
            $this->db->where("retail_productmain.ID", $val);
        }
        $this->db->order_by("retail_productlist.id", 'desc'); 
        $Query = $this->db->get();
        $data = json_encode($Query->result());
		return $data;
    }

    function ajaxdataform(){
		$this->load->library('order');

        /* echo "<pre>";
        print_r($_REQUEST);
        print_r($this->input->post('orderlist'));
        echo "remark :: ";
        print_r($this->input->post('remark_order'));
        echo "</pre>";

        echo ":: ".$this->input->post('bill_update')."::".$this->input->post('CheckStatus')."===".$this->input->post('bill_id');
        exit; */

            if($this->input->post('bill_update') == 'Y' && $this->input->post('bill_id') != ''){
                if($this->input->post('CheckStatus') == "Dis"){

                    $BILLSTATUS = 'T';
                    $this->db->select('*');
                    $this->db->from("retail_bill");
                    $this->db->where('retail_bill.ID', $this->input->post('bill_id'));
                    $this->db->where('retail_bill.STATUS', 1);  
                    $Query_count = $this->db->get();
                    $count = $Query_count->num_rows($Query_count);
                    $Result = $Query_count->row();
                    // Update bill //
                    if($count > 0){
                        // $NET_TOTAL =  ($this->input->post('TBLtotalprice') + ($this->input->post('total-Shippingcost') + $this->input->post('total-Parcelcost') + $this->input->post('shor_money') + $this->input->post('tax')) - $this->input->post('discount'));
                        $NET_TOTAL =  ($this->input->post('TBLtotalprice') + ($this->input->post('total-Shippingcost') + $this->input->post('total-Parcelcost') + $this->input->post('shor_money') + $this->input->post('tax')) + $this->input->post('discount'));
                        if($this->input->post('transferedDate') == '' && $this->input->post('transferedTime') == ''){
                            $TRANSFEREDDAYTIME = null;
                        } else {
                            $TRANSFEREDDAYTIME = $this->input->post('transferedDate').' '.$this->input->post('transferedTime').":00";
                        }
                        if($this->input->post('StatusComplete') == 5){ 
                            $Approve1 = 1; 
                            $StatusComplete = $this->input->post('StatusComplete');
                            $BILLSTATUS = $Result->BILLSTATUS;
                        } else{ 
                            $Approve1 = $Result->STATUS_APPROVE1;
                            $StatusComplete = $Result->STATUS_COMPLETE;
                            $BILLSTATUS = $Result->BILLSTATUS;
                        }  
                        
                        // =========== DELETE TBL BD =========== //
						$dataups = array(
							'status' => 0 
						);
                        $this->db->where('BILL_ID', $this->input->post('bill_id'));
						// $this->db->update('retail_billdetail',$dataups);
                        $this->db->delete('retail_billdetail');
                        // =========================== //
                        $orderlist = $this->input->post('orderlist');
                        $items = [];
                        foreach($orderlist as $row){
                           
							if($row['prolist']){
								$listid = $this->order->get_checkProductIdPro($row['prolist']);
							}else{
								$listid = "";
							}
							
                            $databilldetail = array(
                                'CODE'          => get_valueNullToNull($Result->CODE),
                                'BILL_ID'       => get_valueNullToNull($this->input->post('bill_id')),
                                'PROMAIN_ID'    => get_valueNullToNull($row['promain']),
                                'PROLIST_ID'    => get_valueNullToNull($row['prolist']),
								
                                'LIST_ID'    	=> get_valueNullToNull($listid['productid']),
                                
								'QUANTITY'      => get_valueNullToNull($row['proqty']),
                                'TOTAL_PRICE'   => "",
    
                                'DATE_UPDATE'   => get_valueNullToNull(trim($this->input->post('order_date').' '.date('H:i:s')))  ,
                                'USER_UPDATE' 	=> $this->session->userdata('useradminid'),
                                'STATUS' 		=> 1
                            );
                             
                            $this->db->insert('retail_billdetail', $databilldetail);
                            // ================================= //
                        }

                        // ============== Log_Detail ============== //
                        $log_query = $this->db->last_query();
                        $last_id = $this->session->userdata('log_id');
                        $detail = "Update Retail Bill Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
                        $type = "Update";
                        $arraylog = array(
                            'log_id'  	 	 => $last_id,
                            'detail'  		 => $detail,
                            'logquery'       => $log_query,
                            'type'     	 	 => $type,
                            'date_starts'    => date('Y-m-d H:i:s')
                        );
                        updateLog($arraylog);
                    }
                     
                    $error = 0;
                    $txt = "แก้ไขรายการสำเร็จ";
                    $getid = $this->input->post('bill_id');

                } else {
                    $BILLSTATUS = 'T';
                    $this->db->select('*');
                    $this->db->from("retail_bill");
                    $this->db->where('retail_bill.ID', $this->input->post('bill_id'));
                    $this->db->where('retail_bill.STATUS', 1);  
                    $Query_count = $this->db->get();
                    $count = $Query_count->num_rows($Query_count);
                    $Result = $Query_count->row();
                    // Update bill //
                    if($count > 0){
                        // $NET_TOTAL =  ($this->input->post('TBLtotalprice') + ($this->input->post('total-Shippingcost') + $this->input->post('total-Parcelcost') + $this->input->post('shor_money') + $this->input->post('tax')) - $this->input->post('discount'));
                        $NET_TOTAL =  ($this->input->post('TBLtotalprice') + ($this->input->post('total-Shippingcost') + $this->input->post('total-Parcelcost') + $this->input->post('shor_money') + $this->input->post('tax')) + $this->input->post('discount'));
                        if($this->input->post('transferedDate') == '' && $this->input->post('transferedTime') == ''){
                            $TRANSFEREDDAYTIME = null;
                        } else {
                            $TRANSFEREDDAYTIME = $this->input->post('transferedDate').' '.$this->input->post('transferedTime').":00";
                        }
                        if($this->input->post('StatusComplete') == 5){ 
                            $Approve1 = 1; 
                            $Approve2 = 0; 
                            $StatusComplete = $this->input->post('StatusComplete');
                            $BILLSTATUS = $Result->BILLSTATUS;
                            $TBLtotalprice = $Result->TOTAL_PRICE;
                        } else if($this->input->post('StatusComplete') == 6 || $this->input->post('BillStatus') == "F"){ 
                            $StatusComplete = 2; 
                            $Approve1 = 1;
                            $Approve2 = 1; 
                            $BILLSTATUS = $Result->BILLSTATUS;; 
                            
                            // $TBLtotalprice = 0;
                            // $NET_TOTAL = ($NET_TOTAL-$this->input->post('TBLtotalprice'));
							$TBLtotalprice = 0;
                            $NET_TOTAL = 0;
						   
						   $TRANSFEREDDAYTIME = $Result->TRANSFERED_DAYTIME;
                        } else { 
                            $Approve1 = $Result->STATUS_APPROVE1;
                            $Approve2 = 0; 
                            $StatusComplete = $Result->STATUS_COMPLETE;
                            $TBLtotalprice = $this->input->post('TBLtotalprice');
                            $BILLSTATUS = $Result->BILLSTATUS;
                        }  
                        $databill = array(
                            'DELIVERY_FORMID'   => get_valueNullToNull(trim($this->input->post('deliveryid')))  ,
                            'NAME'              => get_valueNullToNull(trim($this->input->post('name')))  ,
                            'TextCode'          => (trim($this->input->post('TextCode')))  ,
                            'PHONE_NUMBER'      => trim($this->input->post('tel')) ,
                            'ADDRESS'           => get_valueNullToNull(trim($this->input->post('address')))  ,
                            'ZIPCODE'           => get_valueNullToNull(trim($this->input->post('zipcode')))  ,
                            'REMARK_ORDER'      => trim($this->input->post('remark_order')) ,
                            'TEXT_NUMBER'       => trim($this->input->post('text_nameber')) ,
                            'METHODORDER_ID'    => trim($this->input->post('method_order')) ,
        
                            'TRANSFERED_BANIK_ID'   => trim($this->input->post('bankID')) ,
                            'TRANSFERED_DAYTIME'    => $TRANSFEREDDAYTIME,
                            'TRANSFERED_AMOUNT'     => trim($this->input->post('Amount')) ,
                            'TRANSFERED_REMARK'     => trim($this->input->post('TransferedRemark')) ,
    
                            'STATUS_APPROVE1'       => trim($Approve1) ,
                            'STATUS_APPROVE2'       => trim($Approve2) ,
                            'STATUS_COMPLETE'       => trim($StatusComplete) ,
                            'BILLSTATUS'            => trim($BILLSTATUS) ,
    
                            // 'DATE_STARTS'       => get_valueNullToNull(trim($this->input->post('order_date').' '.date('H:i:s')))  ,
                            'DATE_UPDATE'       => get_valueNullToNull(trim($this->input->post('order_date').' '.date('H:i:s')))  ,
                            'USER_UPDATE' 	    => $this->session->userdata('useradminid'),
                            'STATUS' 		    => 1
                        );
                         
                        $this->db->where('id', $this->input->post('bill_id'));
                        $this->db->update('retail_bill', $databill);
                         
                        // ============== Log_Detail ============== //
                        $log_query = $this->db->last_query();
                        $last_id = $this->session->userdata('log_id');
                        $detail = "Update Retail Bill Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
                        $type = "Update";
                        $arraylog = array(
                            'log_id'  	 	 => $last_id,
                            'detail'  		 => $detail,
                            'logquery'       => $log_query,
                            'type'     	 	 => $type,
                            'date_starts'    => date('Y-m-d H:i:s')
                        );
                        updateLog($arraylog);
                        
                        // =========== DELETE TBL BD =========== //
						$dataups = array(
							'status' => 0 
						);
                        $this->db->where('BILL_ID', $this->input->post('bill_id'));
						// $this->db->update('retail_billdetail',$dataups);
                        $this->db->delete('retail_billdetail');
                        // =========================== //
                        $orderlist = $this->input->post('orderlist');
                        $items = [];
                        foreach($orderlist as $row){
						
							if($row['prolist']){
								$listid = $this->order->get_checkProductIdPro($row['prolist']);
							}else{
								$listid = "";
							}
						
                        if($BILLSTATUS == 'F'){ 
                            $databilldetail = array(
                                'CODE'          => get_valueNullToNull($Result->CODE),
                                'BILL_ID'       => get_valueNullToNull($this->input->post('bill_id')),
                                'PROMAIN_ID'    => get_valueNullToNull($row['promain']),
                                'PROLIST_ID'    => get_valueNullToNull($row['prolist']),
                                
								'LIST_ID'    	=> get_valueNullToNull($listid['productid']),
								
								'QUANTITY'      => get_valueNullToNull($row['proqty']),
                                'TOTAL_PRICE'   => "",
    
                                'DATE_UPDATE'   => get_valueNullToNull(trim($this->input->post('order_date').' '.date('H:i:s')))  ,
                                'USER_UPDATE' 	=> $this->session->userdata('useradminid'),
                                'STATUS' 		=> 1
                            );
                        } else {
                            $databilldetail = array(
                                'CODE'          => get_valueNullToNull($Result->CODE),
                                'BILL_ID'       => get_valueNullToNull($this->input->post('bill_id')),
                                'PROMAIN_ID'    => get_valueNullToNull($row['promain']),
                                'PROLIST_ID'    => get_valueNullToNull($row['prolist']),
								
								'LIST_ID'    	=> get_valueNullToNull($listid['productid']),
								
                                'QUANTITY'      => get_valueNullToNull($row['proqty']),
                                'TOTAL_PRICE'   => "",
    
                                'DATE_UPDATE'   => get_valueNullToNull(trim($this->input->post('order_date').' '.date('H:i:s')))  ,
                                'USER_UPDATE' 	=> $this->session->userdata('useradminid'),
                                'STATUS' 		=> 1
                            );
                        }
                             
                            $this->db->insert('retail_billdetail', $databilldetail);
                            // ================================= //
                        }
						
						//
                        //  if creat bill free success system will to creat lot for cut stock
                        $last_bill = $this->input->post('bill_id');
                        if($last_bill){
                            $this->mdl_createorder->editOrderToLot($last_bill);
                        }
                    }
                     
                    $error = 0;
                    $txt = "แก้ไขรายการสำเร็จ";
                    $getid = $this->input->post('bill_id');
                }





            } else {    //  END IF UPDATE

                //  generate code bill
                $table = 'retail_bill';
                $codebill = 'TR2';

                $yearthai = date('Y') + 543;
                $year = substr($yearthai,2);
                $month = date('m');
                $param = $codebill."".$year."".$month;

                $sqlcode = $this->db->select('code')
                    ->from($table)
                    ->where($table . '.code is not null')
                    ->where($table . '.code like "' . $param . '%"')	// Ex. 202111
                    ->order_by($table . '.id','desc');
                $numbercode = $sqlcode->count_all_results(null, false);
                $qcode = $sqlcode->get();
                if($numbercode > 0){
                    $rcode = $qcode->row();
                    $numbernext = substr($rcode->code,7) + 1;
                    $new_number = str_pad($numbernext, 4, '0', STR_PAD_LEFT);

                    $gencode = $param.$new_number;
                    $codeDB = $gencode;
                }else{
                    $gencode = $param . "0001";
                    $codeDB = $gencode;
                }
 
                /* $this->db->select('retail_bill.CODE AS codemax');
                $this->db->from("retail_bill");
                $this->db->order_by('retail_bill.ID', 'DESC');  
                $Query_Max = $this->db->get();
                $num = $Query_Max->num_rows($Query_Max);
                $RowMax = $Query_Max->row();
                if($num > 0){
                    $str = explode("_", $RowMax->codemax);
                     
                     $codeDB = '';
                     $dateY = (date('Y') + 543);

                     $code = $str[1];
                     $codeyear = $str[2];
                     if($codeyear == $dateY){
                         $count = $code + 1;
                         $codeDB = $str[0].'_'.$count.'_'.$codeyear;
                     } else {
                         $code = 0;
                         $count = $code + 1;
                         $codeDB = $str[0].'_'.$count.'_'.$dateY;
                     }
                 } else {
                     $dateY = (date('Y') + 543);
                     $codeDB = 'um_1_'.$dateY;
                 } */
                
                // Insert bill //
                // $NET_TOTAL =  ($this->input->post('TBLtotalprice') + ($this->input->post('total-Shippingcost') + $this->input->post('total-Parcelcost') + $this->input->post('shor_money') + $this->input->post('tax')) - $this->input->post('discount'));
                $NET_TOTAL =  ($this->input->post('TBLtotalprice') + ($this->input->post('total-Shippingcost') + $this->input->post('total-Parcelcost') + $this->input->post('shor_money') + $this->input->post('tax')) + $this->input->post('discount'));
                if($this->input->post('transferedDate') == '' && $this->input->post('transferedTime') == ''){
                    $TRANSFEREDDAYTIME = null;
                } else {
                    $TRANSFEREDDAYTIME = $this->input->post('transferedDate').' '.$this->input->post('transferedTime').":00";
                }
                if($this->input->post('StatusComplete') == 5){ 
                    $StatusComplete = $this->input->post('StatusComplete');
                    $Approve1 = 1;
                    $Approve2 = 0; 
                    $BILLSTATUS = 'C'; 
                    $TBLtotalprice = $this->input->post('TBLtotalprice');
                } else if($this->input->post('StatusComplete') == 6){
                    $StatusComplete = 2; 
                    $Approve1 = 1;
                    $Approve2 = 1; 
                    $BILLSTATUS = 'F';
					
                    // $TBLtotalprice = 0;	
                    // $NET_TOTAL = ($NET_TOTAL-$this->input->post('TBLtotalprice'));
					$TBLtotalprice = 0;
                    $NET_TOTAL = 0;
					
					$TRANSFEREDDAYTIME = date('Y-m-d H:i:s');
                } else if($this->input->post('StatusComplete') == 1){
                    $StatusComplete = 1; 
                    $Approve1 = 1;
                    $Approve2 = 0; 
                    $BILLSTATUS = 'T';
					
                    // $TBLtotalprice = 0;	
                    // $NET_TOTAL = ($NET_TOTAL-$this->input->post('TBLtotalprice'));
					$TBLtotalprice = 0;
                    $NET_TOTAL = 0;
					
					$TRANSFEREDDAYTIME = date('Y-m-d H:i:s');
                } else { 
                    $StatusComplete = $this->input->post('StatusComplete');
                    $Approve1 = 0; 
                    $Approve2 = 0;
                    $BILLSTATUS = 'T';
                    $TBLtotalprice = $this->input->post('TBLtotalprice');
                }
                if($Approve2 == 1){
                    $databill = array(
                        'CODE'              => get_valueNullToNull($codeDB)  ,
                        'DELIVERY_FORMID'   => get_valueNullToNull(trim($this->input->post('deliveryid')))  ,
                        'NAME'              => get_valueNullToNull(trim($this->input->post('name')))  ,
                        'TextCode'          => (trim($this->input->post('TextCode')))  ,
                        'PHONE_NUMBER'      => trim($this->input->post('tel')) ,
                        'ADDRESS'           => get_valueNullToNull(trim($this->input->post('address')))  ,
                        'ZIPCODE'           => get_valueNullToNull(trim($this->input->post('zipcode')))  ,
                        'REMARK_ORDER'      => trim($this->input->post('remark_order')) ,
                        'TEXT_NUMBER'       => trim($this->input->post('text_nameber')) ,
                        'METHODORDER_ID'    => trim($this->input->post('method_order')) ,
    
                        'TOTAL_PRICE'       => trim($TBLtotalprice)  ,
                        'PARCEL_COST'       => trim($this->input->post('total-Parcelcost')) ,
                        'DELIVERY_FEE'      => trim($this->input->post('total-Shippingcost')) ,
                        'DISCOUNT_PRICE'    => trim($this->input->post('discount')) ,
                        'SHOR_MONEY'        => trim($this->input->post('shor_money'))  ,
                        'TAX'               => trim($this->input->post('tax'))  ,
                        'NET_TOTAL'         => trim($NET_TOTAL)  ,
     
                        'TRANSFERED_BANIK_ID'   => trim($this->input->post('bankID')) ,
                        'TRANSFERED_DAYTIME'    => $TRANSFEREDDAYTIME,
                        'TRANSFERED_AMOUNT'     => trim($this->input->post('Amount')) ,
                        'TRANSFERED_REMARK'     => trim($this->input->post('TransferedRemark')) ,
    
                        'STATUS_APPROVE1'       => trim($Approve1) ,
                        'STATUS_APPROVE2'       => trim($Approve2) ,
                        'STATUS_COMPLETE'       => trim($StatusComplete) ,
                        'BILLSTATUS'            => trim($BILLSTATUS) ,
                          
                        'DATE_UPDATE'       => date('Y-m-d H:i:s'),
                        'DATE_STARTS'       => get_valueNullToNull(trim($this->input->post('order_date').' '.date('H:i:s'))) ,
                        'USER_STARTS' 	    => $this->session->userdata('useradminid') ,
                        'STATUS' 		    => 1
                    );
                } else {
                    $databill = array(
                        'CODE'              => get_valueNullToNull($codeDB)  ,
                        'DELIVERY_FORMID'   => get_valueNullToNull(trim($this->input->post('deliveryid')))  ,
                        'NAME'              => get_valueNullToNull(trim($this->input->post('name')))  ,
                        'TextCode'          => (trim($this->input->post('TextCode')))  ,
                        'PHONE_NUMBER'      => trim($this->input->post('tel')) ,
                        'ADDRESS'           => get_valueNullToNull(trim($this->input->post('address')))  ,
                        'ZIPCODE'           => get_valueNullToNull(trim($this->input->post('zipcode')))  ,
                        'REMARK_ORDER'      => trim($this->input->post('remark_order')) ,
                        'TEXT_NUMBER'       => trim($this->input->post('text_nameber')) ,
                        'METHODORDER_ID'    => trim($this->input->post('method_order')) ,

                        'TOTAL_PRICE'       => trim($TBLtotalprice)  ,
                        'PARCEL_COST'       => trim($this->input->post('total-Parcelcost')) ,
                        'DELIVERY_FEE'      => trim($this->input->post('total-Shippingcost')) ,
                        'DISCOUNT_PRICE'    => trim($this->input->post('discount')) ,
                        'SHOR_MONEY'        => trim($this->input->post('shor_money'))  ,
                        'TAX'               => trim($this->input->post('tax'))  ,
                        'NET_TOTAL'         => trim($NET_TOTAL)  ,
    
                        'TRANSFERED_BANIK_ID'   => trim($this->input->post('bankID')) ,
                        'TRANSFERED_DAYTIME'    => $TRANSFEREDDAYTIME,
                        'TRANSFERED_AMOUNT'     => trim($this->input->post('Amount')) ,
                        'TRANSFERED_REMARK'     => trim($this->input->post('TransferedRemark')) ,

                        'STATUS_APPROVE1'       => trim($Approve1) ,
                        'STATUS_APPROVE2'       => trim($Approve2) ,
                        'STATUS_COMPLETE'       => trim($StatusComplete) ,
                        'BILLSTATUS'            => trim($BILLSTATUS) ,
                        
                        'DATE_STARTS'       => get_valueNullToNull(trim($this->input->post('order_date').' '.date('H:i:s'))) ,
                        'USER_STARTS' 	    => $this->session->userdata('useradminid') ,
                        'STATUS' 		    => 1
                    );
                }
                  
                $this->db->insert('retail_bill', $databill);
                $last_bill = $this->db->insert_id();
                 
                // ============== Log_Detail ============== //
                $log_query = $this->db->last_query();
                $last_id = $this->session->userdata('log_id');
                $detail = "Insert Create Bill Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
                $type = "Insert";
                $arraylog = array(
                    'log_id'  	 	 => $last_id,
                    'detail'  		 => $detail,
                    'logquery'       => $log_query,
                    'type'     	 	 => $type,
                    'date_starts'    => date('Y-m-d H:i:s')
                );
                updateLog($arraylog);

                $orderlist = $this->input->post('orderlist');
                $items = [];
                foreach($orderlist as $row){
				
					if($row['prolist']){
						$listid = $this->order->get_checkProductIdPro($row['prolist']);
					}else{
						$listid = "";
					}
				
                    if($BILLSTATUS == 'F'){
                        $databilldetail = array(
                            'CODE'          => get_valueNullToNull($codeDB),
                            'BILL_ID'       => get_valueNullToNull($last_bill),
                            'PROMAIN_ID'    => get_valueNullToNull($row['promain']),
                            'PROLIST_ID'    => get_valueNullToNull($row['prolist']),
							
							'LIST_ID'    	=> get_valueNullToNull($listid['productid']),
							
                            'QUANTITY'      => get_valueNullToNull($row['proqty']),
                            'TOTAL_PRICE'   => "",
    
                            'DATE_STARTS'   => date('Y-m-d H:i:s')  ,
                            'USER_STARTS' 	=> $this->session->userdata('useradminid'),
                            'STATUS' 		=> 1
                        );
                    } else {
                        $databilldetail = array(
                            'CODE'          => get_valueNullToNull($codeDB),
                            'BILL_ID'       => get_valueNullToNull($last_bill),
                            'PROMAIN_ID'    => get_valueNullToNull($row['promain']),
                            'PROLIST_ID'    => get_valueNullToNull($row['prolist']),
							
							'LIST_ID'    	=> get_valueNullToNull($listid['productid']),
							
                            'QUANTITY'      => get_valueNullToNull($row['proqty']),
                            'TOTAL_PRICE'   => "",
    
                            'DATE_STARTS'   => date('Y-m-d H:i:s')  ,
                            'USER_STARTS' 	=> $this->session->userdata('useradminid'),
                            'STATUS' 		=> 1
                        );
                    }
                    $this->db->insert('retail_billdetail', $databilldetail);
					
					/* if($BILLSTATUS == 'F'){
						$this->mdl_createorder->creatOrderToLot($last_bill);
					} */
                }
                $error = 0;
                $txt = "ทำรายการสำเร็จ";
                $getid = $last_bill;
            }

        if($this->input->post('claim_remark')){
            $data = array(
                "error_code" 		=> $error ,
                "txt" 				=> $txt,
                "getid"             => $getid,
                "valradio"          => 2,
                "claim_remark1"      => $this->input->post('claim_remark'),
                "claim_remark2"      => $this->input->post('claim_remark2'),
            );
        } else {
            $data = array(
                "error_code" 		=> $error ,
                "txt" 				=> $txt,
                "getid"             => $getid
            );
        }
        
        $data = json_encode($data);
		return $data;
    }

    function datebilldetail($id){

        $this->db->select('retail_bill.ID AS ID, retail_bill.CODE AS CODE, retail_bill.DELIVERY_FORMID AS DELIVERYFORMID, retail_bill.NAME AS NAME, retail_bill.PHONE_NUMBER AS PHONENUMBER, 
            retail_bill.ADDRESS AS ADDRESS, retail_bill.ZIPCODE AS ZIPCODE, retail_bill.TEXT_NUMBER AS TEXTNUMBER, retail_bill.TOTAL_PRICE AS TOTALPRICE, retail_bill.PARCEL_COST AS PARCELCOST, retail_bill.DELIVERY_FEE AS DELIVERYFEE,
            retail_bill.DISCOUNT_PRICE AS DISCOUNTPRICE, retail_bill.NET_TOTAL AS NETTOTAL, retail_bill.PIC_PAYMENT AS PICPAYMENT, retail_bill.PIC_PAYMENT2 AS PICPAYMENT2, retail_bill.STATUS_APPROVE1 AS STATUSAPPROVE1, 
            retail_bill.STATUS_APPROVE2 AS STATUSAPPROVE2, retail_bill.STATUS_COMPLETE AS STATUSCOMPLETE, retail_bill.REMARK AS REMARK, retail_bill.DATE_STARTS AS DATE_STARTS, retail_bill.STATUS AS BILLSTATUS,
              
            retail_productmain.ID AS PRONAME_MAINID, retail_productmain.NAME_TH AS PRONAME_MAIN, retail_bill.REMARK_ORDER AS REMARKORDER,
            retail_productlist.ID AS PRONAME_LISTID, retail_productlist.NAME_TH AS PRONAME_LIST, retail_productlist.PRICE AS PRICE,
            retail_billdetail.ID AS BD_ID,retail_billdetail.BILL_ID AS BD_BILLID, retail_billdetail.QUANTITY AS QUANTITY, retail_billdetail.TOTAL_PRICE AS RBD_TOTALPRICE,
            retail_bill.USER_STARTS AS USER_STARTS, staff.NAME_TH AS S_NAME_TH, staff.LASTNAME_TH AS S_LASTNAME_TH,
            retail_methodorder.TOPIC AS METHODORDER_TOPIC, retail_methodorder.ID AS METHODORDER_ID, retail_bill.SHOR_MONEY AS SHORMONEY, , retail_bill.TAX AS TAX,

            retail_bill.TRANSFERED_BANIK_ID AS BANIKID, bank.NAME_TH AS BANIKNAME, retail_bill.TRANSFERED_DAYTIME AS TRANSFEREDDAYTIME, 
            retail_bill.TRANSFERED_AMOUNT AS TRANSFEREDAMOUNT, retail_bill.TRANSFERED_REMARK AS TRANSFEREDREMARK,
            retail_billimg.ID AS IMGID, retail_billimg.IMGNAME AS IMGNAME,

            retail_productlist.codemac AS PRO_CODEMAC,

            retail_bill.BILLSTATUS AS BillStatus_Collect, retail_bill.TextCode as TextCode
        ');
		$this->db->from("retail_bill");
        $this->db->join('retail_billdetail','retail_bill.ID = retail_billdetail.BILL_ID ','left'); 
        $this->db->join('retail_productmain','retail_billdetail.PROMAIN_ID = retail_productmain.ID ','left'); 
        // $this->db->join('retail_productlist','if(retail_billdetail.LIST_ID is not null,retail_billdetail.LIST_ID = retail_productlist.ID, retail_billdetail.PROLIST_ID = retail_productlist.ID )','left',false);
        $this->db->join('retail_productlist','retail_billdetail.PROLIST_ID = retail_productlist.ID','left');
        $this->db->join('retail_methodorder','retail_bill.METHODORDER_ID = retail_methodorder.ID ','left');
        $this->db->join('staff','retail_bill.USER_STARTS = staff.CODE ','left');
        $this->db->join('bank','retail_bill.TRANSFERED_BANIK_ID = bank.ID ','left');
        $this->db->join('retail_billimg','retail_bill.ID = retail_billimg.BILLID ','left');
        // $this->db->where("retail_bill.status", 1);
		
        // $this->db->where("retail_productmain.status", 1);
        // $this->db->where("retail_productlist.status", 1);
		
        $this->db->where("retail_bill.ID", $id);
		// echo $this->db->get_compiled_select();
        $Query = $this->db->get();
        
        $items = [];
        foreach($Query->result() AS $row){
           /*  if($row->DELIVERYFORMID == 1){
                $DELIVERYFORMID = 'KERRY';
            } else if($row->DELIVERYFORMID == 2){
                $DELIVERYFORMID = 'EMS';
            } else if($row->DELIVERYFORMID == 3){
                $DELIVERYFORMID = 'FLASH';
            } else if($row->DELIVERYFORMID == 4){
                $DELIVERYFORMID = 'DHL';
            } else if($row->DELIVERYFORMID == 5){
                $DELIVERYFORMID = 'SCG';
            } else if($row->DELIVERYFORMID == 6){
                $DELIVERYFORMID = 'Food (SCG)';
            } else if($row->DELIVERYFORMID == 7){
                $DELIVERYFORMID = 'Shoppee';
            }  */
			
			//	new query
			$sql = $this->db->select('NAME_US')
			->from('delivery')
			->where('id',$row->DELIVERYFORMID)
			->get();
			$numdelevery = $sql->num_rows();
			if($numdelevery > 0){
				$r = $sql->row();
				$DELIVERYFORMID = $r->NAME_US;
			}
			
            // DATA Bill MAIN //
            $items['ID'] = $row->ID;
            $items['CODE'] = $row->CODE;
            $items['TextCode'] = $row->TextCode;
            $items['DELIVERYFORMID'] = $DELIVERYFORMID;
            $items['METHODORDER_TOPIC'] = $row->METHODORDER_TOPIC;
            $items['METHODORDER_ID'] = $row->METHODORDER_ID;
            $items['DELIVERY_FORM'] = $row->DELIVERYFORMID;
            $items['NAME'] = $row->NAME;
            $items['PHONENUMBER'] = $row->PHONENUMBER;
            $items['ADDRESS'] = $row->ADDRESS;
            $items['ZIPCODE'] = $row->ZIPCODE;
            $items['TEXTNUMBER'] = $row->TEXTNUMBER;
            // ===== ADMIN CRATE BILL ===== //
            $items['USER_STARTS'] = $row->USER_STARTS;
            $items['S_NAME_TH'] = $row->S_NAME_TH;
            $items['S_LASTNAME_TH'] = $row->S_LASTNAME_TH;
            // ===== NUMBER FORMAT ===== //
            $items['TOTALPRICE'] = number_format($row->TOTALPRICE, 2);
            $items['PARCELCOST'] = number_format($row->PARCELCOST, 2);
            $items['DELIVERYFEE'] = number_format($row->DELIVERYFEE, 2);
            $items['DISCOUNTPRICE'] = number_format($row->DISCOUNTPRICE, 2);
            $items['SHORMONEY'] = number_format($row->SHORMONEY, 2);
            $items['TAX'] = number_format($row->TAX, 2);
            $items['NETTOTAL'] = number_format($row->NETTOTAL, 2);

            //  TRANSFERED //
            $items['BANIKID'] = $row->BANIKID;
            $items['BANIKNAME'] = $row->BANIKNAME;
            $items['TRANSFEREDDAYTIMETHAI'] = thai_date($row->TRANSFEREDDAYTIME)." เวลา ".date('H:i:s',strtotime($row->TRANSFEREDDAYTIME))." น.";
            $items['TRANSFEREDDAYTIME'] = $row->TRANSFEREDDAYTIME;
            $items['TRANSFEREDAMOUNT'] = $row->TRANSFEREDAMOUNT;
            $items['TRANSFEREDAMOUNTNumber'] = number_format($row->TRANSFEREDAMOUNT,2);
            $items['TRANSFEREDREMARK'] = $row->TRANSFEREDREMARK;
             
            $items['TOTALPRICE_LANG'] = $row->TOTALPRICE;
            $items['PARCELCOST_LANG'] = $row->PARCELCOST;
            $items['DELIVERYFEE_LANG'] = $row->DELIVERYFEE;
            $items['DISCOUNTPRICE_LANG'] = $row->DISCOUNTPRICE;
            $items['SHORMONEY_LANG'] = $row->SHORMONEY;
            $items['NETTOTAL_LANG'] = $row->NETTOTAL;
            // ===== END NUMBER FORMAT ===== // 

            $items['PICPAYMENT'] = $row->PICPAYMENT;
            $items['PICPAYMENT2'] = $row->PICPAYMENT2;
            $items['DATE_STARTS'] = thai_date($row->DATE_STARTS);
            $items['DATE_STARTS_strtotime'] = $row->DATE_STARTS;
            $items['BILLSTATUS'] = $row->BILLSTATUS;
			$items['DATE_STARTSYMD'] = $row->DATE_STARTS;
            // Bill Status //
            $items['STATUSAPPROVE1'] = $row->STATUSAPPROVE1;
            $items['STATUSAPPROVE2'] = $row->STATUSAPPROVE2;
            $items['STATUSCOMPLETE'] = $row->STATUSCOMPLETE;
            $items['REMARK'] = $row->REMARK;
            $items['REMARKORDER'] = $row->REMARKORDER;
            $items['BillStatus_Collect'] = $row->BillStatus_Collect;
            // Bill List //
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_MAINID'] = $row->PRONAME_MAINID;
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_MAIN'] = $row->PRONAME_MAIN;
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['BILLDETAIL_BILLID'] = $row->BD_BILLID;
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['BILLDETAIL_ID'] = $row->BD_ID;
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['PRO_CODEMAC'] = $row->PRO_CODEMAC;
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['PRONAME_LISTID'] = $row->PRONAME_LISTID;
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['PRONAME_LIST'] = $row->PRONAME_LIST;
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['PRICE'] =  number_format($row->PRICE, 2);
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['QUANTITY'] =  number_format($row->QUANTITY);
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['RBD_TOTALPRICE'] =  number_format($row->RBD_TOTALPRICE, 2);
            $items['billist'][$row->PRONAME_MAINID]['PRONAME_LIST'][$row->PRONAME_LISTID.'-'.$row->BD_ID]['RBD_TOTALPRICE_LANG'] = $row->RBD_TOTALPRICE;

            // IMG Multiple //
            $items['IMGNAME'][$row->IMGID]['IMGNAME_ID'] = $row->IMGID;
            $items['IMGNAME'][$row->IMGID]['IMGNAME_NAME'] = $row->IMGNAME;
        }
        
        // echo '<pre>'; print_r($items); exit;
        $data =  $items;
        return $data;
    }

    function deleteorder(){
        $this->load->library('order');
        
        $id = $this->input->post('id');
        $remark = $this->input->post('remark');

        if($id != ''){
            $this->db->select('*');
            $this->db->from('retail_bill');
            $this->db->where('retail_bill.ID', $id);
            $Query  = $this->db->get();
            $row = $Query->row();

            if($row->STATUS == 1){
                $data_bill = array(
                    'STATUS_COMPLETE' => 3,
                    'REMARK' => get_valueNullToNull($remark),

                    'USER_UPDATE' => $this->session->userdata('useradminid'),
                    'DATE_UPDATE' => date('Y-m-d H:i:s'),
                    'STATUS' => 1
                );
                $this->db->where('id', $id);
                $this->db->update('retail_bill', $data_bill);

                $array_param = array(
					'billid'		=> $id
				);
            }

            $this->db->select('*');
            $this->db->from('retail_billdetail');
            $this->db->where('retail_billdetail.BILL_ID', $id);
            $Query  = $this->db->get();
            foreach($Query->result() AS $val){
                if($val->STATUS == 1){
                    $data_billdetail = array('STATUS' => 0,);
                    $this->db->where('id', $val->ID);
                    $this->db->update('retail_billdetail', $data_billdetail);
                }
            }
            $error = 0;
            $txt = 'ยกเลิกออเดอร์สำเร็จ';
        }
 
        $data = array(
            "error_code" 		=> $error ,
            "txt" 				=> $txt
        );
        $data = json_encode($data);
		return $data;
    }
	
	function editOrderToLot($billID){
        //
		//  paramiter array in
		//  @param billid       @int = order bill id  
        $sql = $this->db->select('
            retail_bill.id as rtb_id,
            retail_billdetail.prolist_id as rtbd_proid,
            retail_billdetail.list_id as rtbd_prolistid,
            retail_billdetail.quantity as rtbd_qty,
            retail_bill.date_starts as rtbd_datestart
        ')
        ->from('retail_bill')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_bill.status',1)
        ->where('retail_bill.id',$billID)
        ->where('retail_billdetail.status',1);
		$num = $this->db->count_all_results(null,false);
		$q = $sql->get();

		if($num > 0){
            //  clear old order
            // $status_del = $this->mdl_createorder->delOrderToLot($billID);

            //  creat lot
            /* if($status_del == 0){
                $this->mdl_createorder->creatOrderToLot($billID);
            } */
        }
    }
	
    function delOrderToLot($billID){
		$array_param = array(
					'billid'		=> $billID
			);
		$sticklot = $this->order->cancelLotForEdit($array_param);

        if($sticklot){
            $result = 0;
        }else{
            $result = 1;
        }
        return $result;
    }

    function creatOrderToLot($billID){
        $this->load->library('order');
		//
		//  paramiter array in
		//  @param billid       @int = order bill id  
		//  @param productid    @int = product id  
		//  @param qty          @int = quantity
        $sql = $this->db->select('
            retail_bill.id as rtb_id,
            retail_billdetail.prolist_id as rtbd_proid,
            retail_billdetail.list_id as rtbd_prolistid,
            retail_billdetail.quantity as rtbd_qty,
            retail_bill.date_starts as rtbd_datestart
        ')
        ->from('retail_bill')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_bill.status',1)
        ->where('retail_bill.id',$billID)
        ->where('retail_billdetail.status',1);
		$num = $this->db->count_all_results(null,false);
		$q = $sql->get();
		if($num > 0){
			foreach($q->result() as $row){
			
				//	check item promotion will select item product
				//	because item promotion not have stock
				if($row->rtbd_prolistid != ""){
					$item = $row->rtbd_prolistid;
					$charge = 0;
				}else{
					$item = $row->rtbd_proid;
					$charge = 1;
				}
			
				$array_param = array(
					'billid'		=> $row->rtb_id,
					'productid'		=> $item,
					'charge'		=> $charge,				//	0=not price,1=price
					'qty'			=> $row->rtbd_qty,
					'datestarts'	=> $row->rtbd_datestart
				);
				$sticklot = $this->order->stickLot($array_param);
			}
		}
    }

    function statusapprove(){
        // echo '<pre>'; print_r($_POST); exit;
        $billID = $this->input->post('id');
        $bntVAL = $this->input->post('val');
		
		$error = 1;
		$txt = "บิลนี้มีการอนุมัติไปแล้ว";
		$getid = "";
		
        if($billID != ''){
            $this->db->select('*');
            $this->db->from('retail_bill');
            $this->db->where('retail_bill.ID', $billID);
            $this->db->where('retail_bill.STATUS_COMPLETE !=', 2);
            $Query  = $this->db->get();
			$num = $Query->num_rows();
            $row = $Query->row();
			
			if($num > 0){
				if($row->STATUS == 1){
					if($bntVAL == 1){
						if($row->STATUS_APPROVE2 == 1){
							$data = array(
								'STATUS_APPROVE1' => 1,
								'STATUS_COMPLETE' => 2,
								'DATE_UPDATE'   => date('Y-m-d H:i:s')  ,
								'USER_UPDATE' 	=> $this->session->userdata('useradminid'),
							);

							//  insert lot
							// if(date('Y-m-d',strtotime($row->DATE_STARTS)) >= date('Y-m-d')){
								// $this->mdl_createorder->creatOrderToLot($billID);
							// }
							

						} else {
							$data = array(
								'STATUS_APPROVE1' => 1,
								'STATUS_COMPLETE' => 1,
								'DATE_UPDATE'   => date('Y-m-d H:i:s')  ,
								'USER_UPDATE' 	=> $this->session->userdata('useradminid'),
							);
						}
						$txt = 'ตรวจสอบการโอนเงินสำเร็จ';
					} else if($bntVAL == 2){
						if($row->STATUS_APPROVE1 == 1){
							$data = array(
								'STATUS_APPROVE2' => 1,
								'STATUS_COMPLETE' => 2,
								'DATE_UPDATE'   => date('Y-m-d H:i:s')  ,
								'USER_UPDATE' 	=> $this->session->userdata('useradminid'),
							);

							//  insert lot
							// if(date('Y-m-d',strtotime($row->DATE_STARTS)) >= date('Y-m-d')){
								// $this->mdl_createorder->creatOrderToLot($billID);
							// }

						} else {
							$data = array(
								'STATUS_APPROVE2' => 1,
								'STATUS_COMPLETE' => 1,
								'DATE_UPDATE'   => date('Y-m-d H:i:s')  ,
								'USER_UPDATE' 	=> $this->session->userdata('useradminid'),
							);
						}
						$txt = 'ตรวจสอบรายการสำเร็จ';
					}
				}

				$this->db->where('id', $billID);
				$this->db->update('retail_bill', $data);
				$error = 0;
				$getid = $billID;
			
			}
        }

        $data = array(
            "error_code" 		=> $error ,
            "txt" 				=> $txt,
            "getid"             => $getid
        );
        $data = json_encode($data);
        return $data;
    }

    function claimorder(){
        $id = $this->input->post('id');
        $remark = $this->input->post('remark');
        $valradio = $this->input->post('valradio');
        if($this->input->post('remarkclaim') != ''){
            $remarkclaim = $this->input->post('remarkclaim');
        } else {
            $remarkclaim = '';
        }
        $error = 1; $txt = ''; 
        $result = $this->mdl_sql->get_WherePara('retail_bill', 'ID', $id);
        if($result){
            if( $valradio == 1){
                $TOTALPRICE = 0;
                $NETTOTAL = 0;
            } else if( $valradio == 2){
                $NETTOTAL = $result->NET_TOTAL;
                $TOTALPRICE = $result->TOTAL_PRICE;
            } else if( $valradio == 3){
                $NETTOTAL = ($result->NET_TOTAL - $result->TOTAL_PRICE);
                $TOTALPRICE = 0;
            }
            // ============= Update Status To Claim ============= // 
            $data = array(
                'TOTAL_PRICE' => trim($TOTALPRICE) ,
                'NET_TOTAL'   => trim($NETTOTAL) ,

                'STATUS_COMPLETE' => 4,
                'REMARK' => $remark,
                'STATUS' => 0
            );
            $this->db->where('id', $id);
            $this->db->update('retail_bill', $data);
            if( $valradio == 3){
                $data = array('QUANTITY' => 0, 'TOTAL_PRICE' => 0, 'STATUS' => 0);
            } else {
                $data = array('STATUS' => 0);
            }
            $this->db->where('BILL_ID', $id);
            $this->db->update('retail_billdetail', $data);

            if($result->TRANSFERED_DAYTIME == ''){ $TRANSFEREDDAYTIME = 'null'; } else { $TRANSFERED_DAYTIME = $result->TRANSFERED_DAYTIME; }
            // ============== Insert Order To Claim =============== //
            $dataclaim = array(
                'CODE'              => get_valueNullToNull($result->CODE)  ,
                'TextCode'          => (trim($result->TextCode))  ,
                'BILL_ID'           => get_valueNullToNull($result->ID)  ,
                'DELIVERY_FORMID'   => get_valueNullToNull(trim($result->DELIVERY_FORMID))  ,
                'NAME'              => get_valueNullToNull(trim($result->NAME))  ,
                'PHONE_NUMBER'      => (trim($result->PHONE_NUMBER))  ,
                'ADDRESS'           => get_valueNullToNull(trim($result->ADDRESS))  ,
                'ZIPCODE'           => trim($result->ZIPCODE)  ,
                'TEXT_NUMBER'       => trim($result->TEXT_NUMBER)  ,
                
                'TOTAL_PRICE'       => trim($TOTALPRICE) ,
                'PARCEL_COST'       => trim($result->PARCEL_COST) ,
                'DELIVERY_FEE'      => trim($result->DELIVERY_FEE) ,
                'DISCOUNT_PRICE'      => trim($result->DISCOUNT_PRICE) ,
                'SHOR_MONEY'        => trim($result->SHOR_MONEY) ,
                'TAX'               => trim($result->TAX) ,
                'NET_TOTAL'         => trim($NETTOTAL) ,
                 
                'TRANSFERED_BANIK_ID'   => trim($result->TRANSFERED_BANIK_ID) ,
                'TRANSFERED_DAYTIME'    => trim($TRANSFEREDDAYTIME) ,
                'TRANSFERED_AMOUNT'     => trim($result->TRANSFERED_AMOUNT) ,
                'TRANSFERED_REMARK'     => trim($result->TRANSFERED_REMARK) ,

                'STATUS_APPROVE1'   => trim($result->STATUS_APPROVE1) ,
                'STATUS_APPROVE2'   => trim($result->STATUS_APPROVE2) ,
                'STATUS_COMPLETE'   => 4 ,
                'STATUS_CLAIM'      => $valradio,
                'REMARK'            => $remark,
                'REMARK_CLAIM'      => $remarkclaim,
                'REMARK_ORDER'      => $result->REMARK_ORDER,
   
                'BILLSTATUS'        => $result->BILLSTATUS,
                'DATE_STARTS'       => date('Y-m-d H:i:s') ,
                'USER_STARTS' 	    => $this->session->userdata('useradminid') ,
                'DATE_UPDATE'       => date('Y-m-d H:i:s') ,
                'USER_UPDATE' 	    => $this->session->userdata('useradminid'),
                'STATUS' 		    => 1
            );
            // echo '<pre>'; print_r($dataclaim); exit;
            $this->db->insert('retail_claim', $dataclaim);
            $last_bill = $this->db->insert_id();
            // ============== Log_Detail ============== //
            $log_query = $this->db->last_query();
            $last_id = $this->session->userdata('log_id');
            $detail = "Insert Create Bill Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
            $type = "Insert";
            $arraylog = array(
                'log_id'  	 	 => $last_id,
                'detail'  		 => $detail,
                'logquery'       => $log_query,
                'type'     	 	 => $type,
                'date_starts'    => date('Y-m-d H:i:s')
            );
            updateLog($arraylog);
            
            // ============== Insert Order To Claim Detail =============== //
            $Query = $this->mdl_sql->get_WhereParaqry('retail_billdetail', 'BILL_ID', $result->ID);
            foreach($Query->result() AS $row){
                $data = array(
                    'CODE'          => get_valueNullToNull($row->CODE),
                    'CLAIM_ID'      => get_valueNullToNull($last_bill),
                    'PROMAIN_ID'    => get_valueNullToNull($row->PROMAIN_ID),
                    'PROLIST_ID'    => get_valueNullToNull($row->PROLIST_ID),
                    'QUANTITY'      => trim($row->QUANTITY),
                    'TOTAL_PRICE'   => trim($row->TOTAL_PRICE),

                    'DATE_STARTS'   => date('Y-m-d H:i:s')  ,
                    'USER_STARTS' 	=> $this->session->userdata('useradminid'),
                    'STATUS' 		=> 1
                );
                $this->db->insert('retail_claimdetail', $data);
            }
            
            $error = 0; 
            $txt = 'เคลมรายการออเดอร์ '.$result->CODE.' สำเร็จ';
            $getid = $billID;
        }

        $data = array(
            "error_code" 		=> $error ,
            "txt" 				=> $txt,
            "getid"             => $getid
        );
        $data = json_encode($data);
        return $data;
    }

    function ajaximg(){
        
        $count = count($_FILES);
        $uploadsDir =  FCPATH. "asset/images/front/retail/BillPaymentMultiple/";
        $allowedFileType = array('jpg','png','jpeg', 'JPG', 'gif', 'GIF');
        if (!empty(array_filter($_FILES['ImgPayment']['name']))) {
            if($this->input->post('BillUpdate') == 'Y'){
                $this->db->select('*');
                $this->db->from('retail_billimg');
                $this->db->where('retail_billimg.BILLID', $this->input->post('BillID'));
                $this->db->where('retail_billimg.STATUS', 1);
                $Query  = $this->db->get();
                foreach($Query->result() AS $row){
                    unlink(FCPATH."asset/images/front/retail/BillPaymentMultiple/". $row->IMGNAME);
                }
                $this->db->where('BILLID', $this->input->post('BillID'));
                $this->db->delete('retail_billimg');
                 
                $CodeMax = $this->input->post('BillID');
            } else {
                $this->db->select('MAX(retail_bill.ID) MAX');
                $this->db->from("retail_bill");
                $Query_Max = $this->db->get();
                $RowMax = $Query_Max->row();
                $CodeMax = $RowMax->MAX;
            }
  
            foreach($_FILES['ImgPayment']['name'] as $key => $File){
                 
                $TypeImg         = $_FILES['ImgPayment']['type'][$key];
                $fileName        = $CodeMax.'_'.(date('Y')+543).'_'.date('Y').date('m').date('d').date('H').date('i').'_'.$key;
                $tempLocation    = $_FILES['ImgPayment']['tmp_name'][$key];
                $targetFilePath  = $uploadsDir . $fileName.'.'.explode("/",$TypeImg)[1];
                $fileType        = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                if(in_array($fileType, $allowedFileType)){
                        if(move_uploaded_file($tempLocation, $targetFilePath)){
                            $data = array( 
                                'BILLID '       =>  $this->input->post('BillID'),
                                'IMGNAME'       =>  $fileName.'.'.explode("/",$TypeImg)[1], 
                                'DATE_STARTS'   =>  date('Y-m-d H:i:s') 
                            );
                            $this->db->insert('retail_billimg', $data);
                            $data = array(
                                "status"  => 200,
                                "message" => "seccuse",
                                "getid" => $this->input->post('BillID')
                            );
                        } 
                } else {
                    $data = array(
                        "status" => 500,
                        "message" => "Only .jpg, .jpeg and .png file formats allowed."
                    );
                }
            } 
        }
        
        $data = json_encode($data);
        return $data;
    }
     
}
?>