<!-- Modal Creditnote -->
<div class="modal fade modal_add_store" role="dialog" aria-labelledby="" aria-hidden="true" style="z-index:1050">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h6 class="modal-title mt-0">สร้างใบรับสินค้า</h6>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="loading text-center"></div>
                <form action="" id="frmstore" name="frmstore" class="d-none">

                    <input type="hidden" id="bill_id" name="bill_id" value="">
                    <input type="hidden" id="bill_code" name="bill_code" value="">

                    <div class="">

                        <div class="row">
                            <div class="d-flex col-md-6 col-lg-3">
                                <label for="">เปิดบิลอ้างอิงจากบิลนี้ </label>
                                <p class="">

                                </p>
                            </div>

                            <div class="d-flex col-md-6 col-lg-3">
                                <label for="">เลขบิล :</label>
                                <p class="">
                                    <u class="dotted mx-2 bill_code"></u>
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
                        <style>
                            .is-required {
                                color: red;
                            }
                        </style>

                        <div class="row">

                            <div class="form-group col-sm-12">
                                <div class="p-2 bg-dark mt-2">
                                    <label for="">รายการสินค้า</label>
                                    <!-- <button type="button" class="btn btn-default btn-sm modal-bill float-right" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่ม </button> -->
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
                        <div class="row row-form-tool-btn">
                            <div class="col-sm-12 text-center form-tool-btn">
                                <button type="button" id="submitform" class="btn btn-md w-25 btn-outline-primary">บันทึก</button>
                                <button type="button" class="btn btn-md btn-danger" data-dismiss="modal" aria-hidden="true">ยกเลิก</button>
                            </div>
                        </div>
                </form>

            </div>

        </div>
    </div>
</div>