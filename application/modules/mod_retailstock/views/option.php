<div class="d-flex">
    <div class="flex-grow-1">
        <h5 class="header-title">Stock <small class="text-info">รอบตัดที่ <?php echo ($date_cut ? thai_date($date_cut) : ""); ?></small></h5>
        <p class="sub-header">ระบบ stock จะมีการเก็บยอดจำนวนตามรอบตัด เพื่อใช้ในการระบุจำนวนยอดสินค้า
            <font class="text-danger">(ห้ามแก้ไขรายการเอกสารที่ผ่านรอบตัดมาแล้ว)</font>
        </p>
    </div>
    <div class="">

        <a href="<?php echo site_url("mod_retailstock/ctl_retailstock/stock"); ?>">
            <i class="fa fa-undo" aria-hidden="true"></i> Stock รายวัน
        </a>
    </div>
</div>


<!-- 	Begin button option	 -->
<div class="row">

    <div class="col-md-3">
        <div class="border border-mute p-2">
            <div class="textselect">
                <span>เลือกฟิลด์ที่มีจำนวน</span>
            </div>
            <div class="d-flex flex-wrap">

                <div class="form-check-inline">
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input check-field-column" id="field-bill">
                        <label class="form-check-label" for="field-bill">จำหน่าย</label>
                    </div>
                </div>
                <div class="form-check-inline">
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input check-field-column" id="field-receive">
                        <label class="form-check-label" for="field-receive">รับเข้า</label>
                    </div>
                </div>
                <div class="form-check-inline">
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input check-field-column" id="field-issue">
                        <label class="form-check-label" for="field-issue">เบิก</label>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="d-flex">
            <div class="">

                <div class="form-group">
                    <button id="listmonth" type="button" class="btn btn-success btn-sm w-100" value="<?php echo date('Y-m'); ?>">stock เดือนนี้</button>
                </div>
                <div class="form-group">
                    <select name="" id="select_listmonth" class="form-control form-control-sm bg-warning">
                        <option value="">เลือกเดือน</option>
                        <?php
                            $sql_stcut = $this->db->select('date_starts')
                                ->from('retail_stock')
                                ->where('retail_stock.status', 1)
                                ->group_by('month(retail_stock.date_starts)')
                                ->group_by('year(retail_stock.date_starts)');
                            $q_stcut = $this->db->get();
                            $num_stcut = $q_stcut->num_rows();
                            if ($num_stcut) {
                                foreach ($q_stcut->result() as $r_stcut) {
                                    $idyear = date('Y-m', strtotime($r_stcut->date_starts));        //	43 from 543 thia date
                                    $thaiyear = date('y', strtotime($r_stcut->date_starts)) + 43;   //	43 from 543 thia date
                                    $dataarray[$idyear] = $thaiyear;
                                }
                            }

                            if ($dataarray) {
                                $year = date('Y');
                                $month = date('m');
                                $keyset = $year."-".$month;
                                $thaiyearset = $year + 43;   //	43 from 543 thai date

                                if (!array_search($year."-".$month, $dataarray) !== false) {
                                    $dataarray[$keyset] = $thaiyearset;
                                }
                            }

                            if ($dataarray) {
                                foreach ($dataarray as $key => $value) {
                                    echo "<option value='" . $key . "'>" . thai_date_month($key) . " " . substr($value, -2, 2) . "</option>";
                                }
                            }
                        ?>
                    </select>
                </div>

            </div>

        </div>
    </div>
</div>
<!-- 	End button option	 -->