<?php
// // include composer autoload
// require 'vendor/autoload.php';

// import the Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;

class Upload extends CI_Controller {
	
	public function index ()
	{
		return $this->load->view('upload');
	}

	public function upload_file () 
	{
		ini_set('upload_max_filesize',-1);
		ini_set('post_max_size',-1);
		//*
		//*		setting
		$e = 0;							//	set error value default 0
		$arr_return = array();			//	for return array complete	
		$countarray = count($_FILES);
		$file_inputname = [];
		$number = 0;
		// echo '<pre>'; print_r($_FILES); 
		// foreach($_FILES as $key => $val){
		// 	echo "-->> ".$key;
		// }

		// exit;
		// for($i=1;$i<=$countarray;$i++){
		foreach($_FILES as $key => $val){
			$file_inputname[$number++] = $key;
			if($e != 1){
				//	for first loop $i will 0
				// if($i==1){
				// 	$index = "";
				// }else{
				// 	$index  = $i;
				// }
				$locationfile = $this->input->post('locationflie');
				$upload_path =  FCPATH. "asset/images/front/".$locationfile;
				$new_name = date('Ymd')."_".$_FILES["".$key.""]['name'];

				if(!file_exists($upload_path)) mkdir($upload_path);
				if(!$_FILES) redirect(base_url('upload'));
				$dataupload = array(
						'upload_path'		=> './image/',
						'max_size'			=> 4000,				///	kigabyte
						'allowed_types'		=> 'jpg|jpeg|png|gif',
						'encrypt_name'		=> TRUE,				///	rename set code
						'file_name'			=> $new_name
				);
				$this->load->library('upload',$dataupload);
				$this->upload->initialize($dataupload);
				
				// echo $key." ============== ";
				if($this->upload->do_upload("".$key."") && $e == 0) {

					$uploadimage = $this->upload->data();
					//
					//	** width x height creat image name
					//
					//	resize image 150
					$data_resize_150 = array(
						'image_library'		=> 'gd2',
						'source_image'		=> $uploadimage['full_path'],
						'new_image'			=> $upload_path."/thumb_x150_".$uploadimage['file_name'],	
						'create_thumb'		=> FALSE,
						'maintain_ratio'	=> TRUE,													///	rename set code
						'width'				=> '',
						'height'			=> 150
					);
					$this->load->library('image_lib',$data_resize_150);
					$this->image_lib->initialize($data_resize_150); 
					if (!$this->image_lib->resize() && $e == 0)
					{
						// return $this->load->view('upload',[
						// 	'error'		=> $this->image_lib->display_errors()
						// ]);
						$e = 1;
						$data = array(
							'error'		=> 5,
							'data'		=> $this->image_lib->display_errors()
						);
					}
					//
					//	** if wont to function crop use input 
					//	** <input name="crop" type="hidden" value="1">
					//	crop image
					if(($this->input->post('crop') == 1 || $this->input->get('crop') == 1) && $e == 0){
						$image_width = $uploadimage['image_width'];
						$image_height = $uploadimage['image_height'];

						//Set cropping for y or x axis, depending on image orientation
						$config['image_library'] = 'gd2';
						$config['source_image'] = $uploadimage['full_path'];
						$config['new_image'] = $upload_path."/temp_".$uploadimage['file_name'];
						$config['maintain_ratio'] = FALSE;
						
						if ($image_width > $image_height) {
							$config['width'] = $image_height;
							$config['height'] = $image_height;
							$config['x_axis'] = (($image_width / 2) - ($config['width'] / 2));
						} else {
							$config['height'] = $image_width;
							$config['width'] = $image_width;
							$config['y_axis'] = (($image_height / 2) - ($config['height'] / 2));
						}
						$this->image_lib->clear();
						$this->load->library('image_lib',$config);
						$this->image_lib->initialize($config); 
						if (!$this->image_lib->crop() && $e == 0){
							// return $this->load->view('upload',[
							// 	'error'		=> $this->image_lib->display_errors()
							// ]);
							$e = 1;
							$data = array(
								'error'		=> 4,
								'data'		=> $this->image_lib->display_errors()
							);
						}
						
						//	resize image 200
						$data_resize_200 = array(
							'image_library'		=> 'gd2',
							'source_image'		=> $upload_path."/temp_".$uploadimage['file_name'],
							'new_image'			=> $upload_path."/thumb_200_".$uploadimage['file_name'],	
							'create_thumb'		=> FALSE,
							'maintain_ratio'	=> TRUE,													///	rename set code
							'width'				=> 200,
							'height'			=> 200
						);
						$this->image_lib->clear();
						$this->image_lib->initialize($data_resize_200); 
						if (!$this->image_lib->resize() && $e == 0)
						{
							// return $this->load->view('upload',[
							// 	'error'		=> $this->image_lib->display_errors()
							// ]);
							$e = 1;
							$data = array(
								'error'		=> 3,
								'data'		=> $this->image_lib->display_errors()
							);
						}

						unlink($config['new_image']);
					}	//	end crop image
					//
					//
					//	resize image 1920
					//*	if image for desktop
					if(($uploadimage['image_width'] > 1920 &&  $uploadimage['image_width'] > $uploadimage['image_height']) && $e == 0){
						$data_resize_1920 = array(
							'image_library'		=> 'gd2',
							'source_image'		=> $uploadimage['full_path'],
							'new_image'			=> $upload_path."/".$uploadimage['file_name'],	
							'create_thumb'		=> FALSE,
							'maintain_ratio'	=> TRUE,													///	rename set code
							'width'				=> 1920,
							'height'			=> ''
						);
						$this->image_lib->clear();
						$this->load->library('image_lib',$data_resize_1920);
						$this->image_lib->initialize($data_resize_1920); 
						if (!$this->image_lib->resize() && $e == 0)
						{
							/* return $this->load->view('upload',[
								'error'		=> $this->image_lib->display_errors()
							]); */
							$e = 1;
							$data = array(
								'error'		=> 2,
								'data'		=> $this->image_lib->display_errors()
							);
						}
					}

					/* return $this->load->view('upload',[
						'data'			=> $uploadimage
					]); */
					if($e == 0){
						array_push($arr_return,$uploadimage);
						$data = array(
							'error'		=> 0,
							'count'		=> $countarray,
							'image_filename' => $file_inputname,
							'data'		=> $arr_return
						);
					}
				}else{
					// error file
					/* return $this->load->view('upload',[
						'error'			=> $this->upload->display_errors()
					]); */
					$data = array(
						'error'		=> 1,
						'data'		=> $this->upload->display_errors()
					);
				}
				
			}else{
				$data = json_encode($data);
				echo $data;
				exit();
			}		//	if for check error before loop
		}		
		//  echo '<pre>'; print_r($data); exit;
		$data = json_encode($data);
		echo $data;
	}

	//
	//todo	image and watermark
	public function add_imagestest()
	{
		// ini_set('max_execution_time', '0');
		ini_set('upload_max_filesize',-1);
		ini_set('post_max_size',-1);
		//*
		//*		setting
		$thumb_square_size = 200; 				//Thumbnails will be cropped to 200x200 pixels
		$max_image_size = 1920; 				//Maximum image size (height and width)
		$thumb_prefix = "thumb_"; 				//Normal thumb Prefix
		$number = date('Ymd');
		$destination_folder = FCPATH. "asset/images/front/testimage/";	

		//*
		//! check directory
		if(!file_exists($destination_folder)) mkdir($destination_folder);
		if(!$_FILES) redirect(base_url('upload'));
		//!
		$image_temp = $_FILES['image_file']['tmp_name']; 								//file name
		$image_name = $_FILES['image_file']['name']; 								//file name
		$image_info = pathinfo($image_name);
		$image_extension = strtolower($image_info["extension"]); 					//image extension
		$image_name_only = strtolower($image_info["filename"]); 					//file name only, no extension
		$new_file_name = $image_name_only ."_". $number .'.'.$image_extension;		//new image name
		$image_save_folder = $destination_folder . $new_file_name;

		Image::configure(array('driver' => 'gd'));
		// open an image file
		$img = Image::make($image_temp);

		// echo $img->response();
		// now you are able to resize the instance
		// $img->resize(320,200);

		// and insert a watermark for example
		// $img->insert('public/watermark.png');

		// resize the image to a width of 300 and constrain aspect ratio (auto height)
		$img->resize($max_image_size, null, function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		});

		// save the same file as jpg with default quality
		$img->save($image_save_folder);
		$img->destroy();
		// save file as jpg with medium quality
		// $img->save($image_save_folder, 85);
	}

	//
	//todo	image and crop original
	public function add_images()
	{	
		ini_set("upload_max_filesize","1000M");
		$thumb_square_size = 200; //Thumbnails will be cropped to 200x200 pixels
		$max_image_size = 1920; //Maximum image size (height and width)
		$thumb_prefix = "thumb_"; //Normal thumb Prefix
		$number = date('Ymd');

		$destination_folder = FCPATH. "asset/images/front/testimage/";	
		$jpeg_quality = 82; //jpeg quality
		$image_size = $_FILES['image_file']['size']; //file size
		echo $destination_folder."=====";
		if (isset($_POST) == 'xmlhttprequest') {	
			// check $_FILES['ImageFile'] not empty
			if (!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				die();
			}
			// echo "<br>=====";
			// echo "<pre>";print_r($_FILES['image_file']);
			//
			//* uploaded file info we need to proceed
			$image_name = $_FILES['image_file']['name']; //file name
			$image_size = $_FILES['image_file']['size']; //file size
			$image_temp = $_FILES['image_file']['tmp_name']; //file temp
		
			$image_size_info = getimagesize($image_temp); //get image size
		
			if ($image_size_info) {
				$image_width = $image_size_info[0]; //image width
				$image_height = $image_size_info[1]; //image height
				$image_type = $image_size_info['mime']; //image type
			} else {
				die("Make sure image file is valid!");
			}
		
			//* switch statement below checks allowed image type 
			//* as well as creates new image from given file 
			switch ($image_type) {
				case 'image/png':
					$image_res = imagecreatefrompng($image_temp);
					break;
				case 'image/gif':
					$image_res = imagecreatefromgif($image_temp);
					break;
				case 'image/jpeg': case 'image/pjpeg':
					$image_res = imagecreatefromjpeg($image_temp);
					break;
				default:
					$image_res = false;
			}
		
			if ($image_res) {
				//Get file extension and name to construct new file name 
				$image_info = pathinfo($image_name);
				$image_extension = strtolower($image_info["extension"]); //image extension
				$image_name_only = strtolower($image_info["filename"]); //file name only, no extension
		
				//create a random name for new image (Eg: fileName_293749.jpg) ;
				$new_file_name = $image_name_only ."_". $number .'.'.$image_extension;
				
				//folder path to save resized images and thumbnails
				$thumb_save_folder = $destination_folder . $thumb_prefix . $new_file_name;
				$image_save_folder = $destination_folder . $new_file_name;
		
				//call normal_resize_image() function to proportionally resize image
				if ($this->normal_resize_image($image_res, $image_save_folder, $image_type, $max_image_size, $image_width, $image_height, $jpeg_quality)) {
					//call crop_image_square() function to create square thumbnails
					if (!$this->crop_image_square($image_res, $thumb_save_folder, $image_type, $thumb_square_size, $image_width, $image_height, $jpeg_quality)) {
						die('Error Creating thumbnail');
					}
				}
		
				imagedestroy($image_res); //freeup memory
			}
		}
	}

	public function normal_resize_image($source, $destination, $image_type, $max_size, $image_width, $image_height, $quality) {
		if ($image_width <= 0 || $image_height <= 0) {
			return false;
		} //return false if nothing to resize
		//do not resize if image is smaller than max size
		if ($image_width <= $max_size && $image_height <= $max_size) {
		   if ($this->save_image($source, $destination, $image_type, $quality)) {
				return true;
			}
		}
		//Construct a proportional size of new image
		$image_scale = min($max_size / $image_width, $max_size / $image_height);
		$new_width = ceil($image_scale * $image_width);
		$new_height = ceil($image_scale * $image_height);
		
		$new_canvas = imagecreatetruecolor($new_width, $new_height); //Create a new true color image
	
		//Copy and resize part of an image with resampling
		if (imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)) {
			$this->save_image($new_canvas, $destination, $image_type, $quality); //save resized image
		}
	
		return true;
	}

	public function crop_image_square($source, $destination, $image_type, $square_size, $image_width, $image_height, $quality) {
		if ($image_width <= 0 || $image_height <= 0) {
			return false;
		} //return false if nothing to resize
	
		if ($image_width > $image_height) {
			$y_offset = 0;
			$x_offset = ($image_width - $image_height) / 2;
			$s_size = $image_width - ($x_offset * 2);
		} else {
			$x_offset = 0;
			$y_offset = ($image_height - $image_width) / 2;
			$s_size = $image_height - ($y_offset * 2);
		}
		$new_canvas = imagecreatetruecolor($square_size, $square_size); //Create a new true color image
		//Copy and resize part of an image with resampling
		if (imagecopyresampled($new_canvas, $source, 0, 0, $x_offset, $y_offset, $square_size, $square_size, $s_size, $s_size)) {
			$this->save_image($new_canvas, $destination, $image_type, $quality);	
		}
	
		return true;
	}

	##### Saves image resource to file ##### 
	public function save_image($source, $destination, $image_type, $quality) {
		// configure with favored image driver (gd by default)
		/* Image::configure(array('driver' => 'imagick')); 
		Image::make($_FILES['image']['tmp_name']);

		$iMagick = new Imagick($file);
		$iMagick->setImageResolution(72,72);
		$iMagick->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);
		$geometry = $iMagick->getImageGeometry();
		if ($geometry['height'] > 1920 || $geometry['width'] > 1080) {
			$iMagick->scaleImage(1920, 0);
			if($geometry['height'] > $resizeHeight) {
				$iMagick->scaleImage(0, 1080);
			}
		}
		$iMagick->setImageCompression(Imagick::COMPRESSION_JPEG);
		$iMagick->setImageCompressionQuality($compression);
		$iMagick->setImageFormat("jpg");
		$iMagick->stripImage();
		$iMagick->writeImage($file);
		$Imagick->clear(); */

		switch (strtolower($image_type)) {//determine mime type
			case 'image/png':
				imagepng($source, $destination);
				return true; //save png file
				break;
			case 'image/gif':
				imagegif($source, $destination);
				return true; //save gif file
				break;
			case 'image/jpeg': case 'image/pjpeg':
				imagejpeg($source, $destination);
				return true; //save jpeg file
				break;
			default: return false;
		}
	}

}