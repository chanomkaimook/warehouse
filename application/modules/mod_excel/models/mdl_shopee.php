<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_shopee extends CI_Model {
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
				$textcode = trim($array[$key][0]['หมายเลขคำสั่งซื้อ']);

				//	delivery form
				$array_delivery_formid = strpos(trim($array[$key][0]['ตัวเลือกการจัดส่ง']),"Seller");
				if($array_delivery_formid !== false){
					$delivery_formid = 6;
				}

				$array_delivery_formid = strpos(trim($array[$key][0]['ตัวเลือกการจัดส่ง']),"DHL");
				if($array_delivery_formid !== false){
					$delivery_formid = 9;
				}
				
				$array_delivery_formid = strpos(trim($array[$key][0]['ตัวเลือกการจัดส่ง']),"Shopee");
				if($array_delivery_formid !== false){
					$delivery_formid = 7;
				}

				$array_delivery_formid = strpos(trim($array[$key][0]['ตัวเลือกการจัดส่ง']),"Kerry");
				if($array_delivery_formid !== false){
					$delivery_formid = 1;
				}

				//	methodorder
				$methodorder_id = 6;

				//	customer
				$name = trim($array[$key][0]['ชื่อผู้รับ']);

				//	address
				$phone_number = substr(trim($array[$key][0]['หมายเลขโทรศัพท์']),1);
				$address = trim($array[$key][0]['ที่อยู่ในการจัดส่ง']);
				$substr_address = substr($address,-5);
				$zipcode = $substr_address;

				//	price total
				$total_price = trim($array[$key][0]['ราคาขายสุทธิ']);
				$delivery_fee = 0.00;
				$discountpricebefore = trim($array[$key][0]['Transaction Fee']);

				$shor_money = 0 - $discountpricebefore;	// ให้เลขติดลบ
				// $net_total = $total_price + $shor_money;
				$net_total = 0;

				$billstatus = "C";

				//
				$transfered_banik_id = null;
				$transfered_daytime = null;
				$transfered_amount = null;
				$transfered_remark = null;
				
				$ref = trim($array[$key][0]['หมายเลขคำสั่งซื้อ']);

				//date starts
				$array_date_starts = trim($array[$key][0]['Created At']);
				$explode_date_starts = explode(" ",$array_date_starts);
				$date_starts = $explode_date_starts[0]." ".$explode_date_starts[1];

				$datainsert = array(
					'code'			=> $code,
					'textcode'		=> $textcode,

					'delivery_formid'	=> $delivery_formid,
					'methodorder_id'	=> $methodorder_id,

					'name'			=> $name,
					'phone_number'		=> $phone_number,
					'address'		=> $address,
					'zipcode'		=> $zipcode,

					'total_price'		=> $total_price,
					'delivery_fee'		=> $delivery_fee,
					'shor_money'		=> $shor_money,
					'net_total'			=> $net_total,

					'transfered_banik_id'	=> $transfered_banik_id,
					'transfered_daytime'	=> $transfered_daytime,
					'transfered_amount'	=> $transfered_amount,
					'transfered_remark'	=> $transfered_remark,

					'status_approve1'	=> 1,
					'status_approve2'	=> 1,
					'status_complete'	=> 2,
					'billstatus'	=> $billstatus,
					'ref'			=> $ref,
					'date_starts'	=> date('Y-m-d H:i:s'),
					'user_starts'	=> '00042',	//	shopee
				);
				$this->db->insert($retail_bill,$datainsert);
				$id = $this->db->insert_id();
				if($id){
					$i++;	// count 

					$totalprice = 0;
					foreach($row as $keydetail => $subdetail){
						$array_product = $subdetail['เลขอ้างอิง SKU (SKU Reference No.)'];

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
							
							$totalprice = $totalprice + $subdetail['ราคาขายสุทธิ'];
							
							$datainsertdetail = array(
								'code'	=> $code,
								'bill_id'	=> $id,

								'promain_id'	=> $rowgroup->PROMAIN_ID,
								'prolist_id'	=> $rowgroup->ID,
								'list_id'		=> $list_id,
								'quantity'		=> $subdetail['จำนวน'],
								'total_price'	=> get_valueNullTozero($subdetail['ราคาขายสุทธิ']),

								'date_starts'	=> date('Y-m-d H:i:s'),
								'user_starts'	=> '00042',	//	shopee
							);
							$this->db->insert($retail_billdetail,$datainsertdetail);
							
						}

					}	//	end insert bill_detail
					
					//	update data

					$nettotal = $totalprice + $shor_money;
					$dataupdate = array(
						'total_price'		=> $totalprice,
						'net_total'			=> $nettotal
					);
					$this->db->where('id',$id);
					$this->db->update($retail_bill,$dataupdate);
					
				}

				// SELECT * FROM `retail_bill` where date(DATE_STARTS) = '2021-010-25' and USER_STARTS = '00042' and status = 1
				// UPDATE `retail_bill` SET `STATUS_COMPLETE` = '3', `STATUS` = '0' WHERE date(DATE_STARTS) = '2021-010-25' and USER_STARTS = '00042' and status = 1
			}	/* END INSERT RETAIL_BILL */

			// ============== Log_Detail ============== //
			$log_query = $this->db->last_query();
			$last_id = $this->session->userdata('log_id');
			$detail = "Insert dump bill shopee Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
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