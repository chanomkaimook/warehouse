<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("structer/backend/head.php"); ?>
    <link rel="stylesheet" href="<?php echo $base_bn; ?>frontend/bootstrap-select/css/bootstrap-select.css">
    <style>
        .selectstyle {
            height: calc(1.25rem + 2px);
            font-size: 0.7rem;
            padding: 0rem .75rem;
            width: 25%;
        }

        .bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
            width: 100%;
        }

        .btn-light {
            color: #1f2d3d;
            background-color: #ffffff;
            border: 1px solid #ced4da;
            box-shadow: none;
            font-size: 0.7rem;
        }
    </style>
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
                            <h1><?php echo $submenu; ?></h1>
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

            <section class="content">
                <div class="container-fluid">
                    <div class="row">

                        <section class="col-lg-12 connectedSortable">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i> Manage <?php echo $submenu; ?> </h3>
                                </div>
                                <div class="card-body">

                                    <form id="demo2" name="demo2" class="demo" enctype="multipart/form-data" accept-charset="utf-8" method="post">
                                        <input type="hidden" id="prolist_id" name="prolist_id" value="<?php echo $UPproductlist->ID; ?>">
                                        <div class="titel text-left"> <i class="fa fa-database" aria-hidden="true"></i> Data Management </div>
                                        <div class="form-row">

                                            <label class="form-group col-md-3 text-right" for="name_th"> เลือกเมนูหลัก </label>
                                            <div class="form-group col-md-9 ">
                                                <select id="select-productmain" name="select-productmain" class="selectpicker" data-live-search="true">

                                                    <?php
                                                    $setlist = "";

                                                    if ($UPproductlist->ID) {
                                                        foreach ($Query_productmain->result() as $row) { ?>
                                                            <option <?php if ($UPproductlist->PROMAIN_ID == $row->ID) {
                                                                        echo 'selected';
                                                                        if ($row->ID == 6) {
                                                                            $setlist = "on";
                                                                        }
                                                                    } ?> value="<?php echo $row->ID ?>"><?php echo $row->NAME_TH; ?></option>
                                                        <?php   }
                                                    } else {
                                                        echo ' <option  value=""> -- โปรดเลือกเมนู -- </option>';
                                                        foreach ($Query_productmain->result() as $row) {

                                                        ?>
                                                            <option value="<?php echo $row->ID ?>"><?php echo $row->NAME_TH; ?></option>
                                                    <?php
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                            <?php
                                            if ($setlist == "on") {
                                                $sql = $this->db->select('id,codemac,name_th')
                                                    ->from('retail_productlist')
                                                    ->where('status', 1)
                                                    ->where('promain_id not in (6,12,14,15,16)')
                                                    ->where('id not in(279,278,277)');
                                                $q = $sql->get();
                                                $num = $q->num_rows();
                                            ?>
                                                <label class="form-group col-md-3 text-right" for="code"> ผูกสินค้า </label>
                                                <div class="form-group col-md-9 ">
                                                    <select id="select-listid" name="select-listid" class="selectpicker" data-live-search="true">
                                                        <option value="">เลือกสินค้าผูกกับโปร</option>
                                                        <?php
                                                        if ($num) {    
                                                            foreach ($q->result() as $r) {

                                                                $selected_listid = "";
                                                                if($UPproductlist->LIST_ID == $r->id){
                                                                    $selected_listid = "selected=selected";
                                                                }

                                                                //  name
                                                                if($r->codemac ? $codemac = "(".$r->codemac.")" : $codemac = "");
                                                                $name = $r->name_th."".$codemac;
                                                        ?>
                                                                <option value="<?php echo $r->id; ?>" <?php echo $selected_listid; ?> ><?php echo $name; ?></option>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <ul class="text-secondary">
                                                        <li><span>บิลที่เปิดด้วยรายการสินค้านี้จะเปลี่ยนแปลงรายการที่ผูกให้อัตโนมัติ</span></li>
                                                        <li><span>หากบิลที่มีรายการสินค้านี้ถูกเปิดใบลดหนี้ หรือใบส่งของไปแล้ว จะไม่มีการเปลี่ยนแปลงรายการที่ผูกภายในบิล</span></li>
                                                    </ul>
                                                        
                                                    
                                                    
                                                </div>
                                            <?php
                                            }
                                            ?>
                                            <label class="form-group col-md-3 text-right" for="code"> Code </label>
                                            <div class="form-group col-md-9 ">
                                                <input type="text" class="form-control " name="code" id="code" placeholder="กำหนดชื่อ code" value="<?php echo $UPproductlist->CODE; ?>">
                                            </div>

                                            <label class="form-group col-md-3 text-right" for="name_th"> ชื่อรายการเมนู | TH</label>
                                            <div class="form-group col-md-9 ">
                                                <input type="text" class="form-control " name="name_th" id="name_th" placeholder="ชื่อรายการเมนู" value="<?php echo $UPproductlist->NAME_TH; ?>">
                                            </div>

                                            <label class="form-group col-md-3 text-right" for="name_us"> ชื่อรายการเมนู | US </label>
                                            <div class="form-group col-md-9 ">
                                                <input type="text" class="form-control " name="name_us" id="name_us" placeholder="ชื่อรายการเมนู" value="<?php echo $UPproductlist->NAME_US; ?>">
                                            </div>

                                            <label class="form-group col-md-3 text-right" for="price"> กำหนดราคา </label>
                                            <div class="form-group col-md-9 ">
                                                <input type="text" class="form-control " name="price" id="price" placeholder="กำหนดราคา" value="<?php echo $UPproductlist->PRICE; ?>">
                                            </div>


                                            <?php
                                            $status1_checked = "";
                                            $status2_checked = "";

                                            if ($this->input->get('prolist_id') != '') {
                                                if ($UPproductlist->STATUS == 1) {
                                                    $status1_checked = 'checked';
                                                }
                                                if ($UPproductlist->STATUS == 0) {
                                                    $status2_checked = 'checked';
                                                }
                                            } else {
                                                $status1_checked = 'checked';
                                            }

                                            ?>

                                            <label class="form-group col-md-3 text-right" for="status"> สถานะการแสดงผล</label>
                                            <div class="form-group col-md-9">
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio" id="status1" name="status" <?php echo $status1_checked; ?>>
                                                    <label for="status1" class="custom-control-label">ทำงาน</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio" id="status2" name="status" <?php echo $status2_checked; ?>>
                                                    <label for="status2" class="custom-control-label">ปิดการทำงาน</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio" id="status3" name="status" <?php echo $status2_checked; ?>>
                                                    <label for="status3" class="custom-control-label">ลบจากระบบ</label>
                                                </div>
                                            </div>

                                        </div>

                                        <hr>
                                        <div class="row">
                                            <label class="form-group col-md-3"> </label>
                                            <div class="col-md-9 ">
                                                <?php if ($this->input->get('prolist_id') != '') {
                                                    echo '<button type="button" class="btn btn-default btn-sm" id="Save"><li class="fa fa-floppy-o"> </li> Update</button>';
                                                } else {
                                                    echo '<button type="button" class="btn btn-default btn-sm" id="Save"><li class="fa fa-floppy-o"> </li> Save </button>';
                                                } ?>
                                                <button type="button" class="btn btn-default btn-sm" id="cancel">
                                                    <li class="fa fa-angle-double-left"> </li> Back Main
                                                </button>
                                            </div>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </section>

        </div>
        <?php include("structer/backend/footer.php"); ?>
        <?php include("structer/backend/script.php"); ?>
        <script src="<?php echo $base_bn; ?>frontend/bootstrap-select/js/bootstrap-select.js"></script>
    </div>
    <script>
        $(document).ready(function() {

            $("#cancel").on("click", function(e) {
                window.location.replace('product');
            });

            $("#Save").on("click", function(e) {
                var result = ["name_th", "name_us"];
                for (var x = 0; x < result.length; x++) {
                    if (document.forms["demo2"][result[x]].value == '') {
                        swal("เกิดข้อผิดผลาด", "กรอกข้อมูลให้ครบถ้วน / please insert data", "warning");
                        document.getElementById(result[x]).focus();
                        return false;
                    }
                }
                dataform();
            });

            function dataform() {

                if (document.getElementById("status1").checked == true) {
                    var status = "1";
                } else if (document.getElementById("status2").checked == true) {
                    var status = "0";
                } else {
                    var status = "3";
                }

                var data = new FormData();
                var d = document;
                var prolist_id = '';
                if ($('#prolist_id').val()) {
                    prolist_id = $('#prolist_id').val();
                }
                data.append("prolist_id", prolist_id);
                data.append("name_th", d.getElementById('name_th').value);
                data.append("name_us", d.getElementById('name_us').value);
                data.append("promain_id", d.getElementById('select-productmain').value);
                data.append("price", d.getElementById('price').value);
                data.append("code", d.getElementById('code').value);

                let select_listid = $('#select-listid');
                if(select_listid.length){
                    if(select_listid.val() == ""){
                        swal("ผิดผลาด", "กรุณาระบุช่องผูกสินค้า", "warning");

                        return false;
                    }
                    data.append("listid", select_listid.val());
                }

                data.append("status", status);

                var settings2 = {
                    "crossDomain": true,
                    "url": "ajaxdataProlistForm",
                    "method": "POST",
                    "type": "POST",
                    "processData": false,
                    "contentType": false,
                    "mimeType": "multipart/form-data",
                    "data": data
                }
                $.ajax(settings2).done(function(response) {
                        var obj = jQuery.parseJSON(response);
                        if (obj.error_code == 1) {
                            swal("ผิดผลาด", obj.txt, "warning");
                        } else {
                            swal("บันทึกข้อมูลเรียบร้อย", obj.txt, "success");
                            $(".swal-button").on("click", function(e) {
                                window.location.replace('product_insertlist?prolist_id=' + obj.getid);
                            });

                        }
                    })
                    .fail(function(response) {
                        console.log('Error : ' + response);
                    })
            }


        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>

</body>

</html>