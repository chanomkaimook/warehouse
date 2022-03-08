<div id="form">

    <form action="" id="frm" name="frm" class="d-none">

        <input type="hidden" id="bill_id" name="bill_id" value="">
        <input type="hidden" id="bill_code" name="bill_code" value="">
        <div class="">

            <div class="row print-header text-center mb-4" style="display:none">
                <h1>เอกสาร - ใบเปิด supplier</h1>
            </div>

            <div class="row">
                <div class="d-flex col-xs-6 col-lg-3">
                    <label for="">เลขบิล :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_code"></u>
                    </p>
                </div>

                <div class="d-flex col-xs-6 col-lg-3">
                    <label for="">วันที่ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_datecreate"></u>
                    </p>
                </div>

                <div class="d-flex col-xs-6 col-lg-3">
                    <label for="">สถานะ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_complete"></u>
                    </p>
                </div>

                <div class="d-flex col-xs-6 col-lg-3">
                    <label for="">ตรวจสอบ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_check"></u>
                    </p>
                </div>
            </div>

            <div id="block_textowner" class="row">
                <div class="d-flex col-xs-12 col-lg-6">
                    <label for="">ชื่อ-นามสกุล :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_name"></u>
                    </p>
                </div>

                <div class="d-flex col-xs-12 col-lg-3">
                    <label for="">เบอร์โทร :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_tel"></u>
                    </p>
                </div>

                <div class="d-flex col-xs-12 col-lg-3">
                    <label for="">ภาษี :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_citizen"></u>
                    </p>
                </div>

                <div class="d-flex col-xs-12 col-lg-9">
                    <label for="">ที่อยู่ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_address"></u>
                    </p>
                </div>

                <div class="d-flex col-xs-12 col-lg-3">
                    <label for="">ปณ. :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_zipcode"></u>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="d-flex col-xs-12 col-lg-12">
                    <label for="">supplier :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_supplier"></u>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="d-flex col-xs-6 col-lg-3">
                    <label for="">โดย :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_staffcreate"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-6 col-lg-3">
                    <label for="">แก้ไข :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_staffupdate"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-6 col-lg-3">
                    <label for="">อ้างอิง1 :</label>
                    <?php
                    if ($method == 'editbill') {    
                    ?>
                    <input type="text" class="form-control form-control-sm w-75 bill_textcode" id="ref" name="ref" value="" placeholder="กรอกเลขอ้างอิง" >
                    <?php
                    }else{
                    ?>
                    <p class="">
                        <u class="dotted mx-2 bill_textcode"></u>
                    </p>
                    <?php
                    }
                    ?>
                </div>
                <div class="d-flex col-xs-6 col-lg-3">
                    <label for="">อ้างอิง2 :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_ref2"></u>
                    </p>
                </div>

            </div>

            <div id="block_textchecker" class="row">
                <div class="d-flex col-xs-6 col-lg-6">
                    <label for="">ผู้ตรวจสอบที่ 1 :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_appr_user"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-6 col-lg-6">
                    <label for="">ผู้ตรวจสอบที่ 2 :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_apst_user"></u>
                    </p>
                </div>
            </div>

            <div id="block_textsender" class="row justify-content-end">
                <div class="col-sm-12 px-4 pt-4 border border-secondary text-center">
                    <p class="htmltext-return text-success text-bold">** มีสินค้าส่งกลับจากลูกค้า(สินค้าที่ระบุเอาไว้จะนำส่งคืนคลัง)</p>
                    <p class="htmltext-loss text-danger text-bold d-none">** สินค้าที่ส่งให้ลูกค้าสูญเสีย</p>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-12">
                    <div class="p-2 bg-dark mt-2">
                        <label for="">รายการสินค้า</label>
                        <?php
                        if ($this->uri->segment(3) == 'editbill') {
                        ?>
                            <button type="button" class="btn btn-default btn-sm modal-bill float-right" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่ม </button>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="table-responsive">
                        <table class="tabledetail w-100">
                            <thead>
                                <tr>
                                    <th> </th>
                                    <th>ลำดับ</th>
                                    <th>สินค้า</th>
                                    <th width="60">จำนวน</th>
                                    <th class="text-right">รับแล้ว/คืนแล้ว</th>
                                    <th class="text-right">ขาดรับ/ขาดคืน</th>
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
            <div id="block_listreceive" class="row border">
                <div class="form-group col-sm-12">
                    <label for="">รายการที่รับ</label>
                    <div class="table-responsive">
                        <table class="tablereceivedetail w-100">
                            <thead>
                                <tr>
                                    <th> </th>
                                    <th width="200">เลขบิล</th>
                                    <th width="200">วันที่</th>
                                    <th>สินค้า</th>
                                    <th width="60">จำนวน</th>
                                    <th width="200">โดย</th>
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
            <div id="block_listissue" class="row border d-none">
                <div class="form-group col-sm-12">
                    <label for="">รายการที่คืน</label>
                    <div class="table-responsive">
                        <table class="tableissuedetail w-100">
                            <thead>
                                <tr>
                                    <th> </th>
                                    <th width="200">เลขบิล</th>
                                    <th width="200">วันที่</th>
                                    <th>สินค้า</th>
                                    <th width="60">จำนวน</th>
                                    <th width="200">โดย</th>
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
                    <?php if (chkPermissPage('editsupplier')) { ?>
                        <button type="button" id="editform" class="btn btn-md px-5 btn-outline-primary ">แก้ไข</button>
                    <?php } ?>

                    <?php if (chkPermissPage('cancelsupplier')) { ?>
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