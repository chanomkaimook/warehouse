<!-- Modal Creditnote -->
<div class="modal fade modal_add_creditnote" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h6 class="modal-title mt-0">สร้างใบลดหนี้</h6>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="loading text-center"></div>
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
                    <?php if (chkPermissPage('addcreditnote')) { ?>
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
    </div>
</div>