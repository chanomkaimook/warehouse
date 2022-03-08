<?php
	ini_set('max_execution_time',0);
	//set_time_limit(0);
	ini_set('memory_limit',-1);
	ini_set('upload_max_filesize',-1);
	 
	$get_print = $this->input->get('print');
	if(!empty($get_print)){
		header("Content-Type: application/vnd.ms-excel"); // ประเภทของไฟล์
		header('Content-Disposition: attachment; filename="OrderRetail.xls"'); //กำหนดชื่อไฟล์
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
    <title>Report Retail</title>
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
    .border-002 {
        border: 1px solid #fff;
    }
</style>  
</head>
<body>
 
<div id="container">
        
    <div id="body">
        <div class="row">
            <section class="content">
				<div class="container-fluid">
					<div class="row">
						  
						<section class="col-lg-12 connectedSortable">
							<!-- Custom tabs (Charts with tabs)-->
							<div class="card">
								<div class="card-header">
									<h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i>   </h3>
 								</div> 
								<div class="card-body">
                                    <form id="demo2" name="demo2" class="demo"  enctype="multipart/form-data" accept-charset="utf-8"  method="post">
                                             
                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <table class="table table-bordered border-002">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border-002" colspan="5"> 
                                                                    <?php if($Query_billdetil['REMARKORDER'] != ''){ ?>
                                                                        <b> คำอธิบายเพิ่มเติม : </b> <?php echo $Query_billdetil['REMARKORDER']; ?>
                                                                    <?php } ?>
                                                                    <br>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <table class="table table-bordered border-002">
                                                        <tbody>
 
                                                            <tr>
                                                                <td class="border-002" colspan="2"> <b>ขออนุมัติออเดอร์เจอร์กี้จัดส่งไปรษณีย์</b> </td>
                                                                <td class="border-002" colspan="3"> <b>วันที่ : </b><span> <?php echo $Query_billdetil['DATE_STARTS']; ?></span></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border-002" colspan="2">  <b>ออเดอร์ที่ : </b><span> <?php echo $Query_billdetil['CODE']; ?> </span>  </td>
                                                                <td class="border-002" colspan="3"> <b>รูปแบบการจัดส่ง : </b><span> <?php echo $Query_billdetil['DELIVERYFORMID']; ?> </span> </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border-002" colspan="5"> <b> ช่องทางการรับออเดอร์ : </b> <?php echo $Query_billdetil['METHODORDER_TOPIC']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border-002" colspan="5"> <b>ชื่อ-นามสกุล : </b> <?php echo $Query_billdetil['NAME']; ?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border-002" colspan="5"> <b>เบอร์โทรศัพท์ : </b> <?php echo $Query_billdetil['PHONENUMBER']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border-002" colspan="5"> <b>ที่อยู่ : </b> <?php echo $Query_billdetil['ADDRESS']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border-002" colspan="5"> <b>เลขที่เสียภาษี/เลขที่บัตรประชาชน : </b> <?php echo $Query_billdetil['TEXTNUMBER']; ?></td>
                                                            </tr>
                                                          
                                                        </tbody>
                                                    </table>
                                                </div>
  
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php if($Query_billdetil['BILLSTATUS'] == 0){ ?>
                                                        <div>
                                                            <img src="<?php echo $basepic.'front/retail/icon/main-img1x.png'; ?>" class="cancel-img">
                                                        </div>
                                                    <?php } ?>
                                                   
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id='table-bill'>
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 5%;text-align: center;">ลำดับ</th>
                                                                    <th style="text-align: center;">รายการออเดอร์</th>
                                                                    <th style="width: 10%;text-align: center;">ราคา/บาท</th>
                                                                    <th style="width: 10%;text-align: center;">จำนวน/หน่วย</th>
                                                                    <th style="width: 10%;text-align: center;">รวมเป็นเงิน/บาท</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ORlist">
                                                                <?php foreach($Query_billdetil['billist'] AS $row1){ ?>
                                                                <tr style="background-color: #d9d9d9;"> 
                                                                    <td colspan="5"> <b> <?php echo $row1['PRONAME_MAIN']; ?> </b> </td>
                                                                </tr>
                                                                <?php $index = 1; 
                                                                        foreach($row1['PRONAME_LIST'] AS $row2){ ?>
                                                                <tr class="each-total">
                                                                    <td style="text-align: center;"> <?php echo $index++; ?> </td>
                                                                    <td style="text-align: left;">  <?php  echo $row2['PRONAME_LIST']; ?> </td>
                                                                    <td style="text-align: right;"> <?php  echo $row2['PRICE']; ?></td>
                                                                    <td style="text-align: right;"> <?php  echo $row2['QUANTITY']; ?></td>
                                                                    <td style="text-align: right;"> <?php  echo $row2['RBD_TOTALPRICE']; ?> </td>
                                                                </tr>
                                                                <?php
                                                                        }
                                                                    } 
                                                                ?>
                                                            </tbody>
                                                            <tbody id="total">
                                                                 
                                                                <tr>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;"> <b>รวมยอดขายสุทธิ</b> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;" id='total-price'><?php echo $Query_billdetil['TOTALPRICE']; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;"> <b>ค่ากล่องพัสดุ</b> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;"><?php echo $Query_billdetil['PARCELCOST']; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;"> <b>ค่าบริการจัดส่ง</b> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;"><?php echo $Query_billdetil['DELIVERYFEE']; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;"> <b>ค่าธรรมเนียม shopee</b> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;"><?php echo $Query_billdetil['SHORMONEY']; ?></td>
                                                                </tr>
                                                                <tr style="background-color: #d9d9d9;">
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> <b>ยอดชำระรวมค่าจัดส่ง</b> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-center" style="padding: .2rem;"> </td>
                                                                    <td class="text-right" style="padding: .2rem;" id='total-cost'><?php echo $Query_billdetil['NETTOTAL']; ?></td>
                                                                </tr>
                                                                 
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                 
                                                <div class="col-md-12">
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            <?php if($Query_billdetil['STATUSCOMPLETE'] == 4){ ?>
                                                                <tr>
                                                                    <td class="border-002" style="text-align: left;" colspan="5">
                                                                        <?php
                                                                            echo '<b>หมายเหตุ : </b><br>'.$Query_billdetil['REMARK'].'<hr>';
                                                                            if($Query_billdetil['REMARKCLAIM'] != ''){
                                                                                echo '<b> อธิบายข้อผิดผลาด : </b><br>'.$Query_billdetil['REMARKCLAIM'].'<hr>';
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            <tr>
                                                                <td class="border-002" style="text-align: center;" colspan="5">
                                                                    <div class="div-bottom text-center">
                                                                        <img class="img-moblie" src="<?php echo $basepic.'front/retail/Bill_Pyment/'.$Query_billdetil['PICPAYMENT']; ?>">
                                                                        <?php if($Query_billdetil['PICPAYMENT2'] != ''){ ?>
                                                                            <img src="<?php echo $basepic.'front/retail/Bill_Pyment/'.$Query_billdetil['PICPAYMENT2']; ?>" class="img-pay001">
                                                                        <?php } ?>
                                                                    </div> 
                                                                    <hr>   
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border-002" style="text-align: center;" colspan="5">
                                                                    <b>ลงชื่อผู้รับออเดอร์</b> ........................................................ <br>
                                                                    <?php echo '('.$Query_billdetil['S_NAME_TH'].'  '.$Query_billdetil['S_LASTNAME_TH'].')'; ?><br>
                                                                    เจ้าหน้าที่ Admin และ Social<br>
                                                                    ........../........../..........
                                                                </td>
                                                                
                                                                <!-- <td class="border-002" style="text-align: center;" colspan="2">
                                                                    <b>ลงชื่อเพื่อทราบ</b><br> ........................................................<br>
                                                                    (นายนพรัตน์ สมึงรัมย์)<br>
                                                                    ผู้จัดการฝ่ายโชคชัยสเต็คเบอร์เกอร์<br>
                                                                    ........../........../..........
                                                                </td> -->
                                                            </tr>
                                                          
                                                        </tbody>
                                                    </table>
                                                </div>
                                                 
                                            </div>
                                             
                                        </form>
								</div> 
							</div>
						</section>
 
					</div>
				</div> 
            </section>
        </div>
    </div>
 
</div>
 
</body>
</html>
 