<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_retailcheck extends CI_Model
{

    function searchOrderBarcode()
    {
        $searchtext = trim($this->input->post('searchtext'));
        $billid = $this->input->post('billid');
        $code = 1;

        // 
        //  prevent null value
        if ($searchtext == "") {
            $code = 0;
            $result = "search not value";
        } else {

            //	1.	find product from barcode
            $sqlcode = $this->db->select('
                        retail_productlist.id as rtcode_id,
                        retail_productlist.name_th as rtcode_name
            ')
                ->from('retail_productlist')
                ->like('(retail_productlist.code_product =' . $searchtext . ' or retail_productlist.codemac =' . $searchtext . ')');
            $numcode = $sqlcode->count_all_results(null, false);
            $querycode  = $sqlcode->get();
            if ($numcode > 0) {
                // $row = $querycode->row();
                $array = array();
                foreach ($querycode->result() as $row) {
                    $array[] = $row->rtcode_id;
                }

                if ($array) {

                    $productid = implode(',', $array);

                    $this->db->select('
							retail_bill.id as rt_id,
							retail_productlist.id as rtp_id,
							retail_productlist.name_th as rtp_name,
							retail_productlist.price as rtp_price
				    ');
                    $this->db->from('retail_bill');
                    $this->db->join('retail_billdetail', 'retail_bill.id = retail_billdetail.bill_id', 'left');
                    $this->db->join('retail_productlist', 'if(retail_billdetail.list_id != "", retail_billdetail.list_id = retail_productlist.id ,retail_billdetail.prolist_id = retail_productlist.id)', 'left', false);
                    $this->db->where('retail_bill.id', $billid);
                    // $this->db->where('(retail_billdetail.prolist_id ='.$productid.' or retail_billdetail.list_id ='.$productid.')');
                    $this->db->where('( case when retail_billdetail.list_id is not null then retail_billdetail.list_id in(' . $productid . ') else retail_billdetail.prolist_id in(' . $productid . ') end )', null, false);

                    $num = $this->db->count_all_results(null, false);
                    // echo $this->db->get_compiled_select();exit;
                    $query  = $this->db->get();
                    if ($num >= 1) {
                        $row = $query->row();
                        $code = 0;
                        $result = $row;
                    } else {
                        $result = "Item not found " . $searchtext;
                    }
                }else{
                    $result = "barcode not found " . $searchtext;
                }
            }
        }


        $data = array(
            "error_code"         => $code,
            "txt"                 => $result
        );
        $data = json_encode($data);
        return $data;
    }
    //
    //  get information bill from bill id
    //  @param billid   @text = bill id
    //  return
    //  @param data     @query = query
    //
    function fetch_Billdetail($billid)
    {
        $sql = $this->db->select('
            retail_bill.id as bill_id,
            retail_bill.name as bill_name,
            retail_bill.net_total as bill_nettotal,
            retail_bill.code as bill_code,
            retail_billdetail.code as bill_codescg,
            retail_billdetail.quantity as bill_qty,
            retail_productlist.id as pd_id,
            retail_productlist.list_id as pd_listid,
            retail_productlist.name_th as pd_nameth
        ')
            ->from('retail_bill')
            ->join('retail_billdetail', 'retail_bill.id = retail_billdetail.bill_id', 'left')
            // ->join('retail_productlist','if(retail_billdetail.list_id is not null,retail_billdetail.list_id = retail_productlist.id,retail_billdetail.prolist_id = retail_productlist.id)','left',false)
            ->join('retail_productlist', 'retail_billdetail.prolist_id = retail_productlist.id', 'left')
            ->where('retail_bill.id', $billid);
        $num = $this->db->count_all_results(null, false);
        // echo $sql->get_compiled_select();
        $q = $sql->get();
        if ($num > 0) {
            $result = $q;
        } else {
            $result = "";
        }

        return $result;
    }
}
