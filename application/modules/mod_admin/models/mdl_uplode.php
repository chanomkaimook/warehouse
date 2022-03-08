<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_uplode extends CI_Model {
    
	function checkFile() {
		// if($this->input->post('mode') == 'board'){
		// 	$path = "asset/images/front/board";
		// }else{
		// 	$path = "";
		// }
		if($_FILES['image_file'] != null){
			if($_FILES['image_file']['type'] == 'image/jpeg' || $_FILES['image_file']['type'] == 'image/png'){
				$size=getimagesize($_FILES['image_file']['tmp_name']);  // $file คือ ไฟล์ที่เราต้องการดูขนาด
				$img_w=$size[0];   // ขนาดความกว้าง

				$img_h=$size[1];   // ขนาดความสูง
				if($img_w > 1920 || $img_h > 1920){
					$error 	= 1;
					$txt	= "setting in 1920px";
					$txt_name	= "";
					$txt_exten	= "";
				}else if($_FILES['image_file']['size'] > $this->set['max_upload_image']){
					$error 	= 1;
					$txt	= "size overload";
					$txt_name	= "";
					$txt_exten	= "";
				}else{
					$image_name = $_FILES['image_file']['name'];
					$image_info = pathinfo($image_name);
					$image_extension = strtolower($image_info["extension"]); //image extension
					$image_name_only = strtolower($image_info["filename"]); //file name only, no extension

					$countmax = $this->mdl_sql->get_MAX('promotion', 'status', 1, 'id');
					// echo $this->db->last_query(); /// โชว์ Query
					 
					$error 	= 0;
					$txt	= "";
					$txt_name	= $image_name_only;
					$txt_exten	= $image_extension;
					$countmax = ($countmax->countmax) + 1;
				}
				
			}else{
				$error 	= 1;
				$txt	= "JPG and PNG Onlys";
				$txt_name	= "";
				$txt_exten	= "";
			}
		}else{	////	file image false
				$error 	= 3;
				$txt	= "";
				$txt_name	= "";
				$txt_exten	= "";
		}
		$data = array(
				"error_code" 		=> $error ,
				"txt" 				=> $txt ,
				"txt_name" 			=> $txt_name,
				"txt_exten" 		=> $txt_exten,
				"countmax"			=> $countmax
		);
		
		$data = json_encode($data);
		return $data;
	}
	 
}
?>