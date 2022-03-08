<div id="form">

    <form action="" id="frm" name="frm" class="d-none">

        <div class="">

            <div class="row">
                <?php
                if ($method == 'viewbill') {
                    $element_suppliername = '<p class=""><u class="dotted mx-2 bill_supplier"></u></p>';
                }else{
                    $element_suppliername = '<input id="bill_supplier" name="bill_supplier" class="form-control" type="text">';
                }
                ?>
                <div class="d-flex w-100">
                    <div class="">
                        <label for="">supplier :</label>
                    </div>
                    <div class="col">
                        <?php echo $element_suppliername; ?>
                    </div>
                    
                    
                </div>
                <?php
                if ($method == 'viewbill') {
                ?>
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
                <?php
                }
                ?>
            </div>

        </div>

        <div class="row row-form-tool-btn mt-4">
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
                    <?php if (chkPermissPage('editsupplierlist')) { ?>
                        <button type="button" id="editform" class="btn btn-md px-5 btn-outline-primary ">แก้ไข</button>
                    <?php } ?>

                    <?php if (chkPermissPage('cancelsupplierlist')) { ?>
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