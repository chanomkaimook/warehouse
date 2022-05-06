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

		table tbody td {
			cursor: pointer;
		}

		.modal-detail-result {
			max-height: 10rem;
			height: 10rem;
			overflow: auto;
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

											<?php
												require_once('option.php');
											?>

											<!-- <input type="date" class="form-control datetimepicker-input" data-target="#reservationdate" name="fecha_evento" data-date-format="dd/mm/yyyy" /> -->
											<div class="">
												<table class="table table-centered mb-0 ex1" id="btn-editable">
													<caption id="tablecaption">เอกสารรายงานคลังสินค้า chokchai steakhouse online เมื่อวันที่ <?php echo thai_date($get_date); ?></caption>
													<thead>
														<tr>
															<th width=15>#</th>

															<th width=15>ID</th>
															<th>สินค้า</th>
															<th>คงคลัง</th>
															<th>จำหน่าย</th>
															<th>รับเข้า</th>
															<th>เบิก</th>
															<th>เหลือ</th>
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
				<form action="ajax_report" id="frm" name="frm">
					<input type="hidden" id="date" name="date" value="<?php echo $get_date; ?>">
					<input type="hidden" id="date_cut" name="date_cut" value="<?php echo $date_cut; ?>">
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
			 * end modal
			 * 
			 */

			$(document).on('click', '.check-field-column', function() {
				let ele = $(this);
				
				// dataTable.destroy();
				// $('.table tbody').html('');

				dataTable.ajax.reload();
				// dataList();
			})

			dataList()

			//----------------------------filter--------------------------//
			var dataTable;
			function dataList() {

				let date_ob = new Date();
				let date = ("0" + date_ob.getDate()).slice(-2);
				let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
				let year = date_ob.getFullYear();
				let datenow = year + "-" + month + "-" + date;

				let getDate = params.get("date");
				(getDate ? datesearch = getDate : datesearch = $('input#date').val());
				//
				//  set index column
				let tableColumn = [];
				$('table thead tr th').each(function(index) {
					tableColumn[$(this).text()] = [$(this).index()];
				});

				let tableColumnTotal = [tableColumn['คงคลัง'], tableColumn['จำหน่าย'], tableColumn['รับเข้า'], tableColumn['เบิก'], tableColumn['เหลือ']];
				let tableColumnCal = [tableColumn['คงคลัง'], tableColumn['จำหน่าย'], tableColumn['รับเข้า'], tableColumn['เบิก']];

				//  function on script_tablelayout
				// var moniter = tableLayout();

				dataTable = $('.ex1').DataTable({
					"processing": true,
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
							date: datesearch,
							date_cut: $('#date_cut').val(),
							dataField: {
								bill: function() { return document.getElementById("field-bill").checked },
								receive: function() { return document.getElementById("field-receive").checked },
								issue: function() { return document.getElementById("field-issue").checked },
							},
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
							orderDataType: "dom-text-numeric",
							"targets": 7
						},
						/* { "targets": 7, "render": function ( data, type, row ) {
								// console.log(data);
								if (data === 0 || data === "0") {
									return '<span style="display:none">999</span>' + data;
									// return data;
								} else {
									return data;
								}
							},
							"orderSequence": ["asc"]
						}, */
						// { "type": "numeric-comma", targets: [3,4,5,6,7] }
						// { orderable: false, targets: 0 }
					],
					"preDrawCallback": function(settings) {},
					"createdRow": function(row, data, dataIndex) {

						let totalresult = 0;
						tableColumnCal.forEach(function(item, index, arr) {
							if (!data[item]) {
								data[item] = 0;
							}
						})
						totalresult = parseInt(data[3]) + parseInt(data[5]) - (parseInt(data[4]) + parseInt(data[6]));
						// console.log(data[3]+" - "+data[5]+" - "+data[4]+" - "+data[6]+" - "+totalresult);

						if (totalresult === 0 || totalresult === "0") {
							totalresult = '<span style="display:none">999</span>' + 0;
						}

						$('td', row).eq(7).html(totalresult);

					},
					"rowCallback": function(row, data, index) {
						// console.log(data);

						//	set attribute total
						let product_min = $(data[0]).attr('data-min');

						if (parseInt(data[7]) <= parseInt(product_min) && product_min) {
							$('td', row).css('background-color', '#ffdc72');
						}

						if ($('td', row).eq(7).text() < 0) {
							$('td', row).css('background-color', '#e5a0a0');
							// $('td', row).css('background-color','#ffdc72');
						}

						/* tableColumnTotal.forEach(function(item, index, arr) {

							if (data[item] < 0) {
								$('td', row).css('background-color', '#e5a0a0');
								// $('td', row).css('background-color','#ffdc72');
							}
						}); */
					},
					"initComplete": function(settings, json) {

						// table.order( [ 7, 'desc' ] ).draw();

						//  https://markcell.github.io/jquery-tabledit/#examples
					},
					"paging": false,
					// "scrollY": moniter +'px', 
					// "scrollCollapse": false
				})
			}

			//	create block html date
			creatDate();

			
			
			
			
			new $.fn.dataTable.Buttons( $('.ex1').DataTable(), {
				buttons: [
					{
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
						title: 'Data Stock :',
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
					},
					{
						text: '<i class="fas fa-redo-alt"></i>',
						className: '',
						titleAttr: 'reload',
						action: function(e, dt, node, config) {
							//
							//	API reload(callback,resetPaging [default true,false])
							//
							dt.ajax.reload();
							// dt.ajax.reload(null, false);
						}
					}
				]
			} );
			
			/* $.fn.dataTable
				.tables({
				
				
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
							title: 'Data Stock :',
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
						},
						{
							text: '<i class="fas fa-redo-alt"></i>',
							className: '',
							titleAttr: 'reload',
							action: function(e, dt, node, config) {
								//
								//	API reload(callback,resetPaging [default true,false])
								//
								dt.ajax.reload();
								// dt.ajax.reload(null, false);
							}
						}

					]
				
				
				})
				.adjust() */
			
			
			
			
			

		})
		$.fn.dataTable.ext.order['dom-text-numeric'] = function(settings, col) {
				// console.log(settings);
				return this.api().column(col, {
					order: 'index'
				}).nodes().map(function(td, i) {

					// console.log(JSON.stringify(td));
					// console.log(col+" dom "+order+" td:"+td+" i:"+i);
					// console.log($(td).text() * 1);
					return $(td).text() * 1;
				});
			}
			// console.log($.fn.dataTable.ext);
			$.fn.dataTable.ext.buttons.print = {
				// extend: 'print',
				text: '<i class="fas fa-print"></i>',
				title: 'Data Stock :',
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
			};

		$(document).on('change', 'input#table-datestart', function(event) {
			event.stopPropagation();

			document.frmdate.submit();

		});

		function creatDate() {
			let getdate = params.get("date");

			/* let date_ob = new Date();
			let date = ("0" + date_ob.getDate()).slice(-2);
			let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
			let year = date_ob.getFullYear();
			let datenow = year + "-" + month + "-" + date;

			datenow = new Date().toISOString().substring(0,10); // "2013-12-31" */

			let htmlButton = '';
			htmlButton += '<div class="form-horizontal"><form id="frmdate" name="frmdate" method="get" action="">';

			htmlButton += '<div class="form-group w-100 flex-fill">';
			htmlButton += '<input type="date" class="form-control width-sm" id="table-datestart" name="date" value="' + getdate + '">';
			htmlButton += '</div>';

			htmlButton += '</form></div>';
			$('.toolbar').html(htmlButton);

			// $.fn.datepicker.defaults.format = "dd/mm/yyyy";
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

		let modaldetail = $('.modal-detail');
		$(document).on('click', 'td.stock-cut,td.stock-pull,td.stock-other', function() {
			event.stopPropagation();
			let getdate = params.get("date");
			let datesearch = getdate;
			(getdate ? datesearch = getdate : datesearch = $('input#date').val());
			//	loading
			beginLoading();

			modaldetail.modal({
				show: true
			});


			let item = this.parentElement.getElementsByTagName("div")[0].getAttribute('data-item');
			let url;

			if ($(this).attr('class').includes("stock-cut")) {
				url = 'get_billorderDetail?date=' + datesearch + '&item=' + item;
			} else if ($(this).attr('class').includes("stock-pull")) {
				url = 'get_receiveDetail?date=' + datesearch + '&item=' + item;
			} else {
				url = 'get_issueDetail?date=' + datesearch + '&item=' + item;
			}

			fetch(url)
				.then(res => res.json())
				.then((resp) => {
					$('.modal-detail .loading').addClass('d-none');
					let html_text = "";
					$.each(resp, function(key, value) {

						html_text = value['code'] + "-" + value['name'] + " || " + value['product'] + " = " + value['qty'];
						$('.modal-detail-result ul').append('<li>' + html_text + '</li>');
					});

				})

		})

		$('.modal-detail').on('hide.bs.modal', function() {
			$('.modal-detail-result ul').html('');
		});

		function beginLoading() {
			$('.modal-detail .loading').removeClass('d-none');
			$('.modal-detail-result ul').html('');
		}
		//----------------------------function--------------------------//
	</script>
	<?php require_once("script_stock.php"); ?>
</body>

</html>