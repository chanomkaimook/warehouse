<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("structer/backend/head.php"); ?>
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
							<h1><?php echo "Supplier"; ?></h1>
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

			<style>
				#addbill {
					position: fixed;
					bottom: 30px;
					right: 0px;
					z-index: 999;
				}

				#addbill {
					cursor: pointer;
					transition: transform 0.2s;
					transform: scale(0.8, 0.8);
				}

				#addbill .btnplus {
					transition: transform 0.2s;
				}

				#addbill:hover {
					transform: scale(1, 1);
				}

				.btnplus:hover {
					background-color: #3bc8df !important;
					color: blue;
					transform: rotate(360deg);
				}

				#addbill h1 {
					padding: 0px;
					margin: 0px;
				}
			</style>
			<?php if (chkPermissPage('addsupplierlist')) { ?>
				<div id="addbill" class="m-4">
					<div class="btnplus img-circle bg-info p-4" data-toggle="modal" data-target=".modal-addbill">
						<!-- <h1><i class="fas fa-plus"></i></h1> -->
						<span class="lead">เพิ่ม</span>
					</div>
				</div>
			<?php } ?>
			<section class="content">

				<div class="container-fluid">
					<div class="row">

						<section class="col-lg-12 connectedSortable">
							<!-- Custom tabs (Charts with tabs)-->
							<div class="card">
								<div class="card-header">
									<h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i> <?php echo "Manage " . $mainmenu; ?> </h3>
								</div>
								<div class="card-body">
									<div class="row justify-content-center" style="background-color: rgba(140, 175, 255, 0.15); padding: 20px; margin: 1px;border: 1px solid #87aaff; ">
										<div class="col-6">

											<label class=""> เลือกวันที่ - วันที่ </label>
											<div class="input-group ">
												<input type="date" class=" form-control form-control-sm" id="valdate">
												<input type="date" class=" form-control form-control-sm" id="valdateTo">
											</div>

										</div>

										<div class="col-6">
											<label class=""> เลือกรูปแบบ </label>
											<div class="input-group input-group-sm">
												<!-- <select class="custom-select " name="sel_complete" id="sel_complete">
													<option disabled selected hidden> เลือกรูปแบบ </option>
													<option value=""> ทั้งหมด </option>
													<option value="1"> รอคลังรับสินค้า </option>
													<option value="2"> สำเร็จ </option>
													<option value="3"> ยกเลิก </option>

												</select> -->

												<div class="input-group-append">
													<button type="button" class="btn btn-default btn-sm" id="btnSearch"><i class="fas fa-search text-muted"></i> ค้นหา </button>
													<button type="button" class="btn btn-default btn-sm" id="btnDataRefresh"><i class="fas fa-refresh text-muted"></i> อัพเดต </button>
												</div>
											</div>
										</div>


									</div>

									<div class="table-responsive mt-2">
										<table id="ex1" class="table table-bordered table-hover ">
											<thead class="thead-dark">
												<tr>
													<th width="35px">#</th>
													<th>ชื่อ</th>
													<th width="100px">วันที่</th>
													<th width="100px">โดย</th>
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

			<style>
				span[role="button"] {
					cursor: pointer;
				}

				span[role="button"] p:hover {
					color: #007bff;
					/*	color primary */
				}

				form label {
					color: #888;
				}
			</style>
			<!--	Modal	-->
			<div class="modal modal-addbill fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
				<div class="modal-dialog modal-dialog-centered modal-xl">
					<div class="modal-content">
						<div class="modal-header bg-info">
							<h6 class="modal-title mt-0">สร้าง supplier</h6>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						</div>
						<div class="modal-body">
							<div class="loading col-12 text-center d-none">
								<div class="spinner-border text-info"></div>
							</div>

							<!-- form create bill -->
							<div id="form" class="">
								<form action="" id="frm" name="frm" class="">
									<div class="">
										<div id="block_addsupplier" class="row">
											<div class="d-flex col-md-12 justify-content-center formcontent">

												<input id="suppliername" name="suppliername" type="text" class="form-control w-50" placeholder="กรอกชื่อ supplier เพื่อเพิ่มข้อมูล">
												<div class="ele_btn text-center" style="width:120px">
													<button id="btn_addsupplier" name="btn_addsupplier" type="button" class="btn btn-secondary mx-1 ">เพิ่ม supplier</button>
												</div>
											</div>
										</div>
									</div>
								</form>

							</div>

						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

		</div>
		<?php include("structer/backend/footer.php"); ?>
		<?php include("structer/backend/script.php"); ?>

		<script src="<?php echo $base_bn; ?>plugins/sweetalert2/sweetalert2.min.js"></script>

	</div>

	<?php require_once('script_formbill.php'); ?>

	<script>
		const queryString = decodeURIComponent(window.location.search);
		const params = new URLSearchParams(queryString);
		let getDate = params.get("date");

		// $("#selectsupplier").selectpicker('refresh');

		$(function() {

			//	setting
			let frm = $('form#frm');

			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000
			});

			//	run data table
			createDataTable();

			function createDataTable() {
				let url = 'getDataBill';

				var datatable = $('#ex1').DataTable({
					"processing": true,
					"serverSide": true,
					'order': [
						// [4 , 'asc'],
						// [1 , 'desc']
					],

					'ajax': {
						url: url,
						type: 'get',
						data: function(d) {
							d.valdate = $('#valdate').val();
							d.valdateTo = $('#valdateTo').val();
						},
						error: function(xhr, error, code) {
							//  xhr return array status async
							if (xhr.status != 200) {
								alert('พบข้อผิดพลาด กรุณาแจ้งเจ้าหน้าที่');
							}
						}
					},
					"columns": [{
							"data": "id"
						},
						{
							"data": "name"
						},
						{
							"data": "date"
						},
						{
							"data": "user"
						}
					],

				});

			}

			$(document).on('click', '#btn_addsupplier', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();
				
				add_supplier();
			})

			//	add supplier
			function add_supplier(){
				displayLoadingAdd();

				var data = new FormData();

                data.append("suppliername", $('#suppliername').val());

				fetch('add_supplier', {
						method: 'POST',
						body: data
					})
					.then(res => res.json())
					.then((resp) => {
						if(resp.error_code != 0){
							swal.fire({
                                    type: 'warning',
                                    title: 'ข้อมูลผิดปกติ',
                                    text: resp.txt
                                }).then((result) => {
									hideLoadingAdd();
                                })
						}

						if(resp.error_code == 0){
							hideLoadingAdd();
						}

						//	table refresh
						$('#btnDataRefresh').trigger('click');

					})
					.catch(function(err) {
						console.log(`error : ${err}`)
					})
			}

			$(document).on('click', '#btnSearch', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				$('#ex1').DataTable().search('').draw();
				$('#ex1').DataTable().ajax.reload();
			})

			$(document).on('click', '#btnDataRefresh', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				$('#ex1').DataTable().ajax.reload(null, false);
			})

			//----------------------------function--------------------------//
			function displayLoadingAdd() {
				let loading = '<div class="spinner-border text-info"></div>';

				$('#block_addsupplier .ele_btn button').addClass('d-none');
				$('#block_addsupplier .ele_btn').append(loading);
			}

			function hideLoadingAdd() {
				$('#block_addsupplier .spinner-border').remove();
				$('#block_addsupplier .ele_btn button').removeClass('d-none');

				$('#suppliername').val('');
			}

			function displayLoading(elename) {
				let loading = '<div class="spinner-border text-info"></div>';

				$(elename).removeClass('d-none');

				document.frm.reset();
				$('.thumbnail-image').empty();

				$(elename).html(loading);
			}

			function hideLoading(elename) {
				$(elename).addClass('d-none');
			}

			//	cancel modal
			$('.modal-addbill').on('hide.bs.modal', function() {
				displayLoading();
				$('#selecttype ').removeClass('d-none');

				hideLoading();
			});

		})
	</script>

	<script>
		function checkNumber(ele) {
			var vchar = String.fromCharCode(event.keyCode);
			// console.log(vchar);
			if (vchar < '0' || vchar > '9') {
				return false
			}

		}

		function checkPrice(ele) {
			var vchar = event.keyCode;

			let arraydetail = [45, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58];

			let search = arraydetail.find(res => res == vchar);
			if (!search) {
				return false;
			}
		}

		function formatMoney(number, decPlaces, decSep, thouSep) {
			decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
				decSep = typeof decSep === "undefined" ? "." : decSep;
			thouSep = typeof thouSep === "undefined" ? "," : thouSep;
			var sign = number < 0 ? "-" : "";
			var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
			var j = (j = i.length) > 3 ? j % 3 : 0;

			return sign +
				(j ? i.substr(0, j) + thouSep : "") +
				i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
				(decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
		}
	</script>
</body>

</html>