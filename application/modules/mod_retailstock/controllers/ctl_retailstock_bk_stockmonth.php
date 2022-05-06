<?php

use Mpdf\Tag\I;

defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_retailstock extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('mdl_retailstock');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_retailstock',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid'),
			'mainmenu'		        => 'retail',
			'submenu'		        => 'retailstock',
			'url_begin'		        => $this->uri->segment(1) . "/" . $this->uri->segment(2),
			'datenow'				=> date('Y-m-d')
			// 'datenow'				=> "2022-02-01"
			// 'datenow'				=> $this->input->get('date')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function stock_month()
	{
		if (chkPermiss() == 1) {
			redirect('mod_admin/ctl_login');
		}

		if ($this->input->get('date')) {
			$date = $this->input->get('date');
		} else {
			$date = date('Y-m-d');
		}

		if ($this->input->get('m')) {
			$explode = explode('-', $this->input->get('m'));
			$month = $explode[1];
			$year = $explode[0];
			$totaldays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		} else {
			$month = date('m');
			$year = date('Y');
			$totaldays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		}

		$arrayset = array(
			'date'	=> $date
		);
		$find_date = $this->mdl_retailstock->dateCut($arrayset);
		if ($find_date) {
			$datecut = $find_date['datecut'];
			$datestart = $find_date['datestart'];
		}

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'stock'
		);

		$data['totaldays'] = $totaldays;
		$data['date_cut'] = $datecut;
		$data['get_date'] = $date;
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('stock_month', $data);
	}

	public function stock()
	{
		if (chkPermiss() == 1) {
			redirect('mod_admin/ctl_login');
		}

		if ($this->input->get('date')) {
			$date = $this->input->get('date');
		} else {
			$date = date('Y-m-d');
		}

		$datecut = "";

		/**
		 * * การทำงานของ stock ในการแสดงผลต้องมีเงื่อนไขดังนี้
		 * * - สินค้าทำงาน
		 * * - สินค้ามีการซื้อขาย
		 * 
		 * * ใน 2 เงื่อนไขด้านบน หากมีอันใดอันหนึ่งเป็น true สินค้าจะถูกแสดงบน stock
		 */
		/**
		 * 
		 * todo เริ่มจากการหา รอบวันตัด stock และ วันตั้งต้น เพื่อเป็นค่าวันเริ่มต้น สำหรับคำนวณ
		 * 
		 */
		$arrayset = array(
			'date'	=> $date
		);
		$find_date = $this->mdl_retailstock->dateCut($arrayset);
		if ($find_date) {
			$datecut = $find_date['datecut'];
			$datestart = $find_date['datestart'];

			$item = array();		//	product set id to stock

			/**
			 * 
			 * todo หาสินค้าที่ทำงานอยู่
			 * 
			 */
			$item_on = array();		//	product online
			$arrayquery = array('query' => '*');
			$sqlproduct = $this->mdl_retailstock->sqlProductlist($arrayquery);
			$numproduct = $sqlproduct->count_all_results(null, false);
			$qproduct = $sqlproduct->get();
			if ($numproduct) {
				foreach ($qproduct->result() as $row) {
					$item_on[] = $row->ID;
				}
			}

			/**
			 * 
			 * todo หาสินค้าที่มีการสั่งซื้อ
			 * todo โดยเริ่มหาจาก บิลสั่งซื้อหลังจาก รอบวันตัด stock ล่าสุด จนถึงวันปัจจุบัน (จะไม่หาจากบิลทั้งหมดในระบบ)
			 * 
			 */
			$arrayquery = array(
				'query' => 'retail_billdetail.PROLIST_ID as rt_prolist_id,retail_billdetail.LIST_ID as rt_list_id',
			);
			$sqlproductbill = $this->mdl_retailstock->sqlBillOrderDate($arrayquery)
				->where('date(retail_bill.date_starts) >=', $datecut);
			// ->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id =' . $item_check_del . ',retail_billdetail.prolist_id =' . $item_check_del . ')', null, false)
			// ->where('date(retail_bill.date_starts) <', $this->set['datenow']);
			$numproductbill = $sqlproductbill->count_all_results(null, false);
			$qproductbill = $sqlproductbill->get();
			if ($numproductbill) {
				foreach ($qproductbill->result() as $row) {
					if ($row->rt_list_id) {
						$item_on[] = $row->rt_list_id;
					} else {
						$item_on[] = $row->rt_prolist_id;
					}
				}
			}

			/**
			 * 
			 * todo หาจากการรับเข้า
			 * 
			 */
			$arrayquery = array(
				'query' => 'retail_receivedetail.PROLIST_ID as rt_prolist_id,retail_receivedetail.LIST_ID as rt_list_id',
			);
			$sqlproductreceive = $this->mdl_retailstock->sqlBillReceive($arrayquery)
				->where('date(retail_receive.date_starts) >=', $datecut);
			// ->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id =' . $item_check_del . ',retail_billdetail.prolist_id =' . $item_check_del . ')', null, false)
			// ->where('date(retail_bill.date_starts) <', $this->set['datenow']);
			$numreceive = $sqlproductreceive->count_all_results(null, false);
			$qreceive = $sqlproductreceive->get();
			if ($numreceive) {
				foreach ($qreceive->result() as $row) {
					if ($row->rt_list_id) {
						$item_on[] = $row->rt_list_id;
					} else {
						$item_on[] = $row->rt_prolist_id;
					}
				}
			}

			$item = array_unique($item_on);

			/* echo "<pre>item";
			print_r($item);
			echo "==== insert";
			print_r($datestart);
			echo "</pre>";
			echo "date start = ".$datestart."<br>";
			echo "date cut = ".$datecut."<br>";
			echo "date = ".$date."<br>";
			exit; */

			/**
			 * * ค้นหาจำนวนเหลือบน stock
			 */
			if ($datestart) {
				$setitem_on = implode(',', $item);
				$sqlst = $this->db->select('retail_stock.RETAIL_PRODUCTLIST_ID as productid')
					->from('retail_stock')
					->where('retail_stock.status', 1)
					->where('retail_stock.date_cut >=', $datestart)
					->where('retail_stock.retail_productlist_id not in(' . $setitem_on . ')', null, false)
					->group_by('retail_stock.retail_productlist_id');
				$qst = $sqlst->get();
				$numst = $qst->num_rows();
				if ($numst) {

					foreach ($qst->result() as $rst) {
						$arrayst = array(
							'item'			=> $rst->productid,
							'datestart'		=> $datestart,
							'datecut'		=> $datecut
						);

						$total_bill_order = $this->mdl_retailstock->total_start_billOrder($arrayst);
						$total_bill_issue = $this->mdl_retailstock->total_start_billIssue($arrayst);
						$total_bill_receive = $this->mdl_retailstock->total_start_billReceive($arrayst);

						$arraycal = array(
							'total_bill'	=> $total_bill_order['row'],
							'total_issue'	=> $total_bill_issue['row'],
							'total_receive'	=> $total_bill_receive['row']
						);
						$total = $this->mdl_retailstock->cal_stock($arraycal);

						$insert = array(
							'retail_productlist_id' => $rst->productid,
							'total' 					=> get_valueNullToNull($total),
							'date_cut' 					=> $datecut,
							'date_starts' 				=> $this->set['datenow'],
							'user_starts' 				=> $this->session->userdata('useradminid')
						);
						// echo "stockมี insert :".$val." = ".$total;
						$this->db->insert('retail_stock', $insert);
					}
				}
			}
			//	create value unique

			/* echo "<pre>item";
			print_r($item);
			echo "==== insert";
			print_r($item_on);
			echo "</pre>";
			exit; */

			/**
			 * * ถ้ามีสินค้าที่ต้องนำเข้า stock
			 * 
			 * todo เพิ่มสินค้าเข้าไปในระบบ โดยคำนวณจำนวนทั้งหมด = รับเข้า - (ขาย + เบิก)
			 * todo ระบุวันเริ่มสินค้าเป็นวันปัจจุบัน
			 * 
			 * ! หากมีการย้อนดู stock จะต้องไม่มีการนำเข้าอัพเดตสินค้า (คือ จะไม่มีการเพิ่มตัวสินค้าเข้าไป) เช่น 
			 * ! - เมื่อวานมีข้อมูลสินค้า A ผ่านมาถึงวันนี้ ได้เพิ่มข้อมูลสินค้า B
			 * ! - ในวันนี้สินค้า B จะถูกเพิ่มเข้าระบบ stock
			 * ! - เมื่อย้อนไปดูข้อมูลเมื่อวาน จะต้องมีแต่สินค้า A ซึ่ง B จะต้องไม่ถูกนำเข้าไปในวันเมื่อวาน
			 */
			if ($item && $date == $this->set['datenow']) {

				$datainsert = array();

				foreach ($item as $key => $val) {
					/**
					 * * หาสินค้านี้ว่ามีบนระบบอยู่แล้วหรือไม่
					 * 
					 * todo ตรวจสอบจากวันรอบตัดล่าสุด
					 */
					$chk_stock_item = $this->mdl_retailstock->find_stockItemCut($val, $datecut);
					/* echo "<pre>chk_stock_item";
					print_r($chk_stock_item);
					echo "</pre>======="; */
					// exit;
					/**
					 * * ถ้าสินค้าไม่เคยมีในระบบ stock
					 * 
					 * todo เพิ่มสินค้าเข้าไปในระบบ โดยคำนวณจำนวนทั้งหมด = รับเข้า - (ขาย + เบิก) จากเริ่มต้นจนถึงวันตัดยอดรอบ stock (ยอดรวมจะเป็นของตั้งแต่ ทั้งหมดจนถึงก่อนวันตัดยอด)
					 * todo ระบุวันเริ่มต้นสินค้าเป็นวันปัจจุบัน (เพื่อระบุวันเริ่มแสดงสินค้า)
					 * todo เพราะวันปัจจุบัน เอาไว้คำนวณค่าแสดงอีกที
					 */

					if (!$chk_stock_item) {

						$arrayset = array(
							'item'			=> $val,
							'datestart'		=> $datestart,
							'datecut'		=> $datecut
						);
						$total = $this->mdl_retailstock->total_stock($arrayset);

						$datainsert[] = array(
							'retail_productlist_id' => $val,
							/* 'total_bill_order' 				=> get_valueNullToNull($total_bill_order['row']->rtd_qty),
							'total_bill_issue' 				=> get_valueNullToNull($total_bill_issue['row']->rtd_qty),
							'total_bill_receive' 			=> get_valueNullToNull($total_bill_receive['row']->rtd_qty), */
							'total' 					=> get_valueNullToNull($total),
							'date_cut' 					=> $datecut,
							'date_starts' 				=> $this->set['datenow'],
							'user_starts' 				=> $this->session->userdata('useradminid')
						);
					} else {
						/**
						 * * ////ถ้าสินค้าเคยมีในระบบ ในวันเดียวกัน(วันปัจจุบัน) ในเมื่อเป็นสินค้าที่ต้องทำงานบน stock (จากเงื่อนไขตัวแปรที่ได้มา) จะต้องลบค่า date_end ออก
						 * 
						 * todo date_end คือ ค่าระบุของการปิดสถานะการทำงานของสินค้าใน stock ในวันดังกล่าว
						 * todo เมื่อสินค้ากลับมาทำงาน จะต้องล้างค่า date_end ออก
						 */
						if ($chk_stock_item['result']->STATUS == 0) {
							$date_end = date('Y-m-d', strtotime($chk_stock_item['result']->DATE_END));
							if ($date_end == $this->set['datenow']) {
								$arrayset = array(
									'item'			=> $val,
									'datecut'		=> $datecut
								);
								// $total = $this->mdl_retailstock->total_stock($arrayset);

								$arrayupdate = array(
									'id' 	=> $chk_stock_item['result']->ID,
									'total'	=> $total
								);
								// echo "stockมี update :".$chk_stock_item['result']->ID;
								// $this->mdl_retailstock->update_ProductOnline($arrayupdate);
							} else {

								/**
								 * 
								 * todo แต่ถ้าสินค้านี้ (ที่ถูกปิดการทำงาน) มีช่วงเวลาปิดการทำงานไม่ตรงกับวันปัจจุบัน จะทำการเพิ่มเข้าระบบ stock 
								 * todo เพิ่มเข้า stock โดยกำหนดวันตัดรอบตามระบบ และวันเริ่มสินค้าเป็นวัน ปัจจุบัน (ยอดรวมคำนวณค่าต่างๆจนถึงก่อนวันปัจจุบัน 1 วัน)
								 * 
								 */
								/* $arrayset = array(
									'item'			=> $val,
									'date'			=> $this->set['datenow'],
									'datecut'		=> $datecut
								);

								$total = $this->mdl_retailstock->find_total_stockproduct($arrayset);

								$insert = array(
									'retail_productlist_id' => $val,
									'total' 					=> get_valueNullToNull($total['total']),
									'date_cut' 					=> $datecut,
									'date_starts' 				=> $this->set['datenow'],
									'user_starts' 				=> $this->session->userdata('useradminid')
								);
								echo "stockมี insert :".$val." = ".$total['total'];
								$this->db->insert('retail_stock', $insert); */
							}
						}
					}
				}		//	end foreach จบการเพิ่ม แก้ไข สถานะ stock

				/* echo "<pre>item";
				print_r($item);
				echo "==== insert";
				print_r($datainsert);
				echo "</pre>"; */

				if ($datainsert) {
					$this->db->insert_batch('retail_stock', $datainsert);
				}

				/**
				 * * สินค้าที่มีในระบบ stock ถูกยกเลิกการทำงานในระบบสินค้า
				 * 
				 * todo ต้องตรวจสอบเงื่อนไขก่อน
				 * todo  - ในวันปัจจุบัน สินค้านี้มีการสั่งซื้อ
				 * todo  - ในวันปัจจุบัน สินค้านี้ยังมีจำนวนเหลือบน stock
				 * todo หากเงื่อนไขด้านบนเป็น true อย่างใดอย่างหนึ่ง ระบบ stock ยังคงแสดงรายการสินค้านี้อยู่
				 * todo หากเงื่อนไขด้านบนเป็น false ทั้งคู่ ระบบ stock จะไม่แสดงรายการสินค้านี้ ตั้งแต่วันปัจจุบันนี้เป็นต้นไป
				 * 
				 * ! หากวันปัจจุบันมีการลบสินค้าออกจากระบบ stock เมื่อย้อนกลับไปดูเมื่อวาน รายการสินค้านี้จะยังคงแสดงอยู่บนระบบ
				 */

				$item_off = array();		//	product offline
				($datestart ? $date_before_cut = $datestart : $date_before_cut = $datecut);
				$arrayquery = array('query' => 'retail_stock.total as st_total,retail_stock.retail_productlist_id as st_pro_id,retail_stock.id as st_id,retail_stock.status as st_status,retail_stock.date_end as st_date_end', 'datecut' => $date_before_cut);
				$sqloff = $this->mdl_retailstock->sqlProductlistOff($arrayquery);
				$numoff = $sqloff->count_all_results(null, false);
				$qoff = $sqloff->get();
				// echo "numberoff :".$numoff."<br>";

				if ($numoff) {
					foreach ($qoff->result() as $row) {
						$arrayset = array(
							'datebefore'	=> $datestart,
							'datecut'	=> $datecut,
							'itemid'	=> $row->st_pro_id,
							'stockid'	=> $row->st_id,
							'total'		=> $row->st_total,
							'status'	=> $row->st_status,
							'date_end'	=> date('Y-m-d', strtotime($row->st_date_end))
						);
						$func_del = $this->mdl_retailstock->check_stockDel($arrayset);
						// echo 'item :'.$row->st_pro_id.'<br>';
						// echo $func_del['txt'];
					}
				}
				/* $arraytotal = array(
					'item'		=> $row->p_id,
					'date'		=> $date,
					'datecut'	=> $request['date_cut']
				);
				$start = $this->mdl_retailstock->find_total_stockproductstarts($arraytotal); */

				// exit;
				/* echo "<pre>off<br>";
				print_r($item_off);
				echo "<br>=====";
				print_r($item);
				echo "</pre>"; */
			}	//	end to set stock
		}

		$data = array(
			'mainmenu' 		=> 'retail',
			'submenu' 		=> 'stock'
		);

		$data['date_cut'] = $datecut;
		$data['get_date'] = $date;
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('stock', $data);
	}

	public function stocksetting()
	{
		if (chkPermiss() == 1) {
			redirect('mod_admin/ctl_login');
		}

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> 'stock'
		);

		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('stocksetting', $data);
	}






	//	calculate stock value
	//	@param		dataarray	@array = array result bill
	//							[pid	=> product prolist_id]
	//							[lid	=> product list_id]
	//							[qty	=> quantity]
	//
	//	@param		itemid		@int = product id
	//
	function findscore($dataarray, $itemid)
	{
		$key_pid = array_keys(array_column($dataarray['result'], 'pid'), $itemid);
		$key_lid = array_keys(array_column($dataarray['result'], 'lid'), $itemid);

		$key_array = array_merge($key_pid, $key_lid);
		$totalbill = 0;
		if (count($key_array)) {
			foreach ($key_array as $key => $value) {
				$totalbill += $dataarray['result'][$value]['qty'];
			}
		}
		$total_billOrdert = $totalbill;

		if ($total_billOrdert) {
			$result = $total_billOrdert;
		}

		return $result;
	}
	function fetch_productMonth()
	{
		$fetch_data = $this->mdl_retailstock->make_datatables();
		$data = array();
		$index = 1;

		$request = $_REQUEST;

		($request['date'] ? $date = $request['date'] : $date = date('Y-m'));

		$arrayset = array(
			'date'	=> ($request['date'] ? $request['date'] : date('Y-m-d'))
		);

		//	total starts
		$result_cut = $this->mdl_retailstock->find_stockItemCutquery($request['date_cut']);


		//test

		// echo "date cut ".$request['date_cut'];

		$month = ($request['date'] ? date('m', strtotime($request['date'])) : date('m'));
		$year = ($request['date'] ? date('Y', strtotime($request['date'])) : date('Y'));
		$arraystart = array(
			'datecut'	=> $request['date_cut'],
			'month'		=> $month
		);

		$result_bill_start = $this->mdl_retailstock->result_bill_start($arraystart);
		$result_issue_start = $this->mdl_retailstock->result_issue_start($arraystart);
		$result_receive_start = $this->mdl_retailstock->result_receive_start($arraystart);

		/* echo "<pre>";
		print_r($result_bill_start);
		echo "==================<br>";
		print_r($result_issue_start);
		echo "==================<br>";
		print_r($result_receive_start);
		echo "==================<br>";
		echo "</pre>";
		exit; */

		//	find total days in month
		$totaldays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$data = array();

		$date_begin = ($request['date_begin'] != 'null' ? $request['date_begin'] : 1);
		$date_length = ($request['date_length'] != 'null' ? $request['date_length'] : $totaldays);

		$date_full_begin = $date . "-" . str_pad($date_begin, 2, '0', STR_PAD_LEFT);

		$data_today[] = array();
		//	product in stock this month
		foreach ($fetch_data as $row) {
			$productid = $row->p_id;
			$productname = $row->p_name;
			$datainfo_today = array();

			//	รวมยอด
			$total_bill_net = 0;
			$total_receive_net = 0;
			$total_issue_net = 0;

			// $total_cut = 0;
			if ($result_cut) {
				$key_lid = array_keys(array_column($result_cut['result'], 'pid'), $productid);
				if (count($key_lid)) {
					foreach ($key_lid as $key => $value) {
						//	โชคชัยเจอกี้หมู 60 กรัม
						//	test
						$arraytest = array(
							'item'	=> $productid,
							'date'	=> $date_full_begin,
							'datecut'	=> $request['date_cut'],
						);
						$starts = $this->mdl_retailstock->find_total_stockproductstarts($arraytest);
						/* echo "<pre>";
echo "id = ".$productid."<br>";
echo "cut = ".$arraytest['datecut']."--------".$arraytest['date']."<br>";
echo "cut total = ".$result_cut['result'][$value]['qty']."<br>";
print_r($starts);
echo "</pre>"; */

						// $totalcut = $result_cut['result'][$value]['qty'];

						// $totalcut = $starts['total'];	ถูกในตอนที่เลือกเต็มเดือน

						$totalcut = $starts['total'] + $result_cut['result'][$value]['qty'];
					}
					$total_cut = $totalcut;
				}
			}

			$total_for_length = 0;

			//	ปิดไปก่อน เพราะทำให้จำนวนบน stock ไม่ตรง
			if ($date_begin > 1000 && $date_length) {

				for ($datei = 1; $datei < $date_begin; $datei++) {
					$total_start_bill_net = 0;
					$total_start_receive_net = 0;
					$total_start_issue_net = 0;
					$array_start_bill_result = array();
					$array_start_receive_result = array();
					$array_start_issue_result = array();

					$dateizero = str_pad($datei, 2, '0', STR_PAD_LEFT);
					$date_for = $date . "-" . $dateizero;

					//
					//	รวมยอดบิลก่อนถึงวันปัจจุบัน
					$total_start_bill_start = 0;
					if ($result_bill_start) {
						$array_start_bill_date_today = array_keys(array_column($result_bill_start['result'], 'date_starts'), $date_for);
						if ($array_start_bill_date_today) {
							$arraysub_start_bill_result = array();

							foreach ($array_start_bill_date_today as $key => $value) {
								$arraysub_start_bill_result[] = array(
									'date_starts'	=> $result_bill_start['result'][$value]['date_starts'],
									'pid'			=> $result_bill_start['result'][$value]['pid'],
									'lid'			=> $result_bill_start['result'][$value]['lid'],
									'qty'			=> $result_bill_start['result'][$value]['qty']
								);
							}
							$array_start_bill_result['result'] = $arraysub_start_bill_result;
						}
					}

					//
					//	รวมยอดบิลรับเข้าก่อนถึงวันปัจจุบัน
					$total_start_receive_start = 0;
					if ($result_receive_start) {
						$array_start_receive_date_today = array_keys(array_column($result_receive_start['result'], 'date_starts'), $date_for);
						if ($array_start_receive_date_today) {

							$arraysub_start_receive_result = array();

							foreach ($array_start_receive_date_today as $key => $value) {
								$arraysub_start_receive_result[] = array(
									'date_starts'	=> $result_receive_start['result'][$value]['date_starts'],
									'pid'			=> $result_receive_start['result'][$value]['pid'],
									'lid'			=> $result_receive_start['result'][$value]['lid'],
									'qty'			=> $result_receive_start['result'][$value]['qty']
								);
							}
							$array_start_receive_result['result'] = $arraysub_start_receive_result;
						}
					}

					//
					//	รวมยอดเบิกออกก่อนถึงวันปัจจุบัน
					$total_start_issue_start = 0;
					if ($result_issue_start) {
						$array_start_issue_date_today = array_keys(array_column($result_issue_start['result'], 'date_starts'), $date_for);
						if ($array_start_issue_date_today) {

							$arraysub_start_issue_result = array();

							foreach ($array_start_issue_date_today as $key => $value) {
								$arraysub_start_issue_result[] = array(
									'date_starts'	=> $result_issue_start['result'][$value]['date_starts'],
									'pid'			=> $result_issue_start['result'][$value]['pid'],
									'lid'			=> $result_issue_start['result'][$value]['lid'],
									'qty'			=> $result_issue_start['result'][$value]['qty']
								);
							}
							$array_start_issue_result['result'] = $arraysub_start_issue_result;
						}
					}

					//	หาจำนวนรายการบิลของสินค้าในวันนี้
					if ($array_start_bill_result['result']) {
						$total_start_bill_start = $this->findscore($array_start_bill_result, $productid);
						$total_start_bill_net += $total_start_bill_start;
					}

					//	หาจำนวนรับเข้าของสินค้าในวันนี้
					if ($array_start_receive_result['result']) {
						$total_start_receive_start = $this->findscore($array_start_receive_result, $productid);
						$total_start_receive_net += $total_start_receive_start;
					}

					//	หาจำนวนเบิกออกของสินค้าในวันนี้
					if ($array_start_issue_result['result']) {
						$total_start_issue_start = $this->findscore($array_start_issue_result, $productid);
						$total_start_issue_net += $total_start_issue_start;
					}


					//	find data cut value from data stock
					$arraytotal_start = array(
						'total_bill'	=> $total_start_bill_net,
						'total_issue'	=> $total_start_issue_net,
						'total_receive'	=> $total_start_receive_net
					);
					$net = $this->mdl_retailstock->cal_stock($arraytotal_start);
					/* echo "<pre> date_for = ".$date_for;
					print_r($arraytotal_start);
					echo "</pre>";
					echo "product id : ".$productid." net : ".$net."<br>"; */
					$total_for_length = get_valueNullTozero($total_for_length) + $net;



					/* echo "test".$productid." -- ".$date_for." = ".$totalcut."<br>";
					echo "bill ::".$total_start_bill_net."<br>";
					echo "issue ::".$total_start_issue_net."<br>";
					echo "rece ::".$total_start_receive_net."<br>";
					echo "<pre>";
					// print_r($array_start_bill_result);
					// echo "==================<br>";
					// print_r($array_start_receive_result);
					// echo "==================<br>";
					// print_r($array_start_issue_result);
					// echo "==================<br>";
					echo " total_for_length = ".$total_for_length."<br>";
					echo "</pre>"; */
				}
			}

			//	สำหรับคำนวณกับยอดอื่นๆ
			if ($total_for_length) {
				$totalcut = $totalcut + $total_for_length;
				$total_cut = $totalcut;
				// $totalcut = get_valueNullTozero($totalcut) + $net;
			}
/* echo "total = ".$totalcut."<br>";
echo "==========================================<br>"; */
			for ($d = $date_begin; $d <= $date_length; $d++) {
				$total_bill_start = "";
				$total_receive_start = "";
				$total_issue_start = "";
				$total_net = 0;


				$array_bill_result = array();
				$array_receive_result = array();
				$array_issue_result = array();

				$date_today = $date . '-' . sprintf("%02d", $d);

				//	หา array ในวันนี้ และสร้าง array รอไว้สำหรับหาจำนวน
				//	รวมยอดบิลสั่งซื้อ
				if ($result_bill_start) {
					$array_bill_date_today = array_keys(array_column($result_bill_start['result'], 'date_starts'), $date_today);
					if ($array_bill_date_today) {
						$total_bill_start = 0;
						$arraysub_bill_result = array();

						foreach ($array_bill_date_today as $key => $value) {
							$arraysub_bill_result[] = array(
								'date_starts'	=> $result_bill_start['result'][$value]['date_starts'],
								'pid'			=> $result_bill_start['result'][$value]['pid'],
								'lid'			=> $result_bill_start['result'][$value]['lid'],
								'qty'			=> $result_bill_start['result'][$value]['qty']
							);
						}
						$array_bill_result['result'] = $arraysub_bill_result;
					}
				}
				//
				//

				//	รวมยอดบิลรับเข้า
				if ($result_receive_start) {
					$array_receive_date_today = array_keys(array_column($result_receive_start['result'], 'date_starts'), $date_today);
					if ($array_receive_date_today) {
						$total_receive_start = 0;
						$arraysub_receive_result = array();

						foreach ($array_receive_date_today as $key => $value) {
							$arraysub_receive_result[] = array(
								'date_starts'	=> $result_receive_start['result'][$value]['date_starts'],
								'pid'			=> $result_receive_start['result'][$value]['pid'],
								'lid'			=> $result_receive_start['result'][$value]['lid'],
								'qty'			=> $result_receive_start['result'][$value]['qty']
							);
						}
						$array_receive_result['result'] = $arraysub_receive_result;
					}
				}
				//
				//

				//	รวมยอดเบิกออก
				if ($result_issue_start) {
					$array_issue_date_today = array_keys(array_column($result_issue_start['result'], 'date_starts'), $date_today);
					if ($array_issue_date_today) {
						$total_issue_start = 0;
						$arraysub_issue_result = array();

						foreach ($array_issue_date_today as $key => $value) {
							$arraysub_issue_result[] = array(
								'date_starts'	=> $result_issue_start['result'][$value]['date_starts'],
								'pid'			=> $result_issue_start['result'][$value]['pid'],
								'lid'			=> $result_issue_start['result'][$value]['lid'],
								'qty'			=> $result_issue_start['result'][$value]['qty']
							);
						}
						$array_issue_result['result'] = $arraysub_issue_result;
					}
				}
				//
				//

				//	หาจำนวนรายการบิลของสินค้าในวันนี้
				if ($array_bill_result) {
					$total_bill_start = $this->findscore($array_bill_result, $productid);
					$total_bill_net += $total_bill_start;
				}

				//	หาจำนวนรับเข้าของสินค้าในวันนี้
				if ($array_receive_result) {
					$total_receive_start = $this->findscore($array_receive_result, $productid);
					$total_receive_net += $total_receive_start;
				}

				//	หาจำนวนเบิกออกของสินค้าในวันนี้
				if ($array_issue_result) {
					$total_issue_start = $this->findscore($array_issue_result, $productid);
					$total_issue_net += $total_issue_start;
				}


				//	find data cut value from data stock
				$arraytotal_start = array(
					'total_bill'	=> $total_bill_start,
					'total_issue'	=> $total_issue_start,
					'total_receive'	=> $total_receive_start
				);
				$net = $this->mdl_retailstock->cal_stock($arraytotal_start);
				// $total_cut = get_valueNullTozero($total_cut);
				$total_cut = get_valueNullTozero($total_cut) + $net;

				/* echo "<pre>";
				echo "วันที่ ".$d."-item ".$productid."=bill(".$total_bill_start.") receive(".$total_receive_start.") issue(".$total_issue_start.")<br>";
				echo "</pre>"; */

				$datainfo_today[] = array(
					'date_today'		=> $date_today,
					'product'			=> $productid,
					'bill'				=> get_valueNullToNull($total_bill_start),
					'receive'			=> get_valueNullToNull($total_receive_start),
					'issue'				=> get_valueNullToNull($total_issue_start),
					'total'				=> get_valueNullToZero($total_cut),
				);
			}

			//	find data cut value from data stock
			$arraytotal_net = array(
				'total_bill'	=> $total_bill_net,
				'total_issue'	=> $total_issue_net,
				'total_receive'	=> $total_receive_net
			);
			$totalnet = $this->mdl_retailstock->cal_stock($arraytotal_net);
			$total_net = get_valueNullTozero($totalcut) + $totalnet;

			$data[] = array(
				'no'		=> "",
				'id'		=> $productid,
				'name'		=> $productname,
				'stock'		=> get_valueNullToZero($totalcut),
				'bill_net'			=> get_valueNullToZero($total_bill_net),
				'receive_net'		=> get_valueNullToZero($total_receive_net),
				'issue_net'			=> get_valueNullToZero($total_issue_net),
				'total_net'			=> get_valueNullToZero($total_net),
				'data'		=> $datainfo_today
			);
		}

		$array_cut_bill = array();
		$array_cut_receive = array();
		$array_cut_issue = array();
		//	select field bill
		// echo $request['dataField']['bill'];
		if ($request['dataField']['bill'] !== 'false') {
			$array_cut_bill = array_keys(array_column($data, 'bill_net'), true);
		}

		//	select field receive
		if ($request['dataField']['receive'] !== 'false') {
			$array_cut_receive = array_keys(array_column($data, 'receive_net'), true);
		}

		//	select field issue
		if ($request['dataField']['issue'] !== 'false') {
			$array_cut_issue = array_keys(array_column($data, 'issue_net'), true);
		}

		if ($request['dataField']['bill'] !== 'false' || $request['dataField']['receive'] !== 'false' || $request['dataField']['issue'] !== 'false') {
			// if(count($array_cut_bill) || count($array_cut_receive) || count($array_cut_issue)){
			$array_cut_all = array_merge($array_cut_bill, $array_cut_receive, $array_cut_issue);
			$array_cut = array_unique($array_cut_all);

			/* echo "<pre>";
				print_r($data);
				echo "=========";
				print_r($array_cut_bill);
				echo "*****";
				print_r($array_cut_receive);
				echo "+++++++";
				print_r($array_cut_issue);
				echo "result====";
				print_r($array_cut);
				echo "</pre>"; */

			if ($array_cut) {
				foreach ($data as $key => $val) {
					$t = array_keys($array_cut, $key);
					if (!$t) {
						unset($data[$key]);
					}
				}
				$ar = array_values($data);

				$data = $ar;
			} else {
				$data = array();
			}
		} else {
			$data = $data;
		}

		/* echo "<pre>";
		print_r($_REQUEST);
		echo $totaldays;
		echo "======";
		print_r($data);
		echo "</pre>";
		exit; */

		echo json_encode($data);
	}












	function fetch_product()
	{

		$fetch_data = $this->mdl_retailstock->make_datatables();
		/* echo "<pre>";
		print_r($_REQUEST);
		echo "</pre>";
		exit; */
		$data = array();
		$index = 1;

		$request = $_REQUEST;

		($request['date'] ? $date = $request['date'] : $date = date('Y-m-d'));

		$arrayset = array(
			'date'	=> ($request['date'] ? $request['date'] : date('Y-m-d'))
		);

		$arraystart = array(
			'datecut'	=> $request['date_cut'],
			'date'		=> ($request['date'] ? $request['date'] : date('Y-m-d'))
		);

		//	หาจำนวนขายต่อวัน
		// $totalnumber = $this->mdl_retailstock->countTotalProductOrder($arrayset);

		$result_bill_start = $this->mdl_retailstock->result_bill_start($arraystart);
		$result_issue_start = $this->mdl_retailstock->result_issue_start($arraystart);
		$result_receive_start = $this->mdl_retailstock->result_receive_start($arraystart);

		/* echo "<pre> bill";
		print_r($result_bill_start);
		echo "</pre>";
		echo "<pre> issue";
		print_r($result_issue_start);
		echo "</pre>";
		echo "<pre> receive";
		print_r($result_receive_start);
		echo "</pre>";
		exit; */

		$result_bill = $this->mdl_retailstock->result_bill_today($arrayset);
		$result_issue = $this->mdl_retailstock->result_issue_today($arrayset);
		$result_receive = $this->mdl_retailstock->result_receive_today($arrayset);

		/* echo "<pre> bill";
		print_r($result_bill);
		echo "</pre>";
		echo "<pre> issue";
		print_r($result_issue);
		echo "</pre>";
		echo "<pre> receive";
		print_r($result_receive);
		echo "</pre>";
		exit; */

		$result_cut = $this->mdl_retailstock->find_stockItemCutquery($request['date_cut']);
		/* echo "<pre> bill";
		print_r($result_cut);
		echo "</pre>"; */

		foreach ($fetch_data as $row) {

			$stockid = $row->id;
			$productid = $row->p_id;
			$codemac = $row->codemac;
			$codeproduct = $row->p_codeproduct;

			//	total
			$arrayparam = array(
				'stockid'		=> $stockid
			);

			$arraytotal = array(
				'item'		=> $productid,
				'date'		=> $date,
				'datecut'	=> $request['date_cut']
			);
			// $start = $this->mdl_retailstock->find_total_stockproductstarts($arraytotal);

			//	find array key product id at value before today from data result bill
			if ($result_bill_start) {
				$total_billOrder_start = $this->findscore($result_bill_start, $productid);
			}

			//	find array key product id at value before today from data result issue
			if ($result_issue_start) {
				$total_billIssue_start = $this->findscore($result_issue_start, $productid);
			}

			//	find array key product id at value before today from data result receive
			if ($result_receive_start) {
				$total_billReceive_start = $this->findscore($result_receive_start, $productid);
			}

			$arraytotal_start = array(
				'total_bill'	=> $total_billOrder_start,
				'total_issue'	=> $total_billIssue_start,
				'total_receive'	=> $total_billReceive_start
			);
			$start = $this->mdl_retailstock->cal_stock($arraytotal_start);

			/* echo "<pre> ".$productid."--";
			echo "result=";
			print_r($total_billOrder_start);
			print_r($total_billIssue_start);
			print_r($total_billReceive_start);
			echo "</pre>"; */

			//	find data cut value from data stock
			$total_cut = 0;
			if ($result_cut) {
				$key_lid = array_keys(array_column($result_cut['result'], 'pid'), $productid);
				if (count($key_lid)) {
					foreach ($key_lid as $key => $value) {
						$totalcut = $result_cut['result'][$value]['qty'];
					}
					$total_cut = $totalcut;
				}
			}
			$total_cut_convert = get_valueNullTozero($total_cut);
			$total_start = $total_cut_convert + $start;

			//	find array key product id from data result bill
			if ($result_bill) {
				$total_billOrder = $this->findscore($result_bill, $productid);
			}

			//	find array key product id from data result issue
			if ($result_issue) {
				$total_billIssue = $this->findscore($result_issue, $productid);
			}

			//	find array key product id from data result receive
			if ($result_receive) {
				$total_billReceive = $this->findscore($result_receive, $productid);
			}

			/* echo "<pre> ".$productid."--";
			// print_r($key_bill);
			echo "result=";
			print_r($total_billOrder);
			print_r($total_billIssue);
			print_r($total_billReceive);
			echo "</pre>"; */

			($row->p_codemac ? $textcodemac = " (" . $row->p_codemac . ")" : $textcodemac = "");

			// ($producttotal ? $textproducttotal = "<span> >>> " . $producttotal . "</span>" : $textproducttotal = "");
			$textproducttotal = "";

			$sub_array = array();
			$sub_array[] = "<div class='text-right' data-item='" . $productid . "' data-id='" . $stockid . "' data-min='" . $row->rtst_min . "' >-</div>";
			$sub_array[] = $productid;	//	productlist id
			$sub_array[] = $row->p_name . $textcodemac . $textproducttotal;
			$sub_array[] = $total_start;
			$sub_array[] = $total_billOrder;
			$sub_array[] = $total_billReceive;
			$sub_array[] = $total_billIssue;
			$sub_array[] = "";
			$data[] = $sub_array;
		}

		$array_cut_bill = array();
		$array_cut_receive = array();
		$array_cut_issue = array();
		//	select field bill
		// echo $request['dataField']['bill'];
		if ($request['dataField']['bill'] !== 'false') {
			$array_cut_bill = array_keys(array_column($data, 4), true);
		}

		//	select field receive
		if ($request['dataField']['receive'] !== 'false') {
			$array_cut_receive = array_keys(array_column($data, 5), true);
		}

		//	select field issue
		if ($request['dataField']['issue'] !== 'false') {
			$array_cut_issue = array_keys(array_column($data, 6), true);
		}


		if ($request['dataField']['bill'] !== 'false' || $request['dataField']['receive'] !== 'false' || $request['dataField']['issue'] !== 'false') {
			// if(count($array_cut_bill) || count($array_cut_receive) || count($array_cut_issue)){
			$array_cut_all = array_merge($array_cut_bill, $array_cut_receive, $array_cut_issue);
			$array_cut = array_unique($array_cut_all);

			/* echo "<pre>";
			print_r($data);
			echo "=========";
			echo $request['dataField']['bill']." :::req";
			print_r($array_cut_bill);
			echo "*****";
			print_r($array_cut_receive);
			echo "+++++++";
			print_r($array_cut_issue);
			echo "result====";
			print_r($array_cut);
			echo "</pre>"; */
			if ($array_cut) {
				foreach ($data as $key => $val) {
					$t = array_keys($array_cut, $key);
					if (!$t) {
						unset($data[$key]);
					}
				}
				$ar = array_values($data);

				$output = array(
					"draw"             	=>     intval($_POST["draw"]),
					"recordsTotal"      =>     $this->mdl_retailstock->get_all_data(),
					"recordsFiltered"   =>     $this->mdl_retailstock->get_filtered_data(),
					"data"              =>     $ar
				);
			} else {
				$data = array();
				$output = array(
					"draw"             	=>     intval($_POST["draw"]),
					"recordsTotal"      =>     $this->mdl_retailstock->get_all_data(),
					"recordsFiltered"   =>     $this->mdl_retailstock->get_filtered_data(),
					"data"              =>     $data
				);
			}
		} else {
			$output = array(
				"draw"             	=>     intval($_POST["draw"]),
				"recordsTotal"      =>     $this->mdl_retailstock->get_all_data(),
				"recordsFiltered"   =>     $this->mdl_retailstock->get_filtered_data(),
				"data"              =>     $data
			);
		}

		echo json_encode($output);
	}

	function fetch_setting()
	{
		$fetch_data = $this->mdl_retailstock->datasetting();
		$basepic = base_url() . BASE_PIC;
		$data = array();
		$index = 1;
		$status_bnt = '';
		/* echo "<pre>";
		print_r($fetch_data);
		echo "</pre>"; */
		foreach ($fetch_data as $row) {

			$stockid = $row->id;
			$codemac = $row->codemac;
			$codeproduct = $row->p_codeproduct;

			//	total
			$arrayparam = array(
				'stockid'		=> $stockid
			);

			$scoreother = $row->claim + $row->loss + $row->repack + $row->other;

			$btn = "";

			$btn = '<button id="callModal" name="callModal" data-id="' . $row->id . '" class="btn btn-sm btn-secondary" data-toggle="modal" data-target=".bs-example-modal-center" >+</button>';

			($row->total ? $total = $row->total : $total = 0);

			$sub_array = array();
			$sub_array[] = "<div class='text-right' data-id='" . $stockid . "'>" . $index++ . "</div>";
			$sub_array[] = $row->p_id;
			$sub_array[] = $row->p_name . ($row->p_codemac ? " (" . $row->p_codemac . ") " : "");
			$sub_array[] = $row->rtst_min;
			$sub_array[] = $row->rtst_max;
			$sub_array[] = ($row->staff_nameth ? $row->staff_nameth : $row->staff_name);

			$data[] = $sub_array;
		}
		$output = array(
			"draw"             	=>     intval($_POST["draw"]),
			"recordsTotal"      =>     $this->mdl_retailstock->get_all_data(),
			"recordsFiltered"   =>     $this->mdl_retailstock->get_filtered_data(),
			"data"              =>     $data
		);
		echo json_encode($output);
	}

	public function ajax_getDataOther()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$returns = $this->mdl_retailstock->ajax_getDataOther();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function ajax_report()
	{

		$array = array();
		$fetch_data = $this->mdl_retailstock->make_datatables();
		if ($fetch_data) {

			$request = $_REQUEST;
			$date = $request['date'];
			$datelast = date("Y-m-d", strtotime("-7 days", strtotime($date)));

			foreach ($fetch_data as $row) {

				//	get sum total 
				$arrayparam = array(
					'datestart'	=> $datelast,
					'dateend'	=> $date,
					'id'	=> $row->p_id
				);
				$week = $this->mdl_retailstock->get_sumDateToItem($arrayparam);

				$product_price = $row->p_price;
				$total_stockprice = $product_price * $row->total;


				$rstaff = get_WherePara('staff', 'code', $row->user_update);

				$array[$row->p_id]['p_id'] = $row->p_id;
				$array[$row->p_id]['p_name'] = $row->p_name;
				$array[$row->p_id]['start'] = $row->start;
				$array[$row->p_id]['cut'] = get_valueNullTozero($row->cut);
				$array[$row->p_id]['pull'] = get_valueNullTozero($row->pull);
				$array[$row->p_id]['claim'] = get_valueNullTozero($row->claim);
				$array[$row->p_id]['loss'] = get_valueNullTozero($row->loss);
				$array[$row->p_id]['repack'] = get_valueNullTozero($row->repack);
				$array[$row->p_id]['other'] = get_valueNullTozero($row->other);
				$array[$row->p_id]['total'] = get_valueNullTozero($row->total);
				$array[$row->p_id]['price'] = get_valueNullTozero($product_price);
				// $array[$row->p_id]['total_stockprice'] = (get_valueNullTozero($total_stockprice) ? number_format($total_stockprice) : 0);
				$array[$row->p_id]['total_stockprice'] = get_valueNullTozero($total_stockprice);
				$array[$row->p_id]['cut_week'] = get_valueNullTozero($week->cut);
				$array[$row->p_id]['other_remark'] = $row->other_remark;
				$array[$row->p_id]['date_update'] = $row->date_update;
				$array[$row->p_id]['user_update'] = ($rstaff ? $rstaff->NAME . " " . $rstaff->LASTNAME : null);
			}

			$data['array']	= $array;
			/* echo "<pre>";
			print_r($data);
			echo "</pre>";
			exit; */
			$this->load->view('doc_reportstock', $data);

			$error = 0;
			$txt = 'success';
		} else {

			$error = 1;
			$txt = 'data not found';
		}


		/* $data = array(
				'error_code'	=> $error,
				'txt'			=> $txt
			);
			$return = json_encode($data);
			echo $return; */
	}

	public function reRunStock()
	{
		if ($this->input->server('REQUEST_METHOD')) {
			$input = filter_input_array(INPUT_GET);
			$returns = $this->mdl_retailstock->reRunStock($input);
			// $return = json_decode($returns);
			echo $returns;
		}
	}

	public function get_billorderDetail()
	{
		if ($this->input->server('REQUEST_METHOD') == 'GET') {
			$sql = $this->mdl_retailstock->get_billorderDetail();
			$q = json_decode($sql);

			if ($q->num) {
				foreach ($q->result as $row) {
					$returns[] = array(
						'code'	=> $row->rtd_code,
						'product'	=> $row->rtd_product_name,
						'qty'	=> $row->rtd_qty,
						'name'	=> $row->rtd_name
					);
				}
			}
			$return = json_encode($returns);

			echo $return;
		}
	}

	public function get_receiveDetail()
	{
		if ($this->input->server('REQUEST_METHOD') == 'GET') {
			$sql = $this->mdl_retailstock->get_receiveDetail();
			$q = json_decode($sql);

			if ($q->num) {
				foreach ($q->result as $row) {
					$name = "";
					if ($row->rtd_type == 1) {
						$name = $row->rtd_sp_name;
						$billtype = 'sup';
					} else if ($row->rtd_type == 2) {
						$billtype = 'บิล';
					} else if ($row->rtd_type == 3) {
						$billtype = 'ลดหนี้';
					} else {
						$name = $row->rtd_is_name;
						$billtype = 'ยืม';
					}

					$returns[] = array(
						'code'	=> $row->rtd_code . " " . $billtype . " ",
						'product'	=> $row->rtd_product_name,
						'qty'	=> $row->rtd_qty,
						'name'	=> $name
					);
				}
			}
			$return = json_encode($returns);

			echo $return;
		}
	}

	public function get_issueDetail()
	{
		if ($this->input->server('REQUEST_METHOD') == 'GET') {
			$sql = $this->mdl_retailstock->get_issueDetail();
			$q = json_decode($sql);

			if ($q->num) {
				foreach ($q->result as $row) {

					$name = "";
					if ($row->rtd_type == 1) {
						$name = $row->rtd_name;
						$billtype = 'ยืม';
					} else if ($row->rtd_type == 2) {
						$billtype = 'สูญเสีย';
					} else if ($row->rtd_type == 3) {
						$billtype = 'โอนย้าย';
					} else {
						$billtype = 'เข้าผิด';
					}


					$returns[] = array(
						'code'	=> $row->rtd_code . " " . $billtype . " ",
						'product'	=> $row->rtd_product_name,
						'qty'	=> $row->rtd_qty,
						'name'	=> $name
					);
				}
			}
			$return = json_encode($returns);

			echo $return;
		}
	}
	/**
	 * =================================================================================
	 * =================================================================================
	 * =================================================================================
	 */
}
