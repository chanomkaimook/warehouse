
<!DOCTYPE html>
<html lang="en">
   	<head> 
        <?php include("structer/backend/head.php"); ?>
        <style>
            .modal-lg, .modal-xl {
                max-width: 1080px;
            }
            .bnt-CA001 {
                text-align: right;
                 border-radius: 5px;
             }
            .list-CA001, .status-CA001{
                padding: 0.5rem;
            }
            
            .btn-app3 {
                border-radius: 3px;
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                color: #6c757d;
                font-size: 12px;
                 min-width: 80px;
                position: relative;
                text-align: center;
            }
            .mb-1, .my-1 {
                margin-bottom: 1rem!important;
            }
            .btn-defaultnl {
                /* background-color: #f8f9fa; */
                border-color: #ddd0;
                /* color: #444; */
            }
            .modal-footer {
                display: -ms-flexbox;
                display: flex;
                -ms-flex-align: center;
                align-items: center;
                -ms-flex-pack: end;
                justify-content: flex-end;
                padding: 1rem 0 0;
                border-top: 1px solid #e9ecef;
                border-bottom-right-radius: .3rem;
                border-bottom-left-radius: .3rem;
            }
            .swal2-header {
                padding: 1rem;
                border-bottom: 1px solid #dee2e600;
            }
            .is-warning {
                border: 1px solid #ff5434;
            }
            .div-ems {
                padding: 1rem;
                margin: 0.5rem;
                border: 1px solid;
                border-radius: 20px;
                min-height: 130px;
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
                            <li class="breadcrumb-item"><a href="<?php echo site_url('mod_admin') ?>/ctl_admin/backend_main">Home</a></li>
                            <li class="breadcrumb-item active"><?php echo $submenu; ?></li>
						</ol>
					</div>
					</div>
				</div> 
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
                                <form id="demo2" name="demo2" class="demo" action="sentformems_print"  enctype="multipart/form-data" accept-charset="utf-8"  method="post" target="_blank">
                                    <div class="card-body">
                                        <div class="row" style="background-color: rgba(140, 175, 255, 0.15); padding: 20px; margin: 1px;border: 1px solid #87aaff; ">
                                            
                                            <div class="col-sm-12 text-right">
												<?php
													$report_ems = chkPermissPage('report_ems');
													if($report_ems == 1):
												?>
                                                <input type="button" id="frmcheck" class="btn btn-default btn-sm" value='เช็ครายการ'>
												<input type="button" id="frmscg" class="btn btn-secondary btn-sm" value='ฟอร์ม SCG '>
												<input type="button" id="frmflash" class="btn btn-warning btn-sm" value='ฟอร์ม FLASH'>
												<input type="button" id="frmexpress" class="btn btn-info btn-sm" value='ฟอร์ม IEL'>
                                                <?php
													endif;
												?>
												<input type="submit" class="btn btn-default btn-sm" value='ออกใบส่ง EMS '>
                                                <br>
                                            </div>
                                            
                                            <div class="col-sm-6">
                                                
                                                <label class=""> เลือกวันที่ออกบิล (ระหว่างวันที่) </label>
                                                <div class="input-group ">
                                                    <input type="date" class=" form-control form-control-sm" id="valdate">
                                                    <input type="date" class=" form-control form-control-sm" id="valdateTo">
                                                </div>
                                                
                                            </div>

                                            <div class="col-sm-6">

                                                <label class=""> เลือกรูปแบบการจัดส่ง </label>
                                                <div class="input-group input-group-sm">
                                                    <select class="custom-select " name="deliveryid" id="deliveryid">
														<option value=""> เลือกรูปแบบการจัดส่ง </option>
														<?php
															$sql = $this->db->select('*')
															->from('delivery')
															->where('status',1)
															->get();
															foreach($sql->result() as $row){
																echo '<option value="'.$row->ID.'"> '.$row->NAME_US.' </option>';
															}
														?>
                                                    </select>
                                                
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-default btn-sm" id="bntvaldate"><i class="fas fa-search"></i> Search </button>
                                                        <button type="button" class="btn btn-default btn-sm" id="refresh_page"><i class="fas fa-refresh"></i> Refresh </button>
                                                    </div>
                                                </div>

                                            </div>
                                            
                                        </div>
                                        <hr>
                                        <div class="">
                                            <ul class="nav nav-pills pull-left mb-1">

                                                <li class="nav-item" style="background-color: #33333317;border-radius: 5px;margin: 0 0.1rem;">
                                                    <button type="button" class="btn btn-defaultnl btn-sm nav-link active" data-toggle="tab" value="1" id="statuscomplete"> รอการอนุมัติ </button>
                                                </li>
                                                <li class="nav-item" style="background-color: #33333317;border-radius: 5px;margin: 0 0.1rem;">
                                                    <button type="button" class="btn btn-defaultnl btn-sm nav-link" data-toggle="tab" value="2" id="statuscomplete"> อนุมัติสำเร็จ </button>
                                                </li>
                                                
                                            </ul>
                                        </div>
                                        
                                        <div class="table-responsive"> 
                                            <table id="ex1" class="table table-bordered  ">  
                                                <thead>  
                                                    <tr>  
													
                                                        <th width="2%">เลือก</th>  
                                                        <th width="40%"> ออเดอร์ </th> 
                                                        <th> รายละเอียด </th>  
													<!--
													<th width="40%"> ออเดอร์ </th> 
													-->
                                                    </tr>  
                                                </thead>  
                                            </table>  
                                        </div>
 
                                    </div> 
                                </form>
							</div>
						</section>
 
					</div>
				</div> 
            </section>
               
		</div>
		<?php include("structer/backend/footer.php"); ?>
		<?php include("structer/backend/script.php"); ?>
        <script src="<?php echo $base_bn;?>plugins/sweetalert2/sweetalert2.min.js"></script>
        </div>
        <script>
            
            $(function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
               
			   var valdate = "";
				var valdateTo = "";
				var deliveryid = "";
				
                sentformemslist();
                $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                    event.preventDefault();
                        $(this).ekkoLightbox({
                            alwaysShowClose: true
                        });
                    });
 
                    $('.btn[data-filter]').on('click', function() {
                    $('.btn[data-filter]').removeClass('active');
                    $(this).addClass('active');
                });
                
				$(document).on('click', '#textchkall', function(event) {
				
					var checkedStatus = $('#chkall');
					(checkedStatus.prop("checked")==true)?checkedStatus.prop("checked", false):checkedStatus.prop("checked", true); 
					  
					  var checkBox = checkedStatus.prop('checked');
					  $('#ex1 tbody tr').find('td:first :checkbox').each(function() {
						$(this).prop('checked', checkBox);
					  });
				}); 
				
				$(document).on('click', '#chkall', function(event) {
				  var checkedStatus = this.checked;
				  $('#ex1 tbody tr').find('td:first :checkbox').each(function() {
					$(this).prop('checked', checkedStatus);
				  });
				});
                
                //----------------------------filter--------------------------//
                $(document).on('click', '#bntvaldate', function(event) {
					 valdate = $('#valdate').val();
					 valdateTo = $('#valdateTo').val();
					 deliveryid = $('#deliveryid').val();
                    $('#ex1').DataTable().destroy();
                    sentformemslist(valdate, valdateTo, deliveryid, null);
                }); 
                $(document).on('click', '#statuscomplete', function(event) {
                    var val = this.value;
                    valdate = $('#valdate').val();
					 valdateTo = $('#valdateTo').val();
					 deliveryid = $('#deliveryid').val();
                    // sentformemslist(null, null, null, val);
                    sentformemslist(valdate, valdateTo, deliveryid, val);
                }); 
 
                $(document).on('click', '#refresh_page', function(event) {
                    $('#ex1').DataTable().destroy();
                    sentformemslist();  
                }); 
				
				$(document).on('click', '#frmscg', function(event) {
                   var d = document;
				   $('#demo2').attr('action', 'report_scg');
				   d.demo2.submit();
                });
				
				$(document).on('click', '#frmflash', function(event) {
                   var d = document;
				   $('#demo2').attr('action', 'report_flash');
				   d.demo2.submit();
                });
				
				$(document).on('click', '#frmexpress', function(event) {
                   var d = document;
				   $('#demo2').attr('action', 'report_express');
				   d.demo2.submit();
                });
				
				$(document).on('click', '#frmcheck', function(event) {
                   var d = document;
				   $('#demo2').attr('action', '<?php echo site_url('mod_retailcheck/ctl_retailcheck/checkorder');?>');
				   d.demo2.submit();
                });
 
                function sentformemslist(valdate, valdateTo, deliveryid, statuscomplete) {
					// console.log(valdate+" - "+valdateTo+" - "+deliveryid+" - "+statuscomplete+" ::");
					$('#ex1').DataTable().destroy();
					var dataTable = $('#ex1').DataTable({  
                        // "processing":true,  
                        // "serverSide":true,  
                        "order":[],  
						"lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]  ],
                        "ajax":{  
                                url:"<?php echo base_url() . 'mod_retailcreateorder/ctl_sentformems/fetch_sentformems'; ?>",  
                                type:"POST",
                                data:{
                                    valdate:valdate, 
									valdateTo:valdateTo,
									deliveryid: deliveryid,
									statuscomplete: statuscomplete
                                } 
                        },
						dom:
                            "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
							"<'row'<'col-sm-12'tr>>" +
							"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
						,
						// dom:'<"top"i>rt<"bottom"flp><"clear">',
                        "columnDefs":[  
                                {  
                                    "targets":0,  
                                    "orderable":false,  
                                },  
                        ],  
                    });  
					$('.dataTables_length').append('<span id="" class="mx-3"><input id="chkall" name="chkall" type="checkbox" class="form-group"> <span id="textchkall">เลือกทั้งหมด</span></span>');
                }
				
				
            })
        </script>
	</body>
</html>
 