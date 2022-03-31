<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'third_party/php-jwt/JWT.php';
require_once APPPATH . 'third_party/php-jwt/BeforeValidException.php';
require_once APPPATH . 'third_party/php-jwt/ExpiredException.php';
require_once APPPATH . 'third_party/php-jwt/SignatureInvalidException.php';

use \Firebase\JWT\JWT;

class Ctl_staff extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('mdl_staff');
		$this->load->model('mdl_sql');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper'));

		$this->set	= array(
			'max_upload_image'		=> 1000000,		// 1 k = 1000
			'max_size_image'		=> 1920,
			'ctl_name'				=> 'ctl_staff',
			'mdl'					=> $this->mdl_staff,
			'mainmenu'		        => 'retail',
			'submenu'		        => 'staff',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function testCurl()
	{
		$url = site_url('api/staff/edit/64');

		$curl = curl_init();

		// CURLOPT_URL => 'https://warehouse1.chokchaisteakhouse.com/api/staff/edit/64',
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://warehouse1.chokchaisteakhouse.com/api/staff/edit/64',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'PUT',
			CURLOPT_POSTFIELDS => '[
			{
				"name":"userid",
				"value":"64"
			},
			{
				"name":"name",
				"value":"Tops เซ็นทรัล บางนา"
			},
			{
				"name":"lastname",
				"value":""
			},
			{
				"name":"name_th",
				"value":"Tops เซ็นทรัล บางนา"
			},
			{
				"name":"lastname_th",
				"value":""
			},
			{
				"name":"username",
				"value":"CTBN"
			},
			{
				"name":"password",
				"value":"0161"
			},
			{
				"name":"franshine_text",
				"value":"CTBN Tops เซ็นทรัล บางนา(505)"
			},
			{
				"name":"franshine_id",
				"value":"16"
			}
			]',
			CURLOPT_HTTPHEADER => array(
				'API-KEY: XOGgx6vzY2yIj7li4tS1PMrqckh8dmE5FVQRZGeL',
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
	}

	public function staff()
	{
		if (chkPermiss() == 1) {
			redirect('mod_admin/ctl_login');
		}

		$data = array(
			'mainmenu' 		=> $this->set['mainmenu'],
			'submenu' 		=> $this->set['submenu']
		);

		$data['Query_productmain'] = $this->mdl_sql->get_WhereParaqry('retail_productmain', 'status', 1);
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('table', $data);
	}

	function fetch_list()
	{
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
		$this->token_expire_time  = $this->config->item('token_expire_time');

		$fetch_data = $this->set['mdl']->make_datatables();

		$data = array();
		$index = 1;
		foreach ($fetch_data as $row) {

			// use JWT
			$datainfo = array(
				'id'     	=> $row->st_id,
				'name'     => $row->st_name,
				'lastname'     		=> $row->st_lastname,
				'name_th'     		=> $row->st_name_th,
				'lastname_th'     	=> $row->st_lastname_th,
				'franshine_text'     => $row->st_topic,
				'franshine_id'     	=> $row->st_franshine,
				'username'     		=> $row->st_username,
				'password'     		=> $row->st_password_show

			);
			// $tokenDataInfo = $this->authorization_token->generateToken($datainfo);
			$tokenDataInfo = JWT::encode($datainfo, $this->token_key, $this->token_algorithm);

			$sub_array = array();
			$sub_array['no'] = "";
			$sub_array['id'] = $row->st_id;
			$sub_array['data-info'] = $tokenDataInfo;
			$sub_array['name'] = $row->st_name_th ? $row->st_name_th . " " . $row->st_lastname_th : $row->st_name . " " . $row->st_lastname;
			$sub_array['account'] = $row->st_username;
			$sub_array['franshine'] = get_valueNullToNull($row->st_topic);

			$data[] = $sub_array;
		}
		$output = array(
			"draw"             	=>     intval($_POST["draw"]),
			"recordsTotal"      =>     $this->set['mdl']->get_all_data(),
			"recordsFiltered"   =>     $this->set['mdl']->get_filtered_data(),
			"data"              =>     $data
		);

		echo json_encode($output);
	}

	public function ajaxdataProlistForm()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$this->load->model('mdl_retailstock');

			$returns = $this->mdl_retailproduct->ajaxdataProlistForm();
			$return = json_decode($returns);
			echo $returns;
		}
	}

	public function get_listPromotionRef()
	{

		if ($this->input->server('REQUEST_METHOD') == 'GET') {
			$dataresult = array();

			$sql = $this->db->select('*')
				->from('retail_productlist')
				->where('retail_productlist.list_id', $this->input->get('sku'))
				->where('retail_productlist.status_view', 1)
				->where('retail_productlist.status', 1);
			$num = $sql->count_all_results(null, false);
			$q = $sql->get();
			if ($num) {
				foreach ($q->result() as $row) {
					$dataresult[] = $row->NAME_TH;
				}
			}

			$return = json_encode($dataresult);
			echo $return;
			exit;
		}
	}
}
