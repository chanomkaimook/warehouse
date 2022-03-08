
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
						<h1><?php echo $mainmenu; ?></h1>
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
							
							<div class="card card-default">
								<div class="card-header">
									<h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i> <?php echo "Manage ".$mainmenu; ?> </h3>
								</div>
								<div class="card-body">
									<div class="d-flex flex-wrap">
										<div class="tablink p-4 m-2 bg-info text-center col" data-link="report_c" style="cursor:pointer">
											<h1 class="">Report</h1>
											<span>รายงานแบบใหม่</span>
										</div>
										<div class="tablink p-4 m-2 bg-warning text-center col" data-link="sentformems" style="cursor:pointer">
											<h1 class="">EMS</h1>
											<span>ใบแปะหน้าแบบเก่า</span>
										</div>
									</div>
								</div>
							</div>
							
						</section>
 
					</div>
				</div> 
            </section>
			<form id="frmreport" name="frmreport" method="post" action="<?php echo site_url('mod_retailcreateorder/ctl_sentformems/report_c');?>">
				<input id="urla" name="urla" type="hidden" value="">
			</form>
			<form id="frmems" name="frmems" method="post" action="<?php echo site_url('mod_retailcreateorder/ctl_sentformems/sentformems');?>">
				<input id="urla" name="urla" type="hidden" value="">
			</form>
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
               
                $(document).on('click', '.tablink', function(event) {
                    var d = document;
                    var url = $(this).attr('data-link');

					if(url == 'report_c'){
						d.frmreport.submit();
					}
					
					if(url == 'sentformems'){
						d.frmems.submit();
					}
					
                }); 
            })
        </script>
	</body>
</html>
 