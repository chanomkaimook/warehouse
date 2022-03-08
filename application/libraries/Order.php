<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Order {
	////////////////////////////
	////	setting
	public function __construct()
    {
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
		//
        //	setting
        $ci->orderset = array(
            // 'datenow'   => date('Y-m-d')
            'datenow'	=> $ci->set['datenow']  //  ctl_retailstock
        );
		//
		//
	}
	////////////////////////////
    ////////////////////////////
    //
    //  for infomation stock
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return array
    //  @atr cut_order           @int    = total order
    //  @atr total_order         @int    = total product in stock when cut order success
    //  @atr cut_stock           @int    = total product in order success
    //  @atr waite_stock         @int    = total order success but transfer enother day (date > now)
    //  @atr out_stock           @int    = total order success but no lot id
    //  @atr net_totalstock      @int    = total stock calculate stock add
    //  @atr total_stock         @int    = total to calculated
    //
    function informationStock($productid){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //	setting
        $arrayparam = array(
            'productid'		=> $productid
        );
        
        //
        //  get total stock
        $st_qty = $ci->order->get_totalStock($productid);

        //  total product order success
        $productpay = 0;
        $getproductpay = $ci->order->get_informationStockSuccess($arrayparam);
        if($getproductpay){
            $productpay = $getproductpay['qty'];
        }

        //
        //	total order waite (date_start > now)
        $orderwaite_total = 0;
        $getorderwaite = $ci->order->get_informationOrderWaite($arrayparam);
        if($getorderwaite){
            $orderwaite_total= $getorderwaite['qty'];
        }

        //
        //	total order bill waite approve
        $productorder = 0;
        $getproductorder = $ci->order->get_informationOrder($arrayparam);
        if($getproductorder){
            $productorder= $getproductorder['qty'];
        }

        //
        //	total product in stock lot
        $total_stocklot = 0;
        $total_stocklot = $ci->order->get_productStockLot($arrayparam);

        //
        //	total order bill 
        $order_total = 0;
        $order_total= ($st_qty + $total_stocklot['total']) - $productorder;

        //
        //  order outstanding
        $orderout_total = 0;
        $orderout = $ci->order->get_productOutStockLot($arrayparam);
        $orderout_total = $orderout['total'];
        
        //  total product in stock and stock add 
        // $nettotal = $st_qty + $total_stocklot['total'];			// สำหรับช่องทั้งหมด รวมกับจำนวนสินค้าที่เพิ่มเข้ามาใหม่
        $nettotal = $st_qty;

        //  total product in stock now  
        $stock_total = ($nettotal+$total_stocklot['total']) - $productpay;
		// echo $productid."=".$nettotal."+".$total_stocklot['total']."-".$productpay."<br>";
        //  total product in stock now  ]
        $order_total = ($nettotal+$total_stocklot['total']) - $productorder;
        
        $result = array(
            'cut_order'           => $productorder,
            'total_order'         => $order_total,
            'cut_stock'           => $productpay,
            'waite_stock'         => $orderwaite_total,
            'out_stock'           => $orderout_total,
            'net_totalstock'      => $nettotal,
            'total_stock'         => $stock_total
        );

        return $result;
    }
    //
    //  for update before value to stock now
    function updateStockDaily(){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        $sql = $ci->db->select('
            retail_productlist.ID AS std_productid,
            stock.date_update as std_update
        ')
        ->from('retail_productmain')
        ->join('retail_productlist', "retail_productmain.ID = retail_productlist.PROMAIN_ID", 'right')
        ->join('stock', "retail_productlist.id = stock.retail_productlist_id", 'left')
        // ->where('retail_productlist.promain_id != ',6)
        ->where('retail_productlist.promain_id not in(6,12)')
        ->where('if(stock.date_update is not null, stock.date_update < "'.$ci->orderset["datenow"].'",true)',null,false)
        ->order_by('retail_productlist.id',$ci->orderset['datenow']);
        // echo $sql->get_compiled_select();exit;
        $num = $ci->db->count_all_results(null,false);
        $query = $sql->get();
        
        if($num > 0){
            sleep(2);
            foreach($query->result() as $row){
                // 
                //  check duplicate product
                $chk = get_WhereParaNum('stock','retail_productlist_id',$row->std_productid);
                //
                //  calculate total
                $getstock = $ci->order->totalStockLastday($row->std_productid);
                // echo $getstock."==";exit;
                if($chk > 0){
                    $dataupdate = array(
                        'qty'     => $getstock['total_stock'],
                        'date_update'   => $ci->orderset['datenow']
                    );
                    $ci->db->where('stock.retail_productlist_id', $row->std_productid);
                    $ci->db->update('stock', $dataupdate);
                    //
                    //  update status upload stock detail
                    $dataupdate = array(
                        'statusupload'     => 1
                    );
                    $ci->db->where('stock_lotdetail.retail_productlist_id', $row->std_productid);
                    $ci->db->where('date(stock_lotdetail.date_starts) < ', $ci->orderset['datenow']);
                    $ci->db->update('stock_lotdetail', $dataupdate);

                    //
                    //  update status upload retail lot
                    $dataupdate = array(
                        'statusupload'     => 1
                    );
                    $ci->db->where('retail_lot.retail_billdetail_prolist_id', $row->std_productid);
                    $ci->db->where('date(retail_lot.date_starts) < ', $ci->orderset['datenow']);
                    $ci->db->update('retail_lot', $dataupdate);
                }else{
                    //
                    //  query insert
                    $datainsert = array(
                        'qty'     => $getstock['total_stock'],
                        'date_update'   => $ci->orderset['datenow'],
                        'retail_productlist_id' 	=> $row->std_productid
                    );
                    $ci->db->insert('stock', $datainsert);
                    if($ci->db->insert_id()){
                        //
                        //  update status upload stock detail
                        $dataupdate = array(
                            'statusupload'     => 1
                        );
                        $ci->db->where('stock_lotdetail.retail_productlist_id', $row->std_productid);
                        $ci->db->where('date(stock_lotdetail.date_starts) < ', $ci->orderset['datenow']);
                        $ci->db->update('stock_lotdetail', $dataupdate);

                        //
                        //  update status upload retail lot
                        $dataupdate = array(
                            'statusupload'     => 1
                        );
                        $ci->db->where('retail_lot.retail_billdetail_prolist_id', $row->std_productid);
                        $ci->db->where('date(retail_lot.date_starts) < ', $ci->orderset['datenow']);
                        $ci->db->update('retail_lot', $dataupdate);

                    }
                }
               
            }   //  end foreach
        }
    }
    //
    //  get total stock
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return @int    = total
    //
    function get_totalStock($productid){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  query
        $sql = $ci->db->select('
            stock.qty AS st_qty,
        ')
        ->from('stock')
        ->where('stock.retail_productlist_id',$productid);
        $num = $sql->count_all_results(null,false);
        $query = $sql->get(); 
        if($num > 0){
            $row = $query->row();
            $st_qty = $row->st_qty;
        }else{
            $st_qty = 0;
        }

        $result = $st_qty;
        return $result;
    }
     //
    //  get total stock add last day 
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return @int    = total
    //
    function get_productStockLotLastday($productid){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  query
        $sql = $ci->db->select('
			stock_lot.id as st_id,
            stock_lot.lotdate as st_lotdate,
            stock_lotdetail.id as std_id,
            stock_lotdetail.qty as std_qty
        ')
		->from('stock_lot')
		->join('stock_lotdetail','stock_lot.id = stock_lotdetail.stock_lot_id','left')
		->where('stock_lot.status',1)
        ->where('stock_lotdetail.retail_productlist_id',$productid)
        ->where('stock_lotdetail.statusupload',0)
        ->where('date(stock_lotdetail.date_starts) < ',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        $query = $sql->get();

		$std_qty = 0;
        if($num > 0){
			
			foreach($query->result() as $r) {
				$std_qty += $r->std_qty;
				//
				//	find total to del from retail lot to use a lot id
				$sqlrt = $ci->db->select('
					sum(retail_lot.stock_lotdetail_qty) as rt_qty
				')
				->from('retail_lot')
				->where('retail_lot.stock_lotdetail_id',$r->std_id);
				$numrt = $ci->db->count_all_results(null,false);
				$queryrt = $sqlrt->get(); 
				if($numrt > 0){
					$rrt = $queryrt->row();
					$std_qty -= $rrt->rt_qty;
				}
				
			}
        }else{
            $std_qty = 0;
        }

        $result = $std_qty;
        return $result;
    }
    //
    //  call information success bill product lot table last day
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationStockSuccessLastday($productid){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        //

        $r = "";
        $sql = $ci->db->select('
            sum(retail_lot.stock_lotdetail_qty) as qtytotal
        ')
        ->from('retail_lot')
        ->join('retail_bill','retail_lot.retail_bill_id = retail_bill.id','left')
        ->where('retail_lot.retail_billdetail_prolist_id',$productid)
        // ->where('retail_bill.status',1)
        ->where('retail_bill.status_complete not in(0,1,3)')
        ->where('retail_lot.statusupload',0)
        ->where('date(retail_lot.date_starts) < ',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $r = $q->row();
            $qty = $r->qtytotal;
        }else{
            $qty = "";
        }

        $result = $qty;
        return $result;
    }
    //
    //  get total stock last day
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return array
    //  @atr total_stock         @int    = total to calculated
    //
    function totalStockLastday($productid){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  get total stock
        $st_qty = $ci->order->get_totalStock($productid);

        //
        //	total product in stock lot
        $total_stocklot = 0;
        $total_stocklot = $ci->order->get_productStockLotLastday($productid);

        //  total product order success
        $productpay = 0;
        $getproductpay = $ci->order->get_informationStockSuccessLastday($productid);
        if($getproductpay){
            $productpay = $getproductpay;
        }

        //  total product in stock and stock add 
        $nettotal = $st_qty + $total_stocklot;

        //  total product in stock now  
        $stock_total = $nettotal - $productpay;

        $result = array(
            'total_stock'         => $stock_total
        );

        return $result;
    }
    //
    //  get information stock lot 
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return array
    //  @atr total      @int    = total
    //
    function get_productStockLot($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //  setting
        $productid = $array['productid'];

        $sql = $ci->db->select('
            stock_lot.id as st_id,
            stock_lot.lotdate as st_lotdate,
            stock_lotdetail.id as std_id,
            stock_lotdetail.qty as std_qty
        ')
		->from('stock_lot')
		->join('stock_lotdetail','stock_lot.id = stock_lotdetail.stock_lot_id','left')
		->where('stock_lot.status',1)
        ->where('stock_lotdetail.retail_productlist_id',$productid)
        ->where('date(stock_lotdetail.date_starts)',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        $query = $sql->get(); 
		
		$total = 0;
        if($num > 0){
            // $r = $query->row();
			foreach($query->result() as $r) {
				$total += $r->std_qty;
				//
				//	find total to del from retail lot to use a lot id
				$sqlrt = $ci->db->select('
					sum(retail_lot.stock_lotdetail_qty) as rt_qty
				')
				->from('retail_lot')
				->where('retail_lot.stock_lotdetail_id',$r->std_id);
				$numrt = $ci->db->count_all_results(null,false);
				$queryrt = $sqlrt->get(); 
				if($numrt > 0){
					$rrt = $queryrt->row();
					$total -= $rrt->rt_qty;
				}
				
			}
        }else{
            $total = 0;
        }
        
        $result = array(
            'total'      => $total
        );

        return $result; 
    }
    //
    //  get information stock lot 
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return array
    //  @atr total      @int    = total
    //
    function get_productOutStockLot($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //  setting
        $productid = $array['productid'];

        $sql = $ci->db->select('
            sum(retail_lot.stock_lotdetail_qty) as rt_qty  
        ')
        ->from('retail_lot')
        ->join('retail_bill','retail_lot.retail_bill_id = retail_bill.id','left')
        ->where('retail_lot.retail_billdetail_prolist_id',$productid)
        ->where('retail_lot.stock_lotdetail_id',null)
        ->where('retail_lot.dateorder',null);
        $num = $ci->db->count_all_results(null,false);
        $query = $sql->get(); 

        if($num > 0){
            $r = $query->row();
            $total = $r->rt_qty;
        }else{
            $total = 0;
        }
        
        $result = array(
            'total'      => $total
        );

        return $result; 
    }
    //
    //  call information success bill product lot table 
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationStockSuccess($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            sum(retail_lot.stock_lotdetail_qty) as qtytotal
        ')
        ->from('retail_lot')
        ->join('retail_bill','retail_lot.retail_bill_id = retail_bill.id','left')
        ->where('retail_lot.retail_billdetail_prolist_id',$productid)
        // ->where('retail_bill.status',1)
        ->where('retail_bill.status_complete not in(0,1,3)')
        ->where('date(retail_lot.date_starts)',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $r = $q->row();
            $result = array(
                'qty'       => $r->qtytotal
            );
        }else{
            $result = "";
        }

        return $result;
    }
    //
    //  check order promotion
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_checkProductIdPro($productid){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $proid = "";

        $sql = $ci->db->select('
            retail_productlist.id as proid,
            retail_productlist.list_id as prolist_id,
            retail_productlist.promain_id as promain_id
        ')
        ->from('retail_productlist')
        ->where('retail_productlist.id',$productid);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $r = $q->row();

            if($r->promain_id == 6 || $r->promain_id == 12){
                $proid = $r->prolist_id;
            }else{
                $proid = null;
            }
            
        }

        $result = array(
            'productid'    => $proid
        );

        return $result;
    }
    //
    //  call information order product table 
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationOrder($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            sum(retail_billdetail.quantity) as qtytotal
        ')
        ->from('retail_bill')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        // ->where('retail_billdetail.prolist_id',$productid)
        ->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id = '.$productid.', retail_billdetail.prolist_id = '.$productid.')',null,false)
        ->where('retail_bill.status_complete not in(3)')
        // ->where('retail_billdetail.status',1)
        ->where('date(retail_bill.date_starts)',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $r = $q->row();
            $result = array(
                'qty'       => $r->qtytotal
            );
        }else{
            $result = "";
        }

        return $result;
    }

    //
    //  call information stock product table 
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationStockOnly($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            retail_billdetail.code as rt_code,
            retail_bill.name as rt_name,
            retail_bill.billstatus as rt_billstatus,
            retail_bill.user_starts as rt_users,
            retail_bill.phone_number as rt_phone,
            retail_lot.stock_lotdetail_qty as rt_qty,
            retail_lot.dateorder as rt_order
        ')
        ->from('retail_lot')
        ->join('retail_bill','retail_bill.id = retail_lot.retail_bill_id','left')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_lot.retail_billdetail_prolist_id',$productid)
        ->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id = '.$productid.',retail_billdetail.prolist_id = '.$productid.')',null,false)
        ->where('retail_lot.charge',1)
        ->where('retail_bill.status_complete not in(3)')
        ->where('date(retail_bill.date_starts)',$ci->orderset['datenow'])
        ->group_by('retail_lot.id');
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $countqty = $ci->db->select_sum('retail_lot.stock_lotdetail_qty','qtytotal')
            ->from('retail_lot')
            ->where('retail_lot.retail_billdetail_prolist_id',$productid)
            ->where('retail_lot.charge',1)
            ->where('date(retail_lot.date_starts)',$ci->orderset['datenow']);
            $qcount = $countqty->get();
            $rcount = $qcount->row();

            $result = array(
                'qty'       => $rcount->qtytotal,
                'num'       => $num,
                'data'      => $q
            );
        }else{
            $result = null;
        }

        return $result;
    }

    //
    //  call information stock product table 
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationStockPromotion($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            retail_billdetail.code as rt_code,
            retail_bill.name as rt_name,
            retail_bill.user_starts as rt_users,
            retail_bill.phone_number as rt_phone,
            retail_lot.stock_lotdetail_qty as rt_qty,
            retail_lot.dateorder as rt_order
        ')
        ->from('retail_lot')
        ->join('retail_bill','retail_bill.id = retail_lot.retail_bill_id','left')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_lot.retail_billdetail_prolist_id',$productid)
        ->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id = '.$productid.',retail_billdetail.prolist_id = '.$productid.')',null,false)
        ->where('retail_lot.charge',0)
        ->where('retail_bill.status_complete not in(3)')
        ->where('date(retail_bill.date_starts)',$ci->orderset['datenow'])
        ->group_by('retail_lot.id');
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $countqty = $ci->db->select_sum('retail_lot.stock_lotdetail_qty','qtytotal')
            ->from('retail_lot')
            ->where('retail_lot.retail_billdetail_prolist_id',$productid)
            ->where('retail_lot.charge',0)
            ->where('date(retail_lot.date_starts)',$ci->orderset['datenow']);
            
            $qcount = $countqty->get();
            $rcount = $qcount->row();

            $result = array(
                'qty'       => $rcount->qtytotal,
                'num'       => $num,
                'data'      => $q
            );
        }else{
            $result = null;
        }

        return $result;
    }
    //
    //  call information order product promotion table 
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationOrderOnly($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            retail_billdetail.code as rt_code,
            retail_bill.name as rt_name,
            retail_bill.billstatus as rt_billstatus,
            retail_bill.user_starts as rt_users,
            retail_bill.phone_number as rt_phone,
            retail_billdetail.quantity as rt_qty
        ')
        ->from('retail_bill')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_billdetail.prolist_id',$productid)
        ->where('retail_bill.status_complete not in(3)')
        ->where('date(retail_bill.date_starts)',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $countqty = $ci->db->select_sum('retail_billdetail.quantity','qtytotal')
            ->from('retail_bill')
            ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
            ->where('retail_billdetail.prolist_id',$productid)
            ->where('retail_bill.status_complete not in(3)')
            ->where('date(retail_bill.date_starts)',$ci->orderset['datenow']);
            $qcount = $countqty->get();
            $rcount = $qcount->row();

            $result = array(
                'qty'       => $rcount->qtytotal,
                'num'       => $num,
                'data'      => $q
            );
        }else{
            $result = null;
        }

        return $result;
    }

    //
    //  call information order product promotion table 
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationOrderPromotion($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            retail_billdetail.code as rt_code,
            retail_bill.name as rt_name,
            retail_bill.user_starts as rt_users,
            retail_bill.phone_number as rt_phone,
            retail_billdetail.quantity as rt_qty
        ')
        ->from('retail_bill')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_billdetail.list_id',$productid)
        ->where('retail_bill.status_complete not in(3)')
        ->where('date(retail_bill.date_starts)',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $countqty = $ci->db->select_sum('retail_billdetail.quantity','qtytotal')
            ->from('retail_bill')
            ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
            ->where('retail_billdetail.list_id',$productid)
            ->where('retail_bill.status_complete not in(3)')
            ->where('date(retail_bill.date_starts)',$ci->orderset['datenow']);
            $qcount = $countqty->get();
            $rcount = $qcount->row();

            $result = array(
                'qty'       => $rcount->qtytotal,
                'num'       => $num,
                'data'      => $q
            );
        }else{
            $result = null;
        }

        return $result;
    }

    //
    //  call information order waite
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationWaiteOnly($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            retail_billdetail.code as rt_code,
            retail_bill.name as rt_name,
            retail_bill.user_starts as rt_users,
            date(retail_bill.date_starts) as rt_datestart,
            retail_bill.phone_number as rt_phone,
            retail_billdetail.quantity as rt_qty
        ')
        ->from('retail_bill')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_billdetail.prolist_id',$productid)
        ->where('retail_bill.status_complete not in(3)')
        ->where('date(retail_bill.date_starts) > ',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $countqty = $ci->db->select_sum('retail_billdetail.quantity','qtytotal')
            ->from('retail_bill')
            ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
            ->where('retail_billdetail.prolist_id',$productid)
            ->where('retail_bill.status_complete not in(3)')
            ->where('date(retail_bill.date_starts) > ',$ci->orderset['datenow']);
            $qcount = $countqty->get();
            $rcount = $qcount->row();

            $result = array(
                'qty'       => $rcount->qtytotal,
                'num'       => $num,
                'data'      => $q
            );
        }else{
            $result = null;
        }

        return $result;
    }

    //
    //  call information order waite promotion
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationWaitePromotion($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            retail_billdetail.code as rt_code,
            retail_bill.name as rt_name,
            retail_bill.user_starts as rt_users,
            retail_bill.date_starts as rt_datestart,
            retail_bill.phone_number as rt_phone,
            retail_billdetail.quantity as rt_qty
        ')
        ->from('retail_bill')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_billdetail.list_id',$productid)
        ->where('retail_bill.status_complete not in(3)')
        ->where('date(retail_bill.date_starts) > ',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $countqty = $ci->db->select_sum('retail_billdetail.quantity','qtytotal')
            ->from('retail_bill')
            ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
            ->where('retail_billdetail.list_id',$productid)
            ->where('retail_bill.status_complete not in(3)')
            ->where('date(retail_bill.date_starts) > ',$ci->orderset['datenow']);
            $qcount = $countqty->get();
            $rcount = $qcount->row();

            $result = array(
                'qty'       => $rcount->qtytotal,
                'num'       => $num,
                'data'      => $q
            );
        }else{
            $result = null;
        }

        return $result;
    }

    //
    //  call information order waite product table 
    //  paramiter array in
    //  @param productid    @int = product id  
    //  return array
    //
    function get_informationOrderWaite($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $productid = $array['productid'];
        //

        $r = "";
        $sql = $ci->db->select('
            sum(retail_billdetail.quantity) as qtytotal
        ')
        ->from('retail_bill')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id = '.$productid.',retail_billdetail.prolist_id = '.$productid.')',null,false)
        // ->where('retail_bill.status',1)
        ->where('retail_bill.status_complete not in(3)')
        // ->where('retail_billdetail.status',1)
        ->where('date(retail_bill.date_starts) >',$ci->orderset['datenow']);
        $num = $ci->db->count_all_results(null,false);

        $q = $sql->get();
        if($num > 0){
            $r = $q->row();
            $result = array(
                'qty'       => $r->qtytotal
            );
        }else{
            $result = "";
        }

        return $result;
    }
    //
    //  match lot id with product for stock
    //  paramiter array in
    //  @param billid       @int = order bill id  
    //  @param productid    @int = product id  
    //  @param qty          @int = quantity
    //  return array
    //
	function stickLot($array){
		//=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        // 
        //  setting
        $billid = $array['billid'];
        $productid = $array['productid'];
        $charge = $array['charge'];
        $qty = $array['qty'];
        $datestart = $array['datestarts'];
        $totalpay = 0;     //  set default totalpay for loop
        // echo $billid."+".$productid."+".$qty."---";
        //
        //  clear row null
        // $ci->db->delete('retail_lot', array('retail_bill_id' => $billid));

        $sql = $ci->db->select('
            stock_lot.id as st_id,
            stock_lot.lotdate as st_lotdate,
            stock_lotdetail.id as std_id,
            stock_lotdetail.qty as std_qty
        ')
		->from('stock_lot')
		->join('stock_lotdetail','stock_lot.id = stock_lotdetail.stock_lot_id','left')
		->where('stock_lot.status',1)
        ->where('stock_lotdetail.retail_productlist_id',$productid)
        ->where('stock_lotdetail.status_full',0)
        ->order_by('stock_lot.lotdate','asc');
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();exit;
        $q = $sql->get();

        if($num > 0){
  
            foreach($q->result() as $rowstock){
                //
                //  get total product pay
                //  delete with total stock
                $sqlin = $ci->db->select('
                    sum(retail_lot.stock_lotdetail_qty) as qtytotal
                ')
                ->from('retail_lot')
                ->where('retail_lot.stock_lotdetail_id',$rowstock->std_id)
                ->where('retail_lot.retail_billdetail_prolist_id',$productid);
                $numin = $ci->db->count_all_results(null,false);
                // echo $sql->get_compiled_select();exit;
                $qin = $sqlin->get();

                if($numin > 0){
                    $rin = $qin->row();
                    $totalstock = $rowstock->std_qty - $rin->qtytotal;
                }else{
                    $totalstock = $rowstock->std_qty;
                }

                //
                //  check stock lot to still item over 
                if($totalstock > 0){
                    if($totalpay == 0){
                        $totalpay = $qty;
                    }else{
                        $totalpay = $setvalue;
                    }

                    $setvalue = $totalpay - $totalstock;
                    // $totalpay = $totalpay - $totalstock;
                    // echo $setvalue."::".$totalpay."-".$totalstock." YES <br>";

                    if($setvalue >= 0){
                        $insertvalue = $totalstock;
                        $status = 0;
                    }else{
                        $insertvalue = $totalpay;
                        $status = 1;
                    }
                    //
                    //  query insert
                    $datainsert = array(
                        'retail_bill_id' 	            => $billid,
                        'retail_billdetail_prolist_id' 	=> $productid,
                        'stock_lotdetail_qty' 	        => $insertvalue,
                        'stock_lotdetail_id' 	        => $rowstock->std_id,
                        'dateorder' 	                => $rowstock->st_lotdate,
                        'charge' 	                    => $charge,
                        'date_starts' 	                => $datestart
                    );
                    $ci->db->insert('retail_lot', $datainsert);
                    if($ci->db->insert_id()){

                        if($status == 0){
                            $dataupdate = array(
                                'status_full'                        => 1
                            );
                            $ci->db->where('stock_lotdetail.id', $rowstock->std_id);
                            $ci->db->update('stock_lotdetail', $dataupdate);
                        }

                        $code = 0;
                        $txt = "รายการสำเร็จ";

                        if($setvalue <= 0){
                            break;
                        }
                    }

                }
            }   //  end foreach

            if($setvalue > 0){
                // echo $totalpay."::".$qty."-".$totalstock." over <br>";
                //
                //  query insert
                //  if total stock no have item
                $datainsert = array(
                    'retail_bill_id' 	            => $billid,
                    'retail_billdetail_prolist_id' 	=> $productid,
                    'stock_lotdetail_qty' 	        => abs($setvalue),
                    'stock_lotdetail_id' 	        => null,
                    'dateorder' 	                => null,
                    'charge' 	                    => $charge,
                    'date_starts' 	                => $datestart
                );
                $ci->db->insert('retail_lot', $datainsert);
                if($ci->db->insert_id()){
                    $code = 0;
                    $txt = "รายการสำเร็จ";
                }
            }

        }else{

            if($qty > 0){
                // echo $totalpay."::".$qty."-".$totalstock." NO <br>";
                //
                //  query insert
                //  if total stock no have item
                $datainsert = array(
                    'retail_bill_id' 	            => $billid,
                    'retail_billdetail_prolist_id' 	=> $productid,
                    'stock_lotdetail_qty' 	        => abs($qty),
                    'stock_lotdetail_id' 	        => null,
                    'dateorder' 	                => null,
                    'charge' 	                    => $charge,
                    'date_starts' 	                => $datestart
                );
                $ci->db->insert('retail_lot', $datainsert);
                if($ci->db->insert_id()){
                    $code = 0;
                    $txt = "รายการสำเร็จ";
                }
            }

        }
        
        $result = array(
            'code'	=> $code,
            'txt'	=> $txt
        );
		
		return $result;
    }
    //
    //  cancel Lot id 
    //  paramiter array in
    //  @param billid       @int = order bill id  
    //  return array
    //
	function cancelLotForEdit($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        // 
        //  setting
        $billid = $array['billid'];
        $code = 0;
        $txt = "ไม่มีการทำรายการ";

        $sqlin = $ci->db->select('
            retail_lot.id as rt_id
        ')
        ->from('retail_lot')
        ->where('retail_lot.retail_bill_id',$billid);
        $numin = $ci->db->count_all_results(null,false);
        // echo $sqlin->get_compiled_select();exit;
        $qin = $sqlin->get();
        if($numin > 0){
            $txt = "รายการสำเร็จ";

            // 
            //  check status full on table stock if bill cancel 
            //  stock lot should be restore value
            $sqllotid = $ci->db->select('
                retail_lot.stock_lotdetail_id as rt_stl
            ')
            ->from('retail_lot')
            ->where('retail_lot.retail_bill_id',$billid)
            ->where('retail_lot.stock_lotdetail_id is not null')
            ->group_by('retail_lot.stock_lotdetail_id');
            $numlotid = $sqllotid->count_all_results(null,false);
            $qinlotid = $sqllotid->get();

            if($numlotid > 0){

                foreach($qinlotid->result() as $rowlotid){
                    $dataupdate = array(
                        'status_full'                        => 0
                    );
                    $ci->db->where('stock_lotdetail.id', $rowlotid->rt_stl);
                    $ci->db->update('stock_lotdetail', $dataupdate);
                }
                
            }


            // 
            //  check status upload on table lot if bill cancel 
            //  if uploaded stock should be restore value 
            $sqlupload = $ci->db->select('
                sum(retail_lot.stock_lotdetail_qty) as rt_stqty,
                retail_lot.retail_billdetail_prolist_id as rt_pid
            ')
            ->from('retail_lot')
            ->where('retail_lot.retail_bill_id',$billid)
            ->where('retail_lot.statusupload',1)
            ->group_by('retail_lot.retail_billdetail_prolist_id');
            $numupload = $sqlupload->count_all_results(null,false);
            $qinupload = $sqlupload->get();

            if($numupload > 0){

                foreach($qinupload->result() as $rowupload){
					$total = 0;
					//  new total bill
                    $sqlbill = $ci->db->select('
                        sum(retail_billdetail.quantity) as rtb_qty
                    ')
                    ->from('retail_billdetail')
                    ->where('retail_billdetail.bill_id',$billid)
                    ->where('(retail_billdetail.prolist_id='.$rowupload->rt_pid." or retail_billdetail.list_id=".$rowupload->rt_pid.")");
                    $numbill= $ci->db->count_all_results(null,false);
                    $qinbill = $sqlbill->get();
					if($numbill > 0){
						$rbill = $qinbill->row(); 
						$rowbill = $rbill->rtb_qty;
					}else{
						$rowbill = "";
					}
					
                    //  check stock
                    $sqlstock = $ci->db->select('
                        sum(stock.qty) as st_qty
                    ')
                    ->from('stock')
                    ->where('stock.retail_productlist_id',$rowupload->rt_pid);
                    $numstock = $ci->db->count_all_results(null,false);
                    $qinstock = $sqlstock->get();
                    if($numstock > 0){
                        $rstock = $qinstock->row(); 
                        
                        $total =  ($rstock->st_qty + $rowupload->rt_stqty) - $rowbill;
                        //  update stock
                        $dataupdates = array(
                            'qty'                        => $total
                        );
						// echo $rowupload->rt_pid."=".$total."(".$rstock->st_qty."+".$rowupload->rt_stqty."-".$rowbill.")---<br>";
                        $ci->db->where('stock.retail_productlist_id', $rowupload->rt_pid);
                        $ci->db->update('stock', $dataupdates);

                    }

                }   //  end foreach
                
            }

            //  clear row
            $ci->db->delete('retail_lot', array('retail_bill_id' => $billid));
        }

        $result = array(
            'code'	=> $code,
            'txt'	=> $txt
        );
		
		return $result;
    }
	
	//
    //  cancel Lot id 
    //  paramiter array in
    //  @param billid       @int = order bill id  
    //  return array
    //
	function cancelLot($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        // 
        //  setting
        $billid = $array['billid'];
        $code = 0;
        $txt = "ไม่มีการทำรายการ";

        $sqlin = $ci->db->select('
            retail_lot.id as rt_id
        ')
        ->from('retail_lot')
        ->where('retail_lot.retail_bill_id',$billid);
        $numin = $ci->db->count_all_results(null,false);
        // echo $sqlin->get_compiled_select();exit;
        $qin = $sqlin->get();
        if($numin > 0){
            $txt = "รายการสำเร็จ";

            // 
            //  check status full on table stock if bill cancel 
            //  stock lot should be restore value
            $sqllotid = $ci->db->select('
                retail_lot.stock_lotdetail_id as rt_stl
            ')
            ->from('retail_lot')
            ->where('retail_lot.retail_bill_id',$billid)
            ->where('retail_lot.stock_lotdetail_id is not null')
            ->group_by('retail_lot.stock_lotdetail_id');
            $numlotid = $sqllotid->count_all_results(null,false);
            $qinlotid = $sqllotid->get();

            if($numlotid > 0){

                foreach($qinlotid->result() as $rowlotid){
                    $dataupdate = array(
                        'status_full'                        => 0
                    );
                    $ci->db->where('stock_lotdetail.id', $rowlotid->rt_stl);
                    $ci->db->update('stock_lotdetail', $dataupdate);
                }
                
            }


            // 
            //  check status upload on table lot if bill cancel 
            //  if uploaded stock should be restore value 
            $sqlupload = $ci->db->select('
                sum(retail_lot.stock_lotdetail_qty) as rt_stqty,
                retail_lot.retail_billdetail_prolist_id as rt_pid
            ')
            ->from('retail_lot')
            ->where('retail_lot.retail_bill_id',$billid)
            ->where('retail_lot.statusupload',1)
            ->group_by('retail_lot.retail_billdetail_prolist_id');
            $numupload = $sqlupload->count_all_results(null,false);
            $qinupload = $sqlupload->get();

            if($numupload > 0){

                foreach($qinupload->result() as $rowupload){
					$total = 0;
					
                    //  check stock
                    $sqlstock = $ci->db->select('
                        sum(stock.qty) as st_qty
                    ')
                    ->from('stock')
                    ->where('stock.retail_productlist_id',$rowupload->rt_pid);
                    $numstock = $ci->db->count_all_results(null,false);
                    $qinstock = $sqlstock->get();
                    if($numstock > 0){
                        $rstock = $qinstock->row(); 
                        
                        $total = $rstock->st_qty + $rowupload->rt_stqty;
                        // echo $total." = ".$rstock->st_qty." ... ".$rowupload->rt_stqty."<br>";
                        //  update stock
                        $dataupdates = array(
                            'qty'                        => $total
                        );
                        $ci->db->where('stock.retail_productlist_id', $rowupload->rt_pid);
                        $ci->db->update('stock', $dataupdates);

                    }

                }   //  end foreach
                
            }

            //  clear row
            $ci->db->delete('retail_lot', array('retail_bill_id' => $billid));
        }

        $result = array(
            'code'	=> $code,
            'txt'	=> $txt
        );
		
		return $result;
    }

    //
    //  get information out stock lot 
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return array
    //  @atr data      @array    = query
    //
    function get_listOutStockLot($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //  setting
        $productid = $array['productid'];

        $sql = $ci->db->select('
            retail_billdetail.code as rt_code,
            retail_bill.name as rt_name,
            retail_bill.user_starts as rt_users,
            retail_bill.phone_number as rt_phone,
            retail_lot.stock_lotdetail_qty as rt_qty,
            retail_lot.dateorder as rt_order,
            retail_lot.id as rt_id
        ')
        ->from('retail_lot')
        ->join('retail_bill','retail_bill.id = retail_lot.retail_bill_id','left')
        ->join('retail_billdetail','retail_bill.id = retail_billdetail.bill_id','left')
        ->where('retail_lot.retail_billdetail_prolist_id',$productid)
        ->where('if(retail_billdetail.list_id is not null,retail_billdetail.list_id = '.$productid.',retail_billdetail.prolist_id = '.$productid.')',null,false)
        ->where('retail_lot.stock_lotdetail_id',null)
        ->where('retail_lot.dateorder',null)
		->group_by('retail_lot.id');
		// echo $sql->get_compiled_select();exit;
        $num = $ci->db->count_all_results(null,false);
        $query = $sql->get(); 

        if($num > 0){
            $q = $query;
        }else{
            $q = "";
        }
        
        $result = array(
            'data'      => $q
        );

        return $result; 
    }

    //
    //  get lot have product
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return array
    //  @atr data      @array    = query
    //
    function json_listLotIDStill($product_id,$lotid){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //  setting
        $productid = $product_id;
        $arrayresult = array();

        $sql = $ci->db->select('
            stock_lot.id as st_id,
            stock_lot.lotdate as st_lotdate,
            stock_lotdetail.id as std_id,
            stock_lotdetail.qty as std_qty
        ')
		->from('stock_lot')
		->join('stock_lotdetail','stock_lot.id = stock_lotdetail.stock_lot_id','left')
		->where('stock_lot.status',1)
        ->where('stock_lotdetail.retail_productlist_id',$productid)
        ->where('stock_lotdetail.status_full',0)
        ->order_by('stock_lot.lotdate','asc');
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();exit;
        $q = $sql->get();

        if($num > 0){
            foreach($q->result() as $rowstock){
                //
                //  get total product pay
                //  delete with total stock
                $sqlcut = $ci->db->select('
                    sum(retail_lot.stock_lotdetail_qty) as qtytotal
                ')
                ->from('retail_lot')
                ->where('retail_lot.stock_lotdetail_id',$rowstock->std_id)
                ->where('retail_lot.retail_billdetail_prolist_id',$productid);
                $numcut = $ci->db->count_all_results(null,false);
                // echo $sql->get_compiled_select();exit;
                $qcut = $sqlcut->get();
                if($numcut > 0){
                    $rcut = $qcut->row();
                    $totalcut = $rcut->qtytotal;
                }else{
                    $totalcut = 0;
                }

                //
                //  get total product to select cut stock
                //  delete with total stock
                $sqlin = $ci->db->select('
                    sum(retail_lot.stock_lotdetail_qty) as qtytotal
                ')
                ->from('retail_lot')
                ->where('retail_lot.id',$lotid);
                $numin = $ci->db->count_all_results(null,false);
                $qin = $sqlin->get();

                if($numin > 0){
                    $rin = $qin->row();
                    $totalstock = ($rowstock->std_qty - $totalcut) - $rin->qtytotal;
                }else{
                    $totalstock = $rowstock->std_qty - $totalcut;
                }
                if($totalstock >= 0){
                    //
                    //  status for check cut total product
                    if($totalstock == 0){
                        $status = 1;
                    }else{
                        $status = 0;
                    }

                    array_push($arrayresult,array( 
                        'id'    => $rowstock->st_id, 
                        'value' => $rowstock->st_lotdate." = ".$totalstock,
                        'date'  => $rowstock->st_lotdate,
                        'status_full'  => $status
                    ));
                }
            }
        }

		return $arrayresult;
    }

    //
    //  get information lot in product
    //  paramiter array in
    //  @param productid    @int = product id  
    //
    //  return array
    //  @atr data      @array    = query
    //
    function get_listStockLot($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //  setting
        $productid = $array['productid'];

        $sql = $ci->db->select('
            stock_lot.id as st_id,
            stock_lot.lotdate as st_lotdate,
            stock_lot.user_starts as st_users,
            date(stock_lot.date_starts) as st_date,
            stock_lot.date_starts as st_datetime,
            stock_lotdetail.id as std_id,
            stock_lotdetail.qty as std_qty
        ')
		->from('stock_lot')
		->join('stock_lotdetail','stock_lot.id = stock_lotdetail.stock_lot_id','left')
		->where('stock_lot.status',1)
        ->where('stock_lotdetail.retail_productlist_id',$productid)
        ->where('stock_lotdetail.status_full',0)
        ->order_by('stock_lot.lotdate','asc');
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();exit;
        $q = $sql->get();

        if($num > 0){
            $result = array(
                'num'       => $num,
                'data'      => $q
            );
        }else{
            $result = null;
        }

		return $result;
    }
	
	function get_listStockLotAll($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //  setting
        $productid = $array['productid'];

        $sql = $ci->db->select('
            stock_lot.id as st_id,
            stock_lot.lotdate as st_lotdate,
            stock_lot.user_starts as st_users,
            date(stock_lot.date_starts) as st_date,
            stock_lot.date_starts as st_datetime,
            stock_lotdetail.id as std_id,
            stock_lotdetail.status_full as std_full,
            stock_lotdetail.qty as std_qty
        ')
		->from('stock_lot')
		->join('stock_lotdetail','stock_lot.id = stock_lotdetail.stock_lot_id','left')
		->where('stock_lot.status',1)
        ->where('stock_lotdetail.retail_productlist_id',$productid)
        // ->where('date(stock_lot.date_starts) >= DATE_ADD("'.date('Y-m-d').'",INTERVAL -3 WEEK)')
        // ->where('stock_lotdetail.status_full',0)
        ->order_by('stock_lot.date_starts','asc');
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();exit;
        $q = $sql->get();

        if($num > 0){
            $result = array(
                'num'       => $num,
                'data'      => $q
            );
        }else{
            $result = null;
        }

		return $result;
    }

    //
    //  get product total stock
    //  paramiter array in
    //  @param stockid    @int = stock detail id  
    //
    //  return array
    //  @atr total      @array    = product total in stock
    //
    function get_stockTotal($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //  setting
        $stockid = $array['stockid'];

        $sql = $ci->db->select('
            stock_lotdetail.qty as std_qty
        ')
		->from('stock_lotdetail')
        ->where('stock_lotdetail.id',$stockid)
        ->where('stock_lotdetail.status_full',0);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();exit;
        $q = $sql->get();

        if($num > 0){
            $r = $q->row();

            $sqlin = $ci->db->select('
                sum(retail_lot.stock_lotdetail_qty) as rt_qty
            ')
            ->from('retail_lot')
            ->where('retail_lot.stock_lotdetail_id',$stockid);
            $numin = $ci->db->count_all_results(null,false);
            // echo $sql->get_compiled_select();exit;
            $qin = $sqlin->get();

            if($numin > 0){
                $rin = $qin->row();
                $total = $r->std_qty - $rin->rt_qty;
            }else{
                $total = $r->std_qty;
            }

            $result = array(
                'total'      => $total
            );
        }else{
            $result = null;
        }

		return $result;
    }
	
	//
    //  get product total stock
    //  paramiter array in
    //  @param stockid    @int = stock detail id  
    //
    //  return array
    //  @atr total      @array    = product total in stock
    //
    function get_stockTotalRecheck($array){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //  setting
        $stockid = $array['stockid'];

        $sql = $ci->db->select('
            stock_lotdetail.qty as std_qty
        ')
		->from('stock_lotdetail')
        ->where('stock_lotdetail.id',$stockid);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();exit;
        $q = $sql->get();

        if($num > 0){
            $r = $q->row();

            $sqlin = $ci->db->select('
                sum(retail_lot.stock_lotdetail_qty) as rt_qty
            ')
            ->from('retail_lot')
            ->where('retail_lot.stock_lotdetail_id',$stockid);
            $numin = $ci->db->count_all_results(null,false);
            // echo $sql->get_compiled_select();exit;
            $qin = $sqlin->get();

            if($numin > 0){
                $rin = $qin->row();
                $total = $r->std_qty - $rin->rt_qty;
            }else{
                $total = $r->std_qty;
            }

            $result = array(
                'total'      => $total
            );
        }else{
            $result = null;
        }

		return $result;
    }

    //
    //  call information order product promotion table 
    //  @param userid    @int = user id  
    //  return array
    //
    function get_staff($usercode){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        //
        //  setting
        $userid = $usercode;
        //

        $r = "";
        $sql = $ci->db->select('
            staff.name_th as user_name,
            staff.lastname_th as user_lname
        ')
        ->from('staff')
        ->where('staff.code',$userid);
        $num = $ci->db->count_all_results(null,false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if($num > 0){
            $r = $q->row();
            $result = array(
                'username'       => $r->user_name." ".$r->user_lname
            );
        }else{
            $result = null;
        }

        return $result;
    }
	
	//
    //  call information type bill transfer
    //  @param userid    @text = bill status from retail_bill
    //  return array
    //
    function get_billTypeTransfer($billstatus){
        //=	 call database	=//
		$ci =& get_instance();
		$ci->load->database();
        //===================//
        $status = "";
		switch($billstatus){
			case 'T' :
				$status = "ปกติ";
			break;
			case 'C' :
				$status = "หลัง";
			break;
			case 'F' :
				$status = "อื่นๆ";
			break;
			default :
				$status = "?";
			break;
		}

        if($status){
            $result = array(
                'text'       => $status
            );
        }else{
            $result = null;
        }

        return $result;
    }
   
}

?>