
<!DOCTYPE html>
<html lang="en">
   	<head> 
      <?php include("structer/backend/head.php"); ?>
      <style>
        .container {
            /*color:#fff;*/
            color:#FFF;
        }
        #content {
        left: 50%;
        position: absolute;
        top: 45%;
        transform: translate(-50%, -50%);
        width: 90%;
        text-align: center;
        }
        .img-backgroundfull {
        background: url(<?php echo base_url('asset/images/front/background/stonetexture.jpg'); ?>);
        background-size: cover;
        }
        .modal .modal-content {
            font-size:12px;
            background:rgb(255,255,255,1);
        }
        @media screen and (max-width: 767px){
            .modal .modal-content {
                font-size:8px;
            }
            .modal .modal-dialog {
                width:90% !important;
                margin:0px;
            }
        }
        @media screen and (min-width: 768px){
            .modal .modal-content {

            }
        }
    </style>
    </head>
    <?php
        if($this->input->get('paytype') == ""){
            $getpaytype = "cc";
        }else{
            $getpaytype = $this->input->get('paytype');
        }
    ?>
   	<body class="hold-transition sidebar-mini layout-fixed">
       <input type="hidden" id="getpaytype" name="getpaytype" value="<?php echo $getpaytype;?>">
	   <div class="wrapper">
		<?php 
			include('structer/backend/navbar.php');
			include('structer/backend/menu.php'); 
		?>
		    
		<div class="content-wrapper">
 			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
					<div class="col-sm-6">
						<h1><?php echo $mainmenu; ?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo site_url('mod_admin') ?>/ctl_admin/backend_main">Home</a></li>
						<li class="breadcrumb-item active"><?php echo $submenu; ?></li>
						</ol>
					</div>
					</div>
				</div><!-- /.container-fluid -->
			</section>
		  
			<section class="content">
				<div class="container-fluid">
					<div class="row">
						  
						<section class="col-lg-12 connectedSortable">
							<!-- Custom tabs (Charts with tabs)-->
							<div class="card">
								<div class="card-header">
									<h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i> <?php echo "Manage ".$mainmenu; ?> </h3>
 								</div> 
								<div class="card-body">
                                    <form id="demo1" name="demo1" class="demo"  enctype="multipart/form-data" accept-charset="utf-8"  method="post" action="<?php echo site_url('mod_gateway/url/gen_urlpay');?>">
                                        
                                            <div class="titel text-left"> <i class="fa fa-database" aria-hidden="true"></i>  Data Management (insert)</div>
                                            <div class="form-row">
                                               
                                                <div class="form-group col-md-12 text-center"> <b>กรอกข้อมูลชื่อ และ ราคา ให้ครบถ้วน กดปุ่มยืนยันเพื่อรับ url</b> </div>
                                                <label class="form-group col-md-3 text-right" for="username"> ชื่อ / Name : <span style="color: red;">*</span></label>
                                                <div class="form-group col-md-9 ">
                                                    <input type="text" class="form-control " name="username" id="username" placeholder="กรอกข้อมูล">
                                                </div>

                                                <label class="form-group col-md-3 text-right" for="price"> ราคา / Price : <span style="color: red;">*</span></label>
                                                <div class="form-group col-md-9 ">
                                                    <input type="text" class="form-control " name="price" id="price" placeholder="กรอกข้อมูล">
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <label class="form-group col-md-3"> </label>
                                                <div class="col-md-9 ">
                                                    <button type="submit" class="btn btn-default btn-sm" id="Save"><li class="fa fa-floppy-o"> </li> ยืนยัน | submit</button>
                                                    <button type="button" class="btn btn-default btn-sm" id="cancel"><li class="fa fa-window-close-o "> </li> Cancel</button>
                                                </div>
                                            </div>

                                    </form>
                                    <?php
                                        if($this->input->post('username')){
                                            $value = site_url('mod_gateway/gateway')."?url=".$url->CODEURL;
                                    ?>
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="price">url :</label>
                                            <div class="col-sm-10">
                                            <input type="text" class="form-control" id="urlshow" name="urlshow"  value="<?php echo $value;?>" >
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                        }
                                    ?>
								</div> 
							</div>
						</section>
 
					</div>
				</div> 
            </section>
            <!-- Modal -->
            <div class="modal fade" id="myModal" role="dialog" >
                <div class="modal-dialog">
                <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="text-left"> 
                                ข้อตกลงและเงื่อนไขการชำระเงิน
                                ท่านตกลงที่จะปฏิบัติตามแนวปฏิบัติ คำสั่ง ระเบียบการดำเนินงาน นโยบาย และคำแนะนำทั้งหลายเกี่ยวกับการสั่งซื้อสินค้า รวมถึงการแก้ไขเพิ่มเติมใดๆในสิ่งที่กล่าวมาข้างต้นซึ่งออกใช้บังคับโดยทางเรา และถือว่าท่านได้รับทราบ 
                                และยอมรับข้อผูกพันตามการเปลี่ยนแปลงดังกล่าว โดยข้อตกลง และเงื่อนไขการชำระเงินนี้จะมีผลบังคับใช้ต่อคำสั่งซื้อสินค้าและการชำระเงินที่กระทำโดยท่านและผู้ขายเท่านั้น 
                                <br>
                                สินค้าและการสั่งซื้อ<br>
                                1. การตกลงและยินยอมในการสั่งซื้อสินค้าเกิดขึ้นโดยท่าน  ซึ่งท่านได้ยอมรับในรายละเอียดสินค้าทุกประการรวมไปจนถึงราคาสินค้าก่อนการสั่งซื้อ<br>
                                2. ท่านรับทราบถึงความพยายามอย่างเต็มที่ของผู้ขายในการให้รายละเอียดที่ถูกต้องของสินค้า โดยผู้ขายไม่รับประกันว่ารายละเอียดสินค้าดังกล่าวจะถูกต้อง เป็นปัจจุบันและปราศจากข้อผิดพลาดใดๆ<br>
                                3. คำสั่งซื้อหลังการชำระเงินจะถือว่าเพิกถอนไม่ได้ และไม่มีเงื่อนไขเพิ่มเติมใดๆ อีก อย่างไรก็ตาม ในบางกรณีท่านอาจร้องขอให้ผู้ขายยกเลิกหรือแก้ไขเพิ่มเติมคำสั่งซื้อได้ตามแต่ความเหมาะสมในทางการค้าขาย โดยผู้ขายมิได้มีหน้าที่จำเป็นต้องดำเนินการให้ตามคำขอยกเลิกหรือแก้ไขเพิ่มเติมคำสั่งซื้อใดๆ<br>
                                4. ผู้ขายมีพันธะเฉพาะในการส่งมอบสินค้าให้เป็นไปตามรายละเอียดทั่วไปที่ใช้ในการขายสินค้าดังกล่าว ไม่ว่าจะได้ให้รายละเอียดพิเศษ หรือเป็นการเฉพาะใดๆ หรือไม่ ถือว่าได้ให้รายละเอียดนั้นไว้ตามกฎหมาย อนึ่งรายละเอียดพิเศษ 
                                หรือเป็นการเฉพาะใดๆดังกล่าว จะถือว่าเป็นเพียงการแสดงความคิดเห็นของผู้ขายในเรื่องดังกล่าวเท่านั้น ผู้ขายไม่ได้ให้การรับประกันใดๆในเรื่องคุณภาพ สถานะ สภาพหรือความเหมาะสมของสินค้า<br>
                                การชำระเงินและการคืนเงิน <br>
                                1. ท่านตกลงที่จะชำระค่าสินค้าด้วยบัตรเครดิตหรือบัตรเดบิตที่สามารถใช้ชำระหนี้ตามกฎหมายได้<br>
                                2. ท่านยินยอมที่จะไม่ยกเลิกการชำระเงินในภายหลังเว้นเสียแต่ว่า การสั่งซื้อสินค้าระหว่างท่านและผู้ขายไม่เป็นไปตามข้อตกลง ไม่ได้รับการส่งมอบสินค้า หรือสินค้าที่ท่านได้รับมีลักษณะแตกต่างจากรายละเอียดที่ท่านได้รับจากผู้ขายอย่างมีนัยยะสำคัญ ตามแต่วิจารณญาณของผู้ขาย<br>
                                3. ท่านรับทราบถึงระเบียบการขอคืนเงินที่ชำระสำเร็จจะกระทำได้ต่อเมื่อผู้ขายยินยอม และเป็นผู้แจ้งความประสงค์ที่จะทำการคืนเงินเท่านั้น<br>
                                4. ท่านรับทราบถึงการคืนเงินทั้งหลายให้ทำผ่านกลไลการชำระเงินเดิม และให้แก่บุคคลที่ทำการชำระเงินนั้น<br>
                                5. ท่านรับทราบถึงการไม่รับประกันเงินคืนจะเข้าสู่บัญชีของท่านอย่างตรงต่อเวลา การดำเนินการอาจจะใช้เวลาและขึ้นกับแต่ละธนาคาร และ/หรือผู้ให้บริการการชำระเงินที่ดำเนินการคืนเงินนั้น<br>
                                <br>
                                <div class="checkbox">
                                <label><input type="checkbox" value="" id="checkboxagree" class="">
                                ข้าพเจ้าเข้าใจข้อกำหนดและเงื่อนไขต่างๆตามข้างต้น และขอตกลงยอมรับผูกพันตามข้อกำหนดและเงื่อนไขดังกล่าวทุกประการ
                                </label>
                                <div class="text-center">
                                    <button type="button" id="btn_agree" class="btn btn-info btn-sm">ยอมรับ / agree</button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
		 
		</div>
		<?php include("structer/backend/footer.php"); ?>
        <?php include("structer/backend/script.php"); ?>
        </div>
        <script >
            $(document).ready(function(){
                $("#cancel").on("click", function (e) {
                    window.location.replace('category');   
                }); 
                   
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                bsCustomFileInput.init();
            });
        </script>
	</body>
</html>
 