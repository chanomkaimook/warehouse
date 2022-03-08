<div id="form">

    <form action="" id="frm" name="frm" class="d-none">

        <input type="hidden" id="bill_id" name="bill_id" value="">
        <input type="hidden" id="bill_code" name="bill_code" value="">
        <div class="">


            <div class="row">
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
                    <label for="">สถานะ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_complete"></u>
                    </p>
                </div>

                <div class="d-flex col-md-6 col-lg-3">
                    <label for="">ตรวจสอบ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_check"></u>
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

            <div class="row">
                <div class="d-flex col-md-6 col-lg-3">
                    <label for="">โดย :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_staffcreate"></u>
                    </p>
                </div>
                <div class="d-flex col-md-6 col-lg-3">
                    <label for="">แก้ไข :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_staffupdate"></u>
                    </p>
                </div>
                <div class="d-flex col-md-6 col-lg-3">
                    <label for="">อ้างอิง :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_textcode"></u>
                    </p>
                </div>
                <div class="d-flex col-md-6 col-lg-3">
                    <label for="">เลขที่ภาษีใบลดหนี้ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_codereport"></u>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="d-flex col-md-6 col-lg-6">
                    <label for="">ผู้ตรวจสอบที่ 1 :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_appr_user"></u>
                    </p>
                </div>
                <div class="d-flex col-md-6 col-lg-6">
                    <label for="">ผู้ตรวจสอบที่ 2 :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_apst_user"></u>
                    </p>
                </div>
            </div>

            <div class="row justify-content-end">
                <?php
                if ($method == 'editbill') {
                ?>
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
                <?php
                } else {
                ?>
                    <div class="col-sm-12 px-4 pt-4 border border-secondary text-center">
                        <p class="htmltext-return text-success text-bold">** มีสินค้าส่งกลับจากลูกค้า(สินค้าที่ระบุเอาไว้จะนำส่งคืนคลัง)</p>
                        <p class="htmltext-loss text-danger text-bold d-none">** สินค้าที่ส่งให้ลูกค้าสูญเสีย</p>
                    </div>
                <?php
                }
                ?>
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
                <?php
                if ($method == 'editbill') {
                    $bill_price = '<input disabled type="text" id="bill_price" class="text-right form-control form-control-sm col-2 bill_price" data-loop="bill_loop" data-name="bill_price" value="">';
                    $bill_parcel = '<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_parcel" value="" data-loop="bill_loop" data-name="bill_parcel" OnKeyPress="return checkPrice(this)">';
                    $bill_logis = '<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_logis" value="" data-loop="bill_loop" data-name="bill_logis" OnKeyPress="return checkPrice(this)">';
                    $bill_shor = '<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_shor" value="" data-loop="bill_loop" data-name="bill_shor" OnKeyPress="return checkPrice(this)">';
                    $bill_discount = '<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_discount" value="" data-loop="bill_loop" data-name="bill_discount" OnKeyPress="return checkPrice(this)">';
                    $bill_tax = '<input type="text" class="billnumber text-right form-control form-control-sm col-2 bill_tax" value="" data-loop="bill_loop" data-name="bill_tax" OnKeyPress="return checkPrice(this)">';
                    $bill_net = '<input disabled type="text" id="totalamount" class="text-right form-control form-control-sm col-2 bill_net" value="" data-loop="bill_loop" data-name="totalamount">';
                } else {
                    $bill_price = '<p class="text-right col-2 bill_price"></p>';
                    $bill_parcel = '<p class="text-right col-2 bill_parcel"></p>';
                    $bill_logis = '<p class="text-right col-2 bill_logis"></p>';
                    $bill_shor = '<p class="text-right col-2 bill_shor"></p>';
                    $bill_discount = '<p class="text-right col-2 bill_discount"></p>';
                    $bill_tax = '<p class="text-right col-2 bill_tax"></p>';
                    $bill_net = '<p class="text-right col-2 bill_net"></p>';
                }
                ?>
                <div class="mb-1 col-sm-12 d-flex">
                    <label for="" class="col-10 text-dark">รวมยอดขายสุทธิ</label>
                    <?php echo $bill_price; ?>
                </div>
                <div class="mb-1 col-sm-12 d-flex">
                    <label for="" class="col-10 text-dark">ค่ากล่องพัสดุ</label>
                    <?php echo $bill_parcel; ?>
                </div>
                <div class="mb-1 col-sm-12 d-flex">
                    <label for="" class="col-10 text-dark">ค่าบริการจัดส่ง</label>
                    <?php echo $bill_logis; ?>
                </div>
                <div class="mb-1 col-sm-12 d-flex">
                    <label for="" class="col-10 text-dark">ค่าธรรมเนียม shopee</label>
                    <?php echo $bill_shor; ?>
                </div>
                <div class="mb-1 col-sm-12 d-flex">
                    <label for="" class="col-10 text-dark">ส่วนลด</label>
                    <?php echo $bill_discount; ?>
                </div>
                <div class="mb-1 col-sm-12 d-flex">
                    <label for="" class="col-10 text-dark">ค่าธรรมเนียมเก็บเงินปลายทาง </label>
                    <?php echo $bill_tax; ?>
                </div>
                <div class="mb-1 col-sm-12 d-flex">
                    <label for="" class="col-10 text-primary">
                        <?php
                        if ($method == 'editbill') {
                        ?>
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
                        <?php
                        } else {
                        ?>
                            ยอดรวมสุทธิ
                        <?php
                        }
                        ?>

                    </label>
                    <?php echo $bill_net; ?>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-12">
                    <label for="remark">หมายเหตุ</label>
                    <?php
                    if ($method == 'editbill') {
                    ?>
                        <textarea id="remark" name="remark" class="form-control" rows="2"></textarea>
                    <?php
                    } else {
                    ?>
                        <p id="" name="">
                            <u id="remark" class="dotted mx-2"></u>
                        </p>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <style>
                .bill-image img {
                    max-height: 20rem;
                    height: auto;
                    max-width: 40rem;
                }
            </style>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="">รูปอ้างอิง </label>
                    <div class="bill-image  d-flex flex-wrap">

                    </div>
                </div>
            </div>
            <?php
            if ($method == 'editbill') {
            ?>
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
            <?php
            }
            ?>

        </div>

        <div class="row row-form-tool-btn">
            <div class="col-sm-12 text-center form-tool-btn">
                <?php
                if ($method == 'editbill') {
                    $textbtn_back = "กลับ";
                ?>
                    <button type="button" id="submitform" class="btn btn-md px-5 btn-outline-primary ">บันทึก</button>
                <?php
                } else {
                    $textbtn_back = "ปิด";
                ?>
                    <?php if(chkPermissPage('editcreditnote')){ ?>
                    <button type="button" id="editform" class="btn btn-md px-5 btn-outline-primary ">แก้ไข</button>
                    <?php } ?>

                    <?php if(chkPermissPage('cancelcreditnote')){ ?>
                    <button type="button" id="btn-cancel" class="btn btn-md btn-outline-danger float-right">ยกเลิกรายการ</button>
                    <?php } ?>
                <?php
                }
                ?>
                <button type="button" id="btn-back" class="btn btn-md btn-secondary"><?php echo $textbtn_back; ?></button>


            </div>
        </div>

    </form>

</div>