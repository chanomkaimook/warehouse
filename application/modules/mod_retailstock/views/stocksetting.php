<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("structer/backend/head.php"); ?>
	<link rel="stylesheet" href="<?php echo $base_bn; ?>frontend/bootstrap-select/css/bootstrap-select.css">
	<!--    DataTable    -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('asset/plugin/datatablebutton') . '/datatables.min.css'; ?>" />

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
							<h1>retail</h1>
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

											<h5 class="header-title">Stock</h5>
											<p class="sub-header">stock system for check quanity item from chockchai steakhouse online. </p>

											<div class="table-responsive">
												<table class="table table-centered mb-0 ex1" id="btn-editable">
													<caption id="tablecaption">เอกสารรายงานคลังสินค้า chokchai steakhouse online เมื่อวันที่ <?php echo thai_date($get_date); ?></caption>
													<thead>
														<tr>
															<th>#</th>

															<th>ID</th>
															<th>สินค้า</th>
															<th>Min</th>
															<th>Max</th>
															<th>โดย</th>
														</tr>
													</thead>

													<tbody>

													</tbody>
												</table>
											</div> <!-- end .table-responsive-->
										</div> <!-- end card-body -->
									</div> <!-- end card -->
								</div> <!-- end col -->
							</div> <!-- end row -->
						</section>

					</div>
				</div>
			</section>

		</div>
		<?php include("structer/backend/footer.php"); ?>
		<?php include("structer/backend/script.php"); ?>
		<?php include("structer/backend/script_tablelayout.php"); ?>
		<!--    DataTable    -->
		<script type="text/javascript" src="<?php echo base_url('asset/plugin/datatablebutton') . '/datatables.min.js'; ?>"></script>

		<script src="<?php echo $base_bn; ?>plugins/sweetalert2/sweetalert2.min.js"></script>



	</div>
	<script>
		const queryString = decodeURIComponent(window.location.search);
		const params = new URLSearchParams(queryString);
		let getDate = params.get("date");

		$(function() {

			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000
			});

			/**
			 * 
			 * modal-detail
			 * 
			 */
			let modal = '.modal-detail';

			function resetDataTable(name) {
				$(name).DataTable()
					.search('')
					.order([
						[0, 'asc']
					])
					.draw();
			}

			/**
			 * 
			 * end modal
			 * 
			 */


			dataList();
			//----------------------------filter--------------------------//

			var table = $('.ex1').DataTable();
			// table.order( [ 3, 'desc' ] ).draw();
			function dataList() {

				let date_ob = new Date();
				let date = ("0" + date_ob.getDate()).slice(-2);
				let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
				let year = date_ob.getFullYear();
				let datenow = year + "-" + month + "-" + date;
				//
				//  set index column
				let tableColumn = [];
				$('table thead tr th').each(function(index) {
					tableColumn[$(this).text()] = [$(this).index()];
				});

				let tableColumnTotal = [tableColumn['Min'], tableColumn['Max']];

				//  function on script_tablelayout
				var moniter = tableLayout();

				var dataTable = $('.ex1').DataTable({
					"processing": false,
					"serverSide": false,
					"order": [
						[0, "asc"]
					],
					"ajax": {
						url: "<?php echo base_url() . 'mod_retailstock/ctl_retailstock/fetch_setting'; ?>",
						type: "POST",
						data: {
							date: ""
						},
						error: function(xhr, error, code) {
							//  xhr return array status async
							if (xhr.status != 200) {
								alert('พบข้อผิดพลาด กรุณาแจ้งเจ้าหน้าที่');
							}
						}
					},
					autoWidth: false,
					scrollCollapse: false,
					dom: "<'row'<'col-sm-4 btn-sm'B><'col-sm-4 btn-sm toolbar text-center'><'col-sm-4'f>>" +
						"<'row'<'col-sm-12 small'tr>>" +
						"<'row'<'col-sm-4 small'i><'col-sm-4 text-center d-flex justify-content-center smalll'><'col-sm-4 small'p>>",
					/* dom:
					    '<"fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr"lfr>'+
					    't'+
					    '<"fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-bl ui-corner-br"ip>', */
					buttons: [{
							extend: 'collection',
							text: '<i class="far fa-eye"></i>',
							titleAttr: 'view',
							tag: 'span',
							buttons: ['columnsToggle', 'colvisRestore'],
							fade: true
						},
						{
							extend: 'print',
							text: '<i class="fas fa-print"></i>',
							title: 'Data Stock :' + datenow,
							titleAttr: 'print',
							customize: function(win) {
								$(win.document.body)
									.css('font-size', '10pt')
									.prepend(
										'<img src="<?php echo base_url('asset/images/front/logo/logo_ver_2.png'); ?>" style="position:absolute; top:0; right:20px;opacity:0.5;height:80px" />'
									);

								$(win.document.body).find('table')
									.addClass('compact')
									.css('font-size', 'inherit');
							}
						}

					],
					"columnDefs": [

						{
							width: "3rem",
							class: "stock-min",
							"targets": 3
						},
						{
							width: "3rem",
							class: "stock-max",
							"targets": 4
						}
					],
					"createdRow": function(row, data, dataIndex) {
						$('td', row).eq(3).addClass('text-right');
						$('td', row).eq(4).addClass('text-right');
					},
					"initComplete": function(settings, json) {
						// console.log(json);

						//  https://markcell.github.io/jquery-tabledit/#examples
						tableEdit();

						$('td div.tabledit-toolbar').css('width', '6rem');

					},
					"paging": false,
				});
			}

			function tableEdit() {
				let str = $('#permisspage').val();
				let findPermit = str.includes("stocksetting")
				if (findPermit || str == 'all') {
					$("#btn-editable").Tabledit({
						url: 'ajax_updatestock_setting',
						buttons: {
							edit: {
								class: "btn btn-info px-2 mr-1",
								html: '<i class="nav-icon fa fa-pencil" aria-hidden="true"></i>',
								action: "edit"
							}
						},
						inputClass: "form-control form-control-sm",
						deleteButton: !1,
						saveButton: 1,
						autoFocus: !1,
						hideIdentifier: true,
						columns: {
							identifier: [1, 'id'],
							editable: [
								[3, "min"],
								// [4, "max"]
							]
						},
						// Executed when the ajax request is completed
						onSuccess: function(data, textStatus, jqXHR) {
							// deal with success there
							if (data['error'] == 1) {
								Swal.fire({
									type: 'warning',
									title: 'แจ้งเตือน',
									text: data['txt'],
								}).then((result) => {
									$('tr#' + data["id"] + ' .stock-min span.tabledit-span').html('<font class="text-danger">fail</font>');
									$('tr#' + data["id"] + ' button.tabledit-edit-button').trigger('click');
								})

							} else {

							}
						},
						onAjax: function(actions, serialize) {
							// console.log(actions + " " + serialize);
						},

					})
				}
			}

		})

		$(document).on('keypress','input.tabledit-input',function(event){
			event.stopPropagation;
			event.stopImmediatePropagation;

			return checkNumber(event);
		})

		function ajax_staticReload() {
			$.post("ajax_staticReload", {
					date: getDate
				})
				.done(function(data, status, error) {
					if (error.status == 200) { //	status complete
						var obj = jQuery.parseJSON(data);

						$('span#static-claim').text(obj.data.total_claim);
						$('span#static-loss').text(obj.data.total_loss);
						$('span#static-repack').text(obj.data.total_repack);
						$('span#static-other').text(obj.data.total_other);
					}

				})
				.fail(function(xhr, status, error) {
					// error handling
					alert('พบความผิดปกติ ระบบจะทำการ Reload');
					// window.location.reload();
				});
		}
		//----------------------------function--------------------------//
		function checkNumber(ele) {
			var vchar = String.fromCharCode(event.keyCode);
			console.log(vchar);
			if (vchar < '0' || vchar > '9') {
				return false
			}

		}
	</script>
	<!-- Table Editable plugin-->
	<script src="<?php echo base_url() . 'asset/libs/jquery-tabledit/jquery.tabledit.min.js'; ?>"></script>
</body>

</html>