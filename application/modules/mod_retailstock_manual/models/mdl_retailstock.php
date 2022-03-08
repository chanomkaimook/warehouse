<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_retailstock extends CI_Model {
   
    //---------------------------- DATATABLE ----------------------------//  
    function make_query() {  
        $request = $_REQUEST;
        $datestarts = $request['date'];
        
        $this->db->select('
            retail_productlist.id AS p_id,
            retail_productlist.CODEMAC AS p_codemac,
            retail_productlist.CODE_PRODUCT AS p_codeproduct,
            retail_productlist.NAME_TH AS p_name,
            retail_productlist.price AS p_price,

            retail_stock.id AS id,
            retail_stock.start AS start,
            retail_stock.cut AS cut,
            retail_stock.pull AS pull,
            retail_stock.claim AS claim,
            retail_stock.loss AS loss,
            retail_stock.repack AS repack,
            retail_stock.other AS other,
            retail_stock.other_remark AS other_remark,
            retail_stock.total AS total,
            retail_stock.remark AS remark,
            retail_stock.date_starts AS date_starts,
            retail_stock.user_starts AS user_starts,
            retail_stock.date_update AS date_update,
            retail_stock.user_update AS user_update,

            retail_stocksetting.min AS rtst_min
        ');
        $this->db->from('retail_stock'); 
        $this->db->join('retail_productlist', "retail_productlist.id = retail_stock.retail_productlist_id", 'left');
		$this->db->join('retail_stocksetting','retail_productlist.id=retail_stocksetting.retail_productlist_id','left'); 

        $this->db->where('retail_productlist.promain_id not in(6,12,14,15,16)');    //  14,15,16 dryeage
        $this->db->where('retail_productlist.status_view',1);
        $this->db->where('retail_productlist.status',1); 
        $this->db->where('retail_productlist.id not in(279,278,277)'); 
        
        if($datestarts){
            $this->db->where('date(retail_stock.date_starts)' ,$datestarts);
        }else{
            $this->db->where('date(retail_stock.date_starts)',date('Y-m-d'));
        }

        $this->db->group_by('retail_productlist.id');
        // $this->db->order_by('SIGN(stock.qty) DESC, ABS(stock.qty)');
        // $this->db->order_by('CASE WHEN stock.qty >= 0 THEN 2 ELSE 1 END,ABS(stock.qty) desc',null,false);
        $this->db->order_by('retail_productlist.id desc',null,false);
          
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
    function get_all_data(){  
        $this->db->select("*");  
        $this->db->from('retail_productmain');  
        return $this->db->count_all_results();  
    }  

	//	@param	array	@array = 
	//		id		@int = product id
	//		date	@date = date('Y-m-d')
	function countTotalProductOrder($array) {
		$date = $array['date']; 
		// $date = '2021-11-01'; 

		$sql = $this->db->select('
			retail_bill.id,
			retail_billdetail.prolist_id,
			retail_billdetail.list_id,
			retail_billdetail.quantity
        ');
		$this->db->from('retail_bill'); 
        $this->db->join('retail_billdetail','retail_bill.id=retail_billdetail.bill_id','left');
		// $this->db->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id = '.$productid.', retail_billdetail.prolist_id = '.$productid.')',null,false);
		$this->db->where('retail_bill.status_complete in (2)'); 
		$this->db->where('retail_bill.status',1); 
		$this->db->where('date(retail_bill.date_starts)',$date); 
		// $this->db->protect_identifiers = FALSE;
		$num = $sql->count_all_results(null,false); 
		$q = $sql->get();

		$arrayresult = array();
		if($num){
			foreach($q->result() as $row){
				//	quantity
				$producttotal = $row->quantity;
				
				//	list id
				(get_valueNullToNull($row->list_id) ? $list_id = $row->list_id : $list_id = "null");
				
				// check ยอดรับเข้า หากมีให้นำมาลบยอดรวม
				$sqlt = $this->db->select('retail_receivedetail.quantity as rtd_qty')
				->from('retail_receive')
				->join('retail_receivedetail','retail_receive.id=retail_receivedetail.receive_id','left')
				->where('retail_receive.rt_id',$row->id)
				->where('retail_receive.complete',2)
				->where('retail_receive.status',1)
				->where('retail_receivedetail.status',1)
				->where('(retail_receivedetail.prolist_id ='.$row->prolist_id.' or retail_receivedetail.list_id ='.$list_id.')',null,false);
				
				$qt = $sqlt->get();
				$numt = $qt->num_rows();
				if($numt){
					$rowt = $qt->row();
					$quantity = $rowt->rtd_qty; 

					$producttotal = $producttotal - $quantity;
				}
			
				$arrayresult[] = array(
					'prolist_id' 	=> $row->prolist_id,
					'list_id' 		=> $row->list_id, 
					'num' 			=> $producttotal
				);
			}
		}
		// $this->db->reset_query();
		// $num = $q->num_rows();

		if($num <= 0){
			$num = 0;
		}

		return $arrayresult;
	}

	function querysetting() {  
        $request = $_REQUEST;
        $datestarts = $request['date'];
        
        $this->db->select('
            retail_productlist.id AS p_id,
            retail_productlist.CODEMAC AS p_codemac,
            retail_productlist.CODE_PRODUCT AS p_codeproduct,
            retail_productlist.NAME_TH AS p_name,
            retail_productlist.price AS p_price,
            retail_productlist.status_view AS p_view,

            retail_stocksetting.min AS rtst_min,
            retail_stocksetting.max AS rtst_max,
            retail_stocksetting.user_update AS rtst_user,

            staff.name AS staff_name,
            staff.name_th AS staff_nameth
        ');
        $this->db->from('retail_productlist'); 
        $this->db->join('retail_stocksetting','retail_productlist.id=retail_stocksetting.retail_productlist_id','left'); 
        $this->db->join('staff','retail_stocksetting.user_update=staff.code','left'); 

        $this->db->where('retail_productlist.promain_id not in(6,12,14,15,16)');    //  14,15,16 dryeage
        $this->db->where('retail_productlist.status',1); 
        $this->db->where('retail_productlist.id not in(279,278,277)'); 

        $this->db->order_by('retail_productlist.id desc',null,false);
          
    }
    function datasetting(){ 
        $this->querysetting();  
        if($_POST["length"] != -1) {  
            $this->db->limit($_POST['length'], $_POST['start']);  
        }  
        
        // echo $this->db->get_compiled_select();
        $query = $this->db->get();  
        return $query->result();  
    }

	function get_sumDateToItem($array) {
		// 	setting
		$datelast = $array['datestart'];
		$date = $array['dateend'];
		$id = $array['id'];
		
		$result = "";
		$sql = $this->db->select('
			sum(cut) as cut,
			sum(pull) as pull,
			date(date_starts) as datestarts
		')
		->from('retail_stock')
		->where('date(date_starts) >', $datelast)
		->where('date(date_starts) <=', $date)
		->where('retail_productlist_id', $id)
		->where('status', 1);
		$q = $sql->get();
		$num = $q->num_rows();
		if($num){
			$result = $q->row();
		}

		return $result;
	}

    function ajax_updatestock() {

        $input = filter_input_array(INPUT_POST);
		
		$id = $this->input->post('id');
		
		if($this->input->get('date')){
			$date = $this->input->get('date');
		}
		
		if($date == "null"){
			$date = date('Y-m-d');
		}

		$this->db->select('
            retail_stock.claim AS claim,
            retail_stock.loss AS loss,
            retail_stock.repack AS repack,
            retail_stock.other AS other,
            retail_stock.other_remark AS other_remark,
            retail_stock.total AS total,
        ');
        $this->db->from('retail_stock'); 
        $this->db->where('retail_stock.retail_productlist_id',$id); 
        $this->db->where('date(retail_stock.date_starts)',$date); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();
		}else{
			$r = "";	
		}
		
		if($r){
			$total_other = intval($r->claim) + intval($r->loss) + intval($r->repack) + intval($r->other);
		}else{
			$total_other = 0;
		}
		
        $total = intval($input['start']) + intval($input['pull']) - (intval($input['cut']) + $total_other);
        $dataupdate = array(
            'start'    => $input['start'],
            'cut'      => get_valueNullToNull(intval($input['cut'])),
            'pull'     => get_valueNullToNull($input['pull']),
            'total'    => $total,
            'date_update'    => date('Y-m-d H:i:s'),
            'user_update'    => $this->session->userdata('useradminid')
        );
		
		/* if(get_valueNullToNull($input['cut'])){
			$dataupdate['cut']	= get_valueNullToNull($input['cut']);
			$dataupdate['date_cut']	= date('Y-m-d H:i:s');
			$dataupdate['user_cut']	= $this->session->userdata('useradminid');
		}
		
		if(get_valueNullToNull($input['pull'])){
			$dataupdate['pull']	= get_valueNullToNull($input['pull']);
			$dataupdate['date_pull']	= date('Y-m-d H:i:s');
			$dataupdate['user_pull']	= $this->session->userdata('useradminid');
		} */
		
        $this->db->where('retail_productlist_id',$input['id']);
        $this->db->where('date(retail_stock.date_starts)',$date);
        $this->db->update('retail_stock',$dataupdate);

        
    }
	
	function ajax_updatestock_setting() {

        $input = filter_input_array(INPUT_POST);

		$error = 1;
		$txt = 'ไม่สามารถทำรายการได้';
		/* echo "<pre>";
		print_r($input);
		echo "</pre>"; */
		
 		$min = filter_var($input['min'], FILTER_VALIDATE_INT);
		if(!$min && $input['min'] != ""){
			$txt = 'ค่า min ไม่สามารถบันทึกได้';
			$array = array(
				'error'		=> $error,
				'txt'		=> $txt,
				'id'		=> $input['id']
			);
			echo json_encode($array);
			exit;
		}

		if($this->input->get('date')){
			$date = $this->input->get('date');
		}

		if($date == "null"){
			$date = date('Y-m-d');
		}

		$this->db->select('
            retail_stocksetting.id AS setting_id,
        ');
        $this->db->from('retail_stocksetting'); 
        $this->db->where('retail_stocksetting.retail_productlist_id',$input['id']); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();

			$dataupdate = array(
				'min'    => $min,
				'max'      => $input['max'],
				'date_update'    => date('Y-m-d H:i:s'),
				'user_update'    => $this->session->userdata('useradminid')
			);

			$this->db->where('retail_stocksetting.id',$r->setting_id);
        	$this->db->update('retail_stocksetting',$dataupdate);
			$error = 0;
			$txt = 'success';
		}else{

			$datainsert = array(
				'retail_productlist_id'    => $input['id'],
				'min'    => $min,
				'max'      => $input['max'],
				'date_update'    => date('Y-m-d H:i:s'),
				'user_update'    => $this->session->userdata('useradminid')
			);
        	$this->db->insert('retail_stocksetting',$datainsert);
			$id = $this->db->insert_id();
			if($id){
				$error = 0;
				$txt = 'success';
			}

		}

		$array = array(
			'error'		=> $error,
			'txt'		=> $txt,
			'id'		=> $input['id'],
		);
		echo json_encode($array);

    }

	function ajax_getDataOther() {  
        $request = $_REQUEST;
        $retail_stockid = $request['retail_stockid'];
        
        $this->db->select('
            retail_stock.claim AS claim,
            retail_stock.loss AS loss,
            retail_stock.repack AS repack,
            retail_stock.other AS other,
            retail_stock.other_remark AS other_remark,
            retail_stock.total AS total,
        ');
        $this->db->from('retail_stock'); 
        $this->db->where('retail_stock.id',$retail_stockid); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();
		}else{
			$r = "";	
		}
		
		$data = array(
			'error_code'        => 0,
			'txt'               => 'success',
			'data'				=> $r
		);
		$data = json_encode($data);
		return $data;
          
    }
	
	function ajax_frmOtherSubmit() {
		$request = $_REQUEST;

        $input = filter_input_array(INPUT_POST);

		//	
		$this->db->select('
            retail_stock.start AS start,
            retail_stock.pull AS pull,
            retail_stock.cut AS cut,
            retail_stock.retail_productlist_id AS retail_productlist_id
        ');
        $this->db->from('retail_stock'); 
        $this->db->where('retail_stock.id',$input['stockid']); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();
		}else{
			$r = "";	
		}
		
		$total_other = intval($input['claim']) + intval($input['loss']) + intval($input['repack']) + intval($input['other']);
        
		if($r){
			$total = $r->start + $r->pull - ($r->cut + $total_other);
		}else{
			$total = $total_other;
		}

		$dataupdate = array(

            'claim'    	=> $input['claim'],
            'loss'    	=> $input['loss'],
            'repack'    => $input['repack'],
            'other'    => $input['other'],
            'other_remark'    => trim($input['other_remark']),
            'total'    => $total,
            'date_update'    => date('Y-m-d H:i:s'),
            'user_update'    => $this->session->userdata('useradminid')
        );
        $this->db->where('retail_stock.id',$input['stockid']);
        $this->db->update('retail_stock',$dataupdate);
		
		$dataupdate['id'] = $r->retail_productlist_id;
		$dataupdate['other'] = $total_other;
		
		$error_code = 0;
		$text = 'success';
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text,
			'data'				=> $dataupdate
		);
		$data = json_encode($data);
		return $data;
        
    }

	function get_stockdata($date) {
		
		$sql = $this->db->select('
			sum(retail_stock.pull) as pull,
			sum(retail_stock.cut) as cut,
			sum(retail_stock.total) as total,
			retail_stock.date_starts as date_starts
		')
			->from('retail_stock')
			->where('date(retail_stock.date_starts)',$date )
			->where('retail_stock.status', 1);
		$q = $sql->get();
		$num = $q->num_rows();

		$result = array();
		if ($num) {
			$row = $q->row();

			$result['pull'] = $row->pull;
			$result['cut'] = $row->cut;
			$result['total'] = $row->total;
			$result['date'] = date('H:i:s',strtotime($row->date_starts));
		}
		
		$error_code = 0;
		$text = 'success';
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text,
			'data'				=> $result
		);
		$data = json_encode($data);
		return $data;
	}

	function get_staticClaim($date) {
		
		$var = "claim";
		
		$this->db->select('
            SUM(retail_stock.'.$var.') AS total,
        ');
        $this->db->from('retail_stock'); 
        $this->db->where('date(retail_stock.date_starts)',$date); 
        $this->db->where('retail_stock.status',1); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();
			$total = get_valueNullToZero($r->total);
		}else{
			$total = 0;
		}
		
		$error_code = 0;
		$text = 'success';
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text,
			'total'				=> $total
		);
		$data = json_encode($data);
		return $data;
	}
	
	function get_staticLoss($date) {
		
		$var = "loss";
		
		$this->db->select('
            SUM(retail_stock.'.$var.') AS total,
        ');
        $this->db->from('retail_stock'); 
        $this->db->where('date(retail_stock.date_starts)',$date); 
        $this->db->where('retail_stock.status',1); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();
			$total = get_valueNullToZero($r->total);
		}else{
			$total = 0;
		}
		
		$error_code = 0;
		$text = 'success';
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text,
			'total'				=> $total
		);
		$data = json_encode($data);
		return $data;
	}
	
	function get_staticRepack($date) {
		
		$var = "repack";
		
		$this->db->select('
            SUM(retail_stock.'.$var.') AS total,
        ');
        $this->db->from('retail_stock'); 
        $this->db->where('date(retail_stock.date_starts)',$date); 
        $this->db->where('retail_stock.status',1); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();
			$total = get_valueNullToZero($r->total);
		}else{
			$total = 0;
		}
		
		$error_code = 0;
		$text = 'success';
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text,
			'total'				=> $total
		);
		$data = json_encode($data);
		return $data;
	}
	
	function get_staticOther($date) {
		
		$var = "other";
		
		$this->db->select('
            SUM(retail_stock.'.$var.') AS total,
        ');
        $this->db->from('retail_stock'); 
        $this->db->where('date(retail_stock.date_starts)',$date); 
        $this->db->where('retail_stock.status',1); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();
			$total = get_valueNullToZero($r->total);
		}else{
			$total = 0;
		}
		
		$error_code = 0;
		$text = 'success';
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text,
			'total'				=> $total
		);
		$data = json_encode($data);
		return $data;
	}
	
	function ajax_staticReload() {
		$request = $_REQUEST;
		$get_date = ($request['date'] ? $request['date'] : date('Y-m-d'));
	
		$this->db->select('
            SUM(retail_stock.claim) AS total_claim,
            SUM(retail_stock.loss) AS total_loss,
            SUM(retail_stock.repack) AS total_repack,
            SUM(retail_stock.other) AS total_other
        ');
        $this->db->from('retail_stock'); 
        $this->db->where('date(retail_stock.date_starts)',$get_date); 
        $this->db->where('retail_stock.status',1); 
		$num = $this->db->count_all_results(null,false);
		$q =  $this->db->get();
		if($num){
			$r = $q->row();
		}else{
			$r = "";
		}
		
		$error_code = 0;
		$text = 'success';
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text,
			'data'				=> $r
		);
		$data = json_encode($data);
		return $data;
	}
	
	//	@param	array @array = array[ 
	// 								id => array(product id) ,
	// 								date => date Y-m-d ,
	// 							]
	
	function reRunStock($array) {
		//	setting
		$date = $array['date'];
		$product_id = $array['id'];
		
		$error_code = 0;
		$text = 'ข้อมูลวันที่ '.$date.' อัพเดตสำเร็จ';
		
		if(!$date){
			$error_code = 1;
			$text = 'ไม่มีข้อมูลวันที่';
			
			$data = array(
				'error_code'        => $error_code,
				'txt'               => $text
			);
			$data = json_encode($data);
			return $data;
			
		}
		
		if(!$product_id){
			
			$sqlarray = $this->db->select('id,retail_productlist_id')
			->from('retail_stock')
			->where('status',1)
			->where('date(date_starts)',$date)
			->order_by('retail_productlist_id asc');
			$qarray = $sqlarray->get();
			
			$num = $qarray->num_rows();
			if($num){
				foreach($qarray->result() as $rarray){
					$product_id[] = $rarray->retail_productlist_id;
				}
				
			}
		}
		
		/* echo "<pre>";
		print_r($product_id);
		echo "</pre>";
		exit; */

		if($product_id){
			foreach($product_id as $valin){
				$sqlchk = $this->db->select('id')
				->from('retail_stock')
				->where('retail_productlist_id',$valin)
				->where('status',1)
				->where('date(date_starts)',$date);
				$qchk = $sqlchk->get();
				
				$num = $qchk->num_rows();
				
				if(!$num){
					$error_code = 1;
					$text = 'ไม่มีข้อมูลสินค้า ID'.$valin." ในวันที่ ".$date;
					
					$data = array(
						'error_code'        => $error_code,
						'txt'               => $text
					);
					$data = json_encode($data);
					return $data;
				}
			}
			
			// runprocess
			foreach($product_id as $val){
				// echo "each :".$val." ";
				$sqlpro = $this->db->select('id,total')
				->from('retail_stock')
				->where('retail_productlist_id',$val)
				->where('status',1)
				->where('date(date_starts) < ',$date)
				->order_by('date(date_starts) asc');
				$qpro = $sqlpro->get();
				$rpro = $qpro->row();
				
				$numpro = $qpro->num_rows();
				// echo "num :".$numpro."<br>";
				if($numpro){

					$sqlsum = $this->db->select('
					id,
					sum(cut) as sum_cut,
					sum(pull) as sum_pull,
					sum(claim) as sum_claim,
					sum(loss) as sum_loss,
					sum(repack) as sum_repack,
					sum(other) as sum_other
					')
					->from('retail_stock')
					->where('retail_productlist_id',$val)
					->where('status',1)
					->where('date(date_starts) < ',$date);
					// ->where('id >',$proid);
					$qsum = $sqlsum->get();
					$rsum = $qsum->row();
					
					$total = $rpro->total;
					$sum_cut = $rsum->sum_cut;
					$sum_pull = $rsum->sum_pull;
					$sum_claim = $rsum->sum_claim;
					$sum_loss = $rsum->sum_loss;
					$sum_repack = $rsum->sum_repack;
					$sum_other = $rsum->sum_other;
					
					$totalother = $sum_claim + $sum_loss + $sum_repack + $sum_other;
					$totalresult = ($total + $sum_pull) - ($sum_cut + $totalother);
					
					//	update
					$sqlup = $this->db->select('
						id,
						cut,
						pull,
						claim,
						loss,
						repack,
						other
					')
					->from('retail_stock')
					->where('retail_productlist_id',$val)
					->where('date(date_starts)',$date);
					$qup = $sqlup->get();
					$rup = $qup->row();
					
					$updatetotalother = $rup->claim + $rup->loss + $rup->repack + $rup->other;
					$updatetotal = ($totalresult + $rup->pull) - ($rup->cut + $updatetotalother);
					
					/* echo "<pre>";
					echo "id : ".$val."<br>";

					echo "total :".$total."<br>";
					echo "sum_cut :".$sum_cut."<br>";
					echo "sum_pull :".$sum_pull."<br>";
					echo "sum_claim :".$sum_claim."<br>";
					echo "sum_loss :".$sum_loss."<br>";
					echo "sum_repack :".$sum_repack."<br>";
					echo "sum_other :".$sum_other."<br>";

					echo "sumstart : ".$totalresult."<br>";
					echo "sumtotal : ".$updatetotal;
					// print_r();
					echo "</pre>"; */
					

					$dataupdate = array(
						'start'		=> $totalresult,
						'total'		=> $updatetotal,
						'date_starts'	=> date($date.' H:i:s'),
						'user_starts'	=> $this->session->userdata('useradminid')
					);
					$this->db->where('id',$rup->id);
					$this->db->update('retail_stock',$dataupdate);
				}	//	end if numpro	
			}
		}
		
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text
		);
		$data = json_encode($data);
		return $data;
	}
	function reRunStock__($array) {
		//	setting
		$date = $array['date'];
		$product_id = $array['id'];
		
		$error_code = 0;
		$text = 'ข้อมูลวันที่ '.$date.' อัพเดตสำเร็จ';
		
		if(!$date){
			$error_code = 1;
			$text = 'ไม่มีข้อมูลวันที่';
			
			$data = array(
				'error_code'        => $error_code,
				'txt'               => $text
			);
			$data = json_encode($data);
			return $data;
			
		}
		
		if(!$product_id){
			
			$sqlarray = $this->db->select('id,retail_productlist_id')
			->from('retail_stock')
			->where('status',1)
			->where('date(date_starts)',$date)
			->order_by('retail_productlist_id asc');
			$qarray = $sqlarray->get();
			
			$num = $qarray->num_rows();
			if($num){
				foreach($qarray->result() as $rarray){
					$product_id[] = $rarray->retail_productlist_id;
				}
				
			}
		}
		
		/* echo "<pre>";
		print_r($product_id);
		echo "</pre>";
		exit; */

		if($product_id){
			foreach($product_id as $valin){
				$sqlchk = $this->db->select('id')
				->from('retail_stock')
				->where('retail_productlist_id',$valin)
				->where('status',1)
				->where('date(date_starts)',$date);
				$qchk = $sqlchk->get();
				
				$num = $qchk->num_rows();
				
				if(!$num){
					$error_code = 1;
					$text = 'ไม่มีข้อมูลสินค้า ID'.$valin." ในวันที่ ".$date;
					
					$data = array(
						'error_code'        => $error_code,
						'txt'               => $text
					);
					$data = json_encode($data);
					return $data;
				}
			}
			
			// runprocess
			foreach($product_id as $val){
				// echo "each :".$val." ";
				$sqlpro = $this->db->select('id,total')
				->from('retail_stock')
				->where('retail_productlist_id',$val)
				->where('status',1)
				->where('date(date_starts) < ',$date)
				->order_by('date(date_starts) asc');
				$qpro = $sqlpro->get();
				$rpro = $qpro->row();
				
				$numpro = $qpro->num_rows();
				// echo "num :".$numpro."<br>";
				if($numpro){

					$sqlsum = $this->db->select('
					id,
					sum(cut) as sum_cut,
					sum(pull) as sum_pull,
					sum(claim) as sum_claim,
					sum(loss) as sum_loss,
					sum(repack) as sum_repack,
					sum(other) as sum_other
					')
					->from('retail_stock')
					->where('retail_productlist_id',$val)
					->where('status',1)
					->where('date(date_starts) < ',$date);
					// ->where('id >',$proid);
					$qsum = $sqlsum->get();
					$rsum = $qsum->row();
					
					$total = $rpro->total;
					$sum_cut = $rsum->sum_cut;
					$sum_pull = $rsum->sum_pull;
					$sum_claim = $rsum->sum_claim;
					$sum_loss = $rsum->sum_loss;
					$sum_repack = $rsum->sum_repack;
					$sum_other = $rsum->sum_other;
					
					$totalother = $sum_claim + $sum_loss + $sum_repack + $sum_other;
					$totalresult = ($total + $sum_pull) - ($sum_cut + $totalother);
					
					//	update
					$sqlup = $this->db->select('
						id,
						cut,
						pull,
						claim,
						loss,
						repack,
						other
					')
					->from('retail_stock')
					->where('retail_productlist_id',$val)
					->where('date(date_starts)',$date);
					$qup = $sqlup->get();
					$rup = $qup->row();
					
					$updatetotalother = $rup->claim + $rup->loss + $rup->repack + $rup->other;
					$updatetotal = ($totalresult + $rup->pull) - ($rup->cut + $updatetotalother);
					
					/* echo "<pre>";
					echo "id : ".$val."<br>";

					echo "total :".$total."<br>";
					echo "sum_cut :".$sum_cut."<br>";
					echo "sum_pull :".$sum_pull."<br>";
					echo "sum_claim :".$sum_claim."<br>";
					echo "sum_loss :".$sum_loss."<br>";
					echo "sum_repack :".$sum_repack."<br>";
					echo "sum_other :".$sum_other."<br>";

					echo "sumstart : ".$totalresult."<br>";
					echo "sumtotal : ".$updatetotal;
					// print_r();
					echo "</pre>"; */
					

					$dataupdate = array(
						'start'		=> $totalresult,
						'total'		=> $updatetotal,
						'date_starts'	=> date($date.' H:i:s'),
						'user_starts'	=> $this->session->userdata('useradminid')
					);
					$this->db->where('id',$rup->id);
					$this->db->update('retail_stock',$dataupdate);
				}	//	end if numpro	
			
			}
		}
		
		$data = array(
			'error_code'        => $error_code,
			'txt'               => $text
		);
		$data = json_encode($data);
		return $data;
	}
}
