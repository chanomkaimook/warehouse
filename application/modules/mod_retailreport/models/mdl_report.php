<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', "100M");

defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_report extends CI_Model
{
	public function __construct()
	{
		//
		//	setting
		$table = 'retail_bill';
		$tablesecond = 'retail_billdetail';
		$tabledelivery = 'delivery';
		$tableproductmain = 'retail_productmain';
		$tableproductlist = 'retail_productlist';
		$tableclaim = 'retail_claim';
		$tablemethod = 'retail_methodorder';

		$paramitername = "";

		if ($this->input->get('table')) {
			$paramitername = $this->input->get('table');
		}
		if ($this->input->post('table')) {
			$paramitername = $this->input->post('table');
		}

		// default table
		$this->table = $table;
		$this->tablesecond = $tablesecond;
		$this->tablethird = $tableclaim;

		switch ($paramitername) {

			case 'bill_report':
				$this->table = $table;
				$this->tablesecond = $tablesecond;
				$this->tablethird = $tableclaim;
				$this->tablefour = $tabledelivery;
				$this->tablefive = $tablemethod;
				break;
			case 'bill_summary':
				$this->table = $table;
				$this->tablesecond = $tablesecond;
				$this->tablethird = $tabledelivery;
				$this->tablefour = $tableclaim;
				$this->tablefive = $tablemethod;
				$this->tablesix = $tableproductlist;
				break;
			case 'bill_store':
				$this->table = $table;
				$this->tablesecond = $tablesecond;
				$this->tablethird = $tableproductmain;
				$this->tablefour = $tableproductlist;
				break;
			case 'ranking_sale':
				$this->table = $table;
				$this->tablesecond = $tablesecond;
				break;

				//	form spacial
			case 'bill_vat':
				$this->table = $table;
				$this->tablesecond = $tablesecond;
				$this->tablethird = $tableclaim;
				$this->tablefour = $tableproductmain;
				$this->tablefive = $tableproductlist;
				break;
			case 'bill_receipt':
				$this->table = $table;
				$this->tablesecond = $tablesecond;
				$this->tablethird = $tableclaim;
				$this->tablefour = $tableproductmain;
				$this->tablefive = $tableproductlist;
				break;
			case 'bill_vatlistmonth':
				$this->table = $table;
				$this->tablesecond = $tablesecond;
				$this->tablethird = $tableclaim;
				$this->tablefour = $tableproductmain;
				$this->tablefive = $tableproductlist;
				$this->tablesix = $tabledelivery;
				$this->tableseven = $tablemethod;
				break;
			case 'bill_listorders':
				$this->table = $table;
				$this->tablesecond = $tablesecond;
				$this->tablethird = $tableclaim;
				$this->tablefour = $tableproductmain;
				$this->tablefive = $tableproductlist;
				break;
		}
		//
		//
	}
	public function ajaxSlip($billcode)
	{
		//	query
		$this->db->select('
			' . $this->table . '.id as bill_id,
			' . $this->table . '.name as bill_name,
			' . $this->table . '.pic_payment as bill_pic,
			' . $this->table . '.pic_payment2 as bill_pic2
		');
		$this->db->from($this->table);
		$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
		$this->db->where($this->table . '.code', $billcode);
		$q = $this->db->get();
		$row = $q->row();
		$img1 = "";
		$img2 = "";

		//	your name
		$bill_name = $row->bill_name;

		//	image
		if ($row->bill_pic == '' && $row->bill_pic2 == '') {
			$sqlpic = $this->db->select("imgname")
				->from("retail_billimg")
				->where("retail_billimg.billid", $row->bill_id);
			// ->where("retail_billimg.status",1)
			$sqlpic_num = $sqlpic->count_all_results(null, false);
			if ($sqlpic_num > 0) {
				$result = $sqlpic->get();
				foreach ($result->result() as $r) {
					$img1 .= '<img src="' . base_url() . BASE_PIC . '/front/retail/BillPaymentMultiple/' . $r->imgname . '">';
				}
			}
		} else {
			if ($row->bill_pic) {
				$img1 = '<img src="' . base_url() . BASE_PIC . '/front/retail/Bill_Pyment/' . $row->bill_pic . '">';
			}
			if ($row->bill_pic2) {
				$img2 = '<img src="' . base_url() . BASE_PIC . '/front/retail/Bill_Pyment/' . $row->bill_pic2 . '">';
			}
		}

		$result = array(
			'name' => $bill_name,
			'code' => $billcode,
			'image' => $img1 . $img2
		);
		$data = json_encode($result);

		return $data;
	}
	function countPromotionOnline()
	{
		$r = $this->promotion->get_PromotionOnline();
		$result = $r['count'];

		return $result;
	}
	function countPromotionComming()
	{
		$r = $this->promotion->get_PromotionComming();
		$result = $r['count'];

		return $result;
	}
	function getDatePromotion($datestart, $dateend)
	{
		if (($datestart != "" && $dateend != "") && $datestart == $dateend) {
			$code = "001";
			$text = "เฉพาะ " . thai_date_indent($datestart);
			$status = $this->mdl_report->getPromotionStatus($datestart, $dateend);
		} else if ($datestart && $dateend) {
			$code = "002";
			$text = thai_date_indent($datestart) . " - " . thai_date_indent($dateend);
			$status = $this->mdl_report->getPromotionStatus($datestart, $dateend);
		} else if ($datestart &&  $dateend == "") {
			$code = "003";
			$text = "เริ่ม " . thai_date_indent($datestart) . " เป็นต้นไป";
			$status = $this->mdl_report->getPromotionStatus($datestart, $dateend);
		} else if ($datestart == "" &&  $dateend) {
			$code = "004";
			$text = "จนถึง  " . thai_date_indent($dateend);
			$status = $this->mdl_report->getPromotionStatus($datestart, $dateend);
		} else {
			$code = "";
			$text = "ไม่ระบุ";
			$status = "";
		}
		$data = array(
			'code'		=> $code,
			'status'	=> $status,
			'text'		=> $text
		);
		$result = $data;

		return $result;
	}
	function columnProduct_old()
	{
		$this->db->select('
					retail_productmain.name_th as product_main,
					retail_productlist.status_view as statusview,
					retail_productlist.name_th as product_list
				');
		$this->db->from('retail_productmain');
		$this->db->join('retail_productlist', 'retail_productmain.id=retail_productlist.promain_id', 'left');
		$this->db->where('retail_productmain.status', 1);
		$this->db->where('retail_productlist.status_view', 1);
		$this->db->where('retail_productlist.status', 1);
		$total_result = $this->db->count_all_results(null, FALSE);
		$q = $this->db->get();
		$array = $q->result();

		if ($total_result > 0) {
			$error_code = 0;
		} else {
			$error_code = 1;
		}

		$result = array(
			'error_code'		=> $error_code,
			'numrows'			=> $total_result,
			'query'				=> $array
		);
		$data = json_encode($result);

		return $data;
	}
	function columnProduct()
	{
		$request = $_REQUEST;
		$valdatex = $request['valdate'];
		$valdatetox = $request['valdateto'];

		$this->db->select('
			' . $this->table . '.code as bill_code,
			' . $this->table . '.billstatus as billstatus,
			' . $this->tablefour . '.id as product_id,
			' . $this->tablefour . '.name_th as product_list
		');
		$this->db->from($this->table);
		$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
		$this->db->join($this->tablethird, $this->tablesecond . '.promain_id=' . $this->tablethird . '.id', 'left');
		$this->db->join($this->tablefour, $this->tablesecond . '.prolist_id=' . $this->tablefour . '.id', 'left');
		$this->db->where($this->table . '.status_complete in (2,5)');
		$this->db->where($this->table . '.status', 1);

		if ($valdatex != '' && $valdatetox == '') {
			$this->db->where('date(' . $this->table . '.date_starts) ', $valdatex);
		} else if ($valdatex != '' && $valdatetox != '') {
			$this->db->where('date(' . $this->table . '.date_starts) >=', $valdatex);
			$this->db->where('date(' . $this->table . '.date_starts) <=', $valdatetox);
		} else if ($valdatetox != '') {
			$this->db->where('date(' . $this->table . '.date_starts) <=', $valdatetox);
		} else if ($valdatex == '' && $valdatetox == '') {
			$this->db->where('date(' . $this->table . '.date_starts) =', date('Y-m-d'));
			$querydate = 'and date(' . $this->table . '.date_starts) = "' . date('Y-m-d') . '"';
		}
		// $this->db->where('date('.$this->table.'.date_starts) =','2021-07-05');
		// $querydate = 'and date('.$this->table.'.date_starts) = "2021-07-05"';

		/* 		if($statusproduct != ''){
			$this->db->where(''.$this->table.'.status',$statusproduct);
		} */
		$this->db->group_by($this->tablefour . '.name_th');
		$this->db->order_by($this->tablefour . '.id', 'asc');

		$total_result = $this->db->count_all_results(null, FALSE);
		$q = $this->db->get();
		$array = $q->result();

		if ($total_result > 0) {
			$error_code = 0;
		} else {
			$error_code = 1;
		}

		$result = array(
			'error_code'		=> $error_code,
			'numrows'			=> $total_result,
			'query'				=> $array
		);
		$data = json_encode($result);

		return $data;
	}
	//---------------------------- DATATABLE ----------------------------// 
	function ajaxPost()
	{
		$requestData = $_REQUEST;
		$this->db->select('
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.text_number as bill_textnumber,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.delivery_formid as bill_gateway,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.shor_money as bill_shor,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.transfered_daytime as bill_transferedtime,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.date_starts as bill_datetime,
					' . $this->tablethird . '.total_price as bill_claimtotalprice,
					' . $this->tablethird . '.net_total as bill_claimnettotal,
					' . $this->tablethird . '.remark as bill_remark
				');
		$this->db->from($this->table);
		$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
		$this->db->join($this->tablethird, $this->table . '.id=' . $this->tablethird . '.bill_id', 'left');
		$this->db->where($this->table . '.status_complete = 2');
		$this->db->where($this->table . '.status = 1');

		if ($requestData['search']['value']) {
			$this->db->where($this->table . '.code like "%' . $requestData['search']['value'] . '%"');
		} else {
			$this->db->where('date(' . $this->table . '.date_starts) >=', '2020-07-04');
			$this->db->where('date(' . $this->table . '.date_starts) <=', '2020-11-04');

			$this->db->or_where('('
				. $this->tablethird . '.status_complete=4 
									and ' . $this->tablethird . '.status_claim in (1,2)
									and ' . $this->tablethird . '.status_claimcomplete = 0 )');
		}





		$this->db->group_by($this->table . '.code', 'asc');
		$this->db->order_by($this->table . '.date_starts', 'asc');
		$query = $this->db->get();
		$numrow = $query->num_rows();

		$index = 0;
		foreach ($query->result() as $row) {
			$sub_array = array();

			$username = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->bill_name . "\" >" . $row->bill_name . "</p>";
			$phone = "<p class=\"textoverflow\" data-toggle=\"popover\" title=\"\" data-placement=\"bottom\" data-content=\"" . $row->bill_phone . "\" >" . $row->bill_phone . "</p>";

			$datacreate = "<p>" . date('d-m-Y', strtotime($row->bill_datetime)) . "</p>";
			$billcode = $row->bill_code;
			//
			//	for bill claim price will be 0
			if ($row->bill_claimtotalprice) {
				$price = $row->bill_claimtotalprice;
				$pricetotal = $row->bill_claimnettotal;
			} else {
				$price = $row->bill_totalprice;
				$pricetotal = $row->bill_nettotal;
			}
			//
			$package = $row->bill_parcel;
			$express = $row->bill_delivery;
			$discount = $row->bill_discount;

			$sub_array[] = $index;
			$sub_array[] = $datacreate;
			$sub_array[] = $billcode;
			$sub_array[] = $username;
			$sub_array[] = $phone;
			$sub_array[] = $price;
			$sub_array[] = $package;
			$sub_array[] = $express;
			$sub_array[] = $discount;
			$sub_array[] = $pricetotal;
			$sub_array[] = '<a href=# data-toggle="modal" data-target="#modalslip" data-id="' . $row->bill_code . '" >  
										view
									</a>
									';

			$data[] = $sub_array;
			$index++;
		}

		$getalldata = $this->mdl_report->get_all_data();
		$getfilterdata = $numrow;

		/* $output = array( 
								"draw"             	=>     intval($_POST["draw"]), 
								"recordsTotal"      =>     $getalldata,  
								"recordsFiltered"   =>     $getfilterdata,  
								"data"              =>     $data
							);
					
				return $output; */

		$json_data = array(
			"draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval($getalldata),  // total number of records
			"recordsFiltered" => intval($getfilterdata), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		echo json_encode($json_data);  // send data as json format
	}
	function make_query()
	{
		$requestData = $_REQUEST;
		// $valdate = $this->input->get('valdate');
		// $valdateTo = $this->input->get('valdateto');
		$valdate = $requestData['valdate'];
		$valdateTo = $requestData['valdateto'];

		if ($requestData['valsearchfromdate']) {
			$searchfromdate = $requestData['valsearchfromdate'];
		}

		$searchfromdate = 'date_starts';

		// $statusproduct = $this->input->get('statusproduct');
		$statusproduct = $requestData['statusproduct'];

		#	select order id
		// $listorderid = $this->input->get('listorderid');
		$listorderid = $requestData['listorderid'];



		switch ($requestData['table']) {
			case 'bill_report':
				$this->db->select('
					' . $this->table . '.id as billid,
					' . $this->table . '.code as bill_code,
					' . $this->table . '.textcode as bill_textcode,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.text_number as bill_textnumber,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.delivery_formid as bill_gateway,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.shor_money as bill_shor,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.transfered_daytime as bill_transferedtime,
					' . $this->table . '.status_approve1 as bill_approve1,
					' . $this->table . '.status_approve2 as bill_approve2,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.date_starts as bill_datetime,

					' . $this->table . '.user_starts as bill_user_starts,
					' . $this->table . '.user_update as bill_user_update,

					count(' . $this->table . '.id) as rtd_sum,

					' . $this->tablethird . '.total_price as bill_claimtotalprice,
					' . $this->tablethird . '.net_total as bill_claimnettotal,
					' . $this->tablethird . '.remark as bill_remark,
					' . $this->tablefour . '.name_th as delivery_name,
					' . $this->tablefive . '.topic as method_name
				');
				$this->db->from($this->table);
				$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
				$this->db->join($this->tablethird, $this->table . '.id=' . $this->tablethird . '.bill_id', 'left');
				$this->db->join($this->tablefour, $this->table . '.delivery_formid=' . $this->tablefour . '.id', 'left');
				$this->db->join($this->tablefive, $this->table . '.methodorder_id=' . $this->tablefive . '.id', 'left');
				$this->db->where($this->table . '.status_complete = 2');
				$this->db->where($this->table . '.status = 1');

				/* if($valdate != '' && $valdateTo == ''){
					$this->db->where('(date('.$this->table.'.date_starts) = "'.$valdate.'" or date('.$this->table.'.transfered_daytime) = "'.$valdate.'" )');
					$querydate = 'and (date('.$this->table.'.date_starts) = "'.$valdate.'" or date('.$this->table.'.transfered_daytime) = "'.$valdate.'" )';
				}
				else if($valdate != '' && $valdateTo != ''){
					$this->db->where('(date('.$this->table.'.date_starts) >= "'.$valdate.'" and date('.$this->table.'.date_starts) <= "'.$valdate.'"
									or date('.$this->table.'.transfered_daytime) >= "'.$valdateTo.'" and date('.$this->table.'.transfered_daytime) <= "'.$valdateTo.'"
									)');
					$querydate = 'and (date('.$this->table.'.date_starts) >= "'.$valdate.'" and date('.$this->table.'.date_starts) <= "'.$valdateTo.'"
									or date('.$this->table.'.transfered_daytime) >= "'.$valdateTo.'" and date('.$this->table.'.transfered_daytime) <= "'.$valdateTo.'"
									)';
				}
				else if($valdateTo != ''){
					$this->db->where('(date('.$this->table.'.date_starts) <= "'.$valdateTo.'" or date('.$this->table.'.transfered_daytime) <= "'.$valdateTo.'" )');
					$querydate = 'and (date('.$this->table.'.date_starts) <= "'.$valdateTo.'" or date('.$this->table.'.transfered_daytime) <= "'.$valdateTo.'" )';
				} */
				if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

					$sqlserch = "( ";
					$sqlserch .= $this->table . ".code like '%" . $requestData['search']['value'] . "%' ";
					$sqlserch .= " OR " . $this->table . ".name like '%" . $requestData['search']['value'] . "%' ";
					$sqlserch .= " OR " . $this->table . ".phone_number like '%" . $requestData['search']['value'] . "%' ";
					// $sqlserch .=" OR ".$this->table.".remark like '%".$requestData['search']['value']."%' )";    
					$sqlserch .= " )";
					$this->db->where($sqlserch);
				} else {

					if ($valdate != '' && $valdateTo == '') {
						$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') ', $valdate);
						$querydate = 'and date(' . $this->table . '.' . $searchfromdate . ') = "' . $valdate . '"';
					} else if ($valdate != '' && $valdateTo != '') {
						$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') >=', $valdate);
						$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') <=', $valdateTo);
						$querydate = 'and date(' . $this->table . '.' . $searchfromdate . ') >= "' . $valdate . '" and date(' . $this->table . '.' . $searchfromdate . ') <= "' . $valdateTo . '"';
					} else if ($valdateTo != '') {
						$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') <=', $valdateTo);
						$querydate = 'and date(' . $this->table . '.' . $searchfromdate . ') <= "' . $valdateTo . '"';
					} else if ($valdate == '' && $valdateTo == '') {
						$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') =', date('Y-m-d'));
						$querydate = 'and date(' . $this->table . '.' . $searchfromdate . ') = "' . date('Y-m-d') . '"';
						// $this->db->where('date('.$this->table.'.date_starts) =','2020-08-13');
						// $querydate = 'and date('.$this->table.'.date_starts) = "2020-08-13"';
					}

					if ($statusproduct != '') {
						$this->db->where('' . $this->table . '.status', $statusproduct);
						$querystatus = 'and ' . $this->table . '.status = ' . $statusproduct;
					}

					if ($statusproduct != '') {
						$this->db->where('' . $this->table . '.status', $statusproduct);
						$querystatus = 'and ' . $this->table . '.status = ' . $statusproduct;
					}

					$this->db->or_where('('
						. $this->tablethird . '.status_complete=4 
										and ' . $this->tablethird . '.status_claim in (1,2)
										and ' . $this->tablethird . '.status_claimcomplete = 0 '
						. $querydate . $querystatus .
						')');
				}

				$this->db->group_by($this->table . '.code');
				$this->db->order_by($this->table . '.' . $searchfromdate . '', 'asc');
				break;
			case 'bill_vat': //	ออกบิลใบกำกับภาษี
				$this->db->select('
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.text_number as bill_textnumber,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.shor_money as bill_shor,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.tax as bill_tax,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.transfered_daytime as bill_datetimeslip,
					date(' . $this->table . '.date_starts) as bill_datetime,
					' . $this->tablesecond . '.quantity as bill_qty,
					' . $this->tablesecond . '.total_price as product_price,
					' . $this->tablethird . '.total_price as bill_claimtotalprice,
					' . $this->tablethird . '.net_total as bill_claimnettotal,
					' . $this->tablethird . '.remark as bill_remark,
					' . $this->tablefive . '.name_th as product
				');
				$this->db->from($this->table);
				$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
				$this->db->join($this->tablethird, $this->table . '.id=' . $this->tablethird . '.bill_id', 'left');
				$this->db->join($this->tablefour, $this->tablesecond . '.promain_id=' . $this->tablefour . '.id', 'left');
				$this->db->join($this->tablefive, $this->tablesecond . '.prolist_id=' . $this->tablefive . '.id', 'left');
				// $this->db->where($this->table.'.status_complete = 2');
				$this->db->where($this->table . '.status_complete in(1,2,5)');
				$this->db->where($this->table . '.status = 1');


				if ($valdate != '' && $valdateTo == '') {
					$this->db->where('date(' . $this->table . '.date_starts) ', $valdate);
					$querydate = 'and date(' . $this->table . '.date_starts) = "' . $valdate . '"';
				} else if ($valdate != '' && $valdateTo != '') {
					$this->db->where('date(' . $this->table . '.date_starts) >=', $valdate);
					$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
					$querydate = 'and date(' . $this->table . '.date_starts) >= "' . $valdate . '" and date(' . $this->table . '.date_starts) <= "' . $valdateTo . '"';
				} else if ($valdateTo != '') {
					$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
					$querydate = 'and date(' . $this->table . '.date_starts) <= "' . $valdateTo . '"';
				}

				if ($statusproduct != '') {
					$this->db->where('' . $this->table . '.status', $statusproduct);
					$querystatus = 'and ' . $this->table . '.status = ' . $statusproduct;
				}

				if ($listorderid != '') {
					$this->db->where($this->table . '.id in(' . $listorderid . ')');
					$querystatus = 'and ' . $this->table . '.id in(' . $listorderid . ')';
				}

				// $this->db->where('date('.$this->table.'.date_starts) =','2020-08-13');
				// $querydate = 'and date('.$this->table.'.date_starts) = "2020-08-13"';

				$this->db->or_where('('
					. $this->tablethird . '.status_complete=4 
									and ' . $this->tablethird . '.status_claim in (1,2,5)
									and ' . $this->tablethird . '.status_claimcomplete = 0 '
					. $querydate . $querystatus .
					')');

				// $this->db->group_by($this->table.'.code', 'asc');
				$this->db->order_by($this->table . '.date_starts', 'asc');
				$this->db->order_by($this->table . '.id', 'asc');
				break;
			case 'bill_receipt': //	ออกใบเสร็จรับเงิน
				$this->db->select('
					' . $this->table . '.id as bill_id,
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.text_number as bill_textnumber,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.shor_money as bill_shor,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.tax as bill_tax,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.transfered_daytime as bill_datetimeslip,
					date(' . $this->table . '.date_starts) as bill_datetime,
					' . $this->tablesecond . '.quantity as bill_qty,
					' . $this->tablesecond . '.total_price as product_price,
					' . $this->tablethird . '.total_price as bill_claimtotalprice,
					' . $this->tablethird . '.net_total as bill_claimnettotal,
					' . $this->tablethird . '.remark as bill_remark,
					' . $this->tablefive . '.name_th as product
				');
				$this->db->from($this->table);
				$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
				$this->db->join($this->tablethird, $this->table . '.id=' . $this->tablethird . '.bill_id', 'left');
				$this->db->join($this->tablefour, $this->tablesecond . '.promain_id=' . $this->tablefour . '.id', 'left');
				$this->db->join($this->tablefive, $this->tablesecond . '.prolist_id=' . $this->tablefive . '.id', 'left');
				$this->db->where($this->table . '.status_complete = 2');
				$this->db->where($this->table . '.status = 1');


				if ($valdate != '' && $valdateTo == '') {
					$this->db->where('date(' . $this->table . '.transfered_daytime) ', $valdate);
					$querydate = 'and date(' . $this->table . '.transfered_daytime) = "' . $valdate . '"';
				} else if ($valdate != '' && $valdateTo != '') {
					$this->db->where('date(' . $this->table . '.transfered_daytime) >=', $valdate);
					$this->db->where('date(' . $this->table . '.transfered_daytime) <=', $valdateTo);
					$querydate = 'and date(' . $this->table . '.transfered_daytime) >= "' . $valdate . '" and date(' . $this->table . '.transfered_daytime) <= "' . $valdateTo . '"';
				} else if ($valdateTo != '') {
					$this->db->where('date(' . $this->table . '.transfered_daytime) <=', $valdateTo);
					$querydate = 'and date(' . $this->table . '.transfered_daytime) <= "' . $valdateTo . '"';
				} else {
					$this->db->where('date(' . $this->table . '.transfered_daytime)', date('Y-m-d'));
					$querydate = 'and date(' . $this->table . '.transfered_daytime) = "' . date('Y-m-d') . '"';
				}

				if ($statusproduct != '') {
					$this->db->where('' . $this->table . '.status', $statusproduct);
					$querystatus = 'and ' . $this->table . '.status = ' . $statusproduct;
				}

				// $this->db->where('date('.$this->table.'.transfered_daytime) =','2020-08-13');
				// $querydate = 'and date('.$this->table.'.transfered_daytime) = "2020-08-13"';

				if ($requestData['bill_id'] != '') {
					$this->db->where('' . $this->table . '.id', $requestData['bill_id']);
					$querystatus = 'and ' . $this->table . '.id = ' . $requestData['bill_id'];
				}

				/* $this->db->or_where('('
									.$this->tablethird.'.status_complete=4 
									and '.$this->tablethird.'.status_claim in (1,2)
									and '.$this->tablethird.'.status_claimcomplete = 0 '
									.$querydate.$querystatus.
									')'); */

				// $this->db->group_by($this->table.'.code', 'asc');
				$this->db->order_by($this->table . '.transfered_daytime', 'asc');
				$this->db->order_by($this->table . '.id', 'asc');
				break;
			case 'bill_vatlistmonth': //	ดูรายการใบเสร็จ
				$this->db->select('
					' . $this->table . '.id as bill_id,
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.textcode as bill_textcode,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.text_number as bill_textnumber,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.delivery_formid as bill_gateway,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.shor_money as bill_shor,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.tax as bill_tax,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.transfered_daytime as bill_datetimeslip,
					' . $this->table . '.status_complete as bill_statuscomplete,
					' . $this->table . '.billstatus as bill_status,
					date(' . $this->table . '.date_starts) as bill_datetime,
					' . $this->tablesecond . '.quantity as bill_qty,
					' . $this->tablesecond . '.total_price as product_price,
					' . $this->tablethird . '.total_price as bill_claimtotalprice,
					' . $this->tablethird . '.net_total as bill_claimnettotal,
					' . $this->tablethird . '.remark as bill_remark,
					' . $this->tablefive . '.name_th as product,
					' . $this->tablesix . '.name_th as gateway,
					' . $this->tableseven . '.topic as bill_medthodname
					
				');
				$this->db->from($this->table);
				$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
				$this->db->join($this->tablethird, $this->table . '.id=' . $this->tablethird . '.bill_id', 'left');
				$this->db->join($this->tablefour, $this->tablesecond . '.promain_id=' . $this->tablefour . '.id', 'left');
				$this->db->join($this->tablefive, $this->tablesecond . '.prolist_id=' . $this->tablefive . '.id', 'left');
				$this->db->join($this->tablesix, $this->table . '.delivery_formid=' . $this->tablesix . '.id', 'left');
				$this->db->join($this->tableseven, $this->table . '.methodorder_id=' . $this->tableseven . '.id', 'left');

				$this->db->where($this->table . '.status_complete = 2');
				$this->db->where($this->table . '.status = 1');

				// echo "===".$searchfromdate;exit;
				if ($valdate != '' && $valdateTo == '') {
					$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') ', $valdate);
					$querydate = 'and date(' . $this->table . '.' . $searchfromdate . ') = "' . $valdate . '"';
				} else if ($valdate != '' && $valdateTo != '') {
					$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') >=', $valdate);
					$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') <=', $valdateTo);
					$querydate = 'and date(' . $this->table . '.' . $searchfromdate . ') >= "' . $valdate . '" and date(' . $this->table . '.' . $searchfromdate . ') <= "' . $valdateTo . '"';
				} else if ($valdateTo != '') {
					$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') <=', $valdateTo);
					$querydate = 'and date(' . $this->table . '.' . $searchfromdate . ') <= "' . $valdateTo . '"';
				} else {
					$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') =', date('Y-m-d'));
					$querydate = 'and date(' . $this->table . '.' . $searchfromdate . ') = "' . date('Y-m-d') . '"';
				}

				if ($statusproduct != '') {
					$this->db->where('' . $this->table . '.status', $statusproduct);
					$querystatus = 'and ' . $this->table . '.status = ' . $statusproduct;
				}

				/* if($valdate != '' && $valdateTo == ''){
					$this->db->where('date('.$this->table.'.date_starts) ',$valdate);
					$querydate = 'and date('.$this->table.'.date_starts) = "'.$valdate.'"';
				}
				else if($valdate != '' && $valdateTo != ''){
					$this->db->where('date('.$this->table.'.date_starts) >=',$valdate);
					$this->db->where('date('.$this->table.'.date_starts) <=',$valdateTo);
					$querydate = 'and date('.$this->table.'.date_starts) >= "'.$valdate.'" and date('.$this->table.'.date_starts) <= "'.$valdateTo.'"';
				}
				else if($valdateTo != ''){
					$this->db->where('date('.$this->table.'.date_starts) <=',$valdateTo);
					$querydate = 'and date('.$this->table.'.date_starts) <= "'.$valdateTo.'"';
				}else{
					$this->db->where('date('.$this->table.'.date_starts) =',date('Y-m-d'));
					$querydate = 'and date('.$this->table.'.date_starts) = "'.date('Y-m-d').'"';
				}
				
				if($statusproduct != ''){
					$this->db->where(''.$this->table.'.status',$statusproduct);
					$querystatus = 'and '.$this->table.'.status = '.$statusproduct;
				}
				 */

				$this->db->or_where('('
					. $this->tablethird . '.status_complete=4 
									and ' . $this->tablethird . '.status_claim in (1,2)
									and ' . $this->tablethird . '.status_claimcomplete = 0 '
					. $querydate . $querystatus .
					')');

				// $this->db->group_by($this->table.'.code', 'asc');
				$this->db->order_by($this->table . '.' . $searchfromdate, 'asc');
				$this->db->order_by($this->table . '.id', 'asc');
				break;
			case 'bill_listorders': //	รรายการสินค้าที่สั่ง
				$this->db->select('
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.text_number as bill_textnumber,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.user_starts as bill_recive,
					date(' . $this->table . '.date_starts) as bill_datetime,
					' . $this->tablesecond . '.quantity as bill_qty,
					' . $this->tablesecond . '.total_price as product_price,
					' . $this->tablethird . '.total_price as bill_claimtotalprice,
					' . $this->tablethird . '.net_total as bill_claimnettotal,
					' . $this->tablethird . '.remark as bill_remark,
					' . $this->tablefive . '.name_th as product,
					' . $this->tablefive . '.price as product_qty,
					staff.name as recive
				');
				$this->db->from($this->table);
				$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
				$this->db->join($this->tablethird, $this->table . '.id=' . $this->tablethird . '.bill_id', 'left');
				$this->db->join($this->tablefour, $this->tablesecond . '.promain_id=' . $this->tablefour . '.id', 'left');
				$this->db->join($this->tablefive, $this->tablesecond . '.prolist_id=' . $this->tablefive . '.id', 'left');
				$this->db->join('staff', $this->table . '.user_starts=staff.code', 'left');
				$this->db->where($this->table . '.status_complete = 2');
				$this->db->where($this->table . '.status = 1');


				if ($valdate != '' && $valdateTo == '') {
					$this->db->where('date(' . $this->table . '.date_starts) ', $valdate);
					$querydate = 'and date(' . $this->table . '.date_starts) = "' . $valdate . '"';
				} else if ($valdate != '' && $valdateTo != '') {
					$this->db->where('date(' . $this->table . '.date_starts) >=', $valdate);
					$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
					$querydate = 'and date(' . $this->table . '.date_starts) >= "' . $valdate . '" and date(' . $this->table . '.date_starts) <= "' . $valdateTo . '"';
				} else if ($valdateTo != '') {
					$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
					$querydate = 'and date(' . $this->table . '.date_starts) <= "' . $valdateTo . '"';
				}

				if ($statusproduct != '') {
					$this->db->where('' . $this->table . '.status', $statusproduct);
					$querystatus = 'and ' . $this->table . '.status = ' . $statusproduct;
				}

				if ($listorderid != '') {
					$this->db->where($this->table . '.id in(' . $listorderid . ')');
					$querystatus = 'and ' . $this->table . '.id in(' . $listorderid . ')';
				}

				$this->db->or_where('('
					. $this->tablethird . '.status_complete=4 
									and ' . $this->tablethird . '.status_claim in (1,2)
									and ' . $this->tablethird . '.status_claimcomplete = 0 '
					. $querydate . $querystatus .
					')');

				// $this->db->group_by($this->table.'.code', 'asc');
				$this->db->order_by($this->table . '.date_starts', 'asc');
				$this->db->order_by($this->table . '.id', 'asc');
				break;
			case 'bill_summary':
				$this->db->select('
					' . $this->table . '.id as bill_id,
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.date_starts as bill_datetime,

					' . $this->table . '.user_starts as bill_user_starts,
					' . $this->table . '.user_update as bill_user_update,
					' . $this->tablesecond . '.quantity as bill_qty,
					' . $this->tablesix . '.name_th as product_name,

					' . $this->tablethird . '.name_th as bill_typedelivery,
					' . $this->tablefour . '.total_price as bill_claimtotalprice,
					' . $this->tablefour . '.net_total as bill_claimnettotal,
					' . $this->tablefour . '.remark as bill_remark,
					' . $this->tablefive . '.topic as bill_gateway
				');
				$this->db->from($this->table);
				$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
				$this->db->join($this->tablethird, $this->table . '.delivery_formid=' . $this->tablethird . '.id', 'left');
				$this->db->join($this->tablefour, $this->table . '.id=' . $this->tablefour . '.bill_id', 'left');
				$this->db->join($this->tablefive, $this->table . '.methodorder_id=' . $this->tablefive . '.id', 'left');
				$this->db->join($this->tablesix, 'if('.$this->tablesecond.'.list_id is not null , '.$this->tablesecond.'.list_id='.$this->tablesix.'.id , '.$this->tablesecond.'.prolist_id='.$this->tablesix.'.id)',null,false);
				$this->db->where($this->table . '.status_complete = 2');
				$this->db->where($this->table . '.status = 1');

				if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
					$sqlserch = "( ";
					$sqlserch .= $this->table . ".code like '%" . $requestData['search']['value'] . "%' ";
					$sqlserch .= " OR " . $this->table . ".name like '%" . $requestData['search']['value'] . "%' ";
					$sqlserch .= " OR " . $this->table . ".phone_number like '%" . $requestData['search']['value'] . "%' ";
					// $sqlserch .=" OR ".$this->table.".remark like '%".$requestData['search']['value']."%' )";    
					$sqlserch .= " )";
					$this->db->where($sqlserch);
				} else {
					if ($valdate != '' && $valdateTo == '') {
						$this->db->where('date(' . $this->table . '.date_starts) ', $valdate);
						$querydate = 'and date(' . $this->table . '.date_starts) = "' . $valdate . '"';
					} else if ($valdate != '' && $valdateTo != '') {
						$this->db->where('date(' . $this->table . '.date_starts) >=', $valdate);
						$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
						$querydate = 'and date(' . $this->table . '.date_starts) >= "' . $valdate . '" and date(' . $this->table . '.date_starts) <= "' . $valdateTo . '"';
					} else if ($valdateTo != '') {
						$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
						$querydate = 'and date(' . $this->table . '.date_starts) <= "' . $valdateTo . '"';
					} else if ($valdate == '' && $valdateTo == '') {
						$this->db->where('date(' . $this->table . '.date_starts) =', date('Y-m-d'));
						$querydate = 'and date(' . $this->table . '.date_starts) = "' . date('Y-m-d') . '"';
						// $this->db->where('date('.$this->table.'.date_starts) =','2020-08-13');
						// $querydate = 'and date('.$this->table.'.date_starts) = "2020-08-13"';
					}

					if ($statusproduct != '') {
						$this->db->where('' . $this->table . '.status', $statusproduct);
						$querystatus = 'and ' . $this->table . '.status = ' . $statusproduct;
					}

					$this->db->or_where('('
						. $this->tablefour . '.status_complete=4 
										and ' . $this->tablefour . '.status_claim = 1
										and ' . $this->tablefour . '.status_claimcomplete = 0 '
						. $querydate . $querystatus .
						')');
				}
				// $this->db->group_by('' . $this->table . '.code');
				$this->db->order_by('' . $this->table . '.date_starts', 'asc');
				break;
			case 'bill_store': //	สรุปยอดสินค้า
				$this->db->select('
					' . $this->table . '.code as bill_code,
					' . $this->table . '.date_starts as bill_datetime,
					' . $this->table . '.billstatus as billstatus,

					' . $this->tablesecond . '.quantity as quantity,
					' . $this->tablesecond . '.promain_id as mainid,
					' . $this->tablesecond . '.prolist_id as listid,
					' . $this->tablesecond . '.total_price as price
				');
				$this->db->from($this->table);
				$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
				$this->db->join($this->tablethird, $this->tablesecond . '.promain_id=' . $this->tablethird . '.id', 'left');
				$this->db->join($this->tablefour, $this->tablesecond . '.prolist_id=' . $this->tablefour . '.id', 'left');
				$this->db->where($this->table . '.status_complete in (2,5)');
				$this->db->where($this->table . '.status', 1);

				if ($valdate != '' && $valdateTo == '') {
					$this->db->where('date(' . $this->table . '.date_starts) ', $valdate);
				} else if ($valdate != '' && $valdateTo != '') {
					$this->db->where('date(' . $this->table . '.date_starts) >=', $valdate);
					$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
				} else if ($valdateTo != '') {
					$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
				} else if ($valdate == '' && $valdateTo == '') {
					$this->db->where('date(' . $this->table . '.date_starts) =', date('Y-m-d'));
					$querydate = 'and date(' . $this->table . '.date_starts) = "' . date('Y-m-d') . '"';
					// $this->db->where('date('.$this->table.'.date_starts) =','2020-08-13');
					// $querydate = 'and date('.$this->table.'.date_starts) = "2020-08-13"';
				}

				if ($statusproduct != '') {
					$this->db->where('' . $this->table . '.status', $statusproduct);
				}
				//$this->db->group_by('date('.$this->table.'.date_starts)');
				$this->db->order_by('' . $this->table . '.date_starts', 'asc');
				break;
			case 'ranking_sale': //	ลำดับยอดขาย
				//
				//	split date
				$explodedate = explode("-", $valdate);
				$year = $explodedate[0];
				$month = $explodedate[1];

				/* $this->db->select('
					'.$this->table.'.code as bill_code,
					'.$this->table.'.name as bill_name,
					'.$this->table.'.phone_number as bill_phone,
					SUM('.$this->tablesecond.'.total_price) as bill_totalprice
				'); */
				$this->db->select('
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->tablesecond . '.total_price as bill_totalprice
				');
				$this->db->from($this->table);
				$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
				$this->db->where($this->table . '.status_complete = 2');
				$this->db->where($this->table . '.status = 1');

				if ($valdate != '' && $valdateTo == '') {
					$this->db->where('date(' . $this->table . '.date_starts) ', $valdate);
				} else if ($valdate != '' && $valdateTo != '') {
					$this->db->where('date(' . $this->table . '.date_starts) >=', $valdate);
					$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
				} else if ($valdateTo != '') {
					$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
				} else {
					$this->db->where('month(' . $this->table . '.date_starts) ', date('m'));
					$this->db->where('year(' . $this->table . '.date_starts) ', date('Y'));
				}

				if ($statusproduct != '') {
					$this->db->where('' . $this->table . '.status', $statusproduct);
				}

				if (!empty($requestData['search']['value'])) {
					$this->db->like('' . $this->table . '.name', $requestData['search']['value']);
				}

				// $this->db->group_by(''.$this->table.'.name');

				// $this->db->order_by('sum('.$this->tablesecond.'.total_price) DESC,sum('.$this->table.'.net_total) DESC');

				break;
		}
	}

	function make_datatables()
	{
		//	for total filter before limit
		$sql = $this->mdl_report->make_query();
		$query_nonelimit = $this->db->get();
		$totalfilter = $query_nonelimit->num_rows();

		//	sql to limit show in page
		$requestData = $_REQUEST;
		$sql = $this->mdl_report->make_query();
		// echo $this->db->get_compiled_select()."***";

		switch ($requestData['table']) {
			case 'bill_store':
				// $this->db->limit(0);
				break;
			case 'ranking_sale':
				// $this->db->limit(0);
				break;
			case 'bill_receipt':
				$length = 20;		//	set max total query
				if ($totalfilter > $length) {

					if ($requestData['getlength'] && $requestData['getlength'] <= $requestData['maxlength']) {
						$start = $requestData['getlength'];
					} else {
						$start = 0;
					}

					$this->db->limit($length, $start);
				}
				break;
			default:
				if ($requestData['start'] != 0) {
					$this->db->limit($requestData['length'], $requestData['start']);
				} else {
					$this->db->limit($requestData['length']);
				}
				break;
		}
		// echo $totalfilter."*****".$this->db->get_compiled_select();exit;
		$query = $this->db->get();

		$result = array(
			"query"			=> $query->result(),
			"numberfilter"	=> $totalfilter,
			"startno"		=> $requestData['start']
		);

		return $result;
	}


	function make_datatablesProduct()
	{
		$valdate = $this->input->get('valdate');
		$valdateTo = $this->input->get('valdateto');

		$statusproduct = $this->input->get('statusproduct');

		#	select order id
		$productid = $this->input->get('productid');

		$querydate = "";
		$querystatus = "";
		$queryproduct = "";

		$this->db->select('
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.date_starts as bill_datetime,
					' . $this->table . '.transfered_daytime as bill_datetimeslip,

					' . $this->table . '.user_starts as bill_user_starts,
					' . $this->table . '.user_update as bill_user_update,

					' . $this->tablesecond . '.prolist_id as product_id,
					' . $this->tablesecond . '.quantity as bill_qty,
					' . $this->tablesecond . '.total_price as bill_productprice,
					' . $this->tablethird . '.name_us as bill_typedelivery,
					' . $this->tablefour . '.total_price as bill_claimtotalprice,
					' . $this->tablefour . '.net_total as bill_claimnettotal,
					' . $this->tablefour . '.remark as bill_remark,
					retail_methodorder.topic as methodtopic
				');
		$this->db->from($this->table);
		$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
		$this->db->join($this->tablethird, $this->table . '.delivery_formid=' . $this->tablethird . '.id', 'left');
		$this->db->join($this->tablefour, $this->table . '.id=' . $this->tablefour . '.bill_id', 'left');
		$this->db->join('retail_methodorder', $this->table . '.methodorder_id=retail_methodorder.id', 'left');
		$this->db->where($this->table . '.status_complete = 2');
		$this->db->where($this->table . '.status = 1');

		if ($productid != '' && $productid != "all") {
			$this->db->where($this->tablesecond . '.prolist_id = ' . $productid);
			$queryproduct = 'and date(' . $this->tablesecond . '.prolist_id) = "' . $productid . '"';
		}

		if ($valdate != '' && $valdateTo == '') {
			$this->db->where('date(' . $this->table . '.date_starts) ', $valdate);
			$querydate = 'and date(' . $this->table . '.date_starts) = "' . $valdate . '"';
		} else if ($valdate != '' && $valdateTo != '') {
			$this->db->where('date(' . $this->table . '.date_starts) >=', $valdate);
			$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
			$querydate = 'and date(' . $this->table . '.date_starts) >= "' . $valdate . '" and date(' . $this->table . '.date_starts) <= "' . $valdateTo . '"';
		} else if ($valdateTo != '') {
			$this->db->where('date(' . $this->table . '.date_starts) <=', $valdateTo);
			$querydate = 'and date(' . $this->table . '.date_starts) <= "' . $valdateTo . '"';
		}

		if ($statusproduct != '') {
			$this->db->where('' . $this->table . '.status', $statusproduct);
			$querystatus = 'and ' . $this->table . '.status = ' . $statusproduct;
		}

		$this->db->or_where('('
			. $this->tablefour . '.status_complete=4 
							and ' . $this->tablefour . '.status_claim = 1
							and ' . $this->tablefour . '.status_claimcomplete = 0 '
			. $querydate . $querystatus . $queryproduct .
			')');

		if ($productid != "all") {
			// $this->db->group_by('' . $this->table . '.code', 'asc');
		}
		$this->db->order_by('' . $this->table . '.date_starts', 'asc');

		// echo $this->db->get_compiled_select(null,FALSE);
		if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}
		$query = $this->db->get();
		return $query->result();
	}



	function getPromotionStatus($datestart, $dateend)
	{
		//	setting
		($datestart != '' ? $datepro_start = date('Y-m-d', strtotime($datestart)) : $datepro_start = null);
		($dateend != '' ? $datepro_end = date('Y-m-d', strtotime($dateend)) : $datepro_end = null);
		$curdate = date('Y-m-d');
		$datecome = date('Y-m-d', strtotime('+' . $this->set['totalcomming']));

		if ($datepro_start == $curdate) {
			$result = "<span class='text-success'>online</span>";
		} else if (($datepro_start == null || $datepro_start <= $curdate) && ($datepro_end == null || $datepro_end >= $curdate)) {
			//
			//	promotion online
			$result = "<span class='text-success'>online</span>";
		} else if ($datepro_start > $curdate && $datepro_start <= $datecome) {
			//
			//	promotion comming soon in 7 days
			$result = "<span class='text-warning'>comming</span>";
		} else {
			$result = "";
		}

		return $result;
	}

	public function ajaxListbill()
	{
		$txt = "";
		$valdate = $this->input->post('datestart');
		$valdateTo = $this->input->post('dateto');
		$searchfromdate = $this->input->post('searchfromdate');

		$this->db->select('
					' . $this->table . '.id as bill_id,
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.text_number as bill_textnumber,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.date_starts as bill_datetime,
					' . $this->tablethird . '.total_price as bill_claimtotalprice,
					' . $this->tablethird . '.net_total as bill_claimnettotal,
					' . $this->tablethird . '.remark as bill_remark
				');
		$this->db->from($this->table);
		$this->db->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left');
		$this->db->join($this->tablethird, $this->table . '.id=' . $this->tablethird . '.bill_id', 'left');
		$this->db->where($this->table . '.status_complete = 2');
		$this->db->where($this->table . '.status = 1');

		$sqlin = "";
		if ($valdate != '' && $valdateTo == '') {
			$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') ', $valdate);
			$sqlin .= "and date(" . $this->table . "." . $searchfromdate . " = '" . $valdate . "')";
		} else if ($valdate != '' && $valdateTo != '') {
			$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') >=', $valdate);
			$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') <=', $valdateTo);
			$sqlin .= "and date(" . $this->table . "." . $searchfromdate . " >= '" . $valdate . "')";
			$sqlin .= "and date(" . $this->table . "." . $searchfromdate . " <= '" . $valdateTo . "')";
		} else if ($valdateTo != '') {
			$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') <=', $valdateTo);
			$sqlin .= "and date(" . $this->table . "." . $searchfromdate . " <= '" . $valdateTo . "')";
		} else {
			$this->db->where('date(' . $this->table . '.' . $searchfromdate . ') =', date('Y-m-d'));
			$sqlin = 'and date(' . $this->table . '.' . $searchfromdate . ') = "' . date('Y-m-d') . '"';
		}


		$this->db->or_where('('
			. $this->tablethird . '.status_complete=4 
							and ' . $this->tablethird . '.status_claim in (1,2)
							and ' . $this->tablethird . '.status_claimcomplete = 0 ' .
			$sqlin
			. ')');

		$this->db->group_by($this->table . '.code', 'asc');
		$this->db->order_by($this->table . '.date_starts', 'asc');
		$number = $this->db->count_all_results(null, FALSE);
		$query = $this->db->get();

		if ($number > 0) {
			$error = 0;

			$txt .= '<div class="">';
			$txt .= '<a href="javascript:selectAllCheckbox()" class="">เลือกทั้งหมด';
			$txt .= '</a>';
			$txt .= '</div>';

			$number = 1;
			foreach ($query->result() as $row) {
				$txt .= '<div class="">';
				$txt .= '<input class="" type="checkbox" id="chkbox_' . $row->bill_id . '" value="' . $row->bill_id . '">';
				$txt .= '<label for="customCheckbox1" class=""> ' . $number . ". " . $row->bill_code . ' : ' . $row->bill_name . '</label>';
				$txt .= '</div>';

				$number++;
			}
		} else {
			$error = 1;
		}

		$result = array(
			'error_code' => $error,
			'txt'		 => $txt
		);
		$data = json_encode($result);

		return $data;
	}

	//	@param	array		@array = 
	//			date		@text = date(Y-m-d) starts
	//			dateto		@text = date(Y-m-d) to end
	public function jsonCreditnote($array)
	{
		$this->load->library('creditnote');

		//	setting
		$date = (trim($array['date']) ? $array['date'] : date('Y-m-d'));
		$dateto = ($array['dateto'] ? $array['dateto'] : date('Y-m-d'));

		$arrayselect = array(
			'retail_creditnote.id as cn_id',
			'retail_creditnote.code as cn_code',
			'retail_creditnote.codereport as cn_codereport',
			'retail_creditnote.rt_id as cn_rt_id',
			'retail_creditnote.rt_bill_code as cn_rt_bill_code',
			'retail_creditnote.total_price as cn_total_price',
			'retail_creditnote.parcel_cost as cn_parcel_cost',
			'retail_creditnote.delivery_fee as cn_delivery_fee',
			'retail_creditnote.discount_price as cn_discount_price',
			'retail_creditnote.shor_money as cn_shor_money',
			'retail_creditnote.tax as cn_tax',
			'retail_creditnote.net_total as cn_net_total',
			'retail_creditnote.approve as cn_approve',
			'retail_creditnote.approve_store as cn_approve_store',
			'retail_creditnote.complete as cn_complete',
			'retail_creditnote.remark as cn_remark',
			'retail_creditnote.loss as cn_loss',
			'retail_creditnote.date_starts as cn_date_starts',
			'retail_creditnote.user_starts as cn_user_starts'
		);
		$arraywhere = array(
			/* 'date(retail_creditnote.apst_date) >=' 	=> $date,
			'date(retail_creditnote.apst_date) <=' 	=> $dateto, */
			'retail_creditnote.codereport is not null' 					=> null,
			'if(retail_creditnote.loss = 1,date(retail_creditnote.appr_date) >= "'.$date.'" and date(retail_creditnote.appr_date) <= "'.$dateto.'" ,date(retail_creditnote.apst_date) >= "'.$date.'" and date(retail_creditnote.apst_date) <= "'.$dateto.'" )' 	=> null,
			'retail_creditnote.status' 					=> 1
		);

		$array = array(
			'order_by'		=> array('retail_creditnote.id desc'),
			'limit'			=> array(
				'total'	=> 25,
				'start'	=> 0
			),
		);

		$q = $this->creditnote->query_creditnotebill($arrayselect, $arraywhere, null);
		$num = $q->num_rows();

		$result = "";
		if ($num) {
			#
			#	setting
			$arrayresult = array();
			foreach ($q->result() as $row) {
				$custname = "";
				$custaddress = "";
				$sqlbill = $this->db->select('
					TEXT_NUMBER,
					NAME,
					ADDRESS
				')
				->from('retail_bill')
				->where('id',$row->cn_rt_id);
				$qbill = $sqlbill->get();
				$numbill = $qbill->num_rows();
				if($numbill){
					$rbill = $qbill->row();

					if (trim($rbill->TEXT_NUMBER) != "") {
						$custcitizen = trim($rbill->TEXT_NUMBER);
					} else {
						$custcitizen = "0000000000000";
					}

					if (trim($rbill->NAME) != "") {
						$custname = trim($rbill->NAME);
					}

					if (trim($rbill->ADDRESS) != "") {
						$custaddress = trim($rbill->ADDRESS);
					}
				}

				$tax = $row->cn_tax - ($row->cn_tax * 2);
				$shor = $row->cn_shor_money - ($row->cn_shor_money * 2);
				$parcel = $row->cn_parcel_cost - ($row->cn_parcel_cost * 2);
				$delivery = $row->cn_delivery_fee - ($row->cn_delivery_fee * 2);
				$discount = $row->cn_discount_price - ($row->cn_discount_price * 2);
				$totalprice = $row->cn_total_price - ($row->cn_total_price * 2);
				$netprice = $row->cn_net_total - ($row->cn_net_total * 2);

				$write_array[] = array(
					"id"		=> $row->cn_id,
					"code"		=> $row->cn_code,
					"invoice"	=> $row->cn_codereport,

					"name"		=> $custname,
					"address"		=> $custaddress,
					"citizen"		=> $custcitizen,

					"statuscomplete"	=> $row->cn_complete,
					"totalprice"	=> $totalprice,
					"tax"			=> $tax,
					"shor"			=> $shor,
					"parcel"		=> $parcel,
					"delivery"		=> $delivery,
					"discount"		=> $discount,
					"netprice"		=> $netprice,
					"date"			=> date('Y-m-d', strtotime($row->cn_date_starts))
				);
			}

			#
			#	group date order
			$ardate = unique_multidim_array($write_array, 'date');

			#	group order
			$order = unique_multidim_array($write_array, 'code');

			#
			#	new index
			#	code
			$code = array();
			foreach ($order as $row => $val) {
				array_push($code, $val);
			}
			#	date
			$arr_date = array();
			foreach ($ardate as $row => $val) {
				array_push($arr_date, $val);
				$arr_groupdate[] = array_keys(array_column($code, 'date'), $val['date']);
			}

			$startdate = reset($arr_date);
			$enddate = end($arr_date);
			$dateto = array($startdate['date'], $enddate['date']);

			$number = 1;

			foreach ($arr_date as $rows => $val) {
				foreach ($arr_groupdate[$rows] as $row => $val) {

					if ($code[$val]['tax'] == null ? $tax = 0 : $tax = $code[$val]['tax']);

					$bill_delivery = $code[$val]['delivery'];
					$discount = $code[$val]['discount'];
					$shor = $code[$val]['shor'];	//	divmoney

					$totalprice = $code[$val]['totalprice'];
					$totalsumprice = ($totalprice + $bill_delivery + $shor + $code[$val]['tax']) + $discount;
					$vat = ($totalsumprice * 7) / 107;
					$price = $totalsumprice;

					array_push(
						$arrayresult,
						array(
							"id"		=> $code[$val]['id'],
							"code"		=> $code[$val]['code'],
							"textcode"	=> $code[$val]['textcode'],
							"date"		=> $code[$val]['date'],
							"invoice"	=> $code[$val]['invoice'],
							"name"		=> $code[$val]['name'],
							"citizen"	=> $code[$val]['citizen'],
							"discount"	=> $code[$val]['discount'],
							"tax"		=> $tax,
							"gateway"	=> null,
							"parcel"	=> $code[$val]['parcel'],
							"shor"		=> $code[$val]['shor'],
							"delivery"	=> $code[$val]['delivery'],
							"price"		=> $price,
							"totalprice"	=> $code[$val]['totalprice'],
							"datetimeslip"	=> null,
							"statuscomplete"	=> $code[$val]['statuscomplete'],
							"billstatus"	=> null,
							"vat"			=> $vat,
							"datetrans"		=> null,
							"medthod"		=> null
						)
					);

					$number++;
				}
			}
		}

		$error = 0;
		$result = array(
			"arrayresult"		=> $arrayresult,
			"dateto"			=> $dateto
		);

		return $result;
	}

	public function jsonListmonth($query)
	{
		$result = "";
		if (count($query) > 0) {
			#
			#	setting
			$arrayresult = array();

			#
			#	set array
			/* echo "<pre>";
			print_r($query);
			echo "</pre>"; */
			foreach ($query as $row => $val) {
				$write_array[] = array(
					"id"		=> $val->bill_id,
					"code"		=> $val->bill_code,
					"textcode"	=> $val->bill_textcode,
					"name"		=> $val->bill_name,
					"address"		=> $val->bill_address,
					"citizen"		=> $val->bill_textnumber,
					"product"		=> $val->product,
					"qty"			=> $val->bill_qty,
					"price"			=> $val->product_price,
					"totalprice"	=> $val->bill_totalprice,
					"datetimeslip"	=> $val->bill_datetimeslip,
					"statuscomplete"	=> $val->bill_statuscomplete,
					"billstatus"	=> $val->bill_status,
					"gateway"		=> $val->bill_gateway,
					"tax"			=> $val->bill_tax,
					"shor"			=> $val->bill_shor,
					"parcel"		=> $val->bill_parcel,
					"delivery"		=> $val->bill_delivery,
					"discount"		=> $val->bill_discount,
					"netprice"		=> $val->bill_nettotal,
					"date"			=> $val->bill_datetime,
					"datetrans"			=> $val->bill_datetimeslip,
					"medthod"			=> $val->bill_medthodname,
				);
			}

			#
			#	group date order
			$ardate = unique_multidim_array($write_array, 'date');

			#	group order
			$order = unique_multidim_array($write_array, 'code');

			#
			#	new index
			#	code
			$code = array();
			foreach ($order as $row => $val) {
				array_push($code, $val);
			}
			#	date
			$arr_date = array();
			foreach ($ardate as $row => $val) {
				array_push($arr_date, $val);
				$arr_groupdate[] = array_keys(array_column($code, 'date'), $val['date']);
			}

			$startdate = reset($arr_date);
			$enddate = end($arr_date);
			$dateto = array($startdate['date'], $enddate['date']);


			$number = 1;

			foreach ($arr_date as $rows => $val) {
				#
				#	setting number invoice
				$ordernumber = 0;

				$getinvoice = gen_invretail($val['date']);

				foreach ($arr_groupdate[$rows] as $row => $val) {
					//gen invoice
					$ordernumber++;
					$genordernumber = get_Number3keyonly($ordernumber);
					$invoice = $getinvoice . "-" . $genordernumber;

					if ($code[$val]['citizen'] != "") {
						$citizen = $code[$val]['citizen'];
					} else {
						$citizen = "0000000000000";
					}

					$bill_delivery = $code[$val]['delivery'];
					$discount = $code[$val]['discount'];
					$shor = $code[$val]['shor'];	//	divmoney

					if ($code[$val]['tax'] == null ? $tax = 0 : $tax = $code[$val]['tax']);

					//	find gateway
					$gateway = get_WherePara('delivery', 'id', $code[$val]['gateway']);

					$totalprice = $code[$val]['totalprice'];
					// $totalsumprice = ($totalprice + $bill_delivery + $shor) - $discount;
					$totalsumprice = ($totalprice + $bill_delivery + $shor + $code[$val]['tax']) + $discount;
					$vat = ($totalsumprice * 7) / 107;
					// $vat = $totalprice;
					$price = $totalsumprice;

					if ($code[$val]['datetimeslip'] != "") {
						$ex = explode(" ", $code[$val]['datetimeslip']);
						$datetimeslip = $ex[0];
					}

					array_push(
						$arrayresult,
						array(
							"id"		=> $code[$val]['id'],
							"code"		=> $code[$val]['code'],
							"textcode"	=> $code[$val]['textcode'],
							"date"		=> $code[$val]['date'],
							"invoice"	=> $invoice,
							"name"		=> $code[$val]['name'],
							"citizen"	=> $citizen,
							"discount"	=> $code[$val]['discount'],
							"tax"		=> $tax,
							"gateway"	=> $gateway->NAME_US,
							"parcel"	=> $code[$val]['parcel'],
							"shor"		=> $shor,
							"delivery"	=> $code[$val]['delivery'],
							"price"		=> $price,
							"totalprice"	=> $code[$val]['totalprice'],
							"datetimeslip"	=> $code[$val]['datetimeslip'],
							"statuscomplete"	=> $code[$val]['statuscomplete'],
							"billstatus"	=> $code[$val]['billstatus'],
							"vat"			=> $vat,
							// "datetrans"		=> $datetimeslip." slip2",
							"datetrans"		=> $code[$val]['datetimeslip'],
							"medthod"		=> $code[$val]['medthod']
						)
					);

					$number++;
				}
			}

			$error = 0;
			$result = array(
				"arrayresult"		=> $arrayresult,
				"dateto"			=> $dateto
			);
		}

		return $result;
	}
	#
	#	show data in modal box in webpage
	#
	public function ajaxListmonth($query)
	{
		$txt = "";
		$error = 1;

		$request = $_REQUEST;
		$valdate = $request['valdate'];
		$valdateTo = $request['valdateto'];
		$arrayset = array(
			'date'			=> $valdate,
			'dateto'		=> $valdateTo
		);
		$array = array();

		if (count($query) > 0) {
			#	call function array list invoice
			#	result = array(['arrayresult']);
			#	
			$array = $this->jsonListmonth($query);
			$newarray = $array['arrayresult'];
		}

		//	check creditnote
		$creditnote = $this->jsonCreditnote($arrayset);
		if($creditnote['arrayresult']){
			if($array['arrayresult']){
				$newarray = array_merge($array['arrayresult'], $creditnote['arrayresult']);
			}else{
				$newarray = $creditnote['arrayresult'];
			}
		}

		if (count($newarray) > 0) {
			#
			#	group date order
			$ardate = unique_multidim_array($newarray, 'date');

			#	group order
			$order = unique_multidim_array($newarray, 'code');

			#
			#	new index
			#	code
			$code = array();
			foreach ($order as $row => $val) {
				array_push($code, $val);
			}
			#	date
			$arr_date = array();
			foreach ($ardate as $row => $val) {
				array_push($arr_date, $val);
				$arr_groupdate[] = array_keys(array_column($code, 'date'), $val['date']);
			}

			$startdate = reset($arr_date);
			$enddate = end($arr_date);
			$dateto = array($startdate['date'], $enddate['date']);

			$dataarray = array();
			foreach ($arr_date as $rows => $val) {
				foreach ($arr_groupdate[$rows] as $row => $val) {

					$code[$val]['citizen'];

					array_push(
						$dataarray,
						$code[$val]
					);
				}
			}

			/* echo "<pre>";
			echo "date: " . $valdate;
			echo "dateto: " . $valdateTo . "<br>";
			echo "=======date=======";
			print_r($ardate);
			echo "=======order=======";
			print_r($order);
			echo "=======orderresult=======";
			print_r($dataarray);
			echo "=======set=======";
			print_r($array);
			echo "=================";
			print_r($creditnote);
			echo "</pre>";
			exit; */

			$number = 1;

			//	button report billvat
			$report_receipt = chkPermissPage('report_receipt');


			$txt .= '<table id="" class="table table-sm table-responsive nowrap">';
			$txt .= '<tr>';
			$txt .= '<td></td>';
			$txt .= '<td>Inv.</td>';
			$txt .= '<td>Code.</td>';
			$txt .= '<td>วันที่ใบกำกับ</td>';
			$txt .= '<td>เลขที่ใบกำกับ</td>';
			$txt .= '<td>วันที่โอน</td>';
			$txt .= '<td class="" width="175px;">ชื่อ-นามสกุล</td>';
			// $txt .= '<td>เลขที่ภาษี</td>';
			$txt .= '<td>Total</td>';
			$txt .= '<td>ค่าจัดส่ง</td>';
			$txt .= '<td>Fee SP.</td>';
			$txt .= '<td>Vat</td>';
			$txt .= '<td>Net Total</td>';
			$txt .= '<td>สื่อ</td>';
			$txt .= '<td>ช่องทาง</td>';
			$txt .= '<td>ประเภท</td>';
			$txt .= '</tr>';
			foreach ($dataarray as $key => $val) {
				if ($val['billstatus'] == "C") {
					$typepay = "เก็บปลายทาง";
				} else {
					$typepay = "ปกติ";
				}

				if (strpos($val['code'],'FC') !== false) {
					$typepay = "ใบลดหนี้";
				}

				$btn_specialbill = "";
				if (($report_receipt == 1 && $this->input->get('searchfromdate') == 'transfered_daytime') && $typepay != "ใบลดหนี้") {
					// $btn_specialbill = "<button id='btnsp' class='btnsp_report btn btn-sm btn-light mr-2' value='bill_receipt' data-number='".$number."' >ออกใบเสร็จรับเงิน</button>";
					$btn_specialbill = "<i class='fa fa-file btnsp_report' value='bill_receipt' data-billid='" . $val['id'] . "' data-number='" . $number . "' ></i>";
				}

				$txt .= '<tr>';
				$txt .= '<td>' . $number . "</td>";
				$txt .= '<td>' . $btn_specialbill . $val['code'] . "</td>";
				$txt .= '<td>' . $val['textcode'] . "</td>";
				$txt .= '<td>' . $val['date'] . "</td>";
				$txt .= '<td>' . $val['invoice'] . "</td>";
				$txt .= '<td>' . $val['datetrans'] . "</td>";
				$txt .= '<td class="">' . $val['name'] . "</td>";
				// $txt .= '<td>'.$val['citizen']."</td>";
				$txt .= '<td>' . number_format($val['totalprice'], 2) . "</td>";
				$txt .= '<td>' . number_format($val['delivery'], 2) . "</td>";
				$txt .= '<td>' . number_format($val['shor'], 2) . "</td>";
				$txt .= '<td>' . number_format($val['vat'], 2) . "</td>";
				$txt .= '<td>' . number_format($val['price'], 2) . "</td>";
				$txt .= '<td>' . $val['medthod'] . "</td>";
				$txt .= '<td>' . $val['gateway'] . "</td>";
				$txt .= '<td>' . $typepay . "</td>";
				$txt .= '</tr>';

				$number++;
			}
			$txt .= '</table>';
			$error = 0;
		}
		$result = array(
			'error_code' => $error,
			'txt'		 => $txt
		);
		$data = json_encode($result);

		return $data;
	}

	function billDetail($code)
	{
		$query = $this->db->select('
					' . $this->table . '.code as bill_code,
					' . $this->table . '.name as bill_name,
					' . $this->table . '.address as bill_address,
					' . $this->table . '.text_number as bill_textnumber,
					' . $this->table . '.phone_number as bill_phone,
					' . $this->table . '.parcel_cost as bill_parcel,
					' . $this->table . '.delivery_fee as bill_delivery,
					' . $this->table . '.discount_price as bill_discount,
					' . $this->table . '.net_total as bill_nettotal,
					' . $this->table . '.pic_payment as bill_pic,
					' . $this->table . '.pic_payment2 as bill_pic2,
					' . $this->table . '.total_price as bill_totalprice,
					' . $this->table . '.date_starts as bill_datetime,
					' . $this->tablethird . '.total_price as bill_claimtotalprice,
					' . $this->tablethird . '.net_total as bill_claimnettotal,
					' . $this->tablethird . '.remark as bill_remark
				')
			->from($this->table)
			->join($this->tablesecond, $this->table . '.id=' . $this->tablesecond . '.bill_id', 'left')
			->join($this->tablethird, $this->table . '.id=' . $this->tablethird . '.bill_id', 'left')
			->where($this->table . '.code', $code)
			->get();
		$r = $query->row();

		return $r;
	}

	function get_filtered_data()
	{
		$this->make_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_all_data()
	{
		$this->db->select("ID");
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	#
	#	show data in modal box in webpage
	#
	public function ajaxProductList()
	{
		$txt = "";
		$error = 1;


		$array = $this->mdl_report->jsonListProduct();

		$error = 0;

		$result = array(
			'error_code' => $error,
			'txt'		 => $txt,
			'data'		 => $array
		);
		$data = json_encode($result);

		return $data;
	}

	public function jsonListProduct()
	{
		$result = "";

		#
		#	setting
		// $arrayresult = array();

		#
		#	query
		$sql = $this->db->select(
			'id,
								code,
								name_th'
		)
			->from('retail_productlist')
			->where('retail_productlist.status', 1);
		$total_result = $sql->count_all_results(null, FALSE);
		$query = $sql->get();

		#
		#	set array
		foreach ($query->result() as $row) {
			$write_array[] = array(
				"id"		=> $row->id,
				"code"		=> $row->code,
				"name"		=> $row->name_th
			);
		}

		$error = 0;
		$result = array(
			"arrayresult"		=> $write_array
		);

		return $result;
	}
}
