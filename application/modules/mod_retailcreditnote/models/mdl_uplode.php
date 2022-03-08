<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_uplode extends CI_Model {
    
	function checkFile() {
		 
		if($_FILES['image_file'] != null || $_FILES['image_file2'] != null){
			// echo "-->> ".$_FILES['image_file']['name']." ------------------- ".$_FILES['image_file2']['name']; exit;
			if($_FILES['image_file'] != null){
				if($_FILES['image_file']['type'] == 'image/jpeg' || $_FILES['image_file']['type'] == 'image/png'){
					$size=getimagesize($_FILES['image_file']['tmp_name']);  // $file คือ ไฟล์ที่เราต้องการดูขนาด
					$img_w=$size[0];   // ขนาดความกว้าง

					$img_h=$size[1];   // ขนาดความสูง
					
					if($_FILES['image_file']['size'] > 1000000){		// 1 k = 1000
						$error 	= 1;
						$txt	= "size overload";
						$txt_name	= "";
						$txt_exten	= "";
					}else{
						$image_name = $_FILES['image_file']['name'];
						$image_info = pathinfo($image_name);
						$image_extension = strtolower($image_info["extension"]); //image extension
						$image_name_only = strtolower($image_info["filename"]); //file name only, no extension

						$error 	= 0;
						$txt	= "";
						$txt_name	= $image_name_only;
						$txt_exten	= $image_extension;

						$txt_namelogo	= '';
						$txt_extenlogo	= '';
						// $countmaxlogo = '';
						
					}
					
				}else{
					$error 	= 1;
					$txt	= "JPG and PNG Onlys";
					$txt_name	= "";
					$txt_exten	= "";
				}
			}  
			if($_FILES['image_file2'] != null){
				if($_FILES['image_file2']['type'] == 'image/jpeg' || $_FILES['image_file2']['type'] == 'image/png'){
					$size=getimagesize($_FILES['image_file2']['tmp_name']);  // $file คือ ไฟล์ที่เราต้องการดูขนาด
					$img_w=$size[0];   // ขนาดความกว้าง
			
					$img_h=$size[1];   // ขนาดความสูง
					if($_FILES['image_file2']['size'] > $this->set['max_upload_image']){
						$error 	= 1;
						$txt	= "size overload";
						$txt_name	= "";
						$txt_exten	= "";
					}else{
						$image_name = $_FILES['image_file2']['name'];
						$image_info = pathinfo($image_name);
						$image_extensionlogo = strtolower($image_info["extension"]); //image extension
						$image_name_onlylogo = strtolower($image_info["filename"]); //file name only, no extension
			
						$error 	= 0;
						$txt	= "";
						$txt_namelogo	= $image_name_onlylogo;
						$txt_extenlogo	= $image_extensionlogo;

					}
				}
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
				"txt_name2" 		=> $txt_namelogo,
				"txt_exten2" 		=> $txt_extenlogo
		);

		$data = json_encode($data);
		return $data;
	}
	 
}
?>