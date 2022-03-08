<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("structer/backend/head_dttable.php"); ?>
	<link rel="stylesheet" href="<?php echo $base_bn; ?>frontend/bootstrap-select/css/bootstrap-select.css">
	<!--    DataTable    -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('asset/plugin/datatablebutton') . '/datatables.min.css'; ?>" />
	

	<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css">

	<style>
		.selectstyle {
			height: calc(1.25rem + 2px);
			font-size: 0.7rem;
			padding: 0rem .75rem;
			width: 25%;
		}

		.bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
			width: 100%;
		}

		.btn-light {
			color: #1f2d3d;
			background-color: #ffffff;
			border: 1px solid #ced4da;
			box-shadow: none;
			font-size: 0.7rem;
		}

		table tbody td {
			cursor: pointer;
		}

		.modal-detail-result {
			max-height: 10rem;
			height: 10rem;
			overflow: auto;
		}

		/* Ensure that the demo table scrolls */
		th,td{
			white-space: nowrap;
		}
		div.dataTables_wrapper {
			width:100%;
			margin: 0 auto;
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
							<h1><?php echo $submenu;?></h1>
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
							<div class="row">
								<div class="col-12">
									<div class="card">
										<div class="card-body">

											<?php
												require_once('option.php');
											?>

											<!-- <input type="date" class="form-control datetimepicker-input" data-target="#reservationdate" name="fecha_evento" data-date-format="dd/mm/yyyy" /> -->
											<div class="tables">

											</div> <!-- end .table-responsive-->
										</div> <!-- end card-body -->
									</div> <!-- end card -->
								</div> <!-- end col -->
							</div> <!-- end row -->
						</section>

					</div>
				</div>
				<form action="ajax_report" id="frm" name="frm">
					<input type="hidden" id="date" name="date" value="<?php echo $get_date; ?>">
					<input type="hidden" id="date_cut" name="date_cut" value="<?php echo $date_cut; ?>">
					
					<input type="hidden" id="calendar_length" name="calendar_length" value="<?php echo $totaldays;?>">
				</form>
			</section>

			<!--	Modal	-->
			<div class="modal fade modal-detail" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header bg-info">
							<h4 class="modal-title mt-0">รายละเอียด</h4>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						</div>
						<div class="modal-body">
							<div class="loading"> Loading... </div>

							<div class="modal-detail-result">
								<ul class="list-group">

								</ul>
							</div>

						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

		</div>
		<?php include("structer/backend/footer.php"); ?>
		<?php include("structer/backend/script.php"); ?>
		<?php include("structer/backend/script_tablelayout.php"); ?>
		<!--    DataTable    -->
		<script type="text/javascript" src="<?php echo base_url('asset/plugin/datatablebutton') . '/datatables.min.js'; ?>"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>

		<script src="<?php echo $base_bn; ?>plugins/sweetalert2/sweetalert2.min.js"></script>
	</div>

    <?php require_once('script_stock_month.php'); ?>
	<?php require_once("script_stock.php"); ?>

</body>

</html>