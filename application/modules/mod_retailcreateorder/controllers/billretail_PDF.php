<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Billretail_PDF extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('mdl_sql');
		$this->load->model('mdl_createorder');
		$this->load->model('mdl_uplode');
		$this->load->library('session');
		$this->load->library('Permiss');
		$this->load->helper(array('form', 'url', 'myfunction_helper', 'sql_helper', 'permiss_helper', 'array_helper'));

		$this->set	= array(
			'ctl_name'				=> 'ctl_sentformems',
			'username_session'		=> $this->session->userdata('useradminname'),
			'userid_session'		=> $this->session->userdata('useradminid')
		);
		if ($this->session->userdata('useradminid') == '') {
			redirect('mod_admin/ctl_login');
		}
	}

	public function BillPDF()
	{
		$html = '';
		$id = $this->input->get('id');
		$mdl = $this->input->get('mdl');
		if ($mdl) {
			$mdl = $mdl;
		} else {
			$mdl = 'mdl_createorder';
		}
		$QueryBillDetail = $this->$mdl->datebilldetail($id);
		/* echo "<pre>";
		print_r($QueryBillDetail);
		echo "</pre>";
		exit; */
		// STYLE //
		$html .= '
		<style>
			table {
				width: 100%;
			}

			td {
 				padding: 0.8%;
			}
			td.child {
				padding: 0.2% !important;
		   	}
			th {
				text-align: left;
				padding: 0.4%;
			}
			.border {
				border: 1px solid #111;
			}
			.text-right {text-align: right;}
			.text-center {text-align: center;}
			.text-left {text-align: left;}
			.font-color { color: #FFF;padding: 0.2rem 0.5rem; }
			.title {color: #FFF;padding: 0.2rem 0.5rem;background-color: #333;}
			.P-1rem {padding-top: 0.5rem;}

			.headtext {
				width:60px;
				background-color:#ccc;
			}
			.table {
				border: 1px solid #333;
				border-collapse: collapse;
			}
			.br {
				height:15px;
			}
			#ORlist td {
				border-left: 1px solid #111
			}
			#sign td {
				font-weight:bold;
				vertical-align:bottom;
				padding:20px 0px;
			}
		</style> 
		';
		// HTML //

		$html .= '<table class="">';
		$html .= '	<tbody>';
		$html .= '		<tr>';
		$html .= '			<td class=" text-center" style="width: 85%;vertical-align: bottom;"> 
								<div style=" font-size: 1.5rem; font-weight: bold; "> บริษัท โชคชัยฟาร์มโปรดิวซ์ จำกัด </div> 
								<div style=" font-size: 1.5rem; font-weight: bold; "> ใบโอนย้ายของ/สินค้า </div> 
							</td>';
		$html .= '		</tr>';
		$html .= '	</tbody>';
		$html .= '</table>';

		$html .= '<div class="br"></div>';

		$html .= '<table class="table border P-1rem">';
		$html .= '	<tbody>';


		$html .= '		<tr>';
		$html .= '			<td rowspan=3 class="border" width=70% style="vertical-align:top"> 
			<table class="">
				<tr>
					<td class="child">
						<b>คลังสินค้าเดิม :</b> 
					</td>
					<td class="child">
						601 ศูนย์กระจายสินค้า Umm!...Milk HO 
					</td>
				</tr>
				<tr>
					<td class="child">
						<b>คลังสินค้าใหม่ :</b> 
					</td>
					<td class="child">
						' . $QueryBillDetail["METHODORDER_TOPIC"] . '
					</td>
				</tr>
			</table>
		</td>';

		$html .= '			<td class="border"> <b>เลขที่ :</b> ' . $QueryBillDetail["CODE"] . '</td>';
		$html .= '		</tr>';
		$html .= '		<tr>';
		$html .= '			<td class="border"> <b>วันที่ :</b> ' . $QueryBillDetail["DATE_STARTS"] . '</td>';
		$html .= '		</tr>';
		$html .= '		<tr>';
		$html .= '			<td class="border"> <b>แผนก :</b> ' . $QueryBillDetail["METHODORDER_TOPIC"] . '</td>';
		$html .= '		</tr>';

		$html .= '	</tbody>';
		$html .= '</table>';

		$html .= '<div class="br"></div>';

		$html .= '<div class="P-1rem">';
		$html .= '	<table class="table border" id="table-bill">';
		$html .= '		<thead>';
		$html .= '			<tr style="background-color: #d9d9d9;">';
		$html .= '				<th class="border" style="width: 5px;text-align: center;">ลำดับ</th>';
		$html .= '				<th class="border" style="width: 65px;text-align: center;">รหัสสินค้า</th>';
		$html .= '				<th class="border" style="width: 10px;text-align: center;">รายละเอียด</small></th>';
		$html .= '				<th class="border" style="width: 10px;text-align: center;">หน่วยนับ</th>';
		$html .= '				<th class="border" style="width: 10px;text-align: center;">จำนวน</th>';
		$html .= '			</tr>';
		$html .= '		</thead>';
		$html .= '		<tbody id="ORlist">';
		$index = 1;
		foreach ($QueryBillDetail['billist'] as $row1) {
			foreach ($row1['PRONAME_LIST'] as $row2) {
				$html .= '			<tr class="each-total">';
				$html .= '				<td style="text-align: center;"> ' . $index++ . ' </td>';
				$html .= '				<td style="text-align: center;">  ' . $row2['PRO_CODEMAC'] . ' </td>';
				$html .= '				<td style="text-align: left;">  ' . $row2['PRONAME_LIST'] . ' </td>';
				$html .= '				<td style="text-align: center;"> ชิ้น </td>';
				$html .= '				<td style="text-align: center;"> ' . $row2['QUANTITY'] . '</td>';
				$html .= '			</tr>';
			}
		}
		if($QueryBillDetail['billist'] && count($QueryBillDetail['billist']) < 15){
			for($i=count($QueryBillDetail['billist']);$i<=15;$i++){
				$html .= '			<tr class="each-total">';
				$html .= '				<td style="text-align: center;">'.$i.'</td>';
				$html .= '				<td style="text-align: center;"></td>';
				$html .= '				<td style="text-align: left;"></td>';
				$html .= '				<td style="text-align: center;"></td>';
				$html .= '				<td style="text-align: center;"></td>';
				$html .= '			</tr>';
			}
		}

		if ($QueryBillDetail['REMARKORDER'] != '') {
			$remark = $QueryBillDetail["REMARKORDER"];
		}
		$html .= '		<tr class="border">';
		$html .= '			<td colspan=5 > 
								<div style="padding-bottom: 0.5rem;"> <b> หมายเหตุ : </b> ' . $remark . ' </div>
							</td>';
		$html .= '		</tr>';

		$html .= '		</tbody>';

		$html .= '	</table>';

		$html .= '<div class="br"></div>';

		$html .= '<table id="sign" class="table">';
		$html .= '	<tbody>';
		$html .= '		<tr>';
		$html .= '			<td class="border" style="height:150px;text-align:center"> 
		<div>..................................................</div>
		<div>ผู้เบิกจ่าย</div>
		<div>วันที่______/______/______</div>
		</td>';
		$html .= '			<td class="border" style="text-align:center"> 
		<div>..................................................</div>
		<div>ผู้ส่งของ</div> 
		<div>วันที่______/______/______</div>
		</td>';
		$html .= '			<td class="border" style="text-align:center">
		<div>..................................................</div> 
		<div>ผู้รับของ</div> 
		<div>วันที่______/______/______</div>
		</td>';
		$html .= '		</tr>';

		$html .= '	</tbody>';
		$html .= '</table>';

		$html .= '</div>';



		// echo $html; exit;
		$data['htmlPDF'] = $html;
		$data['base_bn'] = base_url() . BASE_BN;
		$data['basepic'] = base_url() . BASE_PIC;
		$this->load->view('BillPDF', $data);
	}
}
