<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_issue extends CI_Model
{

    //---------------------------- creditnote ----------------------------//

    function make_query()
    {
        $request = $_REQUEST;

        $table = 'retail_issue';
        $tablestaff = 'staff';

        $order_column = array(
            $table . ".ID",
            $table . ".ID",
            $table . ".ID",
            $table . ".ID",
            $table . ".ID",
            $table . ".ID",
            $table . ".ID",
            $table . ".ID",
            $table . ".ID"
        );

        $this->db->select(
            $table . '.ID as tb_id,' .
            $table . '.CODE as tb_code,' .
            $table . '.SP_BILL_ID as tb_sp_bill_id,' .
            $table . '.SP_BILL_NAME as tb_sp_bill_name,' .
            $table . '.COMPLETE as tb_complete,' .
            $table . '.REMARK as tb_remark,' .
            $table . '.TYPE as tb_type,' .
            $table . '.QUANTITY as tb_qty,' .

            $table . '.DATE_STARTS as tb_date_starts,' .
            $table . '.USER_STARTS as tb_user_starts,' .
            $table . '.DATE_UPDATE as tb_date_update,' .
            $table . '.USER_UPDATE as tb_user_update,' . 

            'retail_productlist.NAME_TH as pd_name_th'
        );
        $this->db->from($table);
        $this->db->join('retail_productlist', 'if('.$table.'.LIST_ID,'.$table.'.LIST_ID = retail_productlist.ID,'.$table.'.PROLIST_ID = retail_productlist.ID)', 'left',false);
        $this->db->where($table.'.status',1);

        if (!empty($request["search"]["value"])) {
            $this->db->like($table . ".NAME_TH", $request["search"]["value"]);
            $this->db->or_like($table . ".NAME", $request["search"]["value"]);
            //    $this->db->where('status',0); 
        }


        if (!empty($request["valdate"]) && !empty($request["valdateTo"])) {
            $this->db->where('date(' . $table . '.DATE_STARTS) BETWEEN "' . $request["valdate"] . '" and "' . $request["valdateTo"] . '"');
        } else if (!empty($request["valdate"]) || !empty($request["valdateTo"])) {

            if (!empty($request["valdate"])) {
                $this->db->where('date(' . $table . '.DATE_STARTS)', $request["valdate"]);
            }

            if (!empty($request["valdateTo"])) {
                $this->db->where('date(' . $table . '.DATE_STARTS) <="', $request["valdateTo"]);
            }
        }

        if ($request["sel_complete"] != null) {
            $this->db->where($table . '.COMPLETE', $request["sel_complete"]);
        }

        if (!empty($request["order"])) {
            $this->db->order_by($order_column[$request['order']['0']['column']], $request['order']['0']['dir']);
        }else{
            $this->db->order_by($table.'.id', 'desc');

        }
    }

    function alldata()
    {
        $request = $_REQUEST;

        $this->make_query();
        $query = $this->db->get();
        $total = $query->num_rows();

        return $total;
    }

    function makedata()
    {
        $request = $_REQUEST;

        $this->make_query();
        if ($request['length'] != -1) {
            $this->db->limit($request['length'], $request['start']);
            // $this->db->limit(50,0);
        }

        $query = $this->db->get();
        return $query;
    }

    //	find user name
    //	@param	code	@text = useradmin code
    function findUsernameByCode($code)
    {
        //=	 call database	=//
        $ci = &get_instance();
        $ci->load->database();
        //===================//
        $username = "";

        $sqluser = $ci->db->select('name_th,name,lastname_th,lastname')
            ->from('staff')
            ->where('staff.code', trim($code));
        $quser = $sqluser->get();
        $numuser = $quser->num_rows();
        if ($numuser) {
            $ruser = $quser->row();

            $username = ($ruser->name_th ? $ruser->name_th." ".$ruser->lastname_th : $ruser->name." ".$ruser->lastname);
        }

        return $username;
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
}
