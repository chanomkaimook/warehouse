<?php
ini_set('max_execution_time',0);
ini_set('memory_limit',"100M");

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_excel extends CI_Model {
	public function __construct()
    {
		parent::__construct();
        $this->setting = array(
			'retail_bill'		=> "retail_bill",
			'retail_billdetail'		=> "retail_billdetail"
		);
							
		
	}
	
	//
	//	@param	array		@array = array[ id=>array[codemacitem1,codemacitem2] ]
	//
	function create_bill($array){
		//	setting
		// $retail_billdetail = 'retail_billdetail';
		// $retail_bill = 'retail_bill';
		
		$retail_bill = $this->setting['retail_bill'];
		$retail_billdetail = $this->setting['retail_billdetail'];
		
		$result = array();
		//
		//	array
		if($array){
			$i = 0;
			foreach($array as $key => $row){
				//	generate code
				$code = $this->mdl_excel->gencode();
				$explode = explode(" ",trim($array[$key][0]['Shipping Option']));

				//	delivery form
				$array_delivery_formid = strpos(trim($explode[0]),"SCG");
				if($array_delivery_formid !== false){
					$delivery_formid = 5;
				}
				
				$array_delivery_formid = strpos(trim($explode[0]),"Inter");
				if($array_delivery_formid !== false){
					$delivery_formid = 6;
				}
				
				$array_delivery_formid = strpos(trim($explode[0]),"Food");
				if($array_delivery_formid !== false){
					$delivery_formid = 6;
				}

				$array_delivery_formid = strpos(trim($explode[0]),"DHL");
				if($array_delivery_formid !== false){
					$delivery_formid = 4;
				}
				
				$array_delivery_formid = strpos(trim($explode[0]),"Kerry");
				if($array_delivery_formid !== false){
					$delivery_formid = 1;
				}

				//	methodorder
				$methodorder_id = 2;

				//	customer
				$name = trim($array[$key][0]['Customer Name']);

				//	address
				$phone_number = substr(trim($array[$key][0]['Customer Phone']),1);
				$address = trim($array[$key][0]['Customer Address']);
				$substr_address = substr($address,-5);
				$zipcode = $substr_address;

				//	price total
				$total_price = trim($array[$key][0]['Subtotal']);
				$delivery_fee = trim($array[$key][0]['Shipping Cost']);
				$discount_price = trim($array[$key][0]['Discount']);
				$net_total = trim($array[$key][0]['Total']);

				$provider = trim($array[$key][0]['Payment Provider']);

				//	transfer paid
				$array_paidat = trim($array[$key][0]['Paid At']);
				$explode_paidat = explode(" ",$array_paidat);
				$paidat = $explode_paidat[0]." ".$explode_paidat[1];

				//	transfer amount
				$transfered_amount = trim($array[$key][0]['Paid Amount']);

				//	transfer remark
				$transfered_remark = trim($array[$key][0]['Note']);

				//
				$transfered_banik_id = null;
				$transfered_daytime = null;
				// $transfered_amount = null;
				$transfered_remark = null;
				
				if($provider == 'scb'){
					$transfered_banik_id = 1;
					$transfered_daytime = $paidat;
					$transfered_amount = $transfered_amount;
					$transfered_remark = $transfered_remark;
					
					$status_complete = 2;
					$status_approve1 = 1;
				}else{
					$status_complete = 5;
					$status_approve1 = 0;
				}
				
				$status_complete = 2;
				$status_approve1 = 1;

				//bill status
				$billstatus = "T";
				if($provider == 'cod'){
					$billstatus = "C";
				}
				
				$ref = $key;

				//date starts
				$array_date_starts = trim($array[$key][0]['Created At']);
				$explode_date_starts = explode(" ",$array_date_starts);
				$date_starts = $explode_date_starts[0]." ".$explode_date_starts[1];

				$datainsert = array(
					'code'	=> $code,

					'delivery_formid'	=> $delivery_formid,
					'methodorder_id'	=> $methodorder_id,

					'name'			=> $name,
					'phone_number'		=> $phone_number,
					'address'		=> $address,
					'zipcode'		=> $zipcode,

					'total_price'		=> $total_price,
					'delivery_fee'		=> $delivery_fee,
					'discount_price'	=> $discount_price,
					'net_total'			=> $net_total,

					'transfered_banik_id'	=> $transfered_banik_id,
					'transfered_daytime'	=> $transfered_daytime,
					'transfered_amount'	=> $transfered_amount,
					'transfered_remark'	=> $transfered_remark,

					'status_approve1'	=> $status_approve1,
					'status_approve2'	=> 1,
					'status_complete'	=> $status_complete,
					'billstatus'	=> $billstatus,
					'ref'			=> $ref,
					'date_starts'	=> date('Y-m-d H:i:s'),
					'user_starts'	=> '00041',	//	page365
				);
				$this->db->insert($retail_bill,$datainsert);
				$id = $this->db->insert_id();
				if($id){
					$i++;	// count 
					foreach($row as $keydetail => $subdetail){
						$array_product = $subdetail['Item Code'];

						//	แบบเทียบจาก codemac
						$sqlgroup = $this->db->select('*')
						->from('retail_productlist')
						->where('id',$array_product);
						// ->where('codemac',$array_product);
						$qgroup = $sqlgroup->get();
						$numgroup = $qgroup->num_rows();
						if($numgroup){
							$rowgroup = $qgroup->row();

							if($rowgroup->LIST_ID){
								$list_id = $rowgroup->LIST_ID;
							}else{
								$list_id = null;
							}

							$datainsertdetail = array(
								'code'	=> $code,
								'bill_id'	=> $id,

								'promain_id'	=> $rowgroup->PROMAIN_ID,
								'prolist_id'	=> $rowgroup->ID,
								'list_id'		=> $list_id,
								'quantity'		=> $subdetail['Item Qty'],
								'total_price'	=> get_valueNullTozero($subdetail['Item Subtotal']),

								'date_starts'	=> date('Y-m-d H:i:s'),
								'user_starts'	=> '00041',	//	page365
							);
							$this->db->insert($retail_billdetail,$datainsertdetail);
						}

					}
					
				}

				
			}	/* END INSERT RETAIL_BILL */

			// ============== Log_Detail ============== //
			$log_query = $this->db->last_query();
			$last_id = $this->session->userdata('log_id');
			$detail = "Insert dump bill page365 Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
			$type = "Insert";
			$arraylog = array(
				'log_id'  		 => $last_id,
				'detail'  		 => $detail,
				'logquery'       => $log_query,
				'type'     	 	 => $type,
				'date_starts'    => date('Y-m-d H:i:s')
			);
			updateLog($arraylog);

			$result = array(
				'total'	=> $i
			);
		}

		return $result;
		
	}

	function gencode(){
		$retail_bill = $this->setting['retail_bill'];
		$retail_billdetail = $this->setting['retail_billdetail'];
		
		$this->db->select($retail_bill.'.CODE AS codemax');
		$this->db->from($retail_bill);
		$this->db->order_by($retail_bill.'.ID', 'DESC');  
		$Query_Max = $this->db->get();
		$num = $Query_Max->num_rows($Query_Max);
		$RowMax = $Query_Max->row();
		if($num > 0){
			$str = explode(" ", $RowMax->codemax);
			$Code = explode("_", $str[1]);
			$codeDB = '';
			$dateY = (date('Y') + 543);
			if($Code[1] == $dateY){
				$count = $Code[0] + 1;
				$codeDB = $str[0].' '.$count.'_'.$Code[1];
			} else {
				$Code[0] = 0;
				$count = $Code[0] + 1;
				$codeDB = $str[0].' '.$count.'_'.$dateY;
			}
		} else {
			$dateY = (date('Y') + 543);
			$codeDB = 'Jerky 1_'.$dateY;
		}

		return $codeDB;
	}
}
?>