<!-- Modal Creditnote -->
<div class="modal fade modal_add_bill" role="dialog" aria-labelledby="" aria-hidden="true" style="z-index:1050">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h6 class="modal-title mt-0">สร้างบิลต่อจากเอกสารนี้</h6>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="loading text-center"></div>
                <form action="" id="frmnew" name="frmnew" class="d-none">

                    <input type="hidden" id="bill_id" name="bill_id" value="">
                    <input type="hidden" id="bill_code" name="bill_code" value="">
                    <input type="hidden" id="statuscomplete" name="statuscomplete" value="2">
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

                            <div class="form-group col-md-4">
                                <label for="">วันที่สร้างบิล <span class="is-required">(* ระบุ)</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" class="form-control " name="order_date" id="order_date" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">ช่องทางจัดส่ง <span class="is-required">(* ระบุ)</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-cubes"></i></span>
                                    </div>
                                    <select class="custom-select " name="deliveryid" id="deliveryid">
                                        <option value=""> เลือกรูปแบบการจัดส่ง </option>
                                        <?php
                                        $sql = $this->db->select('*')
                                            ->from('delivery')
                                            ->where('status', 1)
                                            ->get();
                                        foreach ($sql->result() as $row) {
                                            echo '<option value="' . $row->ID . '"> ' . $row->NAME_US . ' </option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">ช่องทางรับรายการ <span class="is-required">(* ระบุ)</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-paper-plane"></i></span>
                                    </div>
                                    <select class="custom-select " name="methodid" id="methodid">
                                        <option value=""> เลือกช่องทางรับรายการ </option>
                                        <?php
                                        $sql = $this->db->select('*')
                                            ->from('retail_methodorder')
                                            ->where('status', 1)
                                            ->get();
                                        foreach ($sql->result() as $row) {
                                            echo '<option value="' . $row->ID . '"> ' . $row->TOPIC . ' </option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-3">
                                <label class="">ชื่อ-นามสกุล</label>
                                <span class="is-required">(* ระบุ)</span>
                                <input type="text" class="form-control " name="cust_name" id="cust_name" placeholder="ชื่อ-นามสกุล">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="">เบอร์โทรศัพท์</label>
                                <span class="is-required">(* ระบุ)</span>
                                <input type="text" class="form-control " name="cust_tel" id="cust_tel" placeholder="เบอร์โทรศัพท์">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="">รหัสไปรษณีย์</label>
                                <span class="is-required">(* ระบุ)</span>
                                <input type="number" class="form-control " name="cust_zipcode" id="cust_zipcode" placeholder="รหัสไปรษณีย์">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="">Text Code : </label>
                                <input type="text" class="form-control " name="cust_textcode" id="cust_textcode" placeholder="Text Code...." onblur="checkTextcode();">
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="">ที่อยู่</label>
                                <span class="is-required">(* ระบุ)</span>
                                <textarea rows="2" class="form-control" name="cust_address" id="cust_address" placeholder="ที่อยู่"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="">เลขที่เสียภาษี/เลขที่บัตรประชาชน</label>
                                <input type="text" class="form-control " name="cust_textnumber" id="cust_textnumber" placeholder="เลขที่เสียภาษี/เลขที่บัตรประชาชน">
                            </div>
                        </div>

                        <!-- Bank -->
                        <div class="row p-2 bg-light" style="border-radius: 1rem;border: 1px solid #9E9E9E;">
                            <div class="form-group col-md-6">
                                <label>ธนาคารที่โอน</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-university"></i></span>
                                    </div>
                                    <select class="custom-select " name="bankid" id="bankid">
                                        <option value=""> เลือกธนาคารที่โอน </option>
                                        <?php
                                        $sql = $this->db->select('*')
                                            ->from('bank')
                                            ->where('status', 1)
                                            ->get();
                                        foreach ($sql->result() as $row) {
                                            echo '<option value="' . $row->ID . '"> ' . $row->NAME_TH . ' </option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>วันที่โอนเงิน</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" class="form-control " name="transfereddate" id="transfereddate">
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>เวลาโอนเงิน </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
                                    </div>
                                    <input type="time" class="form-control " name="transferedtime" id="transferedtime">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="">จำนวนเงิน</label>
                                <input type="number" class="form-control " name="amount" id="amount" placeholder="จำนวนเงิน - Amount" style="height: 62px;">
                            </div>
                            <div class="form-group col-md-8">
                                <label class="">หมายเหตุ</label>
                                <textarea rows="2" class="form-control" name="transferedremark" id="transferedremark" placeholder="กรณีโอนมากว่า 1 รายการกรุณาระบุเลข Invoice/Transfered More Then 1 Order"></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="slipold" value="">
                                    <label for="slipold" class="custom-control-label pr-4">ดึงสลิปจากบิลอ้างอิงแนบด้วย</label>
                                </div>
                            </div>

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


                        <div class="row">

                            <div class="form-group col-sm-12">
                                <div class="p-2 bg-dark mt-2">
                                    <label for="">รายการสินค้า</label>
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
                                <input type="text" class="billnumber bg-warning text-right form-control form-control-sm col-2 bill_discount" value="" data-loop="bill_loop" data-name="bill_discount" OnKeyPress="return checkPrice(this)">
                            </div>
                            <div class="mb-1 col-sm-12 d-flex">
                                <label for="" class="col-10 text-dark">ค่าธรรมเนียมเก็บเงินปลายทาง </label>
                                <input type="text" class="billnumber bg-danger text-right form-control form-control-sm col-2 bill_tax" value="" data-loop="bill_loop" data-name="bill_tax" OnKeyPress="return checkPrice(this)">
                            </div>
                            <div class="mb-1 col-sm-12 d-flex">
                                <label for="" class="col-10 text-primary">ยอดรวมสุทธิ</label>
                                <input disabled type="text" id="totalamount" class="text-right form-control form-control-sm col-2 bill_net" value="" data-loop="bill_loop" data-name="totalamount">
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="">
                                    <ul class="nav nav-pills pull-left mb-1 billstatus">
                                        <li class="nav-item" style="background-color: #33333317;border-radius: 5px;margin: 0.1rem;">
                                            <button type="button" class="btn btn-defaultnl btn-sm nav-link" data-toggle="tab" value="0" data-value="T"> โอนเงิน </button>
                                        </li>
                                        <li class="nav-item" style="background-color: #33333317;border-radius: 5px;margin: 0.1rem;">
                                            <button type="button" class="btn btn-defaultnl btn-sm nav-link" data-toggle="tab" value="5" data-value="C"> เก็บเงินที่หลัง </button>
                                        </li>
                                        <li class="nav-item" style="background-color: #33333317;border-radius: 5px;margin: 0.1rem;">
                                            <button type="button" class="btn btn-defaultnl btn-sm nav-link active" data-toggle="tab" value="2" data-value="F"> อื่นๆ </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label for="remark">หมายเหตุ</label>
                                <textarea id="remark" name="remark" class="form-control" rows="2"></textarea>
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