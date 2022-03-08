<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_supplierlist extends CI_Model
{

    //---------------------------- creditnote ----------------------------//

    function make_query()
    {
        $request = $_REQUEST;

        $table = 'supplier';
        $tablestaff = 'staff';

        $order_column = array(
            $table . ".ID"
        );

        $this->db->select(
            $table . '.ID as sp_id,' .
            $table . '.NAME as sp_name,' .
            $table . '.NAME_TH as sp_name_th,' .
            $table . '.DATE_STARTS as sp_date_starts,' .
            $table . '.USER_STARTS as sp_user_starts,' .
            $table . '.DATE_UPDATE as sp_date_update,' .
            $table . '.USER_UPDATE as sp_user_update,' .

            $tablestaff . '.NAME_TH as staff_name_th,' .
            $tablestaff . '.LASTNAME_TH as staff_lastname_th,' .
            $tablestaff . '.NAME as staff_name,' .
            $tablestaff . '.LASTNAME as staff_lastname'
        );
        $this->db->from($table);
        $this->db->join($tablestaff, $table . '.user_starts=' . $tablestaff . '.code', 'left');
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
}
