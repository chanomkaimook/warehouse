<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("structer/backend/head.php"); ?>
	<link rel="stylesheet" href="<?php echo $base_bn; ?>frontend/bootstrap-select/css/bootstrap-select.css">
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
							<h1><?php echo "ใบรับเข้า"; ?></h1>
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
			<?php if (chkPermissPage('addreceive')) { ?>
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
													<th>ประเภท</th>
													<th width="20%">supplier</th>
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
							<h6 class="modal-title mt-0">สร้างใบรับเข้า</h6>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						</div>
						<div class="modal-body">
							<div class="loading col-12 text-center d-none">
								<div class="spinner-border text-info"></div>
							</div>

							<div id="selecttype" class="row">
								<div class="col-md-4">
									<button type="button" class="btn btn-default w-100 mx-2 btn-sup">เลือกจาก supplier</button>
								</div>
								<div class="col-md-4">
									<button type="button" class="btn btn-default w-100 mx-2 btn-issue">เลือกจาก ใบเบิก</button>
								</div>
								<div class="col-md-4">
									<button type="button" class="btn btn-default w-100 mx-2 btn-order">เลือกจาก บิลสั่งซื้อ</button>
								</div>
							</div>

							<!-- form supplier -->
							<div id="formsupplier" class="d-none">
								<div class="form-group row">
									<label for="search" class="control-label col-sm-8 text-right">ระบุชื่อบิล supplier</label>
									<div class="input-group input-group-sm col-sm-4">
										<input class="form-control form-control-navbar" id="searchsup" type="search" placeholder="Search" aria-label="Search">
										<div class="input-group-append">
											<button class="btn btn-navbar border searchsupplier" type="button">
												<i class="fas fa-search"></i>
											</button>
										</div>
									</div>
								</div>

								<!-- result search supplier bill -->
								<div class="search_resultsupplier overflow-auto d-none" style="height:25vh;"></div>
							</div>

							<!-- form supplier -->
							<div id="formissue" class="d-none">
								<div class="form-group row">
									<label for="search" class="control-label col-sm-8 text-right">ระบุชื่อบิล</label>
									<div class="input-group input-group-sm col-sm-4">
										<input class="form-control form-control-navbar" id="searchissue" type="search" placeholder="Search" aria-label="Search">
										<div class="input-group-append">
											<button class="btn btn-navbar border searchissue" type="button">
												<i class="fas fa-search"></i>
											</button>
										</div>
									</div>
								</div>

								<!-- result search issue bill -->
								<div class="search_resultissue overflow-auto d-none" style="height:25vh;"></div>
							</div>

							<!-- form create bill -->
							<div id="form" class="d-none">
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
									<input type="hidden" id="billtype" name="billtype" value="">
									<input type="hidden" id="set_billcomplete" name="set_billcomplete" value="0">
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

										<div id="block_supplier" class="row">
											<div class="d-flex col-md-12 col-lg-12">
												<label for="">Supplier :</label>
												<p class="">
													<u class="dotted mx-2 bill_sup_name"></u>
												</p>
											</div>

											<div class="col-md-12 col-lg-12">
												<label for="">รายการสินค้าที่สั่ง</label>
												<div class="table-responsive row">
													<table class="bill_sup_product w-100">
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
												</div>
											</div>

										</div>

										<div id="block_texttransfer" class="row">
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

										<div id="block_textowner" class="row">
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

										<div id="block_bank" class="row border pt-2">
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

										<div id="block_textsender" class="row justify-content-end">
											<div class="col-12 px-4 pt-4 border border-secondary">
												<p class="htmltext-return text-success text-bold">** มีสินค้าส่งกลับจากลูกค้า(สินค้าที่ระบุเอาไว้จะนำส่งคืนคลัง)</p>
												<p class="htmltext-loss text-danger text-bold d-none">** สินค้าที่ส่งให้ลูกค้าสูญเสีย</p>
											</div>
										</div>

										<div class="row">
											<div class="form-group col-sm-12">
												<div class="p-2 bg-dark mt-2">
													<label for="" class="text-white">รายการสินค้าที่รับ</label>
													<button type="button" class="btn btn-default btn-sm modal-bill float-right" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่ม </button>
												</div>
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
										<div class="row mt-5 text-right">
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
									<?php if (chkPermissPage('addreceive')) { ?>
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

			<!-- modal Product -->
            <div class="modal fade bd-example-modal-lg" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index:10000">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="titel text-left"> <i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่มรายการ </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="">เลือกเมนูหลัก</label>
                                <select id="select-productmain" name="select-productmain" class="selectpicker" data-live-search="true">
                                    <option value=""> -- โปรดเลือกเมนูหลัก -- </option>
                                    <?php
                                    $sql = $this->db->select('*')
                                        ->from('retail_productmain')
                                        ->where('status', 1)
                                        ->get();
                                    foreach ($sql->result() as $row) {
                                        echo '<option value="' . $row->ID . '"> ' . $row->NAME_TH . ' </option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="SLproductlist">
                                <label class="">เลือกรายการเมนู</label>
                                <select id="select-productlist" name="select-productlist" class="selectpicker selectpicker_1" data-live-search="true" disabled>

                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="">จำนวน</label>
                                <input type="number" class="form-control " name="productqty" id="productqty" placeholder="จำนวน">
                            </div>
                            <div class="form-group col-md-6 text-center p-auto m-auto">
                                <button type="button" class="btn btn-lg btn-info w-100" id="add-order"> <i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่มรายการ </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="htmlvalidate"></div>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> ปิด</button>
                        </div>
                    </div>
                </div>
            </div>

		</div>
		<?php include("structer/backend/footer.php"); ?>
		<?php include("structer/backend/script.php"); ?>
		<script src="<?php echo $base_bn; ?>frontend/bootstrap-select/js/bootstrap-select.js"></script>
		<script src="<?php echo $base_bn; ?>plugins/sweetalert2/sweetalert2.min.js"></script>

	</div>

	<?php require_once('script_formbill.php'); ?>

	<script>
		const queryString = decodeURIComponent(window.location.search);
		const params = new URLSearchParams(queryString);
		let getDate = params.get("date");

		$(function() {

			//	setting
			let search_order = $('#searchtext[type=search]');
			let search_ordersup = $('#searchsup[type=search]');
			let search_orderissue = $('#searchissue[type=search]');
			let frm = $('form#frm');
			let search_result = $('.search_result');

			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000
			});

			$('body').on('hidden.bs.modal', function () {
				if($('.modal[aria-hidden=true]').length > 0)
				{
					$('body').addClass('modal-open');
				}
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
							"data": "typebill"
						},
						{
							"data": "supplier"
						},
						{
							"data": "complete"
						}
					],

				});

			}

			//	select type create bill
			$(document).on('click', '.btn-order', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				openOrderSearch();
			})

			function openOrderSearch() {
				$('#billtype').val(2);
				$('#selecttype ').addClass('d-none');
				$('#form').removeClass('d-none');

				$('#form .form-group').first().removeClass('d-none');
			}

			$(document).on('click', '.btn-sup', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				openSupplieSearch();
			})

			function openSupplieSearch() {
				$('#billtype').val(1);
				$('#selecttype ').addClass('d-none');
				$('#formsupplier').removeClass('d-none');

				//	auto search
				get_orderSearchSupbill('sp');
			}

			$(document).on('click', '.btn-issue', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				openIssueSearch();
			})

			function openIssueSearch() {
				$('#billtype').val(4);
				$('#selecttype ').addClass('d-none');
				$('#formissue').removeClass('d-none');

				//	auto search
				get_orderSearchIssuebill('is');
			}

			//	search bill
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
				let checksup_focus = $('#searchsup').is(":focus");
				let checkissue_focus = $('#searchissue').is(":focus");

				if (keycode == 13 && check_focus) {
					$('.searchorder').trigger('click');
				}

				if (keycode == 13 && checksup_focus) {
					$('.searchsupplier').trigger('click');
				}

				if (keycode == 13 && checkissue_focus) {
					$('.searchissue').trigger('click');
				}
			})

			//	search supplier
			$(document).on('click', '.searchsupplier', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				get_orderSearchSupbill(search_ordersup.val());
			})

			//	search issue
			$(document).on('click', '.searchissue', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				get_orderSearchIssuebill(search_orderissue.val());
			})

			$(document).on('click', '.search_result  span[role="button"]', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				let bill = $(this);
				get_orderBill(bill.attr('data-id'));
			})

			$(document).on('click', '.search_resultsupplier  span[role="button"]', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				let bill = $(this);
				get_orderSupplierBill(bill.attr('data-id'));
			})

			$(document).on('click', '.search_resultissue  span[role="button"]', function(e) {
				e.stopPropagation;
				e.stopImmediatePropagation();

				let bill = $(this);
				get_orderIssueBill(bill.attr('data-id'));
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
						$('#set_billcomplete').val(bill.complete_id);

						formDataInsert(bill);

						formDataDetailInsert(billdetail);

						//	force check zero have hidden
						$('*[for=totalzero]').hide();

						frm.removeClass('d-none');

						//	disable button modal
						$('.modal-bill').attr('disabled','disabled');

					})
					.catch(function(err) {
						console.log(`error : ${err}`)
						search_result.text('ไม่พบข้อมูล');
					})
			}

			function get_orderSupplierBill(bill_id) {
				displayLoading('.search_resultsupplier ');
				displayLoading('.search_resultissue ');

				var data = new FormData();
				data.append("bill_id", bill_id);

				//	วิธี fetch แบบ error handling
				fetch('get_orderSupplierBill', {
						method: 'POST',
						body: data
					})
					.then(res => res.json())
					.then((resp) => {
						hideLoading('.search_resultsupplier ');
						hideLoading('.search_resultissue ');

						let dataresult = resp;
						let resultSearch = "";
						let bill = resp.data;
						let billdetail = resp.datadetail;

						// console.log(resp);
						$('#bill_id').val(bill.id);
						$('#bill_code').val(bill.code);
						$('#set_billcomplete').val(bill.complete_id);

						formDataInsertSup(bill);

						formDataDetailInsertsup(billdetail);

						formDataDetailInsert(billdetail);

						//	force check zero have hidden
						$('*[for=totalzero]').hide();

						frm.removeClass('d-none');

						$('#form').removeClass('d-none');
						$('#form .form-group').first().addClass('d-none');

						//	disable button modal
						$('.modal-bill').attr('disabled','disabled');

					})
					.catch(function(err) {
						console.log(`error : ${err}`)
						$('.search_resultsupplier ').text('ไม่พบข้อมูล');
					})
			}

			function get_orderIssueBill(bill_id) {
				displayLoading('.search_resultsupplier ');
				displayLoading('.search_resultissue ');

				var data = new FormData();
				data.append("bill_id", bill_id);

				//	วิธี fetch แบบ error handling
				fetch('get_orderIssueBill', {
						method: 'POST',
						body: data
					})
					.then(res => res.json())
					.then((resp) => {
						hideLoading('.search_resultsupplier ');
						hideLoading('.search_resultissue ');

						let dataresult = resp;
						let resultSearch = "";
						let bill = resp.data;
						let billdetail = resp.datadetail;

						// console.log(resp);
						$('#bill_id').val(bill.id);
						$('#bill_code').val(bill.code);
						$('#set_billcomplete').val(bill.complete_id);

						formDataInsertSup(bill);

						formDataDetailInsertsup(billdetail);

						$('.tabledetail tbody').html('');
						if(bill.type != 3){	//	3 = แปลงสินค้า
							formDataDetailInsert(billdetail);
							//	disable button modal
							$('.modal-bill').attr('disabled','disabled');
						}

						if(bill.type == 3){	//	3 = แปลงสินค้า
							//	disable button modal
							$('.modal-bill').removeAttr('disabled');
						}

						//	force check zero have hidden
						$('*[for=totalzero]').hide();

						frm.removeClass('d-none');

						$('#form').removeClass('d-none');
						$('#form .form-group').first().addClass('d-none');

					})
					.catch(function(err) {
						console.log(`error : ${err}`)
						$('.search_resultsupplier ').text('ไม่พบข้อมูล');
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
				$('#searchsup').val(null);
				$('#searchissue').val(null);

				$(elename).removeClass('d-none');
				frm.addClass('d-none');

				document.frm.reset();
				$('.thumbnail-image').empty();

				$(elename).html(loading);
			}

			function hideLoading(elename) {
				$(elename).addClass('d-none');
			}

			//	set for show modal *** delete
			//	set step auto
			/* $('.modal-addbill').modal('show');
			var modaladdbill = $('.modal-addbill');

			$('.btn-order').trigger('click');
			get_orderSearchBill('16286'); */

			//	call detail	===============
			/* $('.modal-addbill').modal('show');
			var modaladdbill = $('.modal-addbill');

			$('.btn-sup').trigger('click');
			get_orderSearchSupbill('sp'); */
			//	===========================

			//	cancel modal
			$('.modal-addbill').on('hide.bs.modal', function() {
				displayLoading();
				$('.search_result').addClass('d-none');
				$('.search_resultsupplier').addClass('d-none');
				$('.search_resultissue').addClass('d-none');

				$('#selecttype ').removeClass('d-none');
				$('#form').addClass('d-none');

				$('#formsupplier').addClass('d-none');

				$('#formissue').addClass('d-none');

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

			function get_orderSearchSupbill(ordername) {
				displayLoading('.search_resultsupplier');

				var data = new FormData();
				data.append("searchorder", ordername);

				//	วิธี fetch แบบ error handling
				fetch('get_orderSearchSubbill', {
						method: 'POST',
						body: data
					})
					.then(res => res.json())
					.then((resp) => {
						let dataresult = resp.data;
						let resultSearch = "ไม่พบข้อมูล";
						// console.log(resp);
						if (resp.data) {
							resultSearch = "";
							for (var k in dataresult) {
								resultSearch += `<span role="button" data-id="${dataresult[k]['id']}">`;
								resultSearch += `<p>เลข ${dataresult[k]['code']}`;
								resultSearch += ` ชื่อซัพพลาย ${dataresult[k]['name']}</p>`;
								resultSearch += `</span>`;
							}
						}
						// console.log(resultSearch);
						$('.search_resultsupplier').html(resultSearch);
					})
					.catch(function(err) {
						console.log(`error : ${err}`)
						$('.search_resultsupplier').text('ไม่พบข้อมูล');
					})
			}

			function get_orderSearchIssuebill(ordername) {
				displayLoading('.search_resultissue');

				var data = new FormData();
				data.append("searchorder", ordername);

				//	วิธี fetch แบบ error handling
				fetch('get_orderSearchIssue', {
						method: 'POST',
						body: data
					})
					.then(res => res.json())
					.then((resp) => {
						let dataresult = resp.data;
						let resultSearch = "ไม่พบข้อมูล";
						// console.log(resp);
						if (resp.data) {
							resultSearch = "";
							for (var k in dataresult) {
								resultSearch += `<span role="button" data-id="${dataresult[k]['id']}">`;
								resultSearch += `<p>เลข ${dataresult[k]['code']}`;

								if(dataresult[k]['type'] == 1 ){
									resultSearch += ` ชื่อผู้ยืม ${dataresult[k]['name']}`;
								}else{
									resultSearch += ` ${dataresult[k]['type']}`;
								}
								resultSearch += ` : สินค้า ${dataresult[k]['product']}`;
								resultSearch += ` จำนวน ${dataresult[k]['qty']}</p>`;
								resultSearch += `</span>`;
							}
						}
						// console.log(resultSearch);
						$('.search_resultissue').html(resultSearch);
					})
					.catch(function(err) {
						console.log(`error : ${err}`)
						$('.search_resultissue').text('ไม่พบข้อมูล');
					})
			}

			//  ========= Begin add product   ========
            $(document).on("click", ".modal-bill", function() {
                $('#productqty').val('');
                var html2 = '';
                $("#select-productmain").selectpicker('refresh');
                $("#select-productlist").html(html2).selectpicker('refresh');
            });

            $('.bd-example-modal-lg').on('shown.bs.modal', function() {
                // $("#select-productmain").eq(0).prop('checked');
                $("#select-productmain").val("");
                $(".modal-footer .htmlvalidate").html('');
                $('.selectpicker').selectpicker('refresh')
            });

            $('#select-productlist').change(function() {
                $('#productqty').val('');
            });
            $('#select-productmain').change(function() {
                var option = $(this).find('option:selected'),
                id = option.val();
                name = option.data('name');
                $("#select-productlist").empty();
                $("#select-productlist").removeAttr('disabled')
                ajaxprolist(id);
            });

            function ajaxprolist(id) {
                $.ajax({
                    url: "ajaxselectproductmain",
                    type: 'POST',
                    data: {
                        action: 'my_special_ajax_call',
                        val: id
                    },
                    success: function(results) {
                        var obj = jQuery.parseJSON(results);
                        var html = '';
                        html += ' <option value="">โปรดเลือกรายการเมนู</option>';
                        $.each(obj, function(index, value) {
                            html += ' <option value="' + value.ID + '">' + value.NAME_TH + '</option>';
                        });
                        $("#select-productlist").html(html).selectpicker('refresh');
                    }
                });
            }

            $(document).on("click", "#add-order", function() {
                if ($('.modal_add_bill').is(':visible')) {
                    var frmmodal = $('#frmnew');
                } else {
                    var frmmodal = $('#frmstore');
                }

                var productmainID = $('#select-productmain').val();
                var productlistID = $('#select-productlist').val();

                $('.selectpicker').selectpicker('refresh')

                var qty = $('#productqty').val();
                var result = [{
                    "html": "#select-productmain",
                    "th": "เมนูหลัก"
                }, {
                    "html": "#select-productlist",
                    "th": "รายการเมนู"
                }, {
                    "html": "#productqty",
                    "th": "จำนวน"
                }];


                async function runcheck() {
                    let result1 = await checkValidateProductList(result);

                    if (result1.error_code == 0) {
                        let datasetting = {
                            plist_id: $('#select-productlist').val(),
                            pqty: $('#productqty').val(),
                            form: frmmodal
                        }

						//	choose item only one
						if($('.tabledetail tbody tr').length < 1){
                        	createListProduct(datasetting);
						}
                    } else {
                        $('.modal-footer .htmlvalidate').html('<span class="text-validate text-danger float-left"> โปรดระบุ ' + result1.txt + '</span>');
                    }
                }
                runcheck();
            });

            function checkValidateProductList(result) {
                // let param;
                let error = 0;
                let cal = "unknow";

                $.each(result, function(key, value) {
                    param = $(value.html).val();

                    if (!param) {
                        error = 1;
                        cal = value.th;
                        return false;
                    }
                });

                return new Promise((resolve, reject) => {
                    resolve({
                        error_code: error,
                        txt: cal
                    })
                })
            }

            function createListProduct(dataarray) {
                // console.log(dataarray);
                var data = new FormData();

                data.append("billid", "");
                data.append("pid", dataarray.plist_id);
                data.append("pqty", dataarray.pqty);

                fetch('get_product', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        if(!resp){
                            $('.modal-footer .htmlvalidate').html('<span class="text-validate text-danger float-left"> มีข้อมูลอยู่แล้ว </span>');
                        }else{
                            let totalprice = resp.qty * resp.price;

                            let billdetail = [{
                                list: resp.list,
                                product_name: resp.name_th,
                                product_price: resp.price,
                                product_qty: resp.qty,
                                product_totalprice: formatMoney(totalprice),
                                prolist: resp.id,
                                promain: resp.main,
                                product_rowid: resp.id
                            }]
                            formDataDetailAppend(billdetail);

                            //  close modal
                            $('.bd-example-modal-lg').modal('hide');
                        }
                        
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`);
                    })

            }
            function formDataDetailAppend(billdetail) {
                let hmtl = "";

                // console.log(billdetail);
                let number = $('.bill_sup_product tbody tr').length;
                if (billdetail.length) {

                    billdetail.forEach(function(key, val) {
                        let index = number + 1;
                        let product_name = key.product_name;
                        let product_price = formatMoney(key.product_price);
                        let product_qty = key.product_qty;
                        let product_totalprice = key.product_totalprice;
                        let promain = key.promain;
                        let prolist = key.prolist;
                        let list = key.list;

                        let iddetail = key.product_rowid;

                        let button_del = '<button type="button" class="btn btn-sm btn-danger btn-del"><i class="fa fa-close"></i></button>';
                        let input_qty = '<input type="text" value="' + product_qty + '" class="w-100 input-qty" data-id="' + iddetail + '" data-promain="' + promain + '" data-prolist="' + prolist + '" data-list="' + list + '" data-price="' + product_price + '" OnKeyPress="return checkNumber(this)">';

                        hmtl += '<tr data-row="' + index + '">';
                        hmtl += '<td class="">'+ button_del +'</td>';
                        hmtl += '<td class="index">' + index + '</td>';
                        hmtl += '<td class="name">' + product_name + '</td>';
                        hmtl += '<td class="price">' + product_price + '</td>';
                        hmtl += '<td class="qty">' + input_qty + '</td>';
                        hmtl += '<td class="totalprice text-right">' + product_totalprice + '</td>';
                        hmtl += '</tr>';
                    })

                    frm.find('.tabledetail tbody').append(hmtl);
                }
            }
            //  ========= End add product   ========

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