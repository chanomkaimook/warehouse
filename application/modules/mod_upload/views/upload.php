<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload image</title>
</head>
<body>
	<div style="max-width:500px">
    <form method="POST" enctype="multipart/form-data" action="<?= site_url('mod_upload/upload/upload_file');?>">
		<input type="hidden" name="crop" value="0">
		<input type="hidden" name="locationflie" id="locationflie" value="testimage">
        <input type="file" name="image_file" />
        <input type="submit"/>
    </form>
  <!--   <div><img src="<?php echo site_url("asset/images/front/banner-bg.jpg"); ?>" ></div> -->
    <?php
        if(isset($data['file_name'])) :
    ?>
	<br>
    <img src="<?php echo base_url("asset/images/front/testimage/".$data['file_name'].""); ?>" style="width:100%">
    <p style="float:left"><?php echo "Name : ".$data['file_name']." --- Size : ".$data['file_size']." kb";?></p>
	<br><?php echo "<pre>";print_r($data);?>
    <img src="<?php echo base_url("asset/images/front/testimahumb_x150_".$dge/tata['file_name'].""); ?>" style="max-width:100%">
    <p style="float:left"><?php echo "Name : ".$data['file_name']." --- Size : ".$data['file_size']." kb";?></p>
    <br>
    <img src="<?php echo base_url("asset/images/front/testimage/thumb_200_".$data['file_name'].""); ?>" style="max-width:100%">
    <p style="float:left"><?php echo "Name : thumb_200_".$data['file_name']." kb";?></p>
    <?php
        elseif(isset($error)) :
    ?>
    <p><?php echo $error; ?></p>
    <?php
        endif;
    ?>
	<p style="float:right"><a href="<?php echo site_url('mod_upload/upload');?>">back</a></p>
	</div>
	<div style="clear:both"></div>
    <?php
        // echo phpinfo();
    ?>
</body>
</html>
