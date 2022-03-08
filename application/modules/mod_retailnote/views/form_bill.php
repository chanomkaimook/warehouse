<div id="form">

    <form action="" id="frm" name="frm" class="d-none">

        <input type="hidden" id="bill_id" name="bill_id" value="">
        <input type="hidden" id="bill_code" name="bill_code" value="">
        <div class="">


            <div class="row">
                <div class="d-flex col-md-6 col-lg-6">
                    <label for="">เลขบิล :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_code"></u>
                    </p>
                </div>
                <div class="d-flex col-md-3 col-lg-3">
                    <label for="">สถานะ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_status"></u>
                    </p>
                </div>
                <div class="d-flex col-md-3 col-lg-3">
                    <label for="">ผู้ตรวจสอบ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_approve_store"></u>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="d-flex col-md-6 col-lg-6">
                    <label for="">โดย :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_staffcreate"></u>
                    </p>
                </div>
                <div class="d-flex col-md-6 col-lg-6">
                    <label for="">เมื่อ :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_datecreate"></u>
                    </p>
                </div>
                <div class="d-flex col-md-6 col-lg-6">
                    <label for="">แก้ไขโดย :</label>
                    <p class="">
                        <u class="dotted mx-2 bill_staffedit"></u>
                    </p>
                </div>
                <div class="d-flex col-md-6 col-lg-6">
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
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <!-- <input type="text" id="product" name="product" value="444" > -->
                    </div>
                </div>

            </div>

            <!-- total score ส่วนรับ -->


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
            <!-- <div class="row">
                <div class="form-group col-md-12">
                    <label class="">รูปอ้างอิง </label>
                    <div class="bill-image  d-flex flex-wrap">

                    </div>
                </div>
            </div> -->

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
                    <?php if (chkPermissPage('editnote')) { ?>
                        <button type="button" id="editform" class="btn btn-md px-5 btn-outline-primary ">แก้ไข</button>
                    <?php } ?>

                    <?php if (chkPermissPage('cancelnote')) { ?>
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