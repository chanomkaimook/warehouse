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
							<h1><?php echo "ใบลดหนี้"; ?></h1>
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
			<?php if(chkPermissPage('addcreditnote')){ ?>
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
												<select class="custom-select " name="sel_complete" id="sel_complete">
													<option disabled selected hidden> เลือกรูปแบบ </option>
													<option value=""> ทั้งหมด </option>
													<option value="0"> รอตรวจสอบ </option>
													<option value="1"> รอคลังรับสินค้า </option>
													<option value="2"> สำเร็จ </option>
													<option value="3"> ยกเลิก </option>

												</select>

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
													<th>เลขที่</th>
													<th>ลดหนี้</th>
													<th width="20%">สินค้า</th>
													<th width="15%">สถานะ</th>
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
							<h6 class="modal-title mt-0">สร้างใบลดหนี้</h6>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						</div>
						<div class="modal-body">
							<div class="loading col-12 text-center d-none">
								<div class="spinner-border text-info"></div>
							</div>

							<!-- form create creditnote -->
							<div id="form">
								<div class="form-group row">
									<label for="search" class="control-label col-sm-8 text-right">ระบุชื่อบิลที่ต้องการอ้างอิง</label>
									<div class="input-group input-group-sm col-sm-4">
										<input class="form-control form-control-navbar" id="searchtext" type="search" placeholder="Search" aria-label="Search">
										<div class="input-group-append">
											<button class="btn btn-navbar border searchorder" type="button">
												<i class="fas fa-search"></i>
											</button>
										</div>
									</div>
								</div>

								<!-- result search -->
								<div class="search_result overflow-auto d-none" style="height:25vh;"></div>

								<form action="" id="frm" name="frm" class="d-none">

									<input type="hidden" id="bill_id" name="bill_id" value="">
									<input type="hidden" id="bill_code" name="bill_code" value="">
									<div class="">

										<!-- <div class="form-group">
												<label for="datecreate"><span class="is-required">* <span class="valid"></span></span> วันที่สร้างบิล</label>
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
													</div>
													<input type="date" class="form-control " name="order_date" id="order_date" value="<?php echo date('Y-m-d'); ?>">
												</div>
												</div> -->

										<div class="row">
											<div class="d-flex col-md-6 col-lg-3">
												<label for="">เลขบิล :</label>
												<p class="">
													<u class="dotted mx-2 bill_code"></u>
												</p>
											</div>

											<div class="d-flex col-md-6 col-lg-3">
												<label for="">อ้างอิง :</label>
												<p class="">
													<u class="dotted mx-2 bill_textcode"></u>
												</p>
											</div>

											<div class="d-flex col-md-6 col-lg-3">
												<label for="">วันที่ :</label>
												<p class="">
													<u class="dotted mx-2 bill_datecreate"></u>
												</p>
											</div>

											<div class="d-flex col-md-6 col-lg-3">
												<label for="">โดย :</label>
												<p class="">
													<u class="dotted mx-2 bill_staffcreate"></u>
												</p>
											</div>
										</div>

										<div class="row">
											<div class="d-flex col-md-6 col-lg-3">
												<label for="">ชำระ :</label>
												<p class="">
													<u class="dotted mx-2 bill_paystatus"></u>
												</p>
											</div>

											<div class="d-flex col-md-6 col-lg-3">
												<label for="">ช่องทาง :</label>
												<p class="">
													<u class="dotted mx-2 bill_method"></u>
												</p>
											</div>

											<div class="d-flex col-md-6 col-lg-3">
												<label for="">จัดส่ง :</label>
												<p class="">
													<u class="dotted mx-2 bill_delivery"></u>
												</p>
											</div>

											<div class="d-flex col-md-6 col-lg-3">
												<label for="">สถานะ :</label>
												<p class="">
													<u class="dotted mx-2 bill_complete"></u>
												</p>
											</div>
										</div>

										<div class="row">
											<div class="d-flex col-md-12 col-lg-6">
												<label for="">ชื่อ-นามสกุล :</label>
												<p class="">
													<u class="dotted mx-2 bill_name"></u>
												</p>
											</div>

											<div class="d-flex col-md-12 col-lg-3">
												<label for="">เบอร์โทร :</label>
												<p class="">
													<u class="dotted mx-2 bill_tel"></u>
												</p>
											</div>

											<div class="d-flex col-md-12 col-lg-3">
												<label for="">ภาษี :</label>
												<p class="">
													<u class="dotted mx-2 bill_citizen"></u>
												</p>
											</div>

											<div class="d-flex col-md-12 col-lg-9">
												<label for="">ที่อยู่ :</label>
												<p class="">
													<u class="dotted mx-2 bill_address"></u>
												</p>
											</div>

											<div class="d-flex col-md-12 col-lg-3">
												<label for="">ปณ. :</label>
												<p class="">
													<u class="dotted mx-2 bill_zipcode"></u>
												</p>
											</div>
										</div>

										<div class="row border pt-2">
											<div class="d-flex col-sm-12 col-lg-6">
												<label for="">ธนาคารที่โอน :</label>
												<p class="">
													<u class="dotted mx-2 bill_bank"></u>
												</p>
											</div>

											<div class="d-flex col-sm-12 col-lg-6">
												<label for="">วัน-เวลาโอนเงิน :</label>
												<p class="">
													<u class="dotted mx-2 bill_bank_daytime"></u>
												</p>
											</div>

											<div class="d-flex col-sm-12 col-lg-6">
												<label for="">จำนวนเงิน :</label>
												<p class="">
													<u class="dotted mx-2 bill_bank_amount"></u>
												</p>
											</div>

											<div class="d-flex col-sm-12 col-lg-6">
												<label for="">หมายเหตุ :</label>
												<p class="">
													<u class="dotted mx-2 bill_bank_remark"></u>
												</p>
											</div>

										</div>

										<div class="row mt-2">

											<div class="form-group col-sm-12">
												<label for="">คำอธิบาย</label>
												<p class="">
													<u class="dotted bill_remark"></u>
												</p>
											</div>

										</div>

										<div class="row justify-content-end">
											<div class="col-sm-8 px-4 pt-4 border border-secondary">
												<p class="htmltext-return text-success text-bold">** มีสินค้าส่งกลับจากลูกค้า(สินค้าที่ระบุเอาไว้จะนำส่งคืนคลัง)</p>
												<p class="htmltext-loss text-danger text-bold d-none">** สินค้าที่ส่งให้ลูกค้าสูญเสีย</p>
											</div>
											<div class="col-sm-4 ">
												<div class="form-group ">
													<!-- <div class="custom-control custom-checkbox">
													<input class="custom-control-input" type="checkbox" id="creditloss" value="" checked="checked" >
													<label for="creditloss" class="custom-control-label">มีสินค้าส่งกลับ</label>
												</div> -->
													<select name="select_return" id="select_return" class="form-control form-control-sm">
														<option value="0" selected>สินค้าส่งกลับ</option>
														<option value="1">สินค้าสูญเสีย</option>
													</select>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="form-group col-sm-12">
												<label for="">รายการสินค้า</label>
												<div class="table-responsive">
													<table class="tabledetail w-100">
														<thead>
															<tr>
																<th> </th>
																<th>ลำดับ</th>
																<th>สินค้า</th>
																<th>ราคา</th>
																<th width="60">จำนวน</th>
																<th class="text-right">ยอดรวม</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>

													<!-- <input type="text" id="product" name="product" value="444" > -->
												</div>
											</div>

										</div>

										<!-- total score -->
										<div class="row mt-2 text-right">

											<div class="mb-1 col-sm-12 d-flex">
												<label for="" class="col-10 text-dark">รวมยอดขายสุทธิ</label>
												<input disabled type="text" id="bill_price" class="text-right form-control form-control-sm col-2 bill_price" data-loop="bill_loop" data-name="bill_price" value="">
											</div>
											<div class="mb-1 col-sm-12 d-flex">
												<label for="" class="col-10 text-dark">ค่ากล่องพัสดุ</label>
												<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_parcel" value="" data-loop="bill_loop" data-name="bill_parcel" OnKeyPress="return checkPrice(this)">
											</div>
											<div class="mb-1 col-sm-12 d-flex">
												<label for="" class="col-10 text-dark">ค่าบริการจัดส่ง</label>
												<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_logis" value="" data-loop="bill_loop" data-name="bill_logis" OnKeyPress="return checkPrice(this)">
											</div>
											<div class="mb-1 col-sm-12 d-flex">
												<label for="" class="col-10 text-dark">ค่าธรรมเนียม shopee</label>
												<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_shor" value="" data-loop="bill_loop" data-name="bill_shor" OnKeyPress="return checkPrice(this)">
											</div>
											<div class="mb-1 col-sm-12 d-flex">
												<label for="" class="col-10 text-dark">ส่วนลด</label>
												<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_discount" value="" data-loop="bill_loop" data-name="bill_discount" OnKeyPress="return checkPrice(this)">
											</div>
											<div class="mb-1 col-sm-12 d-flex">
												<label for="" class="col-10 text-dark">ค่าธรรมเนียมเก็บเงินปลายทาง </label>
												<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_tax" value="" data-loop="bill_loop" data-name="bill_tax" OnKeyPress="return checkPrice(this)">
											</div>
											<div class="mb-1 col-sm-12 d-flex">
												<label for="" class="col-10 text-primary">
													<style>
														.custom-control-label::before,
														.custom-control-label::after {
															left: -1.5rem !important;
														}
													</style>
													<div class="custom-control custom-checkbox">
														<input class="custom-control-input" type="checkbox" id="totalzero" value="">
														<label for="totalzero" class="custom-control-label pr-4">ให้ยอดเป็นศูนย์</label>ยอดรวมสุทธิ
													</div>


												</label>
												<input disabled type="text" id="totalamount" class="text-right form-control form-control-sm col-2 bill_net" value="" data-loop="bill_loop" data-name="totalamount">
											</div>

										</div>

										<div class="row">
											<div class="form-group col-sm-12">
												<label for="remark">หมายเหตุ</label>
												<textarea id="remark" name="remark" class="form-control" rows="2"></textarea>
											</div>
										</div>

										<div class="row">
											<div class="form-group col-md-12">
												<label class="">แนบรูปอ้างอิง <span class="text-ImgMultiple"> (ระบุได้มากกว่า 1 ภาพ) </span> </label>
												<div class="input-group">
													<div class="custom-file">
														<input type="file" class="custom-file-input" name="image_file[]" id="image_file" multiple>
														<label class="custom-file-label" for="image_file"><span id="imagedledetail">Choose file</span></label>
													</div>
													<div class="input-group-append">
														<span class="input-group-text" id="cancelimgdetail"><i class="fa fa-window-close"></i></span>
													</div>
												</div>
												<p class="text-danger small">** ขนาดภาพไม่ควรเกิน 1 MB</p>

												<style>
													.thumbnail-image img {
														height: 250px;
													}
												</style>
												<div class="thumbnail-image">
												</div>
											</div>
										</div>

									</div>
									<?php if(chkPermissPage('addcreditnote')){ ?>
									<div class="row row-form-tool-btn">
										<div class="col-sm-12 text-center form-tool-btn">
											<button type="button" id="submitform" class="btn btn-md w-25 btn-outline-primary">บันทึก</button>
											<button type="button" class="btn btn-md btn-danger" data-dismiss="modal" aria-hidden="true">ยกเลิก</button>
										</div>
									</div>
									<?php } ?>
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

	<?php require_once('script_formcreditnote.php'); ?>

	<script>
		const queryString = decodeURIComponent(window.location.search);
		const params = new URLSearchParams(queryString);
		let getDate = params.get("date");

		$(function() {

			//	setting
			let search_order = $('[type=search]');
			let frm = $('form#frm');
			let search_result = $('.search_result');

			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000
			});

			//	run data table
			createDataTable();

			function createDataTable() {
				let url = 'getDataCreditNote';

				var datatable = $('#ex1').DataTable({
					"processing": true,
					"serverSide": true,
					'order': 
						[
							// [4 , 'asc'],
							[1 , 'desc']
						]
					,

					'ajax': {
						url: url,
						type: 'get',
						data: function(d) {
							d.valdate = $('#valdate').val();
							d.valdateTo = $('#valdateTo').val();
							d.sel_complete = $('#sel_complete').val() !== null ? $('#sel_complete').val() : null;
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
							"data": "code"
						},
						{
							"data": "net"
						},
						{
							"data": "loss"
						},
						{
							"data": "complete"
						}
					],

				});

			}

			$(document).on('click', '.searchorder', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				get_orderSearchBill(search_order.val());
			})

			$(document).on('keyup', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				let keycode = e.keyCode;
				let check_focus = $('#searchtext').is(":focus");

				if (keycode == 13 && check_focus) {
					$('.searchorder').trigger('click');
				}
			})

			$(document).on('click', '.search_result  span[role="button"]', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				let bill = $(this);
				get_orderBill(bill.attr('data-id'));
			})

			//	search order
			function get_orderBill(bill_id) {
				displayLoading('.search_result');

				var data = new FormData();
				data.append("bill_id", bill_id);

				//	วิธี fetch แบบ error handling
				fetch('get_orderBill', {
						method: 'POST',
						body: data
					})
					.then(res => res.json())
					.then((resp) => {
						hideLoading('.search_result');

						let dataresult = resp;
						let resultSearch = "";
						let bill = resp.data;
						let billdetail = resp.datadetail;

						// console.log(bill);
						$('#bill_id').val(bill.id);
						$('#bill_code').val(bill.code);

						formDataInsert(bill);

						formDataDetailInsert(billdetail);

						//	force check zero have hidden
						$('*[for=totalzero]').hide();

						frm.removeClass('d-none');
					})
					.catch(function(err) {
						console.log(`error : ${err}`)
						search_result.text('ไม่พบข้อมูล');
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

				get_countMenu();
			})

			//----------------------------function--------------------------//
			function displayLoading(elename) {
				let loading = '<div class="spinner-border text-info"></div>';

				$('#searchtext').val(null);

				$(elename).removeClass('d-none');
				frm.addClass('d-none');

				document.frm.reset();
				$('.thumbnail-image').empty();

				//	product return
				$('.htmltext-return').removeClass('d-none');
				$('.htmltext-loss').addClass('d-none');

				$(elename).html(loading);
			}

			function hideLoading(elename) {
				$(elename).addClass('d-none');
			}

			//	set for show modal *** delete
			//	set step auto
			/* $('.modal-addbill').modal('show');
			var modaladdbill = $('.modal-addbill');
			// add_Creditnote();
			get_orderSearchBill('16286'); */

			//	cancel modal
			$('.modal-addbill').on('hide.bs.modal', function() {
				displayLoading();
				$('.search_result').addClass('d-none');

				hideLoading();
			});


			function get_orderSearchBill(ordername) {
				displayLoading('.search_result');

				var data = new FormData();
				data.append("searchorder", ordername);

				//	วิธี fetch แบบ error handling
				fetch('get_orderSearchBill', {
						method: 'POST',
						body: data
					})
					.then(res => res.json())
					.then((resp) => {
						let dataresult = resp.data;
						let resultSearch = "ไม่พบข้อมูล";

						if (resp.data) {
							resultSearch = "";
							for (var k in dataresult) {
								resultSearch += `<span role="button" data-id="${dataresult[k]['id']}">`;
								resultSearch += `<p>เลข ${dataresult[k]['code']}`;
								resultSearch += ` ชื่อลูกค้า ${dataresult[k]['name']}</p>`;
								resultSearch += `</span>`;
							}
						}

						search_result.html(resultSearch);
					})
					.catch(function(err) {
						console.log(`error : ${err}`)
						search_result.text('ไม่พบข้อมูล');
					})
			}

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