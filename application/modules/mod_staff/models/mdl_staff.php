<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Mdl_staff extends CI_Model
{

    //---------------------------- DATATABLE ----------------------------//
    function make_query()
    {

        $this->db->select('
            staff.id as st_id,
            staff.name as st_name,
            staff.lastname as st_lastname,
            staff.name_th as st_name_th,
            staff.lastname_th as st_lastname_th,
            staff.username as st_username,
            staff.password_show as st_password_show,
            staff.franshine_id as st_franshine,
            retail_methodorder.topic as st_topic,
        ');
        $this->db->from('staff');
        $this->db->join('retail_methodorder', "staff.franshine_id= retail_methodorder.id", 'left');
        $this->db->where('staff.status', 1);    //  for show 

        if (!empty($_POST["search"]["value"])) {
            $this->db->where(
                "(staff.name like '%" . $_POST["search"]["value"] . "%'
                or staff.lastname like '%" . $_POST["search"]["value"] . "%'
                or staff.name_th like '%" . $_POST["search"]["value"] . "%'
                or staff.lastname_th like '%" . $_POST["search"]["value"] . "%'
                )",
                null,
                null
            );
        }

        if (!empty($_POST["order"])) {
            
        } else {
            $this->db->order_by('staff.id', 'DESC');
        }

        if (!empty($_POST["selectfranshine"])) {
            $this->db->where('staff.id', $_POST["selectfranshine"]);
        }
    }
    function make_datatables()
    {
        $this->make_query();
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

    function addStaff()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $arr_name = array_keys(array_column($data,'name'),'add_name');
        $arr_lastname = array_keys(array_column($data,'name'),'add_lastname');
        $arr_name_th = array_keys(array_column($data,'name'),'add_name_th');
        $arr_lastname_th = array_keys(array_column($data,'name'),'add_lastname_th');
        $arr_username = array_keys(array_column($data,'name'),'add_username');
        $arr_password = array_keys(array_column($data,'name'),'add_password');
        $arr_franshine_id = array_keys(array_column($data,'name'),'add_franshine_id');
        $arr_franshine_text = array_keys(array_column($data,'name'),'add_franshine_text');

        $insert = array(
            'name'      => get_valueNullToNull($data[$arr_name[0]]->value),
            'lastname'  => get_valueNullToNull($data[$arr_lastname[0]]->value),
            'name_th'      => get_valueNullToNull($data[$arr_name_th[0]]->value),
            'lastname_th'      => get_valueNullToNull($data[$arr_lastname_th[0]]->value),
            'username'      => get_valueNullToNull($data[$arr_username[0]]->value),
            'password'      => get_valueNullToNull(md5($data[$arr_password[0]]->value)),
            'password_show'      => get_valueNullToNull($data[$arr_password[0]]->value),
            'franshine_id'      => get_valueNullToNull($data[$arr_franshine_id[0]]->value),
            'date_starts'      => date('Y-m-d H:i:s'),
            'user_starts'      => $this->session->userdata('useradminid'),
        );
        $this->db->insert('staff',$insert);
        $newid = $this->db->insert_id();

        // ============== Log_Detail ============== //
        $log_query = $this->db->last_query();
        $last_id = $this->session->userdata('log_id');
        $detail = "Insert Staff Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
        $type = "Insert";
        $arraylog = array(
            'log_id'            => $last_id,
            'detail'           => $detail,
            'logquery'       => $log_query,
            'type'               => $type,
            'date_starts'    => date('Y-m-d H:i:s')
        );
        updateLog($arraylog);

        if($newid){
            $result = array(
                'id'          => $newid,
                'data'        => $insert,
            );
        }
        
        return $result;
    }

    function deleteStaff($id)
    {
        $update = array(
            'status'      => 0,
        );
        $this->db->where('id',$id);
        $this->db->update('staff',$update);

        // ============== Log_Detail ============== //
        $log_query = $this->db->last_query();
        $last_id = $this->session->userdata('log_id');
        $detail = "Update Staff Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
        $type = "DELETE";
        $arraylog = array(
            'log_id'            => $last_id,
            'detail'           => $detail,
            'logquery'       => $log_query,
            'type'               => $type,
            'date_starts'    => date('Y-m-d H:i:s')
        );
        updateLog($arraylog);

        if($this->db->affected_rows()){
            $result = array(
                'id'          => $id,
                'data'        => $update,
            );
        }
        
        return $result;
    }

    function updateStaff($id)
    {
        //  get data
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $arr_name = array_keys(array_column($data,'name'),'name');
        $arr_lastname = array_keys(array_column($data,'name'),'lastname');
        $arr_name_th = array_keys(array_column($data,'name'),'name_th');
        $arr_lastname_th = array_keys(array_column($data,'name'),'lastname_th');
        $arr_username = array_keys(array_column($data,'name'),'username');
        $arr_password = array_keys(array_column($data,'name'),'password');
        $arr_franshine_id = array_keys(array_column($data,'name'),'franshine_id');
        $arr_franshine_text = array_keys(array_column($data,'name'),'franshine_text');

        $update = array(
            'name'      => get_valueNullToNull($data[$arr_name[0]]->value),
            'lastname'  => get_valueNullToNull($data[$arr_lastname[0]]->value),
            'name_th'      => get_valueNullToNull($data[$arr_name_th[0]]->value),
            'lastname_th'      => get_valueNullToNull($data[$arr_lastname_th[0]]->value),
            'username'      => get_valueNullToNull($data[$arr_username[0]]->value),
            'password'      => get_valueNullToNull(md5($data[$arr_password[0]]->value)),
            'password_show'      => get_valueNullToNull($data[$arr_password[0]]->value),
            'franshine_id'      => get_valueNullToNull($data[$arr_franshine_id[0]]->value),
            'date_update'      => date('Y-m-d H:i:s'),
            'user_update'      => $this->session->userdata('useradminid'),
        );
        $this->db->where('id',$id);
        $this->db->update('staff',$update);

        // set array return JWT
        // find method order
        $sql_meth = $this->db->select('topic')
        ->from('retail_methodorder')
        ->where('id',$data[$arr_franshine_id[0]]->value);
        $q_meth = $sql_meth->get();
        $r_meth = $q_meth->row();
        $franshine_name = $r_meth->topic;

        $datainfo = array(
            'id'     	=> $id,
            'name'     => $update['name'],
            'lastname'     		=> $update['lastname'],
            'name_th'     		=> $update['name_th'],
            'lastname_th'     	=> $update['lastname_th'],
            'franshine_text'     => get_valueNullToNull($franshine_name),
            'franshine_id'     	=> $update['franshine_id'],
            'username'     		=> $update['username'],
            'password'     		=> $update['password_show']

        );

        /** 
         * jwt config file load
         */
        $this->load->config('jwt');

        /**
         * Load Config Items Values 
         */
        $this->token_key        = $this->config->item('jwt_key');
        $this->token_algorithm  = $this->config->item('jwt_algorithm');
        $this->token_header  = $this->config->item('token_header');
        
        $tokenDataInfo = JWT::encode($datainfo, $this->token_key, $this->token_algorithm);
        $datainfo['token'] = $tokenDataInfo;

        // ============== Log_Detail ============== //
       /*  $log_query = $this->db->last_query();
        $last_id = $this->session->userdata('log_id');
        $detail = "Update Staff Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
        $type = "Update";
        $arraylog = array(
            'log_id'            => $last_id,
            'detail'           => $detail,
            'logquery'       => $log_query,
            'type'               => $type,
            'date_starts'    => date('Y-m-d H:i:s')
        );
        updateLog($arraylog); */

        if($this->db->affected_rows()){
            $result = array(
                'id'          => $id,
                'data'        => $datainfo,
            );
        }
        
        return $result;
    }
    // ====================== EDIT STATUS ========================== //

    function ajaxeditstatus()
    {
        $id = $this->input->post('id');
        $status_chk = '';
        $this->db->select('retail_productlist.STATUS AS STATUS');
        $this->db->from('retail_productlist');
        $this->db->where('retail_productlist.ID', $id);
        // echo $this->db->get_compiled_select();
        $Query  = $this->db->get();
        $row = $Query->row();

        if ($row->STATUS == 1) {
            $data = array('status' => 0);
        } else {
            $data = array('status' => 1);
        }
        $status_product = $data['status'];
        $status_producttxt = '';
        if ($status_product == 1) {
            $status_producttxt = 'Open';
        } else {
            $status_producttxt = 'Off';
        }

        $this->db->where('id', $id);
        $this->db->update('retail_productlist', $data);

        // ============== Log_Detail ============== //
        $log_query = $this->db->last_query();
        $last_id = $this->session->userdata('log_id');
        $detail = "Update Status product To " . $status_producttxt . " Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
        $type = "Update";
        $arraylog = array(
            'log_id'            => $last_id,
            'detail'           => $detail,
            'logquery'       => $log_query,
            'type'               => $type,
            'date_starts'    => date('Y-m-d H:i:s')
        );
        updateLog($arraylog);
        $code = 0;
        $txt = "";

        $data = array(
            "error_code"         => "",
            "txt"                 => $status_producttxt
        );
        $data = json_encode($data);
        return $data;
    }

    function ajaxdataForm()
    {

        if ($this->input->post('name_th') != "") {
            if ($this->input->post('promain_id') != '') {
                $data = array(
                    'NAME_TH'             => get_valueNullToNull(trim($this->input->post('name_th'))),
                    'NAME_US'             => get_valueNullToNull(trim($this->input->post('name_us'))),

                    'date_update'     => date('Y-m-d H:i:s'),
                    'user_update'     => $this->session->userdata('useradminid'),
                    'status'         => $this->input->post('status')
                );
                $this->db->where('id', $this->input->post('promain_id'));
                $this->db->update('retail_productmain', $data);

                // ============== Log_Detail ============== //
                $log_query = $this->db->last_query();
                $last_id = $this->session->userdata('log_id');
                $detail = "Update Productmain Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
                $type = "Update";
                $arraylog = array(
                    'log_id'            => $last_id,
                    'detail'           => $detail,
                    'logquery'       => $log_query,
                    'type'               => $type,
                    'date_starts'    => date('Y-m-d H:i:s')
                );
                updateLog($arraylog);
                $code = 0;
                $txt = "Update Success";
            } else {
                $data = array(
                    'NAME_TH'             => get_valueNullToNull(trim($this->input->post('name_th'))),
                    'NAME_US'             => get_valueNullToNull(trim($this->input->post('name_us'))),

                    'date_starts'     => date('Y-m-d H:i:s'),
                    'user_starts'     => $this->session->userdata('useradminid'),
                    'status'         => $this->input->post('status')
                );
                $this->db->insert('retail_productmain', $data);
                $last_promoteid = $this->db->insert_id();
                // ============== Log_Detail ============== //
                $log_query = $this->db->last_query();
                $last_id = $this->session->userdata('log_id');
                $detail = "Insert Productmain Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
                $type = "Insert";
                $arraylog = array(
                    'log_id'            => $last_id,
                    'detail'           => $detail,
                    'logquery'       => $log_query,
                    'type'               => $type,
                    'date_starts'    => date('Y-m-d H:i:s')
                );
                updateLog($arraylog);
                $code = 0;
                $txt = "Insert Success";
            }
        } else {
            $code = 1;
            $txt = "ERROR";
        }

        $data = array(
            "error_code"         => "",
            "txt"                 => $txt,
        );

        $data = json_encode($data);
        return $data;
    }

    function ajaxdataProlistForm()
    {
        if ($this->input->post('name_th') != "") {
            if ($this->input->post('prolist_id') != '') {
                //  ตรวจสอบสินค้าที่ผูกกับโปร

                $sqlhook = $this->db->select('list_id')
                    ->from('retail_productlist')
                    ->where('id', $this->input->post('prolist_id'));
                $qhook = $sqlhook->get();
                $numhook = $qhook->num_rows();
                if ($numhook) {
                    $rhook = $qhook->row();

                    $list_old = $rhook->LIST_ID ? $rhook->LIST_ID : null;
                }

                $status = $this->input->post('status');
                $status_view = 1;

                if ($status == 3) {
                    $status = 0;
                    $status_view = 0;
                }

                if (trim($this->input->post('listid'))) {
                    $listid = trim($this->input->post('listid'));
                } else {
                    $listid = null;
                }

                $data = array(
                    'NAME_TH'             => get_valueNullToNull(trim($this->input->post('name_th'))),
                    'NAME_US'             => get_valueNullToNull(trim($this->input->post('name_us'))),
                    'PROMAIN_ID'             => get_valueNullToNull(trim($this->input->post('promain_id'))),
                    'LIST_ID'             => get_valueNullToNull($listid),
                    'PRICE'             => get_valueNullToNull(trim($this->input->post('price'))),
                    'CODE'             => get_valueNullToNull(trim($this->input->post('code'))),

                    'date_update'     => date('Y-m-d H:i:s'),
                    'user_update'     => $this->session->userdata('useradminid'),
                    'status_view'     => $status_view,
                    'status'         => $status
                );

                $this->db->where('id', $this->input->post('prolist_id'));
                $this->db->update('retail_productlist', $data);

                //  update bill
                if ($list_old != $listid) {
                    $dataupdate = array(
                        'list_id'             => $listid
                    );

                    $this->db->where('prolist_id', $this->input->post('prolist_id'));
                    $this->db->where('status', 1);
                    $this->db->update('retail_billdetail', $dataupdate);
                }

                // ============== Log_Detail ============== //
                $log_query = $this->db->last_query();
                $last_id = $this->session->userdata('log_id');
                $detail = "Update Product List Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
                $type = "Update";
                $arraylog = array(
                    'log_id'            => $last_id,
                    'detail'           => $detail,
                    'logquery'       => $log_query,
                    'type'               => $type,
                    'date_starts'    => date('Y-m-d H:i:s')
                );
                updateLog($arraylog);
                $code = 0;
                $txt = "Update Success";
                $getid = $this->input->post('prolist_id');
            } else {
                $data = array(
                    'NAME_TH'             => get_valueNullToNull(trim($this->input->post('name_th'))),
                    'NAME_US'             => get_valueNullToNull(trim($this->input->post('name_us'))),
                    'PROMAIN_ID'             => get_valueNullToNull(trim($this->input->post('promain_id'))),
                    'PRICE'             => get_valueNullToNull(trim($this->input->post('price'))),
                    'CODE'             => get_valueNullToNull(trim($this->input->post('code'))),

                    'date_starts'     => date('Y-m-d H:i:s'),
                    'user_starts'     => $this->session->userdata('useradminid'),
                    'status'         => $this->input->post('status')
                );
                $this->db->insert('retail_productlist', $data);
                $last_productlist = $this->db->insert_id();
                // ============== Log_Detail ============== //
                $log_query = $this->db->last_query();
                $last_id = $this->session->userdata('log_id');
                $detail = "Insert Product List Code : " . $this->session->userdata('useradminid') . " Name : " . $this->session->userdata('useradminname');
                $type = "Insert";
                $arraylog = array(
                    'log_id'            => $last_id,
                    'detail'           => $detail,
                    'logquery'       => $log_query,
                    'type'               => $type,
                    'date_starts'    => date('Y-m-d H:i:s')
                );
                updateLog($arraylog);
                $code = 0;
                $txt = "Insert Success";
                $getid = $last_productlist;
            }
        } else {
            $code = 1;
            $txt = "ERROR";
            $getid = 0;
        }

        $data = array(
            "error_code"         => "",
            "txt"                 => $txt,
            "getid"             => $getid
        );

        $data = json_encode($data);
        return $data;
    }
}
