<?php 
error_reporting(E_ALL & ~E_NOTICE);

//
//	Funtion for set variable use to all module
//	@param param	@text	= variable name
//	return @text
//
function varglobal($param){
	$ci =& get_instance();
	$ci->load->database();
	
	$result = "";
	if(get_valueNullToNull(trim($param))){	
		switch($param){
			case 'url_homepage' :
				$result = 'admin/pagemain/main';
				break;
			case 'url_logout' :
				$result = "login/logout";
				break;
			case 'url_edit' :
				$result = "admin/staff/profilepage_edit?syst=2";
				break;
			case 'url_begin' :
				$result = $ci->uri->segment(1).'/'.$ci->uri->segment(2);
				break;
			case 'url_current' :
				$result = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3);
				break;
			case 'url_pathadmin' :
				$result = 'home/pathadmin';
				break;
			case 'url_log' :
				$result = 'log/log/logpage';
				break;
			case 'permitexpire' :
				$result = 'home/permit';
				break;
			default:
				$result = $result;
				break;
		}
	}
	
	return $result;
}
	
function updateLog($arraylog){
	//=	 call database	=//
	$ci =& get_instance();
	$ci->load->database();
	
	//===================//
	if($arraylog){
		$datains = array(
					   'log_id'			=> $arraylog['log_id'],
					   'detail' 		=> $arraylog['detail'],
					   'logquery'       => $arraylog['logquery'],
					   'type' 			=> $arraylog['type'],
					   'date_starts' 	=> $arraylog['date_starts']
					);
		$ci->db->insert('log_detail', $datains);
	}
	
}
function array_group_by($key, $data) {
	$result = array();

	foreach($data as $val) {
		if(array_key_exists($key, $val)){
			$result[$val[$key]][] = $val;
		}else{
			$result[""][] = $val;
		}
	}

	return $result;
}
function ajaxcoverpage($FranshineID) {
 //=  call database =//
 $ci =& get_instance();
 $ci->load->database();
 //===================//
 $ci->db->select('*');
 $ci->db->from("coverpage");
 $ci->db->where('status', 1);
 $ci->db->where('FRANSHINE', $FranshineID);
 $q = $ci->db->get();
 $r = $q->row();
  $date1 = $r->PRO_END;
  $date2 = date('Y-m-d H:i:s');
  $datetime1 = new DateTime($date1);
  $datetime2 = new DateTime($date2);
  $interval = $datetime1->diff($datetime2);
   
  if(date('Y-m-d') > date('Y-m-d',strtotime($r->PRO_END))){
     
   if($interval->invert == 1){
    $interval = $interval->days + 1;
   } else {
    $interval = $interval->days;
   }

   if($interval > 0){ 
    $data = array ( 'STATUS' => 0,);
    $ci->db->where('id', $r->ID);
    $ci->db->update('coverpage', $data);
   }
  }  
  
 $ci->db->select('*');
 $ci->db->from('coverpage');
 $ci->db->where('status', 1);
 $ci->db->order_by('id', 'DESC');
//   echo $ci->db->get_compiled_select();
 $Querycoverpage = $ci->db->get();
  
 $explode = []; $items1 = []; $items2 = []; $arrin = [1,2,3];  $result = [];
  foreach($Querycoverpage->result() AS $key => $row){
  $explode = explode(",", $row->FRANSHINE);
  $explodeorder = explode(",", $row->ORDER);
  if($explodeorder[0] != ''){
    $index001 = $explodeorder[0];
   if(in_array($FranshineID, $explode)){
    if(in_array($explodeorder[0], $arrin)){
       $result[$index001]['ID'] = $items1[$explodeorder[0]-1]['ID'] = $row->ID;
       $result[$index001]['ORDER'] = $items1[$explodeorder[0]-1]['ORDER'] = $index001;
       $result[$index001]['FRANSHINE'] = $items1[$explodeorder[0]-1]['FRANSHINE'] = $row->FRANSHINE;
       $result[$index001]['TOPIC_TH'] = $items1[$explodeorder[0]-1]['TOPIC_TH'] = $row->TOPIC_TH;
       $result[$index001]['TOPIC_US'] = $items1[$explodeorder[0]-1]['TOPIC_US'] = $row->TOPIC_US;
	   $result[$index001]['PRO_END'] = $items1[$explodeorder[0]-1]['PRO_END'] = $row->PRO_END;
	   $result[$index001]['TYPE'] = $items1[$explodeorder[0]-1]['TYPE'] = $row->TYPE;
       $result[$index001]['LINK_URL'] = $items1[$explodeorder[0]-1]['LINK_URL'] = $row->LINK_URL;
       $result[$index001]['PIC'] = $items1[$explodeorder[0]-1]['PIC'] = $row->PIC;
       $result[$index001]['DATE_STARTS'] = $items1[$explodeorder[0]-1]['DATE_STARTS'] = $row->DATE_STARTS;
       $result[$index001]['DATE_UPDATE'] = $items1[$explodeorder[0]-1]['DATE_UPDATE'] = $row->DATE_UPDATE;
       $result[$index001]['USER_STARTS'] = $items1[$explodeorder[0]-1]['USER_STARTS'] = $row->USER_STARTS;
       $result[$index001]['USER_UPDATE'] = $items1[$explodeorder[0]-1]['USER_UPDATE'] = $row->USER_UPDATE;
       $result[$index001]['STATUS'] = $items1[$explodeorder[0]-1]['STATUS'] = $row->STATUS;
     }
   }
  }
 }
 $number2 = 4;
 foreach($Querycoverpage->result() AS $key => $row){
  $explode = explode(",", $row->FRANSHINE);
  $explodeorder = explode(",", $row->ORDER);
  $index002 = $explodeorder[0];
  if(in_array($FranshineID, $explode)){
   if(!in_array($explodeorder[0], $arrin)){
    $result[$number2]['ID'] = $items1[$key]['ID'] = $row->ID;
    $result[$number2]['ORDER'] = $items1[$key]['ORDER'] = $index002;
    $result[$number2]['FRANSHINE'] = $items1[$key]['FRANSHINE'] = $row->FRANSHINE;
    $result[$number2]['TOPIC_TH'] = $items1[$key]['TOPIC_TH'] = $row->TOPIC_TH;
    $result[$number2]['TOPIC_US'] = $items1[$key]['TOPIC_US'] = $row->TOPIC_US;
	$result[$number2]['PRO_END'] = $items1[$key]['PRO_END'] = $row->PRO_END;
	$result[$number2]['TYPE'] = $items1[$key]['TYPE'] = $row->TYPE;
    $result[$number2]['LINK_URL'] = $items1[$key]['LINK_URL'] = $row->LINK_URL;
    $result[$number2]['PIC'] = $items1[$key]['PIC'] = $row->PIC;
    $result[$number2]['DATE_STARTS'] = $items1[$key]['DATE_STARTS'] = $row->DATE_STARTS;
    $result[$number2]['DATE_UPDATE'] = $items1[$key]['DATE_UPDATE'] = $row->DATE_UPDATE;
    $result[$number2]['USER_STARTS'] = $items1[$key]['USER_STARTS'] = $row->USER_STARTS;
    $result[$number2]['USER_UPDATE'] = $items1[$key]['USER_UPDATE'] = $row->USER_UPDATE;
    $result[$number2]['STATUS'] = $items1[$key]['STATUS'] = $row->STATUS;
    $number2++;
      }
    
  }
 }
  
 ksort($result);
  
 $data = $result;
 return $data;
}
function thai_date($time){					/// [$date = format yyyy-mm-dd]
	// ถ้ามีการเก็บวันที่และเวลาในรูปแบบ timestamp
	// สามารถนำมาใช้งานในฟังก์ชันได้ดังนี้
	// $eng=1224562026; กรณีเป็น timestamp
	//$eng_date=time(); // แสดงวันที่ปัจจุบัน
	//echo thai_date($eng_date);
	
	// ถ้ามีการเก็บวันที่และเวลาในรูปแบบ date
	// คือ 2008-10-31 ต้องเปลี่ยนเป็น timestamp
	// ด้วยคำส่ง strtotime("2008-10-31");
	// แสดงวันที่ 31 เดือน ตุลาคม ปี 2008 แบบภาษาไทย
	//$eng_date=strtotime("2008-10-31"); 
	//echo thai_date($eng_date);
	
	$thai_day_arr=array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
	$thai_month_arr=array(
		"00"=>"",
		"01"=>"มกราคม",
		"02"=>"กุมภาพันธ์",
		"03"=>"มีนาคม",
		"04"=>"เมษายน",
		"05"=>"พฤษภาคม",
		"06"=>"มิถุนายน", 
		"07"=>"กรกฎาคม",
		"08"=>"สิงหาคม",
		"09"=>"กันยายน",
		"10"=>"ตุลาคม",
		"11"=>"พฤศจิกายน",
		"12"=>"ธันวาคม"                 
	);
	$time_day = date("w",$time);
	$time_month = date("m",strtotime($time));
	/*$thai_date_return="วัน".$this->thai_day_arr[$time_day];
	$thai_date_return.= "ที่ ".date("j",$time);
	$thai_date_return.=" เดือน".$this->thai_month_arr[$time_month];
	$thai_date_return.= " พ.ศ.".(date("Y",$time)+543);*/
	
	$thai_date_return = date("j",strtotime($time))." ".$thai_month_arr[$time_month]." ".(date("Y",strtotime($time))+543);
	//$thai_date_return.= "  ".date("H:i",$time)." น.";
	return $thai_date_return;
}

function thai_date_month($month){ 
	 
	$thai_month_arr=array(
		"00"=>"",
		"01"=>"มกราคม",
		"02"=>"กุมภาพันธ์",
		"03"=>"มีนาคม",
		"04"=>"เมษายน",
		"05"=>"พฤษภาคม",
		"06"=>"มิถุนายน", 
		"07"=>"กรกฎาคม",
		"08"=>"สิงหาคม",
		"09"=>"กันยายน",
		"10"=>"ตุลาคม",
		"11"=>"พฤศจิกายน",
		"12"=>"ธันวาคม"                 
	);
 	$time_month = date("m",strtotime($month));
 	$thai_date_return = date($thai_month_arr[$time_month]);
 	return $thai_date_return;
}
 
function get_valueLangadmin($th,$us){			/// ใช้เลือกระหว่าง ภาษา ไทย อังกฤษ
	//=	 call database	=//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	$lang = '';
	////	for cookie
	if($_COOKIE['langadmin']) {
		$lang = $_COOKIE['langadmin'];
	}else if($ci->session->userdata('lang')) {	////	for session
		$lang = $ci->session->userdata('lang');
	}else{
		$lang;
	}
 	if($lang == 'us'){
		$result = $us;
	}else{
		if($th != ''){
			$result = $th;
		}else{
			$result = $us;
		}
	}
	
	// echo " ==:> ".$us; exit;
	////////////////////////////
	////		Result		////
	return $result;
}
 
function get_valueNullToNull($value){		/// ถ้าเป้นค่าว่าง ให้ คืนค่า ว่าง
	if(trim($value) != '' || trim($value) != NULL){
		$r = trim($value);
	}else{
		$r = NULL;
	}
	//
	//	value 0 to null
	if(empty($value)){
		$r = NULL;
	}
	
	return $r;
}
function get_valueNullTozero($value){		/// ถ้าเป้นค่าว่าง ให้ คืนค่า ว่าง
	$value = trim($value);
	if($value != '' || $value != NULL){
		$r = trim($value);
	}else{
		$r = "0";
	}
   
	if(empty($value)){
		$r = "0";
	}
	return $r;
}

function get_valueLang($th,$us){			/// ใช้เลือกระหว่าง ภาษา ไทย อังกฤษ
	//=	 call database	=//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	if($ci->session->userdata('lang') == 'th'){
		$result = $th;
	}else{
		if($us != ''){
			$result = $us;
		}else{
			$result = $th;
		}
	}
	////////////////////////////
	////		Result		////
	return $result;
}

function get_PromotionDate($id) {
	//=	 call database	=//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	//===== setting	=====//
	$beginto = get_valueLang("เริ่มวันที่","Begin");
	$beginend = get_valueLang("เป็นต้นไป","");
	$end = get_valueLang("ตั้งแต่วันนี้","Today");
	$now = get_valueLang("ตั้งแต่วันนี้เป็นต้นไป","From now");
	//===================//
	//===================//
	$ci->db->select('*');
	$ci->db->from("promotion");
	$ci->db->where("id",$id);
	$q = $ci->db->get();
	$num = $q->num_rows();
	if($num > 0){
		$r = $q->row();
		$pro_starts = "";
		$pro_end = "";
		////	covert date
		if(get_valueNullToNull($r->PRO_STARTS) != ""){
			$pro_starts = get_valueLang(thai_date_indent(date('Y-m-d',strtotime($r->PRO_STARTS))),eng_date_indent(date('Y-m-d',strtotime($r->PRO_STARTS))));
		}
		if(get_valueNullToNull($r->PRO_END) != ""){
			$pro_end = get_valueLang(thai_date_indent(date('Y-m-d',strtotime($r->PRO_END))),eng_date_indent(date('Y-m-d',strtotime($r->PRO_END))));
		}
		////	convert text date
		if($pro_starts != '' && $pro_end != ''){
			$text = $beginto." ".$pro_starts." - ".$pro_end;
		}else if($pro_starts !='' && $pro_end == ''){
			$text = $beginto." ".$pro_starts." ".$beginend;
		}else if($pro_starts == '' && $pro_end != ''){
			$text = $end." - ".$pro_end;
		}else{
			$text = $now;
		}
		
		$result = $text;
	}else{
		$result = "";
	}
	
	return $result;
}

function creat_PromotionDate($pro_starts,$pro_end) {		//	date start , date end
	//=	 call database	=//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	//===== setting	=====//
	$beginto = "เริ่มวันที่";
	$beginend = "เป็นต้นไป";
	$end = "ตั้งแต่วันนี้";
	$now = "ตั้งแต่วันนี้เป็นต้นไป";
	//===================//
	//===================//
	$pro_starts = "";
	$pro_end = "";
	$text = "";
	////	covert date
	if(get_valueNullToNull($pro_starts) != ""){
		$pro_starts = get_valueLang(thai_date(date('Y-m-d',strtotime($pro_starts))),eng_date_indent(date('Y-m-d',strtotime($pro_starts))));
	}
	if(get_valueNullToNull($pro_end) != ""){
		$pro_end = get_valueLang(thai_date(date('Y-m-d',strtotime($pro_end))),eng_date_indent(date('Y-m-d',strtotime($pro_end))));
	}
	////	convert text date
	if($pro_starts != '' && $pro_end != ''){
		$text = $beginto." ".$pro_starts." - ".$pro_end;
	}else if($pro_starts !='' && $pro_end == ''){
		$text = $beginto." ".$pro_starts." ".$beginend;
	}else if($pro_starts == '' && $pro_end != ''){
		$text = $end." - ".$pro_end;
	}else{
		$text = $now;
	}

	if($text){
		$result = $text;
	}else{
		$result = "";
	}
	
	return $result;
}

function thai_date_indent($time){					/// [$date = format yyyy-mm-dd]
	// ถ้ามีการเก็บวันที่และเวลาในรูปแบบ timestamp
	// สามารถนำมาใช้งานในฟังก์ชันได้ดังนี้
	// $eng=1224562026; กรณีเป็น timestamp
	//$eng_date=time(); // แสดงวันที่ปัจจุบัน
	//echo thai_date($eng_date);
	
	// ถ้ามีการเก็บวันที่และเวลาในรูปแบบ date
	// คือ 2008-10-31 ต้องเปลี่ยนเป็น timestamp
	// ด้วยคำส่ง strtotime("2008-10-31");
	// แสดงวันที่ 31 เดือน ตุลาคม ปี 2008 แบบภาษาไทย
	//$eng_date=strtotime("2008-10-31"); 
	//echo thai_date($eng_date);
	
	$thai_day_arr=array("อา","จ","อ","พ","พฤ","ศ","ส");
	$thai_month_arr=array(
		"00"=>"",
		"01"=>"ม.ค",
		"02"=>"ก.พ",
		"03"=>"มี.ค",
		"04"=>"เม.ย",
		"05"=>"พ.ค",
		"06"=>"มิ.ย", 
		"07"=>"ก.ค",
		"08"=>"ส.ค",
		"09"=>"ก.ย",
		"10"=>"ต.ค",
		"11"=>"พ.ย",
		"12"=>"ธ.ค"                 
	);
	$time_day = date("w",$time);
	$time_month = date("m",strtotime($time));
	/*$thai_date_return="วัน".$this->thai_day_arr[$time_day];
	$thai_date_return.= "ที่ ".date("j",$time);
	$thai_date_return.=" เดือน".$this->thai_month_arr[$time_month];
	$thai_date_return.= " พ.ศ.".(date("Y",$time)+543);*/
	
	$thai_date_return = date("j",strtotime($time))." ".$thai_month_arr[$time_month]." ".(date("Y",strtotime($time))+543);
	//$thai_date_return.= "  ".date("H:i",$time)." น.";
	return $thai_date_return;
}

function date_indent($time,$icon){					/// [$date = format yyyy-mm-dd]
	$extime = explode("-",$time);
	settype($extime[2],"integer");
	settype($extime[1],"integer");
	$explode_return = $extime[2].$icon.$extime[1].$icon.$extime[0];
	return $explode_return;
}

function eng_date_indent($time){
	// ถ้ามีการเก็บวันที่และเวลาในรูปแบบ timestamp
	// สามารถนำมาใช้งานในฟังก์ชันได้ดังนี้
	// $eng=1224562026; กรณีเป็น timestamp
	//$eng_date=time(); // แสดงวันที่ปัจจุบัน
	//echo thai_date($eng_date);
	
	// ถ้ามีการเก็บวันที่และเวลาในรูปแบบ date
	// คือ 2008-10-31 ต้องเปลี่ยนเป็น timestamp
	// ด้วยคำส่ง strtotime("2008-10-31");
	// แสดงวันที่ 31 เดือน ตุลาคม ปี 2008 แบบภาษาไทย
	//$eng_date=strtotime("2008-10-31"); 
	//echo thai_date($eng_date);
	
	$thai_day_arr = array("Sun","Mon","Tue","Wed","Thi","Fri","Sat");
	$thai_month_arr = array(
		"00"=>"",
		"01"=>"Jan",
		"02"=>"Feb",
		"03"=>"Mar",
		"04"=>"Apr",
		"05"=>"May",
		"06"=>"Jun", 
		"07"=>"Jul",
		"08"=>"Aug",
		"09"=>"Sep",
		"10"=>"Oct",
		"11"=>"Nov",
		"12"=>"Dec"                 
	);
	//$time_day = date("w",$time);
	$time_month = date("m",strtotime($time));
	
	$thai_date_return = date("j",strtotime($time))." ".date("M",strtotime($time))." ".(date("Y",strtotime($time)));
	return $thai_date_return;
}

function get_Franshine($id) {
	//=	 call database	=//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	//===== setting	=====//
	$wording = "";
	$only = "";
	if($ci->session->userdata('lang') == "th"){
		$wording = "เฉพาะสาขา";
	}else{
		$only = "only";
	}
	//===================//
	//===================//
	$ci->db->select('*');
	$ci->db->from("franshine");
	$ci->db->where("id",$id);
	$q = $ci->db->get();
	$num = $q->num_rows();
	if($num > 0){
		$r = $q->row();
		$franshine = get_valueLang($r->NAME_TH,$r->NAME_US);
		$text = "***".$wording."".$franshine." ".$only;
	}
	$result = $text;
	
	return $result;
}
 
function get_lang($id) {
	
	//=	 call database	=//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
  
	$ci->db->select('*');
	$ci->db->from("lang");
	$ci->db->where("id",$id);
	$q = $ci->db->get();
	$num = $q->num_rows();
	 
	if($num > 0){
		$r = $q->row();
		$text = get_valueLang($r->TH,$r->US);
		$result = $text;
	}else{
		$result = "";
	}
	
	return $result;
}
function get_Number3keyonly($number){
	$countnumorder = strlen($number);
	////////////////////////////
	////	  Agument		////
	$convertorder = $number;
	////////////////////////////
	////		Query		////
	if($countnumorder < 3){
		for($i=$countnumorder;$i<3;$i++){
			$convertorder = "0".$convertorder;
			$countnumorder = strlen($convertorder);
		}
		
	}
	if(strlen($convertorder) > 3){
		$convertorder = substr($convertorder,1);
	}
	$result = $convertorder;
	////////////////////////////
	////		Result		////
	return $result;
}
function randText($range){
	$char = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ123456789';	////	cut o
	$start = rand(1,(strlen($char)-$range));
	$shuffled = str_shuffle($char);
	return substr($shuffled,$start,$range);
}

function countorder(){
 //=  call database =//
 $ci =& get_instance();
 $ci->load->database();
 //===================//
 $ci->db->select('*');
 $ci->db->from('retail_bill');
 $ci->db->where('retail_bill.STATUS_COMPLETE in (0,1,5)');
 $ci->db->where('retail_bill.STATUS',1);
 $Query  = $ci->db->get();
  $num = $Query->num_rows($Query);
 return $num;
}

function countCreditnote(){
 //=  call database =//
 $ci =& get_instance();
 $ci->load->database();
 //===================//
 $ci->db->select('ID');
 $ci->db->from('retail_creditnote');
 $ci->db->where('retail_creditnote.COMPLETE in(0)');
 $ci->db->where('retail_creditnote.STATUS',1);
 $Query  = $ci->db->get();
  $num = $Query->num_rows($Query);
 return $num;
}

function countorderclaim(){
	//=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	$ci->db->select('*');
	$ci->db->from('retail_claim');
	$ci->db->where('retail_claim.STATUS_CLAIM', 2); 
	$ci->db->where('retail_claim.STATUS_CLAIMCOMPLETE != 1'); 
	$Query  = $ci->db->get();
 	$num = $Query->num_rows($Query);
	return $num;
}

function deletefile($id, $tbl,  $locationflie){
	//=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$ci->db->where('id', $id);
	$ci->db->delete($tbl);

	$data = array(
		'error_code' => 0,
		'txt' => '' 
	);
	$data = json_encode($data);
 	return $data;
}

function convertNumberToText($number){ 
	$txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ'); 
	$txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'); 
	$number = str_replace(",","",$number); 
	$number = str_replace(" ","",$number); 
	$number = str_replace("บาท","",$number); 
	$number = explode(".",$number); 
	if(sizeof($number)>2){ 
		return 'ทศนิยมหลายตัวนะจ๊ะ'; 
		exit; 
	} 
	$strlen = strlen($number[0]); 
	$convert = ''; 
	for($i=0;$i<$strlen;$i++){ 
		$n = substr($number[0], $i,1); 
		if($n!=0){ 
			if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; } 
			elseif($i==($strlen-2) AND $n==2){  $convert .= 'ยี่'; } 
			elseif($i==($strlen-2) AND $n==1){ $convert .= ''; } 
			else{ $convert .= $txtnum1[$n]; } 
			$convert .= $txtnum2[$strlen-$i-1]; 
		} 
	} 

	$convert .= 'บาท'; 
	if($number[1]=='0' OR $number[1]=='00' OR 
		$number[1]==''){ 
		$convert .= 'ถ้วน'; 
	}else{ 
		$strlen = strlen($number[1]); 
		for($i=0;$i<$strlen;$i++){ 
		$n = substr($number[1], $i,1); 
			if($n!=0){ 
			if($i==($strlen-1) AND $n==1){$convert 
			.= 'เอ็ด';} 
			elseif($i==($strlen-2) AND 
			$n==2){$convert .= 'ยี่';} 
			elseif($i==($strlen-2) AND 
			$n==1){$convert .= '';} 
			else{ $convert .= $txtnum1[$n];} 
			$convert .= $txtnum2[$strlen-$i-1]; 
			} 
		} 
		$convert .= 'สตางค์'; 
	} 
	return $convert; 
}
function wordText($value){ 
	if($value != '' || $value != NULL){
		$str = preg_replace('/[^a-z0-9ก-๙เแ\_\-\. ]/i','',$value);
		$r = preg_replace('/[[:space:]]+/', ' ', trim($str));
	}else{
		$r = $value;
	}
   
	if(empty($value)){
		$r = $value;
	}
	return $r;
}

function _toInt($string) {
	return intval(preg_replace('/[^\d\.-]/', '', $string));
}

function _toFloat($string) {
	return floatval(preg_replace('/[^\d\.-]/', '', $string));
}

//	find document creditnote
function find_Creditnote($id) {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,NET_TOTAL,LOSS,DATE_STARTS')
	->from('retail_creditnote')
	->where('rt_id',$id)
	->where('complete in(1,2)')	//	1 = aprrove , 2 = success
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	
	$q = $sql_cn->get();
	
	$result = array(
		'query'	=> $q,
		'num'	=> $num
	);

	return $result;
}

//	find document receive
function find_Receive($id) {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,DATE_STARTS')
	->from('retail_receive')
	->where('rt_id',$id)
	->where('complete in(2)')	//	1 = aprrove , 2 = success
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	
	$q = $sql_cn->get();
	
	$result = array(
		'query'	=> $q,
		'num'	=> $num
	);

	return $result;
}

//	find document bill new
function find_Billnew($id) {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,NET_TOTAL,BILLSTATUS,DATE_STARTS')
	->from('retail_bill')
	->where('billref_id',$id)
	->where('status_complete in(2)')	//	2 = success
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	
	$q = $sql_cn->get();
	
	$result = array(
		'query'	=> $q,
		'num'	=> $num
	);

	return $result;
}

//	find document bill new
function countBillreceive() {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,DATE_STARTS')
	->from('retail_receive')
	->where('complete in(0,1)')	//	1 = aprrove , 2 = success
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	$q = $sql_cn->get();
	

	return $num;
}

//	find document bill receive
function countNote() {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,DATE_STARTS')
	->from('retail_note')
	->where('complete in(0,1)')	//	1 = aprrove , 2 = success
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	$q = $sql_cn->get();
	

	$result = array(
		'query'	=> $q,
		'num'	=> $num
	);

	return $result;
}

//	find document bill supplier
function countSupplier() {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,DATE_STARTS')
	->from('retail_supplier')
	->where('complete in(0,1)')	//	1 = aprrove , 2 = success
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	$q = $sql_cn->get();
	

	$result = array(
		'query'	=> $q,
		'num'	=> $num
	);

	return $result;
}

//	find document bill issue
function countIssue() {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,DATE_STARTS')
	->from('retail_issue')
	->where('complete in(0,1)')	//	1 = aprrove , 2 = success
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	$q = $sql_cn->get();
	

	$result = array(
		'query'	=> $q,
		'num'	=> $num
	);

	return $result;
}

//	find document bill creditnote
function countBillCreditnote_search($id) {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,DATE_STARTS')
	->from('retail_creditnote')
	->where('complete in(0,1,2)')	//	1 = aprrove , 2 = success
	->where('rt_id',$id)
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	$q = $sql_cn->get();
	
	return $num;
}

//	find document bill creditnote
function countBillreceive_search($id) {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,DATE_STARTS')
	->from('retail_receive')
	->where('complete in(0,1,2)')	//	1 = aprrove , 2 = success
	->where('rt_id',$id)
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	$q = $sql_cn->get();
	
	return $num;
}

//	check status for function document other
function successBill($id) {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$sql_cn = $ci->db->select('ID,CODE,DATE_STARTS')
	->from('retail_bill')
	->where('status_complete',2)
	->where('id',$id)
	->where('status',1);
	$num = $sql_cn->count_all_results(null,false);
	$q = $sql_cn->get();
	
	return $num;
}

//	status retail issue
function status_issue($id) {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	switch($id){
		case 1 :
			$result = 'ยืม,รีโพเสส';
		break;
		case 2 :
			$result = 'สูญเสีย';
		break;
		case 3 :
			$result = 'แปลงสินค้า';
		break;
		case 4 :
			$result = 'คืนสินค้า';
		break;
		case 5 :
			$result = 'รับเข้าผิด';
		break;
	}
	

	return $result;
}

//	status retail complete
function status_complete($complete) {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	switch($complete){
		case 0 :
			$result = 'รอ';
		break;
		case 1 :
			$result = 'รอ';
		break;
		case 2 :
			$result = '<span class="text-success">สำเร็จ</span>';
		break;
		case 3 :
			$result = '<span class="text-danger">ยกเลิก</span>';
		break;
	}
	

	return $result;
}

//	count menu
function get_countMenu() {
	 //=  call database =//
	$ci =& get_instance();
	$ci->load->database();
	//===================//
	
	$bill = countorder();
	$receive = countBillreceive();
	$creditnote = countCreditnote();
	$supplier = countSupplier();
	$issue = countIssue();
	$countnote = countNote();
	
	$result = array(
		'bill'		=> $bill,
		'receive'		=> $receive,
		'creditnote'	=> $creditnote,
		'supplier'		=> $supplier['num'],
		'issue'		=> $issue['num'],
		'note'		=> $countnote['num']
	);

	return $result;
}

?>