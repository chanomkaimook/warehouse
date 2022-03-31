<?php
defined('BASEPATH') or exit('No direct script access allowed');



require APPPATH . 'libraries/RestController.php';

use chriskacerguis\RestServer\RestController;


class AuthDataStaff extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_staff');
        $this->load->library('session');
        $this->load->library('Permiss');
        $this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));
    }

    public function user_get($id)
    {
        /* parse_str(file_get_contents("php://input"),$put_vars);
var_dump($put_vars); */
        // $token = $this->tokengenerate();
        // echo ($token ?  "access to data complete" :  false);
        $array = array('status' => 'ok', 'data' => $id);
        $this->response($array);
    }

    public function user_post($id = "")
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json);
        /* echo "id :".$id;
        echo $this->uri->segment(3);
        echo "<pre>";
        print_r($data);
        echo count($data);
        echo "</pre>"; */
        /* $dataarray['username'] = array_keys(array_column($data,'name'),'add_username');
        $dataarray['password'] = array_keys(array_column($data,'name'),'add_password');
        $dataarray['franshine'] = array_keys(array_column($data,'name'),'add_franshine_id');

        foreach($dataarray as $key => $val){
            if(!$data[$val[0]]->value){
                $result = array(
                    'error_code'  => 1 ,
                    'data'  => 'โปรดระบุ '.$key
                );
                $this->response($result);
            }
            
        } */

        /* foreach($data as $key){
            echo $key->name."-<br>";

            
        } */

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            //  page event
            $event = $this->uri->segment(3);

            if ($event == 'delete') {
                if (!$id) {
                    $result = array(
                        'error_code'    => 1,
                        'data'          => 'Not found userid'
                    );
                    $this->response($result);
                }

                $result = $this->mdl_staff->deleteStaff($id);
                $this->response($result);
            } else if ($event == 'add') {
                //  get data
                $json = file_get_contents('php://input');
                $data = json_decode($json);

                $dataarray['username'] = array_keys(array_column($data, 'name'), 'add_username');
                $dataarray['password'] = array_keys(array_column($data, 'name'), 'add_password');
                $dataarray['franshine'] = array_keys(array_column($data, 'name'), 'add_franshine_id');

                foreach ($dataarray as $key => $val) {
                    if (!$data[$val[0]]->value) {
                        $result = array(
                            'error_code'  => 1,
                            'data'  => 'โปรดระบุ ' . $key
                        );
                        $this->response($result);
                    }
                }

                //  check duplicate
                if ($dataarray['username']) {
                    $sql = $this->db->from('staff')
                        ->where('username', $data[$dataarray['username'][0]]->value)
                        ->where('status', 1);
                    $q = $sql->get();
                    $num = $q->num_rows();
                    if ($num) {
                        $result = array(
                            'error_code'  => 2,
                            'data'  => 'username มีการใช้งานแล้ว '
                        );
                        $this->response($result);
                    }
                }

                $result = $this->mdl_staff->addStaff();
                $this->response($result);
            } else if ($event == 'edit') {

                //  get data
                $json = file_get_contents('php://input');
                $data = json_decode($json);

                $dataarray['username'] = array_keys(array_column($data, 'name'), 'username');
                $dataarray['password'] = array_keys(array_column($data, 'name'), 'password');
                $dataarray['franshine'] = array_keys(array_column($data, 'name'), 'franshine_id');

                foreach ($dataarray as $key => $val) {
                    if (!$data[$val[0]]->value) {
                        $result = array(
                            'error_code'  => 1,
                            'data'  => 'โปรดระบุ ' . $key
                        );
                        $this->response($result);
                    }
                }

                //  check duplicate
                if ($dataarray['username']) {
                    $sql = $this->db->from('staff')
                        ->where('username', $data[$dataarray['username'][0]]->value)
                        ->where('id !=', $id)
                        ->where('status', 1);
                    $q = $sql->get();
                    $num = $q->num_rows();
                    if ($num) {
                        $result = array(
                            'error_code'  => 2,
                            'data'  => 'username มีการใช้งานแล้ว '
                        );
                        $this->response($result);
                    }
                }

                $result = $this->mdl_staff->updateStaff($id);
                $this->response($result);
            } else {
                $result = array(
                    'error_code'  => 3,
                    'data'  => 'ไม่มีการทำงาน'
                );
                $this->response($result);
            }
        } else {
            $json = file_get_contents('php://input');
            $result = json_decode($json);

            $this->response($result);
        }
    }

    public function user_patch($id)
    {
        if ($this->input->server('REQUEST_METHOD') == 'PATCH') {
            //  get data

            $result = $this->mdl_staff->deleteStaff($id);
            $this->response($result);
        } else {
            $json = file_get_contents('php://input');
            $result = json_decode($json);

            $this->response($result);
        }
    }

    public function user_put($id)
    {
        /*         parse_str(file_get_contents("php://input"),$put_vars);
var_dump($put_vars);
 */
        if ($this->input->server('REQUEST_METHOD') == 'PUT') {
            //  get data
            $json = file_get_contents('php://input');
            $data = json_decode($json);

            $dataarray['username'] = array_keys(array_column($data, 'name'), 'username');
            $dataarray['password'] = array_keys(array_column($data, 'name'), 'password');
            $dataarray['franshine'] = array_keys(array_column($data, 'name'), 'franshine_id');

            foreach ($dataarray as $key => $val) {
                if (!$data[$val[0]]->value) {
                    $result = array(
                        'error_code'  => 1,
                        'data'  => 'โปรดระบุ ' . $key
                    );
                    $this->response($result);
                }
            }

            //  check duplicate
            if ($dataarray['username']) {
                $sql = $this->db->from('staff')
                    ->where('username', $data[$dataarray['username'][0]]->value)
                    ->where('id !=', $id)
                    ->where('status', 1);
                $q = $sql->get();
                $num = $q->num_rows();
                if ($num) {
                    $result = array(
                        'error_code'  => 2,
                        'data'  => 'username มีการใช้งานแล้ว ' . $id
                    );
                    $this->response($result);
                }
            }

            //  run update
            $result = $this->mdl_staff->updateStaff($id);
            $this->response($result);
        } else {
            $json = file_get_contents('php://input');
            $result = json_decode($json);

            $this->response($result);
        }

        // $this->response($result,RestController::HTTP_OK);

    }

    public function token_post()
    {
        $data = array(
            'user'      => 'admin',
            'email'     => 'psk@gmail.com'

        );

        $token = $this->authorization_token->generateToken($data);

        $this->response($token);
        // $this->response($token);
    }

    public function tokenverify_post()
    {
        $headers = $this->input->request_headers();

        $decodedToken = $this->authorization_token->validateToken($headers);

        $this->response($decodedToken);
    }
}
