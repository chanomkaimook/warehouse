<!DOCTYPE html>
<html lang="en">
   	<head> 
        <?php include("structer/backend/head.php"); ?>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('asset/plugin/datatablebutton');?>/datatables.min.css"/>
        <style>
            table img {
				max-width:100px;
			}
			.textoverflow {
				white-space: nowrap;
				text-overflow: ellipsis;
				-o-text-overflow: ellipsis;
				-ms-text-overflow: ellipsis;
				overflow: hidden;
				/*width: 100px;*/
				width: 190px;
			}
			.modal img{
				max-width:100%;
			}
			table.nowrap {
				white-space: nowrap
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
						<h1><?php echo $submenu; ?></h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo site_url('mod_admin') ?>/ctl_login/backend_main">Home</a></li>
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
                                    <?php
										$result = array();
										
										$numall = $this->mdl_dashboard->count_allbill_result();
										
										$sql = $this->db->select('*')
										->from('retail_bill')
										->where('date(date_starts)',date('Y-m-d'))
										->group_by('user_starts');
										$numbill = $sql->count_all_results(null,false);
										$query = $sql->get();
										if($numbill > 0){
											foreach($query->result() as $row){
												$user = $row->USER_STARTS;
												
												$sqlstaff = $this->db->select('*')
												->from('staff')
												->where('code',$user);
												$numstaff = $sqlstaff->count_all_results(null,false);
												$querystaff = $sqlstaff->get();
												$rowstaff = $querystaff->row();
												
												$numcount = $this->mdl_dashboard->countresult($user);
												
												$name = $rowstaff->NAME_TH." ".$rowstaff->LASTNAME_TH;

												$result[] = array(
																'username'		=> $name,
																'total'			=> $numcount['total'],
																'approve'		=> $numcount['approve'],
																'cancel'		=> $numcount['cancel'],
																'balance'		=> $numcount['balance']
															);
												
											}
										}
									?>
									
									
									<div class="table-responsive">
										<div class="row d-flex ">
											<div class="form-group m-2 text-primary mr-5">
												<label>จำนวนบิลที่สร้างวันนี้ : </label>
												<h1 class="text-right"><?php echo $numall['total'];?></h1>
											</div>
											<div class="form-group m-2 text-success mr-5">
												<label>จำนวนบิลที่อนุมัติวันนี้ : </label>
												<h1 class="text-right"><?php echo $numall['approve'];?></h1>
											</div>
											<div class="form-group m-2 text-danger mr-5">
												<label>จำนวนบิลที่ยกเลิกวันนี้ : </label>
												<h1 class="text-right"><?php echo $numall['cancel'];?></h1>
											</div>
										</div>
										<table class="table table-bordered">
											<thead>
											<tr>
												<th>ชื่อ-นามสกุล</th>
												<th>บิลที่สร้าง</th>
												<th>บิลที่อนุมติแล้ว</th>
												<th>บิลที่ยกเลิก</th>
												<th>ยังไม่สมบูรณ์</th>
											</tr>
											</thead>
											<tbody>
											<?php
												if(count($result) > 0){
													foreach($result as $text){
												
											?>
											<tr>
												<td><?php echo $text['username'];?></td>
												<td><?php echo $text['total'];?></td>
												<td><?php echo $text['approve'];?></td>
												<td><?php echo $text['cancel'];?></td>
												<td><?php echo $text['balance'];?></td>
											</tr>
											<?php
													}
												}
											?>
											</tbody>
										</table>
									</div>
                                    <hr>
									
								</div>
							</div> 
						</section>
						<!--		Modal		-->
						<div class="modal" id="windowlist">
							<div class="modal-dialog modal-xl">
							  <div class="modal-content">
							  
								<!-- Modal Header -->
								<div class="modal-header">
								  <h4 class="modal-title">เลือกการแสดงผล :</h4>
								  <button type="button" class="close" data-dismiss="modal">&times;</button>
								</div>
								
								<!-- Modal body -->
								<div class="modal-body text-center">

									<div class="row">
										<div class="col-sm-12 text-left d-flex justify-content-left">
											<div class="form-group">
												<label class=""> เลือกวันเวลา </label>
												<div class="input-group ">
													<input type="date" class=" form-control form-control-sm" id="modalvaldate">
													<input type="date" class=" form-control form-control-sm" id="modalvaldateTo">
												</div>
											</div>
											<div class="form-group">
												<label class=""> จากวัน </label>
												<div class="form-group col-md-4">
													<select id="modalsearchfromdate" class="form-control form-control-sm" style="width:120px;">
														<option value="date_starts" selected> วันที่สร้าง </option>
														<option value="transfered_daytime"> วันที่โอนเงิน </option>
													</select>
													
												</div>
											</div>
											<div class="form-group">
												<label class="invisible"> ... </label>
												<div class="datesubmit_button">
													<button type="button" class="btn btn-primary btn-sm" id="btnmodal_datesubmit">Submit</button>
												</div>
											</div>
											
											
										</div>
									</div>
									
									<div class="card mt-2">

										<div class="card-body pre-scrollable">
											<div class="row">
												<div id="list" class="col-sm-12 text-left form-group">
													
												</div>
											</div>
										</div>
									</div>
									
								</div>
								
								<!-- Modal footer -->
								<div class="modal-footer">
									<span class="submit_button">
										<button type="button" class="btnsp_list btn btn-primary" value="bill_listorders">Load Report</button>
									</span>
									<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
								</div>
								
							  </div>
							</div>
						</div>
						<!--		End Modal	-->
						
						<!--		Modal		-->
						<div class="modal" id="modalslip">
							<div class="modal-dialog modal-lg">
							  <div class="modal-content">
							  
								<!-- Modal Header -->
								<div class="modal-header">
								  <h4 class="modal-title">Slip :</h4>
								  <button type="button" class="close" data-dismiss="modal">&times;</button>
								</div>
								
								<!-- Modal body -->
								<div class="modal-body text-center">
								  Modal body..
								</div>
								
								<!-- Modal footer -->
								<div class="modal-footer">
								  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
								</div>
								
							  </div>
							</div>
						</div>
						<!--		End Modal	-->
					</div>
					
				</div>
			</section>
			
		</div>
		<?php include("structer/backend/footer.php"); ?>
		<?php include("structer/backend/script.php"); ?>
		<script src="<?php echo $base_bn;?>plugins/sweetalert2/sweetalert2.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url('asset/plugin/datatablebutton');?>/datatables.min.js"></script>
		
		<script>
            // function viewreportall() {
            //   	window.open('<?php echo site_url('mod_promote') ?>/ctl_promote/viewreportpromoteall');
            // }
            $(function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
				//
				//	setting
				var frmdate = $('input#valdate[type=hidden]');
				var frmdateto = $('input#valdateto[type=hidden]');
				var searchfromdate = $('#searchfromdate');
				var selectdate = $('input#valdate[type=date]');
				var selectdateto = $('input#valdateTo[type=date]');
				
				var select_reporttype = $('#reporttype');
				
				//	modal function
				$(document).on('click', 'a[data-target="#modalslip"]', function(event) {
					var loading = '<div class="spinner-border text-primary"></div>';
					$('.modal-header h4').html('');
					$('.modal-body').html(loading);
					
					var codeid = $(this).attr('data-id');
					var tablename = $('#table').val();
                    $.post("ajaxslip", { id:  codeid,table:  tablename})
					.done(function(result) {
						setTimeout(function(){
						var obj = jQuery.parseJSON(result);
						var html = '';
						html += obj.image;
						   
						$('.modal-header h4').html('slip : <small>'+obj.code+' '+obj.name+'</small>');
						$('.modal-body').html(html);
						// console.log( "second success" );
						},1000);
					})
					.fail(function() {
						console.log( "error" );
					})
					
                });
				
				//	clear value to run page next time
				function clearFormData(){
					$('#bill_summary_productid').val('');
					
					$('#subtable').val('');
				}
				
                // dataTables();
				$(document).on('click', '#btn_report', function(event) {
					//	for table detail
					if($('table.detailtable').length > 0){
						if($('table.detailtable').has('tbody').length == 0){
							swal.fire("ไม่พบข้อมูล","", "warning");
							return false;
						}
					}
					
					if(select_reporttype.val() == ""){
						swal.fire("เลือกประเภทรายงาน","", "warning");
					}else{
						var valdate = selectdate.val();
						var valdateto = selectdateto.val();
						var statusproduct = $('#statusproduct').val();
						var reporttype = $('#reporttype').val();
						$('input#productid').val($('#selectproduct').val());
						
						frmdate.val(valdate);
						frmdateto.val(valdateto);
						searchfromdate.val();
						$('#status').val(statusproduct);
						$('#table').val(reporttype);
						
						var d = document;
						
						d.frmreport.submit();
					}
                });
				
				//
				//	from modal
				//
				$(document).on('click', '#btnmodal_datesubmit', function(event) {
					if(select_reporttype.val() == ""){
						swal.fire("เลือกประเภทรายงาน","", "warning");
					}else{
						var valdate = $('#modalvaldate').val();
						var valdateto = $('#modalvaldateTo').val();
						var reporttype = $('#reporttype').val();
						var modalsearchfromdateval = $('#modalsearchfromdate').val();

						$.post("ajaxListbill", { 
									searchfromdate: modalsearchfromdateval,
									datestart: valdate,
									dateto: valdateto,
									table:reporttype 
									}, function(result){
							var obj = jQuery.parseJSON(result);
							
							if(obj.error_code == 0){
								$('#windowlist #list').html(obj.txt);
							}else{
								$('#windowlist #list').html("Data not found.");
							}
						});
					}
                });
				
				//
				//	from modal
				//
				$(document).on('click', '#btnmodal_listmonthsubmit', function(event) {
					if(select_reporttype.val() == ""){
						swal.fire("เลือกประเภทรายงาน","", "warning");
					}else{
						
						var valdate = $('#modalvaldate').val();
						var valdateto = $('#modalvaldateTo').val();
						var reporttype = $(this).val();
						var modalsearchfromdateval = $('#modalsearchfromdate').val();

						//	update date form for load report
						frmdate.val(valdate);
						frmdateto.val(valdateto);

						$.get("ajaxListmonth", { 
								searchfromdate: modalsearchfromdateval,
								valdate: valdate,
								valdateto: valdateto,
								table:reporttype 
								}, function(result){
							var obj = jQuery.parseJSON(result);

							if(obj.error_code == 0){
								$('#windowlist #list').html(obj.txt);
							}else{
								$('#windowlist #list').html("Data not found.");
							}
						});
					}
                });
				
				//
				//	from button special
				//
				$(document).on('click', '.listmonth', function(event) {
					var btnDateSubHtml = '<button type="button" class="btn btn-primary btn-sm" value="bill_vatlistmonth" id="btnmodal_listmonthsubmit">';
					btnDateSubHtml += 'Submit';
					btnDateSubHtml += '</button>';
					
					var btnSubmitHtml = '<button type="button" class="btnsp_listmonth btn btn-primary" value="bill_vatlistmonth">';
					btnSubmitHtml += 'Load Report';
					btnSubmitHtml += '</button>';
					
					$('#windowlist .datesubmit_button').html(btnDateSubHtml);
					$('#windowlist .submit_button').html(btnSubmitHtml);
                });
				
				//
				//	from button special
				//
				$(document).on('click', '.btnsp_listorder', function(event) {
					var btnDateSubHtml = '<button type="button" class="btn btn-primary btn-sm" value="" id="btnmodal_datesubmit">';
					btnDateSubHtml += 'Submit';
					btnDateSubHtml += '</button>';
					
					var btnSubmitHtml = '<button type="button" class="btnsp_list btn btn-primary" value="bill_listorders">';
					btnSubmitHtml += 'Load Report';
					btnSubmitHtml += '</button>';
					
					$('#windowlist .datesubmit_button').html(btnDateSubHtml);
					$('#windowlist .submit_button').html(btnSubmitHtml);
                });
				
				//
				//	from button special
				//
				$(document).on('click', '.btnsp_report', function(event) {
					if(select_reporttype.val() == ""){
						swal.fire("เลือกประเภทรายงาน","", "warning");
					}else{
						var valdate = selectdate.val();
						var valdateto = selectdateto.val();
						var statusproduct = $('#statusproduct').val();
						var reporttype = $(this).val();

						frmdate.val(valdate);
						frmdateto.val(valdateto);
						$('#status').val(statusproduct);
						$('#table').val(reporttype);
	
						var d = document;
						d.frmreport.submit();
					}
                });
				
				$(document).on('click', '.btnsp_list', function(event) {
					var chknum = "";
					var modal_chk = $('#windowlist .modal-body input:checkbox:checked');
					
					if(modal_chk.length == 0){
						Toast.fire({
                            type: 'warning',
                            title: 'No data select'
                        });
						return false;
					}
					
					modal_chk.each(function(index, element){
						chknum += $(this).val()
						if (index !== (modal_chk.length - 1)) {
							chknum += ",";
						}
					});
					
					$('#listorderid').val(chknum);

					if(select_reporttype.val() == ""){
						swal.fire("เลือกประเภทรายงาน","", "warning");
					}else{
						var reporttype = $(this).val();

						$('#table').val(reporttype);
						
						var d = document;
						d.frmreport.submit();
					}
                });
				
				$(document).on('click', '.btnsp_listmonth', function(event) {
					if(select_reporttype.val() == ""){
						swal.fire("เลือกประเภทรายงาน","", "warning");
					}else{
						// console.log(frmdate.val());
						
						var statusproduct = $('#statusproduct').val();
						var reporttype = $(this).val();
						var modalsearchfromdateval = $('#modalsearchfromdate').val();
						console.log(modalsearchfromdateval+'++++'+reporttype);
						
						$('#status').val(statusproduct);
						$('#table').val(reporttype);
						$('#valsearchfromdate').val(modalsearchfromdateval);

						var d = document;
						d.frmreport.submit();
					}
                });
				
				//
				//	if modal close
				//
				$('#windowlist').on('hide.bs.modal', function () {
					//
					//	reset date
					$('#windowlist #modalvaldate').val("");
					$('#windowlist #modalvaldateTo').val("");
					
					$('#windowlist #list').empty();
				});
				
                //----------------------------filter--------------------------//
                $(document).on('click', '#bntvaldate', function(event) {
					// console.log(selectdate.val()+"==="+selectdateto.val()+"----"+frmdateto.val());
					if(select_reporttype.val() == ""){
						swal.fire("เลือกประเภทรายงาน","", "warning");
					}else{
						clearFormData();

						frmdate.val(selectdate.val());
						frmdateto.val(selectdateto.val());
						$('#status').val($('#statusproduct').val());
						$('#buttonspecial').empty();
						$('#ex1').DataTable().destroy();
						dataTables();
					}
                }); 

                $(document).on('click', '#refresh_page', function(event) {
					frmdate.val('');
					frmdateto.val('');
					$('#statusproduct').val('');
					$('input#status[type=hidden]').val('');
                    $('#ex1').DataTable().destroy();
					$('#buttonspecial').empty();
                    dataTables();  
                });

                $(document).on('change', '#reporttype', function(event) {
					$('#table').val($(this).val());
					
                    $('#ex1').DataTable().destroy();
					
					clearFormData();
					
					var loading = '<div class="text-center" data="loading"><div class="text-center spinner-grow spinner-grow-sm text-primary"></div><div class="text-center spinner-grow spinner-grow-sm text-primary"></div><div class="text-center spinner-grow spinner-grow-sm text-primary"></div></div>';
					$("#datainformationtable").fadeIn();
					
					if($('#table').val() == ""){
						// $('#ex1').html('');
						$("#datainformationtable").fadeOut('fast');

						return false;
					}else{
						dataTables();
					}
					
					/* var loading = '<div class="text-center" data="loading"><div class="text-center spinner-grow spinner-grow-sm text-primary"></div><div class="text-center spinner-grow spinner-grow-sm text-primary"></div><div class="text-center spinner-grow spinner-grow-sm text-primary"></div></div>';
					var btnspecial = '<div id="buttonspecial"></div>';
					var tablerespon ='<div class="table-responsive"></div>';
					
					$("#datainformationtable").fadeIn(function(e){
					
							$('#datainformationtable #custom-tabs-one-tabContent').html(btnspecial+tablerespon);
						
					}).append(loading); */
                });
				
				$(document).on('click', '#custom-tabs-three-tab .nav-item .nav-link', function(event) {
					if(select_reporttype.val() == ""){
						swal.fire("เลือกประเภทรายงาน","", "warning");
						return false;
					}
					
					clearFormData();
					
					var tableid = $(this).attr('id');
					if(tableid != 'custom-tabs-three-main-tab'){
						//	subtable for report
						$('#subtable').val($(this).attr('data-report'));
						
						var divtable = $('.tab-pane[aria-labelledby='+tableid+']').html();
						
						if(divtable == ""){
							dataTableDetail();
						}
						
					}
					
                });
				
                function dataTables(valdate, valdateto, statusproduct, reporttype) {
					
					var valdate = frmdate.val();
					var valdateto = frmdateto.val();
					
					var searchfromdateval = searchfromdate.val();
					
					var statusproduct = $('#status').val();
					var reporttype = $('#table').val();

					var serverside = true;
					
					var pagevalue = true;	//	sometable do not pageination
					
					var input_summary_product = $('input#bill_summary_product');
					
					// reporttype = "activity_report";
					// console.log(valdate+"=="+valdateTo+"++"+statusproduct+"---"+reporttype);
					
					var hmtl_tablename = "<table id='ex1' class='table table-bordered' style=\"display:none\">";
					var html_th = "<thead>";
					var html_th_tr;
					var ul = "";
					//	find template table from select type
					switch(reporttype){
						case 'bill_report':
							var createdRow_array = [
														6,
														7,
														8,
														9,
														10
												];
							var valprice = 5;
							var tabpriceheight = true;
							//	write html
							html_th_tr = "<th width='4%'></th>";
							html_th_tr += "<th width='10%'>วันทำรายการ</th>";
							html_th_tr += "<th width='10%'>เลขที่</th>";
							html_th_tr += "<th width='10%'>codeร้านค้า</th>";
							html_th_tr += "<th width='20%'>ชื่อ-นามสกุล</th>";
							html_th_tr += "<th width='15%'>เบอร์ติดต่อ</th>";
							html_th_tr += "<th width='5%'>ยอดขาย</th>";
							html_th_tr += "<th width='5%'>ค่าพัศดุ</th>";
							html_th_tr += "<th width='5%'>ค่าจัดส่ง</th>";
							html_th_tr += "<th width='5%'>ส่วนลด</th>";
							html_th_tr += "<th width='5%'>ยอดขายสุทธิ</th>";
							html_th_tr += "<th width='6%'>รูป</th>";
							creatData();
						break;
						case 'bill_summary':
							var createdRow_array = [
														5,
														6,
														7,
														8,
														9
												];
							var valprice = 5;
							var tabpriceheight = true;
							//	write html					
							html_th_tr = "<th width='4%'></th>";
							html_th_tr += "<th width='10%'>วันทำรายการ</th>";
							html_th_tr += "<th width='20%'>เลขที่</th>";
							html_th_tr += "<th width='20%'>ชื่อ-นามสกุล</th>";
							html_th_tr += "<th width='10%'>เบอร์ติดต่อ</th>";
							html_th_tr += "<th width='5%'>ยอดชำระ</th>";
							html_th_tr += "<th width='5%'>ค่าพัศดุ</th>";
							html_th_tr += "<th width='5%'>ค่าจัดส่ง</th>";
							html_th_tr += "<th width='5%'>ส่วนลด</th>";
							html_th_tr += "<th width='5%'>ยอดโอน</th>";
							html_th_tr += "<th width='5%'>การส่ง</th>";
							html_th_tr += "<th width='6%'>รูป</th>";
							
							ul += '<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">';				
							ul += '<li class="nav-item">';
							ul += '<a class="nav-link active" id="custom-tabs-three-main-tab" data-toggle="pill" href="#custom-tabs-three-main" role="tab" aria-controls="custom-tabs-three-main" aria-selected="true">Home</a>';
							ul += '</li>';
							if(input_summary_product.val() == 1){
								ul += '<li class="nav-item">';
								ul += '<a class="nav-link" id="custom-tabs-three-product-tab" data-report="report_summaryproduct" data-toggle="pill" href="#custom-tabs-three-product" role="tab" aria-controls="custom-tabs-three-product" aria-selected="false">Product</a>';
								ul += '</li>';
							}
							ul += '</ul>';
												
							creatData();
						break;
						case 'bill_store':
							pagevalue = false;
							var createdRow_array;
							function callTest(handleData){
								var arrayresult  = "";
								var classview = "";
								$.get("columnProduct", {table: reporttype}, function(result,statusTxt,xhr){
									html_th_tr += "<th width='10%'>sss</th>";
									
									if(statusTxt == "success")
									var obj = jQuery.parseJSON(result);
									if(obj.error_code == 0){
										$.each(obj.query,function( index, value ){
											//	set product list view status
											/* if(value['statusview'] != 0){
												arrayresult += "<th width='' class='"+classview+"'>"+value['product_list']+"</th>";
											} */
											arrayresult += "<th width='' class='"+classview+"'>"+value['product_list']+"</th>";
											
										});
									}
									handleData(arrayresult);		//	return array data
								});
							}
							callTest(function(output){
							  // console.log(output);
							  html_th_tr = "<th width='4%'></th>";
							  html_th_tr += "<th width='10%'>วันทำรายการ</th>";
							  html_th_tr += output;
							  
							  creatData();
							});
						break;
						case 'ranking_sale':
							serverside = false;

							var createdRow_array = [
														3
												];
							var valprice = 3;
							var tabpriceheight;
							//	write html
							html_th_tr = "<th width='4%'></th>";
							html_th_tr += "<th width='20%'>ชื่อ-นามสกุล</th>";
							html_th_tr += "<th width='15%'>เบอร์ติดต่อ</th>";
							html_th_tr += "<th width='5%'>ยอดขาย</th>";
							html_th_tr += "<th width='5%'>จำนวนบิล</th>";
							creatData();
						break;
					}
					
					function creatData(){
						var btn_specialbill = "";
						var loading = '<div class="text-center" data="loading"><div class="text-center spinner-grow spinner-grow-sm text-primary"></div><div class="text-center spinner-grow spinner-grow-sm text-primary"></div><div class="text-center spinner-grow spinner-grow-sm text-primary"></div></div>';
						
						//
						//	For button special
						//	button report billvat
						var input_billvat = $('input#btn_billvat');
						if(input_billvat.val() == 1 && reporttype == 'bill_report'){
							btn_specialbill += "<button id='btnsp' class='btnsp_report btn btn-sm btn-light mr-2' value='bill_vat'>ออกใบกำกับภาษี</button>";
						}
						
						//	button report billvat
						var input_receipt = $('input#btn_receipt');
						if(input_receipt.val() == 1 && reporttype == 'bill_report'){
							btn_specialbill += "<button id='btnsp' class='btnsp_report btn btn-sm btn-light mr-2' value='bill_receipt'>ออกใบเสร็จรับเงิน</button>";
						}
						
						//	button report billvat list month
						var input_listmonth = $('input#btn_listmonth');
						if(input_listmonth.val() == 1 && reporttype == 'bill_report'){
							btn_specialbill += "<button id='btnsp' class='listmonth btn btn-sm btn-light mr-2' value='' data-toggle='modal' data-target='#windowlist'>ดูรายการใบเสร็จ</button>";
						}
						
						//	button report billvat
						var input_listorders = $('input#btn_listorders');
						if(input_listorders.val() == 1 && reporttype == 'bill_report'){
							btn_specialbill += "<button id='btnsp' class='btnsp_listorder btn btn-sm btn-light mr-2' value='' data-toggle='modal' data-target='#windowlist'>รายการสินค้าที่สั่ง</button>";
						}
						
						var hmtl_table = "";
						hmtl_table += '<div class="tab-pane fade show active" id="custom-tabs-three-main" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">';
						hmtl_table += hmtl_tablename+html_th+html_th_tr;
						
						hmtl_table += "</table>";
						// hmtl_table += "abc";
						hmtl_table += "</div>";		//	div tab-pane
						
						hmtl_table += '<div class="tab-pane fade" id="custom-tabs-three-product" role="tabpanel" aria-labelledby="custom-tabs-three-product-tab"></div>';
						
						hmtl_table += '<div class="tab-pane fade" id="custom-tabs-three-detail" role="tabpanel" aria-labelledby="custom-tabs-three-detail-tab"></div>';

						hmtl_table += loading;
					
					// $('.table-responsive').html(hmtl_table);
					$('#custom-tabs-three-tabContent').html(hmtl_table);
					setTimeout(function(){
						//	insert tab
						$("#datainformationtable .card-header").html(ul);

						var dataTable = $('#ex1').DataTable({  
							"serverSide": serverside,
							"processing": serverside,  
							"paging": pagevalue,
							"lengthChange": true,
							"searching": true,
							"ordering": true,
							"order": [[ 0, 'desc' ]],
							"select": true,
							"info": true,
							"autoWidth": false,

							// "serverSide": true,
							"ajax":{  
									url:"fetch_data",  
									type:"post",
									dataType : "json",
									data:{
										valdate:valdate, 
										valdateto:valdateto,
										searchfromdate:searchfromdateval,
										statusproduct: statusproduct,
										table: reporttype
									}
							},
							//	call back data before process
							"stateSave": true,
							"stateSaveCallback": function (settings, data) {
								 $('[data-toggle="popover"]').popover();
								// Send an Ajax request to the server with the state object
								/* $.ajax( {
								  "url": "/state_save",
								  "data": data,
								  "dataType": "json",
								  "type": "POST",
								  "success": function () {}
								} ); */
							},
							// deferRender:    true,
							
							//	load finish
							"initComplete": function(settings, json) {
								// $('[data-toggle="popover"]').popover();
								$('*[data=loading]').hide();
								var totalJSON = json.recordsTotal;
								if(totalJSON){
									// var outreport = "<button type=\"button\" id=\"btn_report\" class=\"btn btn-outline-primary btn-sm pull-left\">ออกรายงาน</button>";
									// $('.table-responsive').prepend(outreport);
								}else{
									$('.table-responsive').empty();
								}
								//	find data in json 
								// console.log(json.data[39][5]);
							},
							//	data load before 
							"preDrawCallback": function( settings ) {
								$('[data-toggle="popover"]').popover('hide');
							},
							//	data load after 
							"rowCallback": function( row, data) {
								if(createdRow_array && tabpriceheight == true){
									var priceInt = numberWithCommas(data[valprice]);
									if ( data[valprice] >= 1500 ) {
										$(row).css('background','#A1D991');
									}
									if ( data[valprice] == 0 ) {
										$(row).css('background','#ffc8cd');
									}
								}
							},
							"createdRow": function ( row, data, index ) {
								if(createdRow_array){
									var result;
									//	convert number to comma
									$.each(createdRow_array, function(index,value) {
									  result = numberWithCommas(data[value]);
									  $('td', row).eq(value).html(result);
									});
								}
							},
							// dom: 'Bfrtip',
							dom:
								"<'row'<'col-sm-6 btn-sm'B><'col-sm-6 form-control-sm'f>>" +
								"<'row'<'col-sm-12 small'tr>>" +
								"<'row'<'col-sm-4 small'i><'col-sm-4 d-flex justify-content-center small'l><'col-sm-4 small'p>>",
							buttons: [
								'print',
								{
									extend: 'collection',
									text: 'Export',
									buttons: [ 'excel', 'pdf', 'copy' ],
									fade: true
								},
								{
									extend: 'collection',
									text: 'Tool',
									buttons: [ 'columnsToggle', 'colvisRestore' ],
									fade: true
								}
							],
							"columnDefs":[  
								{  
									"orderable": true, 
									"targets": 0 
								}
							]
							/* "columns": [
								{ "orderable": false },
								null
							] */
						});
						/*
						//	click table
						$('#ex1 tbody').on('click', 'tr', function () {
							var data = dataTable.row( this ).data();
						} );
						*/
						$('#ex1').fadeIn('slow');
						$('div[data=loading]').hide();
						/* 
						//	refresh data table
						setInterval( function () {
							dataTable.ajax.reload();
						}, 3000 );
						 */
						//
						//	insert button special
						$('#buttonspecial').html(btn_specialbill)
					}, 1000);
					}
                }		//	end function

				
				$(document).on('change', '#selectproduct', function(event) {
					
					$('#bill_summary_productid').val($(this).val());
					if($('#bill_summary_productid').val() != ""){
						dataTableDetail();
					}
					
                });
				
				//	datatablesdetail
				function dataTableDetail(valdate, valdateto, statusproduct, reporttype) {
					var valdate = frmdate.val();
					var valdateto = frmdateto.val();
					var statusproduct = $('#status').val();
					var productid = $('#selectproduct').val();
					var reporttype = $('#table').val();
					var summary_productid = $('#bill_summary_productid').val();
					
					var btnSpClass = 'buttonspecial-ex2';
					var hmtl_tablename = '<div class="'+btnSpClass+'"></div>';
					hmtl_tablename += "<table id='ex2' class='detailtable table table-bordered' style=\"display:none\">";
					var html_th = "<thead>";
					var html_th_tr;
					var ul = "";
					
					var createdRow_array = [

												6
										];
					var valprice = 6;
					var tabpriceheight = true;
					//	write html
					html_th_tr = "<th width='4%'></th>";
					html_th_tr += "<th width='10%'>วันทำรายการ</th>";
					html_th_tr += "<th width='20%'>เลขที่</th>";
					html_th_tr += "<th width='20%'>ชื่อ-นามสกุล</th>";
					html_th_tr += "<th width='15%'>เบอร์ติดต่อ</th>";
					html_th_tr += "<th width=''>ชื่อสินค้า</th>";
					html_th_tr += "<th width='5%'>จำนวน</th>";

					html_th_tr += "<th width='5%'>ช่องทาง</th>";
					html_th_tr += "<th width='6%'>รูป</th>";
					
					var btn_specialbill = "";
					var loading = '<div class="text-center" data="loading"><div class="text-center spinner-grow spinner-grow-sm text-primary"></div><div class="text-center spinner-grow spinner-grow-sm text-primary"></div><div class="text-center spinner-grow spinner-grow-sm text-primary"></div></div>';
					
					//
					//	For button special
					//	select product list
					var codeid = $(this).attr('data-id');
					var tablename = $('#table').val();
					
                  
					
					var input_summary_product = $('input#bill_summary_product');

					if(input_summary_product.val() == 1 && reporttype == 'bill_summary'){
						
						$.post("ajaxProductList", {})
						.done(function(result) {
							var obj = jQuery.parseJSON(result);
							var selected;
							
							if(summary_productid == "all"){
								selectedall = 'selected';
							}else{
								selectedall = '';
							}
							
							btn_specialbill += '<div class="form-group">';
							btn_specialbill += '<select class="form-control form-control-sm" id="selectproduct" name="selectproduct">';
							btn_specialbill += '<option value="">เลือกรายการสินค้า</option>';
							btn_specialbill += '<option value="all" '+selectedall+' >เลือกทั้งหมด</option>';
							
							obj.data['arrayresult'].forEach(function(e){
								
								//	check select	
								if(summary_productid == e.id){
									selected = 'selected';
								}else{
									selected = '';
								}
								
								btn_specialbill += '<option value="'+e.id+'" '+selected+'>'+e.name+'</option>';
							});
							
							btn_specialbill += '</select>';
							btn_specialbill += '</div>';
						})
						
						
					}
					
					var hmtl_table = "";
					hmtl_table += hmtl_tablename+html_th+html_th_tr;
					hmtl_table += loading;
				
					// $('.tab-content').html(hmtl_table);
					$('#custom-tabs-three-product').html(hmtl_table);

					setTimeout(function(){
						if(productid != null){
						var dataTable = $('#ex2').DataTable({  
							// "serverSide": true,
							// "processing": true,  
							"paging": true,
							"lengthChange": false,
							"searching": true,
							"ordering": true,
							"order": [[ 0, 'desc' ]],
							"select": true,
							"info": true,
							"autoWidth": false,
							"ajax":{  
									url:"<?php echo site_url() . 'mod_retailreport/ctl_report/fetch_dataMKT'; ?>",  
									type:"GET",
									data:{
										valdate:valdate, 
										valdateto:valdateto,
										statusproduct: statusproduct,
										productid: productid,
										table: reporttype
									}
							},
							//	call back data before process
							"stateSave": true,
							"stateSaveCallback": function (settings, data) {
								 $('[data-toggle="popover"]').popover();
								// Send an Ajax request to the server with the state object
								/* $.ajax( {
								  "url": "/state_save",
								  "data": data,
								  "dataType": "json",
								  "type": "POST",
								  "success": function () {}
								} ); */
							},
							
							//	load finish
							"initComplete": function(settings, json) {
								// $('[data-toggle="popover"]').popover();
								$('*[data=loading]').hide();
								var totalJSON = json.recordsTotal;
								if(totalJSON){
									// var outreport = "<button type=\"button\" id=\"btn_report\" class=\"btn btn-outline-primary btn-sm pull-left\">ออกรายงาน</button>";
									// $('.table-responsive').prepend(outreport);
								}else{
									$('.table-responsive').empty();
								}
								//	find data in json 
								// console.log(json.data[39][5]);
							},
							//	data load before 
							"preDrawCallback": function( settings ) {
								$('[data-toggle="popover"]').popover('hide');
							},
							//	data load after 
							"rowCallback": function( row, data) {
								if(createdRow_array && tabpriceheight == true){
									var priceInt = numberWithCommas(data[valprice]);
									if ( data[valprice] >= 1500 ) {
										$(row).css('background','#A1D991');
									}
									if ( data[valprice] == 0 ) {
										$(row).css('background','#ffc8cd');
									}
								}
							},
							"createdRow": function ( row, data, index ) {
								if(createdRow_array){
									var result;
									//	convert number to comma
									$.each(createdRow_array, function(index,value) {
									  result = numberWithCommas(data[value]);
									  $('td', row).eq(value).html(result);
									});
								}
							},
							dom:
								"<'row'<'col-sm-6 btn-sm'B><'col-sm-6 form-control-sm'f>>" +
								"<'row'<'col-sm-12 small'tr>>" +
								"<'row'<'col-sm-4 small'i><'col-sm-4 d-flex justify-content-center small'l><'col-sm-4 small'p>>",
							buttons: [
								'print',
								{
									extend: 'collection',
									text: 'Export',
									buttons: [ 'excel', 'pdf', 'copy' ],
									fade: true
								},
								{
									extend: 'collection',
									text: 'Tool',
									buttons: [ 'columnsToggle', 'colvisRestore' ],
									fade: true
								}
							],
							"columnDefs":[  
								{  
									// "orderable": true, 
									"targets": 0 
								}
							]
							/* "columns": [
								{ "orderable": false },
								null
							] */
						});
						
						}	//	if product
						/*
						//	click table
						$('#ex1 tbody').on('click', 'tr', function () {
							var data = dataTable.row( this ).data();
						} );
						*/
						$('#ex2').fadeIn('slow');
						$('div[data=loading]').hide();
						/* 
						//	refresh data table
						setInterval( function () {
							dataTable.ajax.reload();
						}, 3000 );
						 */
						//
						//	insert button special
						$('.'+btnSpClass).html(btn_specialbill);
						
					}, 1000);
				
				}		//	end function dataTableDetail
				
            })
			
			
			
			/* $(document).ajaxComplete(function() {
				
			}); */
			function numberWithCommas(x) {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}
			
			function selectAllCheckbox(){
				$('#windowlist .modal-body input[type=checkbox]').attr('checked',true);
			}
			
			$('body').on('click', function (e) {
				//e.stopImmediatePropagation();
				$('[data-toggle="popover"]').each(function () {
					// hide any open popovers when the anywhere else in the body is clicked
					if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
						$(this).popover('hide');
					}
				});
			});
        </script>
	</body>
</html>
 