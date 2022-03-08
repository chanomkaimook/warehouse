<?php
	//echo phpinfo();
?>
<!DOCTYPE html>
<html lang="en">
   	<head> 
	  <?php include("structer/backend/head.php"); ?>
	<style>
		.bg-info, .bg-info>a {
			color: #fff9f9!important;
		}
		.bg-info {
			background-color: #9E9E9E!important;
		}
		.small-box h3 {
			font-size: 1.5rem;
 		}
		.box{
			background-color: #9e9e9e;
			border: 1px solid #8e8e8e;
			border-radius: 5px;
			padding: 1rem;
			margin: 1rem 0;
			min-height: 333px;
		}
		.box-topic {
			font-size: 1rem;
			font-weight: bold;
			color: #fff;
		}
		.box-detail {
			padding: 0.2rem;
			color: #686868;
			background-color: #f4f6f9;
			margin: .5rem;
			border-radius: 3px;
			min-height: 259px;
		}
 	</style>
   	</head>
   	<body class="hold-transition sidebar-mini layout-fixed">

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
							<h1>Chokchaisteakhouse</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="<?php echo site_url('mod_admin') ?>/ctl_admin/backend_main">Home</a></li>
							<li class="breadcrumb-item active"><i class="fa fa-users" aria-hidden="true"></i> <?php if($report3 != ''){ echo number_format($report3)." คน"; } else { echo "0 คน";} ?> </li>
							</ol>
						</div>
					</div>
				</div><!-- /.container-fluid -->
			</section>
		  
			<section class="content">
				<div class="container-fluid">
					<div class="row">

						<div class="col-lg-6 col-12">
		 					<div class="box">
								<div class="box-topic"><li class="fa fa-users"></li> ข้อมูลลูกค้า </div>
								<div class="box-detail"> 
									<table class="table table-bordered">
										<thead>
											<tr>
												<th style="padding: 0.4rem;">รายการ</th>
												<th style="padding: 0.4rem;text-align: center;">จำนวน/คน</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="padding: 0.4rem;">จำนวนลูกค้าที่เพิ่มในวันนี้</td>
												<td style="padding: 0.4rem;text-align: center;"><?php if($report2->countcus != ''){ echo number_format($report2->countcus); } else { echo "0";} ?></td>
											</tr>
											<tr>
												<td style="padding: 0.4rem;">จำนวนลูกค้าทั้งหมด</td>
												<td style="padding: 0.4rem;text-align: center;"><?php if($report3 != ''){ echo number_format($report3); } else { echo "0";} ?></td>
											</tr>
										</tbody>
									</table>
 								</div>
							</div>
						</div>

						<div class="col-lg-6 col-12">
		 					<div class="box">
								<div class="box-topic"><i class="fa fa-bar-chart" aria-hidden="true"></i> ข้อมูลการใช้คะแนน </div>
								<div class="box-detail"> 
									<table class="table table-bordered">
										<thead>
											<tr>
												<th style="padding: 0.4rem;">รายการ</th>
												<th style="padding: 0.4rem;text-align: center;">จำนวน/คะแนน</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="padding: 0.4rem;">คะแนนที่ลูกค้าใช้ในวันนี้</td>
												<td style="padding: 0.4rem;text-align: center;"><?php if($report1->sumpoint != ''){ echo number_format($report1->sumpoint); } else { echo "0";} ?></td>
											</tr>
											<tr>
												<td style="padding: 0.4rem;">คะแนนที่ลูกค้าใช้ทั้งหมด</td>
												<td style="padding: 0.4rem;text-align: center;"><?php if($report4->sumpoint != ''){ echo number_format($report4->sumpoint); } else { echo "0";} ?></td>
											</tr>
											<tr>
												<td style="padding: 0.4rem;">คะแนนที่ให้ลูกค้าวันนี้</td>
												<td style="padding: 0.4rem;text-align: center;"> <?php if($report10->sumpoint != ''){ echo $report10->sumpoint; } else { echo "0";} ?> </td>
											</tr>
											<tr>
												<td style="padding: 0.4rem;">คะแนนที่ให้ลูกค้าทั้งหมด</td>
												<td style="padding: 0.4rem;text-align: center;"> <?php if($report11->sumpoint != ''){ echo $report11->sumpoint; } else { echo "0";} ?> </td>
											</tr>
											<tr>
												<td style="padding: 0.4rem;">คะแนนที่นำกลับในวันนี้ <a target="_blank" href="<?php echo site_url('mod_customer/ctl_customer/customer_report?valdate='.date('Y-m-d').'&valdateTo=&statuscustomer=&Sname=&gender=&pointstatus=03') ?>" class="small-box-footer"> ดูข้อมูล <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> </a></td>
												<td style="padding: 0.4rem;text-align: center;"> <?php if($report12->sumpoint != ''){ echo $report12->sumpoint; } else { echo "0";} ?>  </td>
											</tr>
											<tr>
												<td style="padding: 0.4rem;">คะแนนที่นำกลับทั้งหมด <a target="_blank" href="<?php echo site_url('mod_customer/ctl_customer/customer_report?valdate=&valdateTo=&statuscustomer=&Sname=&gender=&pointstatus=03') ?>" class="small-box-footer"> ดูข้อมูล <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> </a></td>
												<td style="padding: 0.4rem;text-align: center;">  <?php if($report13->sumpoint != ''){ echo $report13->sumpoint; } else { echo "0";} ?>  </td>
											</tr>
										</tbody>
									</table>
								 
								</div>
							</div>
						</div>

						<div class="col-lg-12 col-12">
		 					<div class="box">
								<div class="box-topic"><i class="fa fa-line-chart" aria-hidden="true"></i> ข้อมูลเกียวกับโปรโมชั่น </div>
								<div class="box-detail"> 
									<table class="table table-bordered">
										<thead>
											<tr>
												<th style="padding: 0.4rem;">รายการ</th>
												<th style="padding: 0.4rem;text-align: center;">จำนวน</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="padding: 0.4rem;">จำนวนโปรโมชั่นทั้งหมด</td>
												<td style="padding: 0.4rem;text-align: center;"> <?php if($report5 != ''){ echo $report5." รายการ"; } else { echo "0 รายการ";} ?> </td>
											</tr>
											<tr>
												<td style="padding: 0.4rem;">โปรที่กำลังจะมากึงภายใน 15 วัน</td>
												<td style="padding: 0.4rem;text-align: center;">  <?php if($report6 != ''){ echo $report6." รายการ"; } else { echo "0 รายการ";} ?> </td>
											</tr>
											<tr>
												<td style="padding: 0.4rem;">จำนวนโปรที่ผ่านไปแล้ว</td>
												<td style="padding: 0.4rem;text-align: center;">  <?php if($report7 != ''){ echo $report7." รายการ"; } else { echo "0 รายการ";} ?> </td>
											</tr>
											<tr>
												<td colspan="2" style="padding: 0.4rem;">ลำดับโปรที่ลูกค้ากดใช้คะแนน <a href="#" data-toggle="modal" data-target="#myModal" class="small-box-footer"> ดูข้อมูล <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> </a></td>
 											</tr>
										</tbody>
									</table>
									 
								</div>
							</div>
						</div>
  
					</div>
  
					  
				</div> 
			</section>

			<section class="content">
				<div class="container-fluid">
					<div class="row">
						 

						<section class="col-lg-6 connectedSortable">
							<!-- Custom tabs (Charts with tabs)-->
							<div class="card">
								<div class="card-header">
									<h3 class="card-title"> <i class="fa fa-arrow-circle-o-up" aria-hidden="true"></i> ลำดับการใช้คะแนน </h3>
 								</div> 
								<div class="card-body">
									<div class="tab-content p-0">
										 
											<label class=""> เลือกวัน/เดือน/ปี </label>
                                            <div class="input-group input-group-sm">
												<input type="date" class=" form-control form-control-sm" id="valdate1">
                                                <input type="date" class=" form-control form-control-sm" id="valdateTo1">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-default btn-sm" id="bntvaldate1"><i class="fas fa-search"></i> Search </button>
                                                    <button type="button" class="btn btn-default btn-sm" id="refresh_page1"><i class="fas fa-refresh"></i> Refresh </button>
                                                </div>
											</div>
											<br>
											 
											<div class="table-responsive"> 
												<table id="ex1" class="table table-bordered  ">  
													<thead>  
														<tr>
															<th class="text-center">#</th>
															<th class="text-left">ชื่อ - นามสกุล</th>
															<th class="text-center">จำนวนคะแนน</th>
														</tr>
													</thead>  
												</table>  
											</div>
											 
									</div>
								</div> 
							</div>
						</section>
						
						<section class="col-lg-6 connectedSortable">
							<!-- Custom tabs (Charts with tabs)-->
							<div class="card">
								<div class="card-header">
									<h3 class="card-title"> <i class="fa fa-arrow-circle-o-down" aria-hidden="true"></i> ความเคลื่อนไหวการใช้คะแนนของลูกค้า </h3>
 								</div> 
								<div class="card-body">
									<div class="tab-content p-0">
										
										<label class=""> ภายใน <span id="text-month"> 1 </span> เดือนที่แล้ว</label> 
                                        <div class="input-group input-group-sm">
                                                
												<select id="valdate2"  class=" form-control form-control-sm">
													<option value="30"> ภายใน 1 เดือนที่แล้ว</option>
													<option value="60"> ภายใน 2 เดือนที่แล้ว</option>
													<option value="90"> ภายใน 3 เดือนที่แล้ว</option>
													<option value="120"> ภายใน 4 เดือนที่แล้ว</option>
 												</select>
                                             <div class="input-group-append">
                                                <button type="button" class="btn btn-default btn-sm" id="bntvaldate2"><i class="fas fa-search"></i> Search </button>
                                                <button type="button" class="btn btn-default btn-sm" id="refresh_page2"><i class="fas fa-refresh"></i> Refresh </button>
                                            </div>
                                        </div>
											<br>
 											<div class="table-responsive"> 
												 <table id="ex2" class="table table-bordered">  
													 <thead>  
														 <tr>
															 <th class="text-center">#</th>
															 <th class="text-left">ชื่อ - นามสกุล</th>
															 <th class="text-left">รายละเอียด</th>
														 </tr>
													 </thead>  
												 </table>  
											 </div>
									</div>
								</div> 
							</div>
						</section>


					</div>
				</div> 
			</section>
			
			<!-- The Modal -->
			<div class="modal fade" id="myModal">
				<div class="modal-dialog modal-xl">
					<div class="modal-content">
					
						<!-- Modal Header -->
						<div class="modal-header">
							<h4 class="modal-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> ลำดับโปรโมชั่นที่ลูกค้ากดใช้คะแนน (Rangking)</h4>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						
						<!-- Modal body -->
						<div class="modal-body">
							<label class=""> ในเดือน <span id="text-month_Prorangking"><?php echo thai_date_month(date('Y-m-d')); ?></span> </label> 
                             	<div class="input-group input-group-sm">
									<input type="date" class=" form-control form-control-sm" id="valdate3">
									<input type="date" class=" form-control form-control-sm" id="valdateTo3">
                                    <div class="input-group-append">
                                    	<button type="button" class="btn btn-default btn-sm" id="bntvaldate3"><i class="fas fa-search"></i> Search </button>
                                    	<button type="button" class="btn btn-default btn-sm" id="refresh_page3"><i class="fas fa-refresh"></i> Refresh </button>
										<button type="button" class="btn btn-default btn-sm" id="report1"><i class="fa fa-print"></i> Report </button>
                                    </div>
								</div>
								<br>
								<div class="table-responsive"> 
									<table id="ex3" class="table table-bordered" style="width:100%">  
										<thead>  
											<tr>
												<th class="text-center">#</th>
												<th class="text-left">รายการ</th>
												<th class="text-center">รวมคะแนน</th>
												<th class="text-center">จำนวนรายการ/ครั้ง</th>
											</tr>
										</thead>  
									</table>  
								</div>
						</div>
						 
					</div>
				</div>
			</div>
		 
		</div>
		<?php include("structer/backend/footer.php"); ?>
		<?php include("structer/backend/script.php"); ?>
		<script>
				report8();
				report9();
				reportProrangking();
				// Report //
				$(document).on('click', '#report1', function(event) {
					if($('#valdate3').val() != ''){ var valdate = $('#valdate3').val(); } else { var valdate = ''; }
                	if($('#valdateTo3').val() != ''){ var valdateTo = $('#valdateTo3').val(); } else { var valdateTo = ''; }
					window.open('report1?valdate='+valdate+'&valdateTo='+valdateTo);
				});
 				//----------------------------DATA TABLE 1--------------------------//
                $(document).on('click', '#bntvaldate1', function(event) {
                    if($('#valdate1').val() != ''){ var valdate = $('#valdate1').val(); } else { var valdate = null; }
                    if($('#valdateTo1').val() != ''){ var valdateTo = $('#valdateTo1').val(); } else { var valdateTo = null; }
                     
                    $('#ex1').DataTable().destroy();
                    report8(valdate, valdateTo);
                     
                }); 

                $(document).on('click', '#refresh_page1', function(event) {
                    $('#ex1').DataTable().destroy();
                    report8();  
                }); 
				function report8(valdate, valdateTo) {
                     var dataTable = $('#ex1').DataTable({  
                        "processing":true,  
                        "serverSide":true,  
                        "order":[],  
                        "ajax":{  
                                url:"<?php echo base_url() . 'mod_admin/ctl_admin/fetch_report8'; ?>",  
                                type:"POST",
                                data:{
                                    valdate:valdate, valdateTo:valdateTo
                                } 
                        },  
                        "columnDefs":[  
                                {  
                                    "targets":0,  
                                    "orderable":false,  
                                },  
                        ],  
                    });  
                }

				//----------------------------DATA TABLE 2--------------------------//
                $(document).on('click', '#bntvaldate2', function(event) {
                    if($('#valdate2').val() != ''){ var valdate = $('#valdate2').val(); } else { var valdate = null; }
                    if($('#valdateTo2').val() != ''){ var valdateTo = $('#valdateTo2').val(); } else { var valdateTo = null; }
					var M = 0;
					if($('#valdate2').val() == 30){
						M = 1;
					} else if($('#valdate2').val() == 60){
						M = 2;
					} else if($('#valdate2').val() == 90){
						M = 3;
					} else if($('#valdate2').val() == 120){
						M = 4;
					}
					$('#text-month').text(M);
                    $('#ex2').DataTable().destroy();
                    report9(valdate, valdateTo);
                }); 

                $(document).on('click', '#refresh_page2', function(event) {
					 
					$('#text-month').text(1);
                    $('#ex2').DataTable().destroy();
                    report9();  
                }); 
				function report9(valdate, valdateTo) {
                     var dataTable = $('#ex2').DataTable({  
                        "processing":true,  
                        "serverSide":true,  
                        "order":[],  
                        "ajax":{  
                                url:"<?php echo base_url() . 'mod_admin/ctl_admin/fetch_report9'; ?>",  
                                type:"POST",
                                data:{
                                    valdate:valdate, valdateTo:valdateTo
                                } 
                        },  
                        "columnDefs":[  
                                {  
                                    "targets":0,  
                                    "orderable":false,  
                                },  
                        ],  
                    });  
				}
				//----------------------------DATA TABLE 3--------------------------//
                $(document).on('click', '#bntvaldate3', function(event) {
                    if($('#valdate3').val() != ''){ var valdate = $('#valdate3').val(); } else { var valdate = null; }
                    if($('#valdateTo3').val() != ''){ var valdateTo = $('#valdateTo3').val(); } else { var valdateTo = null; }
					$.post("ajaxtextmonth", {valdate: valdate, valdateTo: valdateTo}, function(result){
						var obj = jQuery.parseJSON(result);
						var textmonth = "";
						if($('#valdate3').val() != '' && $('#valdateTo3').val()){
							textmonth = obj.valdate+" ถึง "+obj.valdateTo; 
 						} else if($('#valdate3').val() != ''){
							textmonth = obj.valdate; 
 						} else {
							textmonth = "<?php echo thai_date_month(date("Y-m-d")); ?>";
						}
						$('#text-month_Prorangking').text(textmonth);
					});
                    $('#ex3').DataTable().destroy();
                    reportProrangking(valdate, valdateTo);
                }); 

                $(document).on('click', '#refresh_page3', function(event) {
					textmonth = "<?php echo thai_date_month(date("Y-m-d")); ?>";
					$('#text-month_Prorangking').text(textmonth);
                    $('#ex3').DataTable().destroy();
                    reportProrangking();  
                }); 
				function reportProrangking(valdate, valdateTo) {
                     var dataTable = $('#ex3').DataTable({  
                        "processing":true,  
                        "serverSide":true,  
                        "order":[],  
                        "ajax":{  
                                url:"<?php echo base_url() . 'mod_admin/ctl_admin/fetch_reportProrangking'; ?>",  
                                type:"POST",
                                data:{
                                    valdate:valdate, valdateTo:valdateTo
                                } 
                        },  
                        "columnDefs":[  
                                {  
                                    "targets":0,  
                                    "orderable":false,  
                                },  
                        ],  
                    });  
                }
		</script>
 		</div>
		
	</body>
</html>
 