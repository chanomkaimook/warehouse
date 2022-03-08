<div id="form">

    <form action="" id="frm" name="frm" class="d-none">

        <input type="hidden" id="bill_id" name="bill_id" value="">
        <input type="hidden" id="bill_code" name="bill_code" value="">
        <div class="">

            <div class="row print-header text-center mb-4" style="display:none">
                <h1>เอกสาร - ใบเบิก</h1>
            </div>

            <div class="row">
                <div class="d-flex col-xs-3 col-lg-3">
                    <label for="">เลขบิล :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_code"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-3 col-lg-3">
                    <label for="">ประเภท :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_type"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-3 col-lg-3">
                    <label for="">ผู้ยืม :</label>
                    <p class="">
                        <u class="dotted mx-2 billto"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-3 col-lg-3">
                    <label for="">สถานะ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_status"></u>
                    </p>
                </div>
            </div>



            <div class="row">
                <div class="d-flex col-xs-6 col-lg-6">
                    <label for="">โดย :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_staffcreate"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-3 col-lg-3">
                    <label for="">เมื่อ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_datecreate"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-3 col-lg-3">
                    <label for="">อ้างอิง :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_ref"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-6 col-lg-6">
                    <label for="">แก้ไขโดย :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_staffedit"></u>
                    </p>
                </div>
                <div class="d-flex col-xs-6 col-lg-6">
                    <label for="">แก้ไขเมื่อ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_dateedit"></u>
                    </p>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-sm-12">
                    <div class="p-2 bg-dark mt-2">
                        <label for="">รายการสินค้า</label>
                        <?php
                        if ($this->uri->segment(3) == 'editbill') {
                        ?>
                            <!-- <button type="button" class="btn btn-default btn-sm modal-bill float-right" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่ม </button> -->
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
                                    <th class="text-right">รับแล้ว</th>
                                    <th class="text-right">ขาด</th>
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
            <div id="block_convert" class="row border d-none">
                <div class="form-group col-sm-12">
                    <label for="">แปลงเป็น</label>
                    <div class="table-responsive">
                        <table class="tablereceiveconvertdetail w-100">
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
                <div class="form-group col-xs-12">
                    <label class="">รูปอ้างอิง </label>
                    <div class="bill-image  d-flex flex-wrap">

                    </div>
                </div>
            </div>
            <?php
            if ($method == 'editbill') {
            ?>
                <div class="row">
                    <div class="form-group col-xs-12">
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
                    <?php if (chkPermissPage('editissue')) { ?>
                        <button type="button" id="editform" class="btn btn-md px-5 btn-outline-primary ">แก้ไข</button>
                    <?php } ?>

                    <?php if (chkPermissPage('cancelissue')) { ?>
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