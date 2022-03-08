<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_retailstock extends CI_Model
{
	// $input = filter_input_array(INPUT_POST);
	public function __construct()
	{
		parent::__construct();

		$this->set	= array(
			'datenow'				=> date('Y-m-d')
			// 'datenow'				=> "2022-02-01"
			// 'datenow'				=> "2021-01-30"
			// 'datenow'				=> "2021-05-30"
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}
	//---------------------------- DATATABLE ----------------------------//  
	function make_query()
	{
		$request = $_REQUEST;
		$datesearch = $request['date'];
		$date_cut = $request['date_cut'];

		$this->db->select('
            retail_productlist.id AS p_id,
            retail_productlist.CODEMAC AS p_codemac,
            retail_productlist.CODE_PRODUCT AS p_codeproduct,
            retail_productlist.NAME_TH AS p_name,
            retail_productlist.price AS p_price,

            retail_stock.id AS id,
            retail_stock.total AS total,
            retail_stock.date_starts AS date_starts,
            retail_stock.user_starts AS user_starts,
            retail_stock.date_end AS date_update,
            retail_stock.user_end AS user_update,

            retail_stocksetting.min AS rtst_min
        ');
		$this->db->from('retail_stock');
		$this->db->join('retail_productlist', "retail_productlist.id = retail_stock.retail_productlist_id", 'left');
		$this->db->join('retail_stocksetting', 'retail_productlist.id=retail_stocksetting.retail_productlist_id', 'left');

		$this->db->where('retail_productlist.promain_id not in(6,12,14,15,16)');    //  14,15,16 dryeage
		$this->db->where('retail_stock.date_cut', $date_cut);

		if ($datesearch) {
			$this->db->where('retail_stock.date_starts <=', $datesearch);
		} else {
			$this->db->where('retail_stock.date_starts <=', date('Y-m-d'));
		}

		$this->db->where('if(retail_stock.date_end is not null, date(retail_stock.date_end) > "'.$datesearch.'" and retail_stock.status = 0 ,retail_stock.status = 1 )',null,false );

		$this->db->group_by('retail_productlist.id');
		// $this->db->order_by('SIGN(retail_stock.total) asc');
		// $this->db->order_by('CASE WHEN retail_stock.total >= 0 THEN 2 ELSE 1 END,ABS(retail_stock.total) desc',null,false);
		$this->db->order_by('retail_productlist.id desc,retail_stock.id desc', null, false);
	}

	function make_queryMonth()
	{
		$request = $_REQUEST;
		$monthsearch = date('m',strtotime($request['date']));
		$date_cut = $request['date_cut'];

		$this->db->select('
            retail_productlist.id AS p_id,
            retail_productlist.CODEMAC AS p_codemac,
            retail_productlist.CODE_PRODUCT AS p_codeproduct,
            retail_productlist.NAME_TH AS p_name,
            retail_productlist.price AS p_price,

            retail_stock.id AS id,
            retail_stock.total AS total,
            retail_stock.date_starts AS date_starts,
            retail_stock.user_starts AS user_starts,
            retail_stock.date_end AS date_update,
            retail_stock.user_end AS user_update,

            retail_stocksetting.min AS rtst_min
        ');
		$this->db->from('retail_stock');
		$this->db->join('retail_productlist', "retail_productlist.id = retail_stock.retail_productlist_id", 'left');
		$this->db->join('retail_stocksetting', 'retail_productlist.id=retail_stocksetting.retail_productlist_id', 'left');

		$this->db->where('retail_productlist.promain_id not in(6,12,14,15,16)');    //  14,15,16 dryeage
		$this->db->where('retail_stock.date_cut', $date_cut);

		$this->db->where('month(retail_stock.date_starts)', $monthsearch);

		$this->db->where('if(retail_stock.date_end is not null, month(retail_stock.date_end) = "'.$monthsearch.'" and date(retail_stock.date_starts)!=date(retail_stock.date_end) and retail_stock.status = 0 ,retail_stock.status = 1 )',null,false );

		$this->db->group_by('retail_productlist.id');
		// $this->db->order_by('SIGN(retail_stock.total) asc');
		// $this->db->order_by('CASE WHEN retail_stock.total >= 0 THEN 2 ELSE 1 END,ABS(retail_stock.total) desc',null,false);
		$this->db->order_by('retail_productlist.id desc,retail_stock.id desc', null, false);
	}

	function make_datatables()
	{	
		$urlfunc = $this->uri->segment(3);
		if($urlfunc == 'fetch_productMonth'){
			$this->make_queryMonth();
		}else{
			$this->make_query();
		}

		
		if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}

		// echo $this->db->get_compiled_select();
		$query = $this->db->get();
		return $query->result();
	}
	function get_filtered_data()
	{
		$this->make_query();
		$query = $this->db->get();
		return $query->num_rows();
	}
	function get_all_data()
	{
		$this->db->select("*");
		$this->db->from('retail_productmain');
		return $this->db->count_all_results();
	}

	//	@param	array	@array = 
	//		date	@date = date('Y-m-d')
	function countTotalProductOrder($array)
	{
		$date = $array['date'];
		// $date = '2021-11-01'; 

		$sql = $this->db->select('
			retail_bill.id,
			retail_billdetail.prolist_id,
			retail_billdetail.list_id,
			retail_billdetail.quantity
        ');
		$this->db->from('retail_bill');
		$this->db->join('retail_billdetail', 'retail_bill.id=retail_billdetail.bill_id', 'left');
		// $this->db->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id = '.$productid.', retail_billdetail.prolist_id = '.$productid.')',null,false);
		$this->db->where('retail_bill.status_complete in (2,5)');
		$this->db->where('retail_bill.status', 1);
		$this->db->where('date(retail_bill.date_starts)', $date);
		// $this->db->protect_identifiers = FALSE;
		$num = $sql->count_all_results(null, false);
		$q = $sql->get();

		$arrayresult = array();
		if ($num) {
			foreach ($q->result() as $row) {
				//	quantity
				$producttotal = $row->quantity;

				//	list id
				(get_valueNullToNull($row->list_id) ? $list_id = $row->list_id : $list_id = "null");

				// check ยอดรับเข้า หากมีให้นำมาลบยอดรวม
				$sqlt = $this->db->select('retail_receivedetail.quantity as rtd_qty')
					->from('retail_receive')
					->join('retail_receivedetail', 'retail_receive.id=retail_receivedetail.receive_id', 'left')
					->where('retail_receive.rt_id', $row->id)
					->where('retail_receive.complete', 2)
					->where('retail_receive.status', 1)
					->where('retail_receivedetail.status', 1)
					->where('(retail_receivedetail.prolist_id =' . $row->prolist_id . ' or retail_receivedetail.list_id =' . $list_id . ')', null, false);

				$qt = $sqlt->get();
				$numt = $qt->num_rows();
				if ($numt) {
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

		if ($num <= 0) {
			$num = 0;
		}

		return $arrayresult;
	}

	function querysetting()
	{
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
		$this->db->join('retail_stocksetting', 'retail_productlist.id=retail_stocksetting.retail_productlist_id', 'left');
		$this->db->join('staff', 'retail_stocksetting.user_update=staff.code', 'left');

		$this->db->where('retail_productlist.promain_id not in(6,12,14,15,16)');    //  14,15,16 dryeage
		$this->db->where('retail_productlist.status', 1);
		$this->db->where('retail_productlist.id not in(279,278,277)');

		$this->db->order_by('retail_productlist.id desc', null, false);
	}
	function datasetting()
	{
		$this->querysetting();
		if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}

		// echo $this->db->get_compiled_select();
		$query = $this->db->get();
		return $query->result();
	}

	function get_sumDateToItem($array)
	{
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
		if ($num) {
			$result = $q->row();
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								id => array(product id) ,
	// 								date => date Y-m-d ,
	// 							]

	function reRunStock($array)
	{
		//	setting
		$date = $array['date'];
		$product_id = $array['id'];

		$error_code = 0;
		$text = 'ข้อมูลวันที่ ' . $date . ' อัพเดตสำเร็จ';

		if (!$date) {
			$error_code = 1;
			$text = 'ไม่มีข้อมูลวันที่';

			$data = array(
				'error_code'        => $error_code,
				'txt'               => $text
			);
			$data = json_encode($data);
			return $data;
		}

		if (!$product_id) {

			$sqlarray = $this->db->select('id,retail_productlist_id')
				->from('retail_stock')
				->where('status', 1)
				->where('date(date_starts)', $date)
				->order_by('retail_productlist_id asc');
			$qarray = $sqlarray->get();

			$num = $qarray->num_rows();
			if ($num) {
				foreach ($qarray->result() as $rarray) {
					$product_id[] = $rarray->retail_productlist_id;
				}
			}
		}

		/* echo "<pre>";
		print_r($product_id);
		echo "</pre>";
		exit; */

		if ($product_id) {
			foreach ($product_id as $valin) {
				$sqlchk = $this->db->select('id')
					->from('retail_stock')
					->where('retail_productlist_id', $valin)
					->where('status', 1)
					->where('date(date_starts)', $date);
				$qchk = $sqlchk->get();

				$num = $qchk->num_rows();

				if (!$num) {
					$error_code = 1;
					$text = 'ไม่มีข้อมูลสินค้า ID' . $valin . " ในวันที่ " . $date;

					$data = array(
						'error_code'        => $error_code,
						'txt'               => $text
					);
					$data = json_encode($data);
					return $data;
				}
			}

			// runprocess
			foreach ($product_id as $val) {
				// echo "each :".$val." ";
				$sqlpro = $this->db->select('id,total')
					->from('retail_stock')
					->where('retail_productlist_id', $val)
					->where('status', 1)
					->where('date(date_starts) < ', $date)
					->order_by('date(date_starts) asc');
				$qpro = $sqlpro->get();
				$rpro = $qpro->row();

				$numpro = $qpro->num_rows();
				// echo "num :".$numpro."<br>";
				if ($numpro) {

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
						->where('retail_productlist_id', $val)
						->where('status', 1)
						->where('date(date_starts) < ', $date);
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
						->where('retail_productlist_id', $val)
						->where('date(date_starts)', $date);
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
						'date_starts'	=> date($date . ' H:i:s'),
						'user_starts'	=> $this->session->userdata('useradminid')
					);
					$this->db->where('id', $rup->id);
					$this->db->update('retail_stock', $dataupdate);
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

	//	@param	array @array = array[ 
	// 								date		@date => date select ,
	// 
	function dateCut($array)
	{
		$result = "";

		$sql = $this->db->select('*')
			->from('retail_stockcut')
			->where('retail_stockcut.datecut <=', $array['date'])
			->limit('2')
			->order_by('retail_stockcut.id', 'desc');
		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			if($num == 1){
				$r = $q->row();
				$datecut = $r->DATECUT;
				$datestart = "";
			}else{
				$index = 1;
				foreach($q->result() as $r){
					if($index == 1){
						$datecut = $r->DATECUT;
					}else{
						$datestart = $r->DATECUT;
					}
					$index++;
				}
			}

			//	หาระยะห่างในการเก็บรอบตัด stock
			$sqlcut = $this->db->select("timestampdiff(month,'".$datecut."','".$array['date']."') as lengthcut")
			->from('retail_stockcut');
			$qcut = $sqlcut->get();
			$numcut = $qcut->num_rows();
			if($numcut){
				$rcut = $qcut->row();
				if($rcut->lengthcut >= 3){	//	ห่าง 3 เดือน
					$time = strtotime($array['date']);
					$year = date('Y',$time);
					$month = date('m',$time);
					$newcut = $year.'-'.$month.'-01';

					$datainsert = array(
						'datecut'	=> $newcut
					);
					$this->db->insert('retail_stockcut',$datainsert);
					$id = $this->db->insert_id();
					if($id){

						$sqlnew = $this->db->select('*')
							->from('retail_stockcut')
							->where('retail_stockcut.datecut <=', $array['date'])
							->order_by('retail_stockcut.id', 'desc')
							->limit('2');
						$qnew = $sqlnew->get();

						$index = 1;
						foreach($qnew->result() as $rnew){
							if($index == 1){
								$datecut = $rnew->DATECUT;
							}else{
								$datestart = $rnew->DATECUT;
							}
							$index++;
						}
					}

					// $datecut = $newcut;
				}

				$result = array(
					'datecut'		=> $datecut,
					'datestart'		=> $datestart
				);
			}
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								datestart	@date => date before cut stock now 
	// 								datecut		@date => date cut stock 
	// 	
	function total_start_billOrder($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_billdetail.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillOrderDate($arrayset)
			->where('date(retail_bill.date_starts) <', $array['datecut'])
			->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id =' . $array['item'] . ',retail_billdetail.prolist_id =' . $array['item'] . ')', null, false);
		
		if($array['datestart']){
			$sql->where('date(retail_bill.date_starts) >=', $array['datestart']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$rowqty = $row->rtd_qty;

			if($array['datestart']){
				$sqlset = $this->mdl_retailstock->sqlStock()
				->where('retail_stock.date_cut',$array['datestart']);
				$qset = $sqlset->get();
				$numset = $qset->num_rows();
				if($numset){
					$rowset = $qset->row();
					$total = $rowset->TOTAL;
					$rowqty = $total + $row->rtd_qty;
				}
				
			}else{
				$rowqty = $row->rtd_qty;
			}

			$result = array(
				'num'		=> $num,
				'row'		=> $rowqty,
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	//								datestart	@date => date before cut stock now 
	// 								datecut		@date => date cut stock 
	// 	
	function total_start_billIssue($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_issue.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillIssue($arrayset)
			->where('date(retail_issue.date_starts) <', $array['datecut'])
			->where('if(retail_issue.list_id is not null,retail_issue.list_id =' . $array['item'] . ',retail_issue.prolist_id =' . $array['item'] . ')', null, false);
		
		if($array['datestart']){
			$sql->where('date(retail_issue.date_starts) >=', $array['datestart']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$rowqty = $row->rtd_qty;

			if($array['datestart']){
				$sqlset = $this->mdl_retailstock->sqlStock()
				->where('retail_stock.date_cut',$array['datestart']);
				$qset = $sqlset->get();
				$numset = $qset->num_rows();
				if($numset){
					$rowset = $qset->row();
					$total = $rowset->TOTAL;
					$rowqty = $total + $row->rtd_qty;
				}
				
			}else{
				$rowqty = $row->rtd_qty;
			}

			$result = array(
				'num'		=> $num,
				'row'		=> $rowqty,
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	//								datestart	@date => date before cut stock now 
	// 								datecut		@date => date cut stock 
	// 	
	function total_start_billReceive($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_receivedetail.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillReceive($arrayset)
			->where('date(retail_receive.date_starts) <', $array['datecut'])
			->where('if(retail_receivedetail.list_id is not null,retail_receivedetail.list_id =' . $array['item'] . ',retail_receivedetail.prolist_id =' . $array['item'] . ')', null, false);
		
		if($array['datestart']){
			$sql->where('date(retail_receive.date_starts) >=', $array['datestart']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$rowqty = $row->rtd_qty;

			if($array['datestart']){
				$sqlset = $this->mdl_retailstock->sqlStock()
				->where('retail_stock.date_cut',$array['datestart']);
				$qset = $sqlset->get();
				$numset = $qset->num_rows();
				if($numset){
					$rowset = $qset->row();
					$total = $rowset->TOTAL;
					$rowqty = $total + $row->rtd_qty;
				}
				
			}else{
				$rowqty = $row->rtd_qty;
			}

			$result = array(
				'num'		=> $num,
				'row'		=> $rowqty,
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								date		@date => date select ,
	// 								datecut		@date => date cut stock ,
	// 	
	function result_bill_start($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'date(retail_bill.date_starts) as rt_date_starts,
						retail_billdetail.quantity as rtd_qty,
						retail_billdetail.prolist_id as rtd_pid,
						retail_billdetail.list_id as rtd_lid,'
		);
		$sql = $this->mdl_retailstock->sqlBillOrderDate($arrayset)
			->where('date(retail_bill.date_starts) >=', $array['datecut']);
		if ($array['date']) {
			$sql->where('date(retail_bill.date_starts) <', $array['date']);
		}
		if ($array['month']) {
			$sql->where('month(retail_bill.date_starts)', $array['month']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			
			foreach($q->result() as $row){
				$data[] = array(
					'date_starts'	=> $row->rt_date_starts,
					'pid'	=> $row->rtd_pid,
					'lid'	=> $row->rtd_lid,
					'qty'	=> $row->rtd_qty
				);
			}

			$result = array(
				'num'		=> $num,
				'result'	=> $data
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								date		@date => date select ,
	// 								datecut		@date => date cut stock ,
	// 	
	function result_issue_start($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'date(retail_issue.date_starts) as rt_date_starts,
						retail_issue.quantity as rtd_qty,
						retail_issue.prolist_id as rtd_pid,
						retail_issue.list_id as rtd_lid,'
		);
		$sql = $this->mdl_retailstock->sqlBillIssue($arrayset)
			->where('date(retail_issue.date_starts) >=', $array['datecut']);
		if ($array['date']) {
			$sql->where('date(retail_issue.date_starts) <', $array['date']);
		}
		if ($array['month']) {
			$sql->where('month(retail_issue.date_starts)', $array['month']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {

			foreach($q->result() as $row){
				$data[] = array(
					'date_starts'	=> $row->rt_date_starts,
					'pid'	=> $row->rtd_pid,
					'lid'	=> $row->rtd_lid,
					'qty'	=> $row->rtd_qty
				);
			}

			$result = array(
				'num'		=> $num,
				'result'	=> $data
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								date		@date => date select ,
	// 								datecut		@date => date cut stock ,
	// 	
	function result_receive_start($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'date(retail_receive.date_starts) as rt_date_starts,
						retail_receivedetail.quantity as rtd_qty,
						retail_receivedetail.prolist_id as rtd_pid,
						retail_receivedetail.list_id as rtd_lid,'
		);
		$sql = $this->mdl_retailstock->sqlBillReceive($arrayset)
			->where('date(retail_receive.date_starts) >=', $array['datecut']);
		if ($array['date']) {
			$sql->where('date(retail_receive.date_starts) <', $array['date']);
		}
		if ($array['month']) {
			$sql->where('month(retail_receive.date_starts)', $array['month']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {

			foreach($q->result() as $row){
				$data[] = array(
					'date_starts'	=> $row->rt_date_starts,
					'pid'	=> $row->rtd_pid,
					'lid'	=> $row->rtd_lid,
					'qty'	=> $row->rtd_qty
				);
			}

			$result = array(
				'num'		=> $num,
				'result'	=> $data
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								date		@date => date select ,
	// 	
	function total_billOrder_starts($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_billdetail.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillOrderDate($arrayset)
			->where('date(retail_bill.date_starts) >=', $array['datecut'])
			->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id =' . $array['item'] . ',retail_billdetail.prolist_id =' . $array['item'] . ')', null, false);
		if ($array['date']) {
			$sql->where('date(retail_bill.date_starts) <', $array['date']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								date		@date => date select ,
	// 								datecut		@date => date stockcut working 
	// 	
	function total_billIssue_starts($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_issue.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillIssue($arrayset)
			->where('date(retail_issue.date_starts) >=', $array['datecut'])
			->where('if(retail_issue.list_id is not null,retail_issue.list_id =' . $array['item'] . ',retail_issue.prolist_id =' . $array['item'] . ')', null, false);
		if ($array['date']) {
			$sql->where('date(retail_issue.date_starts) <', $array['date']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								date		@date => date select ,
	// 								datecut		@date => date stockcut working 
	// 	
	function total_billReceive_starts($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_receivedetail.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillReceive($arrayset)
			->where('date(retail_receive.date_starts) >=', $array['datecut'])
			->where('if(retail_receivedetail.list_id is not null,retail_receivedetail.list_id =' . $array['item'] . ',retail_receivedetail.prolist_id =' . $array['item'] . ')', null, false);
		if ($array['date']) {
			$sql->where('date(retail_receive.date_starts) <', $array['date']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								date		@date => date select 
	// 	
	function result_bill_today($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'retail_billdetail.quantity as rtd_qty,retail_billdetail.prolist_id as rtd_pid,retail_billdetail.list_id as rtd_lid,'
		);
		$sql = $this->mdl_retailstock->sqlBillOrderDate($arrayset)
			->where('date(retail_bill.date_starts)', $array['date']);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			
			foreach($q->result() as $row){
				$data[] = array(
					'pid'	=> $row->rtd_pid,
					'lid'	=> $row->rtd_lid,
					'qty'	=> $row->rtd_qty
				);
			}

			$result = array(
				'num'		=> $num,
				'result'	=> $data
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								date		@date => date select ,
	// 	
	function result_issue_today($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'retail_issue.quantity as rtd_qty,retail_issue.prolist_id as rtd_pid,retail_issue.list_id as rtd_lid,'
		);
		$sql = $this->mdl_retailstock->sqlBillIssue($arrayset)
			->where('date(retail_issue.date_starts)', $array['date']);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {

			foreach($q->result() as $row){
				$data[] = array(
					'pid'	=> $row->rtd_pid,
					'lid'	=> $row->rtd_lid,
					'qty'	=> $row->rtd_qty
				);
			}

			$result = array(
				'num'		=> $num,
				'result'	=> $data
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								date		@date => date select ,
	// 	
	function result_receive_today($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'retail_receivedetail.quantity as rtd_qty,retail_receivedetail.prolist_id as rtd_pid,retail_receivedetail.list_id as rtd_lid,'
		);
		$sql = $this->mdl_retailstock->sqlBillReceive($arrayset)
			->where('date(retail_receive.date_starts)', $array['date']);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			
			foreach($q->result() as $row){
				$data[] = array(
					'pid'	=> $row->rtd_pid,
					'lid'	=> $row->rtd_lid,
					'qty'	=> $row->rtd_qty
				);
			}

			$result = array(
				'num'		=> $num,
				'result'	=> $data
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								date		@date => date select 
	// 	
	function total_billOrder_today($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_billdetail.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillOrderDate($arrayset)
			->where('date(retail_bill.date_starts)', $array['date'])
			->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id =' . $array['item'] . ',retail_billdetail.prolist_id =' . $array['item'] . ')', null, false);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								date		@date => date select ,
	// 								datecut		@date => date stockcut working 
	// 	
	function total_billIssue_today($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_issue.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillIssue($arrayset)
			->where('date(retail_issue.date_starts)', $array['date'])
			->where('if(retail_issue.list_id is not null,retail_issue.list_id =' . $array['item'] . ',retail_issue.prolist_id =' . $array['item'] . ')', null, false);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								date		@date => date select ,
	// 								datecut		@date => date stockcut working 
	// 	
	function total_billReceive_today($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_receivedetail.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillReceive($arrayset)
			->where('date(retail_receive.date_starts)', $array['date'])
			->where('if(retail_receivedetail.list_id is not null,retail_receivedetail.list_id =' . $array['item'] . ',retail_receivedetail.prolist_id =' . $array['item'] . ')', null, false);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								date		@date => date select 
	// 								datecut		@date => date stockcut working 
	// 	
	function total_billOrder($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_billdetail.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillOrderDate($arrayset)
			->where('date(retail_bill.date_starts) >=', $array['datecut'])
			->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id =' . $array['item'] . ',retail_billdetail.prolist_id =' . $array['item'] . ')', null, false);
		if ($array['date']) {
			$sql->where('date(retail_bill.date_starts) <=', $array['date']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								date		@date => date select ,
	// 								datecut		@date => date stockcut working 
	// 	
	function total_billIssue($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_issue.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillIssue($arrayset)
			->where('date(retail_issue.date_starts) >=', $array['datecut'])
			->where('if(retail_issue.list_id is not null,retail_issue.list_id =' . $array['item'] . ',retail_issue.prolist_id =' . $array['item'] . ')', null, false);
		if ($array['date']) {
			$sql->where('date(retail_issue.date_starts) <=', $array['date']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								date		@date => date select ,
	// 								datecut		@date => date stockcut working 
	// 	
	function total_billReceive($array)
	{
		//	setting
		$result = "";
		$arrayset = array(
			'query'	=> 'sum(retail_receivedetail.quantity) as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillReceive($arrayset)
			->where('date(retail_receive.date_starts) >=', $array['datecut'])
			->where('if(retail_receivedetail.list_id is not null,retail_receivedetail.list_id =' . $array['item'] . ',retail_receivedetail.prolist_id =' . $array['item'] . ')', null, false);
		if ($array['date']) {
			$sql->where('date(retail_receive.date_starts) <=', $array['date']);
		}

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'row'		=> $q->row(),
				'result'	=> $q->result()
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@text => product id ,
	// 								datecut		@date => date stockcut working 
	// 	
	function total_stock($array)
	{
		//	setting
		$result = "";

		$total_bill_order = $this->mdl_retailstock->total_start_billOrder($array);
		$total_bill_issue = $this->mdl_retailstock->total_start_billIssue($array);
		$total_bill_receive = $this->mdl_retailstock->total_start_billReceive($array);
		/* echo "<pre>";
		echo "bill";
		print_r($total_bill_order);
		echo "issue";
		print_r($total_bill_issue);
		echo "receive";
		print_r($total_bill_receive);
		echo "<pre>"; */

		$arraycal = array(
			'total_bill'	=> $total_bill_order['row'],
			'total_issue'	=> $total_bill_issue['row'],
			'total_receive'	=> $total_bill_receive['row']
		);
		$total = $this->mdl_retailstock->cal_stock($arraycal);

		if ($total) {
			$result = $total;
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								total_bill			@int => total bill order ,
	// 								total_issue			@int => total bill issue ,
	// 								total_receive		@int => total bill receive 
	// 	
	function cal_stock($array)
	{
		$total_bill = 0;
		$total_issue = 0;
		$total_receive = 0;

		($array['total_bill'] ? $total_bill = $array['total_bill'] : true);
		($array['total_issue'] ? $total_issue = $array['total_issue'] : true);
		($array['total_receive'] ? $total_receive = $array['total_receive'] : true);
		
		$result = $total_receive - ($total_bill + $total_issue);
		// echo $total_receive."-(".$total_bill."+".$total_issue.") = ".$result;
		/* if($result < 0){
			$result = "";
		} */

		return $result;
	}

	//	@param	itemid @array = product id
	//	@param	datecut @array = date stockcut
	// 	
	function find_stockItemCut($itemid, $datecut)
	{
		//	setting
		$result = "";
		$sql = $this->db->select('*')
			->from('retail_stock')
			->where('retail_stock.retail_productlist_id', $itemid)
			->where('retail_stock.date_cut', $datecut)
			->order_by('retail_stock.id','desc');
			// ->where('if(retail_stock.date_end is not null, retail_stock.date_end < '.$this->set['datenow'].',retail_stock.date_starts <= '.$this->set['datenow'].')',null,false);
			// ->where('retail_stock.status', 1);
		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$result = array(
				'num'		=> $num,
				'result'	=> $q->row()
			);
		}

		return $result;
	}

	//	@param	itemid @array = product id
	//	@param	datecut @array = date stockcut
	// 	
	function find_stockItemCutquery($datecut)
	{
		//	setting
		$result = "";
		$sql = $this->db->select('retail_stock.retail_productlist_id as rt_pid,retail_stock.total as rt_qty')
			->from('retail_stock')
			->where('retail_stock.date_cut', $datecut)
			->group_by('retail_stock.retail_productlist_id')
			->order_by('retail_stock.id','desc');
			// ->where('if(retail_stock.date_end is not null, retail_stock.date_end < '.$this->set['datenow'].',retail_stock.date_starts <= '.$this->set['datenow'].')',null,false);
			// ->where('retail_stock.status', 1);
		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {

			foreach($q->result() as $row){
				$data[] = array(
					'pid'	=> $row->rt_pid,
					'qty'	=> $row->rt_qty
				);
			}

			$result = array(
				'num'		=> $num,
				'result'	=> $data
			);
		}

		return $result;
	}

	//	@param	array @array = array[ 
	// 								itemid		@int => product id ,
	// 								stockid		@int => stock id ,
	// 								datecut		@date => date cut stock ,
	// 								status		@int => status stock ,
	// 								date_end	@date => date end stock
	// 	
	function check_stockDel($array)
	{
		//	setting
		$stage_delerror = 0;		//	status delete item error 1 = true , 0 = false
		$txt = '';

		$item_check_del = $array['itemid'];
		$item_check_id = $array['stockid'];
		$item_check_status = $array['status'];
		$item_check_date_end = $array['date_end'];

		/**
		 * * ค้นหาบิลที่มีการสั่งซื้อสินค้านี้
		 */
		$arrayquery = array(
			'query' => 'retail_billdetail.PROLIST_ID as rt_prolist_id,retail_billdetail.LIST_ID as rt_list_id',
		);
		$sqlproductbill = $this->mdl_retailstock->sqlBillOrderDate($arrayquery)
			->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id =' . $item_check_del . ',retail_billdetail.prolist_id =' . $item_check_del . ')', null, false)
			->where('date(retail_bill.date_starts) >=', $array['datecut'])
			// ->where('date(retail_bill.date_starts) <=', $this->set['datenow']);
			->where('date(retail_bill.date_starts)', $this->set['datenow']);
		$numproductbill = $sqlproductbill->count_all_results(null, false);
		$qproductbill = $sqlproductbill->get();
		if ($numproductbill) {
			$stage_delerror = 1;
			$txt = 'มีการขาย';
		}
		// echo "order : ".$numproductbill."<br>";

		/**
		 * * ค้นหาจำนวนเหลือบน stock
		 */
		$arraytotal = array(
			'item'		=> $item_check_del,
			'datestart'		=> $array['datebefore'],
			'datecut'	=> $array['datecut']
		);
		
		$total_bill_order = $this->mdl_retailstock->total_start_billOrder($arraytotal);
		$total_bill_issue = $this->mdl_retailstock->total_start_billIssue($arraytotal);
		$total_bill_receive = $this->mdl_retailstock->total_start_billReceive($arraytotal);

		$arraycal = array(
			'total_bill'	=> $total_bill_order['row'],
			'total_issue'	=> $total_bill_issue['row'],
			'total_receive'	=> $total_bill_receive['row']
		);
		$total = $this->mdl_retailstock->cal_stock($arraycal);

		if ($total > 0) {
			$stage_delerror = 2;
			$txt = 'มีจำนวนเหลือใน stock '.$total;
		}

		// echo "total : ".$total."<br>";
/* 		echo $stage_delerror." :: st";
exit; */
		/**
		 * * ลบการแสดงผลของสินค้านั้นหากผ่านเงื่อนไขการตรวจสอบ
		 */
		if ($stage_delerror < 1) {
			$dataupdate = array(
				'status'	=> 0,
				'date_end'	=> date('Y-m-d H:i:s'),
				'user_end'	=> $this->session->userdata('useradminid')
			);
			$this->db->where('retail_stock.id', $item_check_id);
			$this->db->update('retail_stock', $dataupdate);
		}else{
			/**
			 * * ถ้าสินค้าที่ปิดไป เกิดมีการขาย หรือ จำนวนขึ้นมาใน stock
			 */
			if($item_check_status == 0){
				/**
				 * * ถ้าวันปิดสินค้าตรงกับวันปัจจุบันในระบบ stock
				 */
				if($item_check_date_end && $item_check_date_end == $this->set['datenow'] ){
					$dataupdate = array(
						'status'	=> 1,
						'date_end'	=> null
					);
					$this->db->where('retail_stock.id', $item_check_id);
					$this->db->update('retail_stock', $dataupdate);
				}else{
					/**
					 * * ถ้าวันปิดสินค้าไม่ตรงกับวันปัจจุบัน
					 */
					/* $arrayset = array(
						'item'			=> $item_check_del,
						'datecut'		=> $array['datecut']
					);
					$total = $this->mdl_retailstock->find_total_stockproduct($arrayset);

					$insert = array(
						'retail_productlist_id' 	=> $item_check_del,
						'total' 					=> get_valueNullToNull($total),
						'date_cut' 					=> $array['datecut'],
						'date_starts' 				=> $this->set['datenow'],
						'user_starts' 				=> $this->session->userdata('useradminid')
					);
					echo "stockมี insert :".$item_check_del;
					$this->db->insert('retail_stock', $insert); */

				}
			}
		}

		$result = array(
			'error_code'	=> $stage_delerror,
			'txt'			=> $txt
		);
		
		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@int => product id ,
	// 								date		@date => date now ,
	// 								datecut		@date => date cut stock ,
	// 
	function find_total_stockproduct($array){
		//	setting
		$item = $array['item'];
		$date = $array['date'];
		$datecut = $array['datecut'];
		$total = 0;

		$total_billOrder = $this->mdl_retailstock->total_billOrder($array);
		$total_billIssue = $this->mdl_retailstock->total_billIssue($array);
		$total_billReceive = $this->mdl_retailstock->total_billReceive($array);
		
		$arraytotal = array(
			'total_bill'	=> $total_billOrder['row']->rtd_qty,
			'total_issue'	=> $total_billIssue['row']->rtd_qty,
			'total_receive'	=> $total_billReceive['row']->rtd_qty
		);

		$total = $this->mdl_retailstock->cal_stock($arraytotal);

		$result = array(
			'total'			=> get_valueNullTozero($total),
			// 'total'			=> $total_billReceive['row']->rtd_qty." - (".$total_billOrder['row']->rtd_qty."+".$total_billIssue['row']->rtd_qty.")"
		);

		return $result;
	}

	//	@param	array @array = array[ 
	// 								item		@int => product id ,
	// 								date		@date => date now ,
	// 								datecut		@date => date cut stock ,
	// 
	function find_total_stockproductstarts($array){
		//	setting
		$item = $array['item'];
		$date = $array['date'];
		$datecut = $array['datecut'];
		$total = 0;

		$total_billOrder = $this->mdl_retailstock->total_billOrder_starts($array);
		$total_billIssue = $this->mdl_retailstock->total_billIssue_starts($array);
		$total_billReceive = $this->mdl_retailstock->total_billReceive_starts($array);
		
		$arraytotal = array(
			'total_bill'	=> $total_billOrder['row']->rtd_qty,
			'total_issue'	=> $total_billIssue['row']->rtd_qty,
			'total_receive'	=> $total_billReceive['row']->rtd_qty
		);
		$total = $this->mdl_retailstock->cal_stock($arraytotal);

		$result = array(
			'total'			=> get_valueNullTozero($total),
			// 'total'			=> $total_billReceive['row']->rtd_qty." - (".$total_billOrder['row']->rtd_qty."+".$total_billIssue['row']->rtd_qty.")"
		);

		return $result;
	}

	//	@param	array @array = array[ 
	// 								id			@int => stock id ,
	// 								total		@int => stock total ,
	//
	function update_ProductOnline($array) {
		if ($array['id']) {
			$dataupdate = array(
				'total'			=> $array['total'],
				'status'		=> 1,
				'date_end'		=> null,
				'user_starts'	=> $this->session->userdata('useradminid')
			);
			$this->db->where('retail_stock.id', $array['id']);
			$this->db->update('retail_stock', $dataupdate);
		}
	}
	
	//	@param	date 	@date = date now
	//	@param	item 	@int = item stock
	//
	function get_billorderDetail() {
		$input = filter_input_array(INPUT_GET);

		//	setting
		$date = $input['date'];
		$item = $input['item'];

		$result = "";
		$arrayset = array(
			'query'	=> 'retail_bill.code as rtd_code,retail_bill.name as rtd_name,retail_productlist.name_th as rtd_product_name,retail_billdetail.quantity as rtd_qty'
		);
		$sql = $this->mdl_retailstock->sqlBillOrderDate($arrayset)
			->join('retail_productlist', 'if(retail_billdetail.list_id is not null,retail_productlist.id = retail_billdetail.list_id,retail_productlist.id = retail_billdetail.prolist_id)','left', false)
			->where('date(retail_bill.date_starts)', $date)
			->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id =' . $item . ',retail_billdetail.prolist_id =' . $item . ')', null, false);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'result'	=> $q->result()
			);
		}

		$return = json_encode($result);

		return $return;
	}

	//	@param	date 	@date = date now
	//	@param	item 	@int = item stock
	//
	function get_receiveDetail() {
		$input = filter_input_array(INPUT_GET);

		//	setting
		$date = $input['date'];
		$item = $input['item'];

		$result = "";
		$arrayset = array(
			'query'	=> '
			retail_receive.code as rtd_code,
			retail_receive.is_bill_name as rtd_is_name,
			retail_receive.sp_bill_name as rtd_sp_name,
			retail_receive.billtype as rtd_type,
			retail_productlist.name_th as rtd_product_name,
			retail_receivedetail.quantity as rtd_qty
			'
		);
		$sql = $this->mdl_retailstock->sqlBillReceive($arrayset)
			->join('retail_productlist', 'if(retail_receivedetail.list_id is not null,retail_productlist.id = retail_receivedetail.list_id,retail_productlist.id = retail_receivedetail.prolist_id)','left', false)
			->where('date(retail_receive.date_starts)', $date)
			->where('if(retail_receivedetail.list_id is not null,retail_receivedetail.list_id =' . $item . ',retail_receivedetail.prolist_id =' . $item . ')', null, false);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'result'	=> $q->result()
			);
		}

		$return = json_encode($result);

		return $return;
	}

	//	@param	date 	@date = date now
	//	@param	item 	@int = item stock
	//
	function get_issueDetail() {
		$input = filter_input_array(INPUT_GET);

		//	setting
		$date = $input['date'];
		$item = $input['item'];

		$result = "";
		$arrayset = array(
			'query'	=> '
			retail_issue.code as rtd_code,
			retail_issue.billto as rtd_name,
			retail_issue.type as rtd_type,
			retail_productlist.name_th as rtd_product_name,
			retail_issue.quantity as rtd_qty
			'
		);
		$sql = $this->mdl_retailstock->sqlBillIssue($arrayset)
			->join('retail_productlist', 'if(retail_issue.list_id is not null,retail_productlist.id = retail_issue.list_id,retail_productlist.id = retail_issue.prolist_id)','left', false)
			->where('date(retail_issue.date_starts)', $date)
			->where('if(retail_issue.list_id is not null,retail_issue.list_id =' . $item . ',retail_issue.prolist_id =' . $item . ')', null, false);

		$q = $sql->get();
		$num = $q->num_rows();
		if ($num) {
			$row = $q->row();
			$result = array(
				'num'		=> $num,
				'result'	=> $q->result()
			);
		}

		$return = json_encode($result);

		return $return;
	}

	/**
	 * ===========================================================================
	 * QUERY INFORMATION
	 * ===========================================================================
	 */
	//	@param	array @array = array[ 
	// 								query		@text => query select ,
	// 								datecut		@date => date cut stock ,
	// 	
	function sqlProductlistOff($array)
	{
		return $this->db->select($array['query'])
			->from('retail_productlist')
			->join('retail_stock', 'retail_productlist.id=retail_stock.retail_productlist_id')
			->where('retail_productlist.promain_id not in(6,12,14,15,16)')    //  14,15,16 dryeage
			->where('retail_stock.date_starts >=', $array['datecut'])
			->where('if(retail_stock.date_end is not null,date(retail_stock.date_end) >= "'.$this->set['datenow'].'",retail_stock.date_end is null)',null,false)
			->where('retail_productlist.status', 0);
			// ->where('retail_stock.status', 1);
	}

	//	@param	array @array = array[ 
	// 								query		@text => query select ,
	// 	
	function sqlProductlist($array)
	{
		return $this->db->select($array['query'])
			->from('retail_productlist')
			->where('retail_productlist.promain_id not in(6,12,14,15,16)')    //  14,15,16 dryeage
			->where('retail_productlist.status', 1);
	}

	//	@param	array @array = array[ 
	// 								query		@text => query select ,
	// 	
	function sqlBillOrderDate($array)
	{
		return $this->db->select($array['query'])
			->from('retail_bill')
			->join('retail_billdetail', 'retail_bill.id=retail_billdetail.bill_id', 'left')
			->where('retail_bill.status_complete in (2,5)')
			->where('retail_bill.status', 1)
			->where('retail_billdetail.status', 1);
	}

	//	@param	array @array = array[ 
	// 								query		@text => query select ,
	// 	
	function sqlBillIssue($array)
	{
		return $this->db->select($array['query'])
			->from('retail_issue')
			->where('retail_issue.complete in (0,1,2)')
			// ->where('date(retail_issue.date_starts) <=', $array['date'])
			->where('retail_issue.status', 1);
	}

	//	@param	array @array = array[ 
	// 								query		@text => query select ,
	// 	
	function sqlBillReceive($array)
	{
		return $this->db->select($array['query'])
			->from('retail_receive')
			->join('retail_receivedetail', 'retail_receive.id=retail_receivedetail.receive_id', 'left')
			->where('retail_receive.complete', 2)
			->where('retail_receive.status', 1)
			// ->where('date(retail_receive.date_starts) <=', $array['date'])
			->where('retail_receivedetail.status', 1);
	}

	function sqlStock()
	{
		return $this->db->select('*')
			->from('retail_stock')
			->where('retail_stock.status', 1);
	}

}
