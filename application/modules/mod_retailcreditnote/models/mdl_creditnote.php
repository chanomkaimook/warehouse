<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_creditnote extends CI_Model
{

    //---------------------------- creditnote ----------------------------//

    function make_query()
    {
        $request = $_REQUEST;

        $table = 'retail_creditnote';
        $tabledetail = 'retail_creditnotedetail';
        $tablestaff = 'staff';

        $order_column = array(
            $table . ".ID",
            $table . ".CODE",
            $table . ".NET_TOTAL",
            $table . ".LOSS",
            $table . ".COMPLETE"
        );

        $this->db->select(
            $table . '.ID as cn_id,' .
                $table . '.CODE as cn_code,' .
                $table . '.NET_TOTAL as cn_net,' .
                $table . '.RT_ID as cn_rt_id,' .
                $table . '.LOSS as cn_loss,' .
                $table . '.COMPLETE as cn_complete,' .
                $table . '.RT_BILL_CODE as cn_rt_bill_code,' .
                $table . '.APPROVE as cn_approve,' .
                $table . '.APPROVE_STORE as cn_approve_store,' .
                $table . '.DATE_STARTS as cn_date_starts,' .

                $tablestaff . '.NAME as cn_name,' .
                $tablestaff . '.NAME_TH as cn_name_th,' .
                $tablestaff . '.LASTNAME as cn_lastname,' .
                $tablestaff . '.LASTNAME_TH as cn_lastname_th,'
        );
        $this->db->from($table);
        $this->db->join($tabledetail, $table . '.id=' . $tabledetail . '.creditnote_id', 'left');
        $this->db->join($tablestaff, $table . '.user_starts=' . $tablestaff . '.id', 'left');
        $this->db->where($table.'.status',1); 

        if (!empty($request["statuscomplete"])) {
        }

        if (!empty($request["search"]["value"])) {
            $this->db->like($table . ".CODE", $request["search"]["value"]);
            $this->db->or_like($table . ".RT_BILL_CODE", $request["search"]["value"]);
            $this->db->or_like($table . ".NET_TOTAL", $request["search"]["value"]);
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
        }

        $this->db->group_by($table . ".CODE");
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

            $username = ($ruser->name_th ? $ruser->name_th : $ruser->name);
        }

        return $username;
    }
}
