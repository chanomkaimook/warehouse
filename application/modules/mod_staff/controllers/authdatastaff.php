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
        $this->load->helper(array('form', 'url','myfunction_helper','sql_helper','permiss_helper'));
    }

    public function user_get()
    {
        // $token = $this->tokengenerate();
        // echo ($token ?  "access to data complete" :  false);
        $array = array('status' => 'ok', 'data' => 1);
        $this->response($array);
    }

    public function user_post()
    {
        $result = array(
            'error_code'    => 1,
            'data'          => "----",
        );
        if($this->input->server('REQUEST_METHOD') == 'POST'){
			$result = $this->mdl_staff->addStaff();
		}

        $this->response($result);

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
