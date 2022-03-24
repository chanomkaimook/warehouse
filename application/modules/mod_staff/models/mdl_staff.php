<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_staff extends CI_Model
{

    //---------------------------- DATATABLE ----------------------------//
    var $order_column = array("ID", "RPL_NAME_TH", "RPL_NAME_US", null, null);
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
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
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
        $error_code = 1;

        $result = array(
            'error_code'    => $error_code,
            'data'          => $_REQUEST,
        );
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
