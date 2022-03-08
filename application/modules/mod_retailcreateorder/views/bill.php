
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
            .divstatus-claim {
                padding: 0.2rem;
                background-color: #FF9800;
                border-radius: 5px;
                color: #FFF;
                width: 40%;
                text-align: center;
                margin: 0.5rem 0 0;
                font-weight: 100;
            }
            .divstatus-claim2 {
                padding: 0.2rem;
                background-color: #8BC34A;
                border-radius: 5px;
                color: #FFF;
                width: 35%;
                text-align: center;
                margin: 0.5rem 0 0;
                font-weight: 100;
            }
            .divstatus-claim3 {
                padding: 0.2rem;
                background-color: #9E9E9E;
                border-radius: 5px;
                color: #FFF;
                width: 35%;
                text-align: center;
                margin: 0.5rem 0 0;
                font-weight: 100;
            }
            .divstatus-claim4 {
                padding: 0.2rem;
                background-color: #F44336;
                border-radius: 5px;
                color: #FFF;
                width: 40%;
                text-align: center;
                margin: 0.5rem 0 0;
                font-weight: 100;
            }
            .st-claim-6{
                background-color: #ffd6d3;
                padding: 0.5rem;
                border-radius: 5px;
            }
            .st-claim-5{
                background-color: #e6e6e6;
                padding: 0.5rem;
                border-radius: 5px;
            }
            .st-claim-4{
                background-color: #FFE0B2;
                padding: 0.5rem;
                border-radius: 5px;
            }
            .st-claim-2 {
                background-color: #DCEDC8;
                padding: 0.5rem;
                border-radius: 5px;
            }
            .swal2-header {
                padding: 1rem;
                border-bottom: 1px solid #ffffff;
            }
            .span-Status-001 {
                font-weight: 300;
                color: #ffffff;
                padding: 0.1rem 0.5rem;
                background-color: #FF9800;
                border-radius: 10px;
            }
            .span-Status-002 {
                font-weight: 300;
                color: #ffffff;
                padding: 0.1rem 0.5rem;
                background-color: #2196f3;
                border-radius: 10px;
            }
            .badge a {
                color:inherit !important
            }
            @media screen and (max-width: 991px){
                .divstatus-claim {  width: 100%; }
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
								<div class="card-body">
                                    <div class="row" style="background-color: rgba(140, 175, 255, 0.15); padding: 20px; margin: 1px;border: 1px solid #87aaff; ">
                                        <?php
											$billinsert = chkPermissPage('createbill');
											if($billinsert == 1):
										?>
										<div class="col-sm-12 text-right"> 
                                            <a href="<?php echo site_url('mod_retailcreateorder') ?>/ctl_createorder/createbill " class="btn btn-primary btn-sm"><li class="fa fa-file-text-o"></li> สร้างบิล </a>
                                        </div>
										<?php
											endif;
										?>
                                        <div class="col-sm-6">

                                            <label class=""> เลือกวันที่</label>
                                            <div class="input-group ">
                                                <input type="date" class=" form-control form-control-sm" id="valdate">
                                                <input type="date" class=" form-control form-control-sm" id="valdateTo">
                                            </div>
                                            
                                        </div>

                                        <div class="col-sm-6">

                                            <label class=""> เลือกรูปแบบ </label>
                                            <div class="input-group input-group-sm">
                                                <select class="custom-select " name="deliveryid" id="deliveryid">
                                                    
                                                    <?php
														$sql = $this->db->select('*')
														->from('delivery')
														->where('id',1)
														->where('status',1)
														->get();
														foreach($sql->result() as $row){
															echo '<option value="'.$row->ID.'" selected> '.$row->NAME_US.' </option>';
														}
													?>
                                                </select>
                                                <select class="custom-select " name="method_order" id="method_order">
                                                    <!-- <option value=""> เลือกสาขา </option> -->
                                                    <?php
														$sql = $this->db->select('*')
														->from('retail_methodorder')
                                                        ->where('status',1);
                                                        if($this->session->userdata('franshine')){
                                                            $sql->where('id',$this->session->userdata('franshine'));
                                                        }
														$q = $sql->get();
														foreach($q->result() as $row){
															echo '<option value="'.$row->ID.'"> '.$row->TOPIC.' </option>';
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
                                            <li class="nav-item" style="background-color: #33333317;border-radius: 5px;margin: 0.1rem;">
                                                <button type="button" class="btn btn-defaultnl btn-sm nav-link active" data-toggle="tab" value="1" id="statuscomplete"> รอการอนุมัติ </button>
                                            </li>
                                            <li class="nav-item" style="background-color: #33333317;border-radius: 5px;margin: 0.1rem;">
                                                <button type="button" class="btn btn-defaultnl btn-sm nav-link" data-toggle="tab" value="2" id="statuscomplete"> อนุมัติสำเร็จ </button>
                                            </li>
                                            <li class="nav-item" style="background-color: #33333317;border-radius: 5px;margin: 0.1rem;">
                                                <button type="button" class="btn btn-defaultnl btn-sm nav-link" data-toggle="tab" value="3" id="statuscomplete"> ยกเลิกรายการ </button>
                                            </li>
 										</ul>
									</div>
                                     
                                    <div class="table-responsive"> 
                                        <table id="ex1" class="table table-bordered  ">  
                                            <thead>  
                                                <tr>  
                                                    <th width="5%">#</th>  
                                                    <th>รายการ</th>  
                                                </tr>  
                                            </thead>  
                                        </table>  
                                    </div>

                                     
								</div> 
							</div>
						</section>
 
					</div>
				</div> 
            </section>
            
            <!-- Modal Delete Order -->
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-calendar-times-o" aria-hidden="true"></i> ยกเลิกออเดอร์</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="demo2" name="demo2" class="demo"  enctype="multipart/form-data" accept-charset="utf-8"  method="post">
                                <input type="hidden" id="hdfdeleteorder" name="hdfdeleteorder" value="">  
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label class="">หมายเหตุ : </label>
                                        <textarea class="form-control" rows="3" name="remark" id="remark" placeholder="คำอธิบาย..."></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" id="confirmdelete"> <i class="fa fa-floppy-o" aria-hidden="true"></i> ยืนยันการยกเลิก </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Claim -->
            <div class="modal fade" id="modalclaim" tabindex="-1" role="dialog" aria-labelledby="modalclaimTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-calendar-times-o" aria-hidden="true"></i> ยกเลิกออเดอร์</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="demo2" name="demo2" class="demo" action="<?php echo site_url('mod_retailcreateorder/ctl_createorder/claimorder_update'); ?>"  enctype="multipart/form-data" accept-charset="utf-8"  method="post">
                            <input type="hidden" id="hdfclaimorder" name="hdfclaimorder" value="">
                            <div class="modal-body">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <!-- <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="customRadio1" name="customRadio" value="1">
                                                <label for="customRadio1" class="custom-control-label">ลูกค้าติดปัญหา</label>
                                            </div> -->
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="customRadio2" name="customRadio" value="2">
                                                <label for="customRadio2" class="custom-control-label"> เคลมแก้ไขรายการ <small>(แก้ไขรายการบิลได้)</small></label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="customRadio3" name="customRadio" value="3">
                                                <label for="customRadio3" class="custom-control-label"> เคลมยกเลิกรายการ <small>(เฉพาะยกเลิกรายการเท่านั้น)</small></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="">หมายเหตุ : </label>
                                        <textarea class="form-control" rows="3" name="claim_remark" id="claim_remark" placeholder="คำอธิบาย..." required></textarea>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-cremark1" data-dismiss="modal" id="confirmclaim"> <i class="fa fa-floppy-o" aria-hidden="true"></i> ยืนยันการยกเลิก </button>
                                <input type="submit" class="btn btn-default btn-cremark2" value="แก้ไขรายการเคลม">
                                <button type="button" class="btn btn-default btn-cremark3" data-dismiss="modal" id="confirmclaim"> <i class="fa fa-floppy-o" aria-hidden="true"></i> ยืนยันการเคลม (*เก็บเงินที่หลัง) </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		 
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

                $('#confirmclaim').prop('disabled', true);
                 $("#claim_remark").keyup(function( event ) {
                    if(this.value.length > 4){
                        $('#confirmclaim').prop('disabled', false);
                    } else {
                        $('#confirmclaim').prop('disabled', true);
                    }
                });
                
                // ======== CLAIM ======= //
                $('.btn-cremark1').hide();
                $('.btn-cremark2').hide();
                $('.btn-cremark3').hide();
                
                $(document).on('click', '#customRadio1', function(event) {
                    $('#claim_remark').val('');
                    $('#claim_remark2').val('');
                    $('.btn-cremark1').show();
                    $('.btn-cremark2').hide();
                    $('.btn-cremark3').hide();
                });

                $(document).on('click', '#customRadio2', function(event) {
                    $('#claim_remark').val('');
                    $('#claim_remark2').val('');
                    $('.btn-cremark2').show();
                    $('.btn-cremark1').hide();
                    $('.btn-cremark3').hide();
                });

                $(document).on('click', '#customRadio3', function(event) {
                    $('#claim_remark').val('');
                    $('#claim_remark2').val('');
                    $('.btn-cremark2').hide();
                    $('.btn-cremark1').hide();
                    $('.btn-cremark3').show();
                });
                // ======================= //

                createorderlist();
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
                
                //----------------------------filter--------------------------//
                $(document).on('click', '#bntvaldate', function(event) {
                    var valdate = $('#valdate').val();
                    var valdateTo = $('#valdateTo').val();
                    var deliveryid = $('#deliveryid').val();
                    var methodorder = $('#method_order').val();
                    $('#ex1').DataTable().destroy();
                    createorderlist(valdate, valdateTo, deliveryid, null, methodorder);
                }); 
                $(document).on('click', '#statuscomplete', function(event) {
                    var val = this.value;
                    $('#ex1').DataTable().destroy();
                    createorderlist(null, null, null, val, null);
                }); 
 
                $(document).on('click', '#refresh_page', function(event) {
                    $('#ex1').DataTable().destroy();
                    createorderlist();  
                }); 
 
                // DELETE ORDER //
                $(document).on('click', '#deleteorder', function(event) {
                    $('#hdfdeleteorder').val(this.value);
                    $('#remark').val('');
                }); 
                $(document).on('click', '#confirmdelete', function(event) {
                    var val = $('#hdfdeleteorder').val();
                    var remark = $('#remark').val();
                    $.post("deleteorder", { id: val, remark: remark }, function(result){
                        var obj = jQuery.parseJSON(result);
                        Toast.fire({
                            type: 'success',
                            title: obj.txt
                        });
                        $('#ex1').DataTable().destroy();
                        createorderlist();  
                    });
                });
                
                // Claim Order //
                $(document).on('click', '#modalclaim-btn', function(event) {
                    $('#hdfclaimorder').val(this.value);
                    $('#claim_remark').val('');
                });

                $(document).on('click', '#confirmclaim', function(event) {
                    var remarkclaim = '';
                    var val = $('#hdfclaimorder').val();
                    var valradio = $("input[name='customRadio']:checked").val();
                    var remark = $('#claim_remark').val();
                    $.post("claimorder", { 
                        id: val, 
                        remark: remark, 
                        valradio: valradio,
                        remarkclaim: remarkclaim
                    }, function(result){
                        var obj = jQuery.parseJSON(result);
                        Swal.fire('Success!', obj.txt, 'success')
                        $(".swal2-confirm").on("click", function (e) {
                            window.location.replace('bill');
                        });
                    });
                });
 
                function createorderlist(valdate, valdateTo, deliveryid, statuscomplete, methodorder) {
                    var dataTable = $('#ex1').DataTable({  
                        "processing":true,  
                        "serverSide":true,  
                        "order":[],  
                        "ajax":{  
                                url:"<?php echo base_url() . 'mod_retailcreateorder/ctl_createorder/fetch_createorder'; ?>",  
                                type:"POST",
                                data:{
                                    valdate:valdate, valdateTo:valdateTo, deliveryid: deliveryid, statuscomplete: statuscomplete, methodorder: methodorder
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

                $(document).on('change', '#deliveryid', function(event) {
                    let method_id = $('select#deliveryid')

                    fetch('get_retailMethod?method_id='+method_id.val())
                    .then(res => res.json())
                    .then((resp) => {
                        // console.log(resp);
                        let text = '<option value=""> เลือกสาขา </option>';
                        if(resp.error_code == 0){
                            text += resp.data;
                            $('select#method_order').html(text);
                        }else{
                            $('select#method_order').html(text);
                        }
                    })
                    .catch(function(error){
                        console.log(`Error ${error}`);
                    })
                });

            })
        </script>
	</body>
</html>
 