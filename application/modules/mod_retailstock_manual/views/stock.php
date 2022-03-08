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

											<p>
												<a class="btn btn-primary" data-toggle="collapse" href="#collapseStatic" role="button" aria-expanded="false" aria-controls="collapseExample">
													สถิติวันนี้
												</a>
												<a id="setting" href='stocksetting' class="btn btn-outline-primary float-right ml-2">
													<i class="fas fa-cog"></i> ตั้งค่า
												</a>
												<?php if (chkPermissPage("rerunstock") == 1) { ?>
													<a id="rerunstock" class="btn btn-danger float-right ml-2">
														อัพเดต stock
													</a>
												<?php } ?>
												<a id="reportstock" class="btn btn-warning float-right">
													ออกรายงาน
												</a>
												
											</p>

											<div class="collapse active show mb-4 border p-2" id="collapseStatic">
												<?php include_once('htmlstatics.php'); ?>

												<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
												<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
												<div class=" mt-4">
													<ul class="nav nav-tabs">
														<li class="nav-item">
															<a href="#home" data-toggle="tab" aria-expanded="false" class="nav-link">
																<span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
																<span class="d-none d-sm-block">stock</span>
															</a>
														</li>
														<li class="nav-item">
															<a href="#pullcut" data-toggle="tab" aria-expanded="true" class="nav-link">
																<span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
																<span class="d-none d-sm-block">รับเข้า-ขาย</span>
															</a>
														</li>
														<li class="nav-item">
															<a href="#offgraph" data-toggle="tab" aria-expanded="false" class="nav-link">
																<span class="d-block d-sm-none"><i class="mdi mdi-settings"></i></span>
																<span class="d-none d-sm-block text-muted">ปิดกราฟ</span>
															</a>
														</li>
													</ul>
												</div>
												<div class="tab-content">
													<style>
														.chartrank {
															position: relative;
															height: 40vh;
															width: auto"; 

														}
													</style>
													<div id="home" class="tab-pane fade in">
														<?php include_once('chartrank.php'); ?>
													</div>

													<div id="pullcut" class="tab-pane fade">
														<?php include_once('chartpullcut.php'); ?>
													</div>

													<div id="offgraph" class="tab-pane fade">

													</div>

												</div>

											</div>





											<div class="table-responsive">
												<table class="table table-centered mb-0 ex1" id="btn-editable">
													<caption id="tablecaption">เอกสารรายงานคลังสินค้า chokchai steakhouse online เมื่อวันที่ <?php echo thai_date($get_date); ?></caption>
													<thead>
														<tr>
															<th>#</th>

															<th>ID</th>
															<th>สินค้า</th>
															<th>คงคลัง</th>
															<th>จำหน่าย</th>
															<th>รับเข้า</th>
															<th>อื่นๆ</th>
															<th>เหลือ</th>
														</tr>
													</thead>

													<tbody>
														<!-- <tr>
                                                            <td>1</td>
                                                            <td>Tiger Nixon</td>
                                                            <td>System Architect</td>
                                                            <td>Edinburgh</td>
                                                            <td>61</td>
                                                            <td>2011/04/25</td>
                                                            <td>$320,800</td>
                                                        </tr> -->
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
				<form action="ajax_report" id="frm" name="frm">
					<input type="hidden" id="date" name="date" value="<?php echo $get_date; ?>">
				</form>
			</section>

			<!--	Modal	-->
			<div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header bg-info">
							<h4 class="modal-title mt-0">รายการคัดออก</h4>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						</div>
						<div class="modal-body">
							<div class="loading"> Loading... </div>
							<form id="frmOther" name="frmOther" method="get" class="form-horizontal">
								<input id="stockid" name="stockid" type="hidden" value="">
								<div class="form-group row">
									<h6 class="col-md-4 lead">เคลม</h6>
									<div class="col-md-8">
										<input id="claim" name="claim" value="" class="form-control form-input-sm" placeholder="กรอกตัวเลข">
										<span class="col-md-8 text-secondary">สินค้าที่ต้องนำไปซ่อม บำรุง ปรับปรุง</span>
									</div>
								</div>

								<div class="form-group row">
									<h6 class="col-md-4 lead">สูญเสีย</h6>
									<div class="col-md-8">
										<input id="loss" name="loss" value="" class="form-control form-input-sm" placeholder="กรอกตัวเลข">
										<span class="col-md-8 text-secondary">สินค้าที่ไม่ได้คุณภาพ เสียหาย ชำรุด</span>
									</div>
								</div>

								<div class="form-group row">
									<h6 class="col-md-4 lead">รีเเพ็ค</h6>
									<div class="col-md-8">
										<input id="repack" name="repack" value="" class="form-control form-input-sm" placeholder="กรอกตัวเลข">
										<span class="col-md-8 text-secondary">สินค้าที่ต้องกลับไป packaging ใหม่</span>
									</div>
								</div>

								<div class="form-group row">
									<h6 class="col-md-4 lead">อื่นๆ</h6>
									<div class="col-md-8">
										<input id="other" name="other" value="" class="form-control form-input-sm" placeholder="กรอกตัวเลข">
										<span class="col-md-8 text-secondary">การนำออกสินค้าในรูปแบบอื่นๆ</span>
									</div>
								</div>

								<div class="form-group row">
									<h6 class="col-md-4 lead">หมายเหตุ</h6>
									<div class="col-md-8">
										<textarea id="other_remark" name="other_remark" value="" class="form-control form-input-sm"></textarea>
										<span class="col-md-8 text-secondary">การนำออกสินค้าในรูปแบบอื่นๆ</span>
									</div>
								</div>
								<?php
								if (chkPermissPage('manage_stock')) {
								?>
									<div class="col-md-12 text-center">
										<button type="button" id="btn_frmOtherSubmit" name="btn_frmOtherSubmit" class="btn btn-md btn-success">บันทึก</button>
									</div>
								<?php
								}
								?>
							</form>

						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<!--	Modal	-->
			<div class="modal modal-detail fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-lg">
					<div class="modal-content">
						<div class="modal-header bg-info">
							<h4 class="modal-title mt-0">...</h4>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						</div>
						<div class="modal-body">
							<div class="loading text-center"> Loading... </div>

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
			//
			//	pull
			$(document).on('click', 'a#pull-detail', function(event) {
				event.stopPropagation();

				$('.modal-content .modal-header h4', modal).text('รายละเอียดรับเข้า');
				if (!$('.modal-body', modal).find('.table-pull').length) {
					//	run table
					create_pullDetail();
				} else {
					//	reset value filter
					resetDataTable('#tabledetail');
				}

			});

			function create_pullDetail() {
				$.ajax({
						method: "get",
						beforeSend: function() {
							// eChartrank.html('Loading...');
						},
						data: {
							date: getDate
						},
						url: "create_pullDetail",
						success: function(result) {
							// eChartrank.html(result);
							if (result) {
								let obj = jQuery.parseJSON(result);

								let thead = "";
								let tr = "";

								thead += "<thead>";
								thead += "<td width='20px'> # </td>";
								thead += "<td> สินค้า </td>";
								thead += "<td width='60px'> รับเข้า </td>";
								thead += "<td width='120px'> เวลา </td>";
								thead += "</thead>";

								obj.forEach(function(item, index) {
									let number = index + 1;

									tr += "<tr>";
									tr += "<td> " + number + " </td>";
									tr += "<td> " + item.product_name + " </td>";
									tr += "<td> " + item.pull + " </td>";
									tr += "<td> " + item.date + " </td>";
									tr += "</tr>";
								})

								let table = "<div class='table-responsive-sm'><table id='tabledetail' class='table table-pull table-striped '>";
								table += thead;
								table += tr;
								table += "</table></div>";
								$('.modal-body', modal).html(table);
							}
						},
						complete: function(result) {
							$('#tabledetail').DataTable({});
						},
						error: function(error) {
							alert("error occured: " + error.status + " " + error.statusText);
						}
					})
					.fail(function(xhr, status, error) {
						// error handling
						alert('error');
						// window.location.reload();
					});
			}

			//
			//	cut
			$(document).on('click', 'a#cut-detail', function(event) {
				event.stopPropagation();

				$('.modal-content .modal-header h4', modal).text('รายละเอียดจำหน่าย');
				if (!$('.modal-body', modal).find('.table-cut').length) {
					//	run table
					create_cutDetail();
				} else {
					//	reset value filter
					resetDataTable('#tabledetail');
				}

			});

			function create_cutDetail() {
				$.ajax({
						method: "get",
						beforeSend: function() {
							// eChartrank.html('Loading...');
						},
						data: {
							date: getDate
						},
						url: "create_cutDetail",
						success: function(result) {
							// eChartrank.html(result);
							if (result) {
								let obj = jQuery.parseJSON(result);

								let thead = "";
								let tr = "";

								thead += "<thead>";
								thead += "<td width='20px'> # </td>";
								thead += "<td> สินค้า </td>";
								thead += "<td width='60px'> จำหน่าย </td>";
								thead += "<td width='120px'> เวลา </td>";
								thead += "</thead>";

								obj.forEach(function(item, index) {
									let number = index + 1;

									tr += "<tr>";
									tr += "<td> " + number + " </td>";
									tr += "<td> " + item.product_name + " </td>";
									tr += "<td> " + item.cut + " </td>";
									tr += "<td> " + item.date + " </td>";
									tr += "</tr>";
								})

								let table = "<div class='table-responsive-sm'><table id='tabledetail' class='table table-cut table-striped '>";
								table += thead;
								table += tr;
								table += "</table></div>";
								$('.modal-body', modal).html(table);
							}
						},
						complete: function(result) {
							$('#tabledetail').DataTable({});
						},
						error: function(error) {
							alert("error occured: " + error.status + " " + error.statusText);
						}
					})
					.fail(function(xhr, status, error) {
						// error handling
						alert('error');
						// window.location.reload();
					});
			}

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

				let tableColumnTotal = [tableColumn['คงคลัง'], tableColumn['จำหน่าย'], tableColumn['รับเข้า'], tableColumn['อื่นๆ'], tableColumn['เหลือ']];

				//  function on script_tablelayout
				var moniter = tableLayout();

				var dataTable = $('.ex1').DataTable({
					"processing": false,
					"serverSide": false,
					"order": [
						[tableColumn['เหลือ'], "asc"]
						/* [tableColumn['คงคลัง'], "asc"],
						[tableColumn['ทั้งหมด'], "asc"], */
					],
					"ajax": {
						url: "<?php echo base_url() . 'mod_retailstock/ctl_retailstock/fetch_product'; ?>",
						type: "POST",
						data: {
							date: getDate
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
					dom: "<'row'<'col-sm-4 btn-sm'Bi><'col-sm-4 btn-sm toolbar text-center'><'col-sm-4'f>>" +
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
						/* {
						    extend: 'collection',
						    text: '<i class="fas fa-file-download"></i>',
						    titleAttr: 'export',
						    tag: 'span',
						    buttons: ['excel', 'copy'],
						    fade: true
						}, */
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
					/* "orderFixed": {
					    "pre": [ 6, 'asc' ],
					    "post": [ 4, 'desc' ]
					    // "pre": [[ 6, 'asc' ], [ 4, 'desc' ]]
					}, */
					"columnDefs": [

						{
							width: "5rem",
							class: "text-right stock-start",
							"targets": 3
						},
						{
							width: "5rem",
							class: "text-right stock-cut",
							"targets": 4
						},
						{
							width: "5rem",
							class: "text-right stock-pull",
							"targets": 5
						},
						{
							width: "5rem",
							class: "text-right stock-other",
							"targets": 6
						},
						{
							width: "5rem",
							class: "text-right total",
							"targets": 7
						},
						{ "targets": 7, "render": function ( data, type, row ) {
								// console.log(data);
								if (data === 0 || data === "0") {
									return '<span style="display:none">999</span>' + data;
									// return data;
								} else {
									return data;
								}
							},
							"orderSequence": ["asc"]
						},
						// { "type": "numeric-comma", targets: [3,4,5,6,7] }
						// { orderable: false, targets: 0 }
					],
					"preDrawCallback": function(settings) {},
					"createdRow": function(row, data, dataIndex) {

					},
					"rowCallback": function(row, data, index) {
						// console.log(data);
						let product_min = $(data[0]).attr('data-min');

						if(parseInt(data[7]) <= parseInt(product_min) && product_min){
							$('td', row).css('background-color', '#ffdc72');
						}
						
						tableColumnTotal.forEach(function(item, index, arr) {

							if (data[item] < 0) {
								$('td', row).css('background-color', '#e5a0a0');
								// $('td', row).css('background-color','#ffdc72');
							}
						});
					},
					"initComplete": function(settings, json) {
						// console.log(json);

						//  https://markcell.github.io/jquery-tabledit/#examples
						tableEdit();
						
						$('td div.tabledit-toolbar').css('width', '6rem');

					},
					"paging": false,
					// "scrollY": moniter +'px', 
					// "scrollCollapse": false
				});
			}

			$(".buttons-print").on("click", function() {
				// table.button( '.buttons-print' ).trigger();
			});

			function tableEdit() {
				let str = $('#permisspage').val();
				let findPermit = str.includes("manage_stock")
				if (findPermit || str == 'all') {
					$("#btn-editable").Tabledit({
						url: '<?php echo site_url('mod_retailstock/ctl_retailstock/ajax_updatestock'); ?>?date=' + getDate,
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
								[3, "start"],
								[4, "cut"],
								[5, "pull"]
							]
						},
						onAjax: function(actions, serialize) {
							/* console.log('onAjax(action, serialize)');
							console.log(actions);
							console.log(serialize); */

							let start = 0;
							let cut = 0;
							let pull = 0;
							let total = 0;
							var namesplit = serialize.split("&");
							$.each(namesplit, function(key, value) {
								var valuesplit = value.split("=");
								console.log(key+" - "+valuesplit[0]+" = "+valuesplit[1]);

								switch (valuesplit[0]) {
									case 'id':
										id = valuesplit[1];
										break;
									case 'start':
										start = parseInt(valuesplit[1]);
										break;
									case 'cut':
										cut = (parseInt(valuesplit[1]) ? parseInt(valuesplit[1]) : 0);
										break;
									case 'pull':
										pull = (parseInt(valuesplit[1]) ? parseInt(valuesplit[1]) : 0);
										break;
								}
							});

							let other = $('table tr#' + id + ' td.stock-other span').text();
							other = (parseInt(other) ? parseInt(other) : 0);

							// console.log("id:"+id+" = "+start +"+"+ pull +"- ("+cut+" + "+other+")");
							total = start + pull - (cut + parseInt(other));
							/* $('table tr#' + id + ' td.stock-start span.tabledit-span').text(start);
							$('table tr#' + id + ' td.stock-cut span.tabledit-span').text(cut+"88");
							$('table tr#' + id + ' td.stock-pull span.tabledit-span').text(pull); */
							$('table tr#' + id + ' td.total').text(total);

							ajax_staticReload();
						}
					})
				}
			}

			//	create block html date
			creatDate();


		})

		$(document).on('change', 'input#table-datestart', function(event) {
			event.stopPropagation();

			document.frmdate.submit();

		});

		var modalOther = '.bs-example-modal-center';
		$(document).on('click', 'button#callModal', function(event) {
			event.stopPropagation();

			let stockid = $(this).attr('data-id');

			$.ajax({
					method: "get",
					beforeSend: function() {
						$(modalOther + " .modal-body .loading").removeClass("d-none");
						$(modalOther + " .modal-body .form-horizontal").addClass("d-none");
					},
					data: {
						retail_stockid: stockid
					},
					url: "ajax_getDataOther",
					success: function(result) {
						var obj = jQuery.parseJSON(result);

						$(modalOther + ' #stockid').val(stockid);

						$.each(obj.data, function(key, value) {
							if (value != 0 && value != null) {
								$(modalOther + ' #' + key).val(value);
							} else {
								$(modalOther + ' #' + key).val("");
							}
						})

					},
					complete: function() {
						$(modalOther + " .modal-body .loading").addClass("d-none");
						$(modalOther + " .modal-body .form-horizontal").removeClass("d-none");
					},
					error: function(error) {
						alert("error occured: " + error.status + " " + error.statusText);
					}
				})
				.fail(function(xhr, status, error) {
					// error handling
					alert('<?php echo trim($this->lang->line("main_alertWarning")); ?>');
					window.location.reload();
				});

		});

		//	modal frmOther submit
		$(document).on('click', 'button#btn_frmOtherSubmit', function(event) {
			event.stopPropagation();

			$.post("ajax_frmOtherSubmit",

					$("#frmOther").serializeArray()
				)
				.done(function(data, status, error) {
					if (error.status == 200) { //	status complete
						var obj = jQuery.parseJSON(data);

						$('table tr#' + obj.data.id + ' td.stock-other span').text(obj.data.other);
						$('table tr#' + obj.data.id + ' td.total').text(obj.data.total);

						ajax_staticReload()

						$(modalOther).modal('hide');
					}

				})
				.fail(function(xhr, status, error) {
					// error handling
					alert('พบความผิดปกติ ระบบจะทำการ Reload');
					window.location.reload();
				});

		});

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
					alert('พบความผิดปกติ');
					// window.location.reload();
				});
		}

		function creatDate() {
			let getdate = params.get("date");

			htmlButton = '<div class="form-horizontal"><form id="frmdate" name="frmdate" method="get" action="">';

			htmlButton += '<div class="form-group w-100 flex-fill">';
			htmlButton += '<input type="date" class="form-control width-sm" id="table-datestart" name="date" value="' + getdate + '">';
			htmlButton += '</div>';

			htmlButton += '</form></div>';
			$('.toolbar').html(htmlButton);
		}


		//	button print report
		$(document).on('click', '#reportstock', function(event) {
			event.stopPropagation();

			document.frm.submit();

		});

		//	button update score stock
		$(document).on('click', '#rerunstock', function(event) {
			event.stopPropagation();

			Swal.fire({
				type: 'warning',
				title: 'คำเตือน',
				// timer: 2000,
				showConfirmButton: true,
				showCancelButton: true,
				text: 'การทำงานนี้อาจใช้เวลานาน กด OK เพื่อเริ่มกระบวนการ',
			}).then((result) => {
				//
				// 
				if (result.value) {

					if (!getDate) {
						Swal.fire("ผิดพลาด", "โปรดเลือกวันที่ต้องการอัพเดตที่หน้าตาราง", "warning");
						return false;
					}

					Swal.fire({
						title: 'Wait ...',
						allowOutsideClick: false,
						async onOpen(result) {
							let result1 = await ajax_reRunStock();

							if (result1.error_code == 1) {
								Swal.fire("ผิดพลาด", result1.txt, "warning");
							} else {

								Swal.fire({
									type: 'success',
									title: result1.txt,
									showConfirmButton: true
									// text: 'Something went wrong!',
								}).then((result) => {
									// window.location.replace('<?php echo site_url('mod_reservation/ctl_checkin/queue_success', 'http'); ?>');
									window.location.reload();

								})
							}

						},
						onBeforeOpen() {
							Swal.showLoading()
						},
						onAfterClose() {
							// Swal.hideLoading()
						}
					})
				} //	end if result confirm

			})

		});

		function ajax_reRunStock() {

			return new Promise((resolve, reject) => {
				$.get("reRunStock", {
						//  paramiter
						id: [],
						date: getDate
					})
					.done(function(data, status, error) {
						if (error.status == 200) { //	status complete

							var obj = jQuery.parseJSON(data);
							resolve({
								error_code: obj.error_code,
								txt: obj.txt
							})
						}

					})
					.fail(function(xhr, status, error) {
						// error handling
						console.log('error');
					});

			})
		}
		//----------------------------function--------------------------//
	</script>
	<!-- Table Editable plugin-->
	<script src="<?php echo base_url() . 'asset/libs/jquery-tabledit/jquery.tabledit.min.js'; ?>"></script>
</body>

</html>