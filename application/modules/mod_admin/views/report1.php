<?php
	ini_set('max_execution_time',0);
	//set_time_limit(0);
	ini_set('memory_limit',-1);
	ini_set('upload_max_filesize',-1);
	 
	$get_print = $this->input->get('print');
	if(!empty($get_print)){
		header("Content-Type: application/vnd.ms-excel"); // ประเภทของไฟล์
		header('Content-Disposition: attachment; filename="reportlog.xls"'); //กำหนดชื่อไฟล์
		header("Content-Type: application/force-download"); // กำหนดให้ถ้าเปิดหน้านี้ให้ดาวน์โหลดไฟล์
		header("Content-Type: application/octet-stream"); 
		header("Content-Type: application/download"); // กำหนดให้ถ้าเปิดหน้านี้ให้ดาวน์โหลดไฟล์
		header("Content-Transfer-Encoding: binary" ); 
	}
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report Promotion</title>
    <link href="https://fonts.googleapis.com/css?family=Sarabun&display=swap" rel="stylesheet">
<style>
    body { font-family: sarabun; font-size: 16px;}     
    table {
        border: 1px solid #333;
        border-collapse: collapse;
        width: 100%;
    }

    td {
        border: 1px solid #333;
        /* text-align: left; */
        padding: 0.4%;
    }
    th {
        border: 1px solid #333;
         text-align: left;
        padding: 0.4%;
    }
 
</style>  
</head>
<body>
 
<div id="container">
        
    <div id="body">
        <div class="row">

            <table class="table" style="margin-top: 2rem;">
                <tbody>
                    <tr>
                        <th colspan="3" class="text-left"> ลำดับโปรโมชั่นที่ลูกค้ากดใช้คะแนน (Rangking) </th>
                        <th class="text-left"> <a href="<?php echo site_url('mod_admin/ctl_admin/report1?valdate='.$this->input->get('valdate').'&valdateTo='.$this->input->get('valdateTo').'&print=1'); ?>"> ออกรายงาน </a>  </th>
                    </tr>
                    <tr style="background-color: #f0f0f0; ">
						<th class="text-center">ลำดับ</th>
						<th class="text-center">รายการ</th>
						<th class="text-center">รวมคะแนน</th>
						<th class="text-center">จำนวนรายการ/ครั้ง</th>
					</tr>
                    <?php $index= 1; foreach($report_pro AS $row){ ?>
                        <tr >
                            <td class="text-center"> <?php echo $index++; ?> </td>
                            <td class="text-left"> <?php echo $row->Proname; ?> </td>
                            <td class="text-center"> <?php echo $row->Cpoint; ?> </td>
                            <td class="text-center"> <?php echo $row->count; ?> </td>
                        </tr>
                    <?php } ?>
                </tbody>  
            </table>   
        </div>
    </div>
 
</div>
 
</body>
</html>
 