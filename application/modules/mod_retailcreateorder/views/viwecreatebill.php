<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("structer/backend/head.php"); ?>
    <link rel="stylesheet" href="<?php echo $base_bn; ?>frontend/bootstrap-select/css/bootstrap-select.css">
    <link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/wickedcss.min.css">
    <style>
        .modal-open .modal {
                overflow-y: auto !important;
            }
        .modal {
            overflow-y: auto !important;
        }
        .boder-title {
            border-bottom: 1px dotted #333;
        }

        .D-flex {
            display: flex;
        }

        .M-001 {
            width: 80%;
            padding: 0 0 0 1rem;
        }

        .width5 {
            width: 5%;
        }

        .width20 {
            width: 20%;
        }

        .width80 {
            width: 80%;
        }

        .width95 {
            width: 95%;
        }

        .div-bottom {
            border: 1px solid #333;
            padding: 1rem;
        }

        .cancel-img {
            width: 30%;
            margin: 1rem 0;
            position: absolute;
            /* transform: rotate(30deg); */
            /* z-index: 9999; */
            left: 29rem;
            top: -4rem;
        }

        .swal2-header {
            padding: 1rem;
            border-bottom: 1px solid #dee2e600;
        }

        .img-pay001 {
            width: 20%;
        }

        .transfered-text {
            color: #ffffff;
            background-color: #2196F3;
            padding: 0.2rem;
            border-radius: 10rem;
        }

        .span-status-01 {
            margin-left: 1rem;
            background-color: #F44336;
            padding: 0.5rem;
            border-radius: 50rem;
            color: #FFF;
        }

        .span-status-02 {
            margin-left: 1rem;
            background-color: #4caf50;
            padding: 0.5rem;
            border-radius: 50rem;
            color: #FFF;
        }

        .span-status-03 {
            margin-left: 1rem;
            background-color: #8BC34A;
            padding: 0.5rem 2rem;
            border-radius: 50rem;
            color: #FFF;
        }

        .span-status {
            margin-left: 0;
            padding: 0.5rem;
            border-radius: 50rem;
            color: #FFF;
        }

        @media screen and (max-width: 991px) {
            .D-flex {
                display: block;
            }

            .width5 {
                width: 100%;
            }

            .width20 {
                width: 100%;
            }

            .width80 {
                width: 100%;
            }

            .width95 {
                width: 100%;
            }

            .table-bordered thead th {
                border-bottom-width: 2px;
                font-size: 0.3rem;
            }

            .btn-app {
                min-width: 100%;
            }

            .img-pay001 {
                width: 100%;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">

    <div class="wrapper">
        <?php
        include('structer/backend/navbar.php');
        include('structer/backend/menu.php');
        ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-xl-6">
                            <h1><?php echo $submenu; ?></h1>
                        </div>
                        <div class="col-xl-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo site_url('mod_admin') ?>/ctl_admin/backend_main">Home</a></li>
                                <li class="breadcrumb-item active"><?php echo $submenu; ?></li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <section class="content">
                <div class="container">
                    <div class="row">

                        <section class="col-lg-12 connectedSortable">
                            <!-- Custom tabs (Charts with tabs)-->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i> </h3>
                                </div>
                                <div class="card-body">
                                    <form id="demo2" name="demo2" class="demo" enctype="multipart/form-data" accept-charset="utf-8" method="post">
                                        <input type="hidden" id="bill_ID" name="bill_ID" value="<?php echo $this->input->get('id'); ?>">
                                        <div class="titel text-left">
                                            <i class="fa fa-print" aria-hidden="true"></i> ใบออเดอร์
                                            <a target="_blank" href="<?php echo site_url('mod_retailcreateorder/billretail_PDF/BillPDF?id=' . $Query_billdetil['ID']); ?>" class="btn btn-default btn-sm" style="padding: 0rem .5rem; position: absolute; right: 2rem;">
                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                                            </a>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4 col-xl-4 text-left">
                                                <b>ออเดอร์ที่ : </b><span> <?php echo $Query_billdetil['CODE']; ?> </span>
                                                <?php if ($Query_billdetil['STATUSCOMPLETE'] == 4) {
                                                    echo '<span style="background-color: #17a2b8; color: #FFF; padding: 0.1rem 1rem; border-radius: 5px;"> <li class="fa fa-archive"> </li> CLAIM </span>';
                                                } ?>
                                            </div>

                                            <div class="form-group col-md-4 col-xl-4">
                                                <b>วันที่ : </b><span> <?php echo $Query_billdetil['DATE_STARTS']; ?></span>
                                            </div>

                                            <div class="form-group col-md-2 col-xl-2 text-right">
                                                <b>เขต : </b><span> <?php echo $Query_billdetil['DELIVERYFORMID']; ?> </span>
                                            </div>
                                            <div class="form-group col-md-2 col-xl-2 text-right">
                                                <b>สาขา : </b><span> <?php echo $Query_billdetil['METHODORDER_TOPIC']; ?> </span>
                                            </div>

                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4 col-xl-4">
                                                <b>โดย : </b><span> <?php echo $Query_billdetil['S_NAME_TH'] . '  ' . $Query_billdetil['S_LASTNAME_TH']; ?></span>
                                            </div>
                                            <div class="form-group col-md-4 col-xl-4">
                                                <b>สร้างเมื่อ : </b><span> <?php echo $Query_billdetil['DATE_STARTS']." ".date('H:i:s',strtotime($Query_billdetil['DATE_STARTS_strtotime'])); ?></span>
                                            </div>
                                        </div>
                                        
                                        <br>
                                        <div class="titel text-left">
                                            <i class="fa fa-file-text" aria-hidden="true"></i> รายการออเดอร์
                                        </div>
                                        <?php if ($Query_billdetil['REMARKORDER'] != '') { ?>
                                            <div style="padding-bottom: 0.5rem;"> <b> คำอธิบายเพิ่มเติม : </b> <?php echo $Query_billdetil['REMARKORDER']; ?> </div>
                                        <?php } ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id='table-bill'>
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 5%;text-align: center;">ลำดับ</th>
                                                                <th style="text-align: center;">รายการสินค้า</th>
                                                                <th style="width: 10%;text-align: center;">ราคา/บาท</th>
                                                                <th style="width: 10%;text-align: center;">จำนวน/หน่วย</th>
                                                                <th style="width: 10%;text-align: center;">รวม/บาท</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="ORlist">
                                                            <?php if ($Query_billdetil['billist']) {
                                                                foreach ($Query_billdetil['billist'] as $row1) { ?>
                                                                    <tr style="background-color: #d9d9d9;">
                                                                        <td colspan="5"> <b> <?php echo $row1['PRONAME_MAIN']; ?> </b> </td>
                                                                    </tr>
                                                                    <?php $index = 1;
                                                                    foreach ($row1['PRONAME_LIST'] as $row2) { ?>
                                                                        <tr class="each-total">
                                                                            <td style="text-align: center;"> <?php echo $index++; ?> </td>
                                                                            <td style="text-align: left;"> <?php echo $row2['PRONAME_LIST']; ?> </td>
                                                                            <td style="text-align: right;"> <?php echo "-"; ?></td>
                                                                            <td style="text-align: right;"> <?php echo $row2['QUANTITY']; ?></td>
                                                                            <td style="text-align: right;"> <?php echo "-"; ?> </td>
                                                                        </tr>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <?php if ($Query_billdetil['BILLSTATUS'] == 1) { ?>
                                                    <?php
                                                    //
                                                    //	button
                                                    $btn_approve_transfer = "";
                                                    $btn_approve_order = "";
                                                    $btn_cancelorder = "";
                                                    $btn_editorder = "";
                                                    $btn_billvat = "";

                                                    $billapprovetransfer = chkPermissPage('btn_approvetranfer');
                                                    if ($billapprovetransfer == 1) {
                                                        $btn_approve_transfer = '<button type="button" id="btn_approve" value="1" class="btn btn-app bg-warning"> <i class="fas fa-utensils"></i> ยืนยันการตรวจสอบจากสาขา </button>';
                                                    }
                                                    $billapproveorder = chkPermissPage('btn_approveorder');
                                                    if ($billapproveorder == 1) {
                                                        $btn_approve_order = '<button type="button" id="btn_approve" value="2" class="btn btn-app bg-warning"> <i class="fa fa-cubes"></i> ยืนยันการตรวจสอบจากเขต </button>';
                                                    }
                                                    $billcancelorder = chkPermissPage('btn_cancelorder');
                                                    //
                                                    //  check document other
                                                    $func_findCreditnote = find_Creditnote($Query_billdetil['ID']);
                                                    $num = $func_findCreditnote['num'];

                                                    if ($billcancelorder == 1 && $num == 0) {
                                                        $btn_cancelorder = '<button type="button" class="btn btn-app bg-danger" data-toggle="modal" data-target="#ModalDelete" id="deleteorder" value="' . $Query_billdetil['ID'] . '"> <li class="fa fa-trash-o"> </li>  ยกเลิกรายการ </button>';
                                                    }
                                                    $billeditorder = chkPermissPage('btn_editorder');
                                                    if ($billeditorder == 1) {
                                                        $btn_editorder = '<a href="' . site_url('mod_retailcreateorder/ctl_createorder/createorder_update?id=' . $Query_billdetil['ID'] . '') . '" class="btn btn-app"> <i class="fa fa-edit"></i> แก้ไขรายการออเดอร์ </a>';
                                                    }

                                                    if ($Query_billdetil['STATUSCOMPLETE'] == 1 || $Query_billdetil['STATUSCOMPLETE'] == 0) {
                                                        if ($Query_billdetil['STATUSAPPROVE1'] == 1) {
                                                            echo '<button type="button" class="btn btn-app bg-success" disabled> <i class="fa fa-check-circle-o"></i>  สาขาตรวจสอบแล้ว </button>';
                                                        } else {
                                                            echo $btn_approve_transfer;
                                                            echo $btnEditorderStaff;
                                                        }
                                                        if ($Query_billdetil['STATUSAPPROVE2'] == 1) {
                                                            echo '<button type="button" class="btn btn-app bg-success" disabled> <i class="fa fa-check-circle-o"></i>  จัดส่งสินค้าเรียบร้อย </button>';
                                                        } else {
                                                            echo $btn_approve_order;
                                                        }
                                                    } else if ($Query_billdetil['STATUSCOMPLETE'] == 2) {
                                                        echo '<button type="button" class="btn btn-app bg-success" > <i class="fa fa-check-circle-o"></i>  ทำรายการสำเร็จแล้ว </button>';
                                                    } else if ($Query_billdetil['STATUSCOMPLETE'] == 5) {
                                                        if ($Query_billdetil['STATUSAPPROVE1'] == 1) {
                                                            echo '<button type="button" class="btn btn-app bg-success" disabled> <i class="fa fa-check-circle-o"></i>  เขตตรวจสอบแล้ว </button>';
                                                        } else {
                                                            echo $btn_approve_transfer;
                                                        }
                                                        if ($Query_billdetil['STATUSAPPROVE2'] == 1) {
                                                            echo '<button type="button" class="btn btn-app bg-success" disabled> <i class="fa fa-check-circle-o"></i>  จัดส่งสินค้าเรียบร้อย </button>';
                                                        } else {
                                                            echo $btn_approve_order;
                                                        }
                                                    }

                                                    if ($Query_billdetil['STATUSCOMPLETE'] != 4) {
                                                        if ($Query_billdetil['STATUSCOMPLETE'] == 0 || $Query_billdetil['STATUSCOMPLETE'] == 1) {
                                                            echo $btn_cancelorder;
                                                            echo $btn_editorder;
                                                            // if($Query_billdetil['STATUSAPPROVE1'] != 1 && $Query_billdetil['STATUSAPPROVE2'] != 1){
                                                            //     echo $btn_editorder;
                                                            // }
                                                        } else if ($Query_billdetil['STATUSCOMPLETE'] == 2 || $Query_billdetil['STATUSCOMPLETE'] == 5) {
                                                            $billeditorderapprove = chkPermissPage('btn_editorderapprove');
                                                            if ($billeditorderapprove == 1) {
                                                                $btn_editorderapprove = '<a href="' . site_url('mod_retailcreateorder/ctl_createorder/createorder_update?id=' . $Query_billdetil['ID'] . '') . '" class="btn btn-app"> <i class="fa fa-edit"></i> แก้ไขรายการออเดอร์ </a>';
                                                            }
                                                            echo $btn_editorderapprove;
                                                            echo $btn_cancelorder;

                                                            echo $btnEditorderStaff;
                                                        }
                                                    }
                                                    ?>

                                                <?php } ?>
                                                <a href="<?php echo site_url('mod_retailcreateorder/ctl_createorder/bill'); ?>" class="btn btn-app"> <i class="fa fa-home"></i> กลับหน้าหลัก </a>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </section>
            <!-- Modal ZOOM -->
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-file-image-o" aria-hidden="true"></i> หลักฐานการโอนเงิน</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php
                            if ($Query_billdetil['PICPAYMENT'] == '' && $Query_billdetil['PICPAYMENT2'] == '') {
                                if ($Query_billdetil['IMGNAME']) {
                                    foreach ($Query_billdetil['IMGNAME'] as $row) {
                                        if ($row['IMGNAME_NAME']) {
                                            echo '<img src="' . $basepic . 'front/retail/BillPaymentMultiple/' . $row['IMGNAME_NAME'] . '" style="width: 100%;">';
                                        } else {
                                            echo '<img src="https://heuft.com/upload/image/400x267/no_image_placeholder.png" style="width: 100%;">';
                                        }
                                    }
                                }
                            } else {
                                echo '<img src="' . $basepic . 'front/retail/Bill_Pyment/' . $Query_billdetil['PICPAYMENT'] . '" style="width: 100%;">';
                                if ($Query_billdetil['PICPAYMENT2'] != '') {
                                    echo '<img src="' . $basepic . 'front/retail/Bill_Pyment/' . $Query_billdetil['PICPAYMENT2'] . '" style="width: 100%;">';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal DELETE BILL -->
            <div class="modal fade" id="ModalDelete" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-calendar-times-o" aria-hidden="true"></i> ยกเลิกรายการ</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="demo2" name="demo2" class="demo" enctype="multipart/form-data" accept-charset="utf-8" method="post">
                                <input type="hidden" id="hdfdeleteorder" name="hdfdeleteorder" value="">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label class="">หมายเหตุ : </label>
                                        <textarea class="form-control" rows="3" name="remark" id="remark" placeholder="คำอธิบาย..."></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" id="confirmdelete"> <i class="fa fa-floppy-o" aria-hidden="true"></i> ยืนยันการยกเลิก </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- modal Product -->
            <div class="modal fade bd-example-modal-lg" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index:10000">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="titel text-left"> <i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่มรายการ </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="">เลือกเมนูหลัก</label>
                                <select id="select-productmain" name="select-productmain" class="selectpicker" data-live-search="true">
                                    <option value=""> -- โปรดเลือกเมนูหลัก -- </option>
                                    <?php
                                    $sql = $this->db->select('*')
                                        ->from('retail_productmain')
                                        ->where('status', 1)
                                        ->get();
                                    foreach ($sql->result() as $row) {
                                        echo '<option value="' . $row->ID . '"> ' . $row->NAME_TH . ' </option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="SLproductlist">
                                <label class="">เลือกรายการเมนู</label>
                                <select id="select-productlist" name="select-productlist" class="selectpicker selectpicker_1" data-live-search="true" disabled>

                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="">จำนวน</label>
                                <input type="number" class="form-control " name="productqty" id="productqty" placeholder="จำนวน">
                            </div>
                            <div class="form-group col-md-6 text-center p-auto m-auto">
                                <button type="button" class="btn btn-lg btn-info w-100" id="add-order"> <i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่มรายการ </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="htmlvalidate"></div>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> ปิด</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php require_once('modal_billnew.php'); ?>
            <?php require_once('modal_creditnote.php'); ?>
            <?php require_once('modal_store.php'); ?>

            <!-- form report -->
            <form id="frmreport" name="frmreport" method="get" action="<?php echo site_url('mod_retailreport/ctl_report/report'); ?>" target="_blank">
                <input type="hidden" id="table" name="table" value="">
                <input type="hidden" id="listorderid" name="listorderid" value="">
            </form>

        </div>
        <?php include("structer/backend/footer.php"); ?>
        <?php include("structer/backend/script.php"); ?>
        <script src="<?php echo $base_bn; ?>frontend/bootstrap-select/js/bootstrap-select.js"></script>
        <script src="<?php echo $base_bn; ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    </div>
    <script>
        $(function() {

            const queryString = decodeURIComponent(window.location.search);
            const params = new URLSearchParams(queryString);
            let billID = params.get("id");

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            $('body').on('hidden.bs.modal', function() {
                if ($('.modal').hasClass('show')) {
                    $('body').addClass('modal-open');
                } else {
                    $('body').removeClass('modal-open');
                }
            });

            // การอนุมัติ
            $(document).on('click', '#btn_approve', function(event) {
                var bill_ID = $('#bill_ID').val();
                var bnt_val = this.value
                $.post("statusapprove", {
                    id: bill_ID,
                    val: bnt_val
                }, function(result) {
                    var obj = jQuery.parseJSON(result);
                    if (obj.error_code == 0) {
                        Swal.fire('Success!', obj.txt, 'success')
                        $(".swal2-confirm").on("click", function(e) {
                            window.location.replace('viwecreatebill?id=' + obj.getid);
                        });
                        // setTimeout(function(){ 
                        //     window.location.replace('viwecreatebill?id='+obj.getid);
                        // }, 1500);
                    } else {
                        Swal.fire('Success!', obj.txt, 'warning')
                    }
                });
            });
            /**
             * ========================================================================================
             * ส่วนการทำงานของปุ่มเอกสารเพิ่มเติม Start
             * ========================================================================================
             */
            let frm = $('form#frm');
            let frmnew = $('form#frmnew');
            let frmstore = $('form#frmstore');
            let modal_doc = $('.modal_doc');

            //  ปิดการทำงาน modal ที่เลือกเอกสาร
            $(document).on('click', '.modal_doc', 'button', function(event) {
                event.stopPropagation;
                event.stopImmediatePropagation;

                modal_doc.modal('hide');
            })

            // ปุ่มเปิดบิลต่อ
            $(document).on('click', '.btn-open-bill', function(event) {
                event.stopPropagation;
                event.stopImmediatePropagation;
                event.preventDefault;

                if (modal_doc.modal('hide')) {
                    setTimeout(() => {
                        openBill();
                    }, 200);
                }

            })

            function openBill() {
                let modal_add_bill = $('.modal_add_bill');

                modal_add_bill.modal({
                    show: true
                });

                //  open bill
                get_orderBillForNew(billID);
            }

            //	search order
            function get_orderBillForNew(bill_id) {
                displayLoading('.loading', $('#frmnew'));

                var data = new FormData();
                data.append("bill_id", bill_id);

                //	วิธี fetch แบบ error handling
                fetch('get_orderBill', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        hideLoading('.loading');

                        let dataresult = resp;
                        let resultSearch = "";
                        let bill = resp.data;
                        let billdetail = resp.datadetail;

                        // console.log(billdetail);
                        $('#frmnew #bill_id').val(bill.id);
                        $('#frmnew #bill_code').val(bill.code);

                        formDataInsert(bill, frmnew);

                        formDataDetailInsert(billdetail, frmnew);

                        frmnew.removeClass('d-none');
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`)
                    })
            }

            // ปุ่มเปิดใบลดหนี้
            $(document).on('click', '.btn-open-creditnote', function(event) {
                event.stopPropagation;
                event.stopImmediatePropagation;
                event.preventDefault;

                if (modal_doc.modal('hide')) {
                    setTimeout(() => {
                        openCreditnote();
                    }, 200);
                }

            })

            function openCreditnote() {
                let modal_add_creditnote = $('.modal_add_creditnote');

                modal_add_creditnote.modal({
                    show: true
                });

                //  open bill
                get_orderBill(billID);
            }

            //	search order
            function get_orderBill(bill_id) {
                displayLoading('.loading', $('#frm'));

                var data = new FormData();
                data.append("bill_id", bill_id);

                //	วิธี fetch แบบ error handling
                fetch('get_orderBill', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        hideLoading('.loading');

                        let dataresult = resp;
                        let resultSearch = "";
                        let bill = resp.data;
                        let billdetail = resp.datadetail;

                        // console.log(billdetail);
                        $('#frm #bill_id').val(bill.id);
                        $('#frm #bill_code').val(bill.code);

                        formDataInsert(bill, frm);

                        formDataDetailInsert(billdetail, frm);

                        //	force check zero have hidden
                        $('*[for=totalzero]').hide();

                        frm.removeClass('d-none');
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`)
                    })
            }

            function formDataInsert(bill, frm) {
                frm.find('.bill_code').text(bill.code);
                frm.find('.bill_textcode').text(bill.textcode);
                frm.find('.bill_datecreate').text(bill.datecreate);
                frm.find('.bill_staffcreate').text(bill.staffcreate);

                frm.find('.bill_paystatus').text(bill.billstatus);
                frm.find('.bill_method').text(bill.receive);
                frm.find('.bill_delivery').text(bill.delivery);
                frm.find('.bill_complete').text(bill.complete);

                frm.find('.bill_name').text(bill.name);
                frm.find('.bill_tel').text(bill.tel);
                frm.find('.bill_citizen').text(bill.citizen);
                frm.find('.bill_address').text(bill.address);
                frm.find('.bill_zipcode').text(bill.zipcode);

                frm.find('#cust_name').val(bill.name);
                frm.find('#cust_tel').val(bill.tel);
                frm.find('#cust_textnumber').val(bill.citizen);
                frm.find('#cust_address').val(bill.address);
                frm.find('#cust_zipcode').val(bill.zipcode);

                frm.find('.bill_bank').text(bill.bank);
                frm.find('.bill_bank_daytime').text(bill.bank_daytime);
                frm.find('.bill_bank_amount').text(formatMoney(bill.bank_amount));
                frm.find('.bill_bank_remark').text(bill.bank_remark);

                frm.find('.bill_price').val(formatMoney(bill.price));
                frm.find('.bill_parcel').val(bill.parcel);
                frm.find('.bill_logis').val(bill.logis);
                frm.find('.bill_shor').val(bill.shor);
                frm.find('.bill_discount').val(bill.discount);
                frm.find('.bill_tax').val(bill.tax);
                frm.find('.bill_net').val(formatMoney(bill.net));

                frm.find('.bill_remark').text(bill.remark);

                if (frm.find('.billstatus button.active').attr('data-value') == 'F') {
                    frm.find('.bill_net').val('0.00');
                }

            }

            function formDataDetailInsert(billdetail, frm) {
                let hmtl = "";

                billdetail.forEach(function(key, val) {
                    let index = val + 1;
                    let product_name = key.product_name;
                    let product_price = formatMoney(key.product_price);
                    let product_qty = key.product_qty;
                    let product_totalprice = key.product_totalprice;
                    let promain = key.promain;
                    let prolist = key.prolist;
                    let list = key.list;

                    hmtl += '<tr data-row="' + index + '">';
                    hmtl += '<td class=""><button type="button" class="btn btn-sm btn-danger btn-del"><i class="fa fa-close"></i></button></td>';
                    hmtl += '<td class="index">' + index + '</td>';
                    hmtl += '<td class="name"> ' + product_name + ' </td>';
                    hmtl += '<td class="price">' + product_price + '</td>';
                    hmtl += '<td class="qty">';
                    hmtl += '<input type="text" value="' + product_qty + '" class="w-100 input-qty" data-promain="' + promain + '" data-prolist="' + prolist + '" data-list="' + list + '" data-price="' + product_price + '" OnKeyPress="return checkNumber(this)">';
                    hmtl += '</td>';
                    hmtl += '<td class="totalprice text-right">' + product_totalprice + '</td>';
                    hmtl += '</tr>';
                })

                frm.find('.tabledetail tbody').html(hmtl);
            }

            // ปุ่มเปิดใบส่งของ
            $(document).on('click', '.btn-open-store', function(event) {
                event.stopPropagation;
                event.stopImmediatePropagation;
                event.preventDefault;

                if (modal_doc.modal('hide')) {
                    setTimeout(() => {
                        openStore();
                    }, 200);
                }
            })

            function openStore() {
                let modal_add_store = $('.modal_add_store');

                modal_add_store.modal({
                    show: true
                });

                //  open bill
                get_orderBillStore(billID);
            }

            //	search order
            function get_orderBillStore(bill_id) {
                displayLoading('.loading', $('#frmstore'));

                var data = new FormData();
                data.append("bill_id", bill_id);

                //	วิธี fetch แบบ error handling
                fetch('get_orderBill', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        hideLoading('.loading');

                        let dataresult = resp;
                        let resultSearch = "";
                        let bill = resp.data;
                        let billdetail = resp.datadetail;

                        // console.log(billdetail);
                        $('#frmstore #bill_id').val(bill.id);
                        $('#frmstore #bill_code').val(bill.code);

                        formDataInsert(bill, frmstore);

                        formDataDetailInsert(billdetail, frmstore);

                        frmstore.removeClass('d-none');
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`)
                    })
            }

            function displayLoading(elename, frm) {
                let loading = '<div class="spinner-border text-info"></div>';

                frm.addClass('d-none');

                frm[0].reset();

                // document.frm.reset();
                $('.thumbnail-image').empty();

                //	product return
                $('.htmltext-return').removeClass('d-none');
                $('.htmltext-loss').addClass('d-none');

                $(elename).html(loading);
            }

            function hideLoading(elename) {
                $(elename).html("");
            }

            //	submit form
            $(document).on('click', '#frm #submitform', function() {
                add_Creditnote();
            })

            //	submit form
            $(document).on('click', '#frmnew #submitform', function() {
                add_BillNew();
            })

            //	submit form
            $(document).on('click', '#frmstore #submitform', function() {
                add_Store();
            })

            $(document).on('click', '#frm #cancelimgdetail', function() {
                $("#frm #imagedledetail").text("Choose file");
                $("#frm #image_file").val("");
                $('#frm .thumbnail-image').html('');
            });

            $(document).on('click', '#frmnew #cancelimgdetail', function() {
                $("#frmnew #imagedledetail").text("Choose file");
                $("#frmnew #image_file").val("");
                $('#frmnew .thumbnail-image').html('');
            });

            $(document).on('click', '.billstatus button', function(event) {
                var val = this.value;
                let datavalue = this.getAttribute('data-value');
                $('#frmnew #statuscomplete').val(val);

                if (datavalue == 'F') {
                    $('#frmnew #totalamount').val('0.00');
                } else {
                    calculateBill();
                }
            });

            $(document).on("click", ".modal-bill", function() {
                $('#productqty').val('');
                var html2 = '';
                $("#select-productmain").selectpicker('refresh');
                $("#select-productlist").html(html2).selectpicker('refresh');
            });

            $('.bd-example-modal-lg').on('shown.bs.modal', function() {
                // $("#select-productmain").eq(0).prop('checked');
                $("#select-productmain").val("");
                $(".modal-footer .htmlvalidate").html('');
                $('.selectpicker').selectpicker('refresh')
            });

            $('#select-productlist').change(function() {
                $('#productqty').val('');
            });
            $('#select-productmain').change(function() {
                var option = $(this).find('option:selected'),
                    id = option.val();
                name = option.data('name');
                $("#select-productlist").empty();
                $("#select-productlist").removeAttr('disabled')
                ajaxprolist(id);
            });

            function ajaxprolist(id) {
                $.ajax({
                    url: "ajaxselectproductmain",
                    type: 'POST',
                    data: {
                        action: 'my_special_ajax_call',
                        val: id
                    },
                    success: function(results) {
                        var obj = jQuery.parseJSON(results);
                        var html = '';
                        html += ' <option value="">โปรดเลือกรายการเมนู</option>';
                        $.each(obj, function(index, value) {
                            html += ' <option value="' + value.ID + '">' + value.NAME_TH + '</option>';
                        });
                        $("#select-productlist").html(html).selectpicker('refresh');
                    }
                });
            }

            $(document).on("click", "#add-order", function() {
                if ($('.modal_add_bill').is(':visible')) {
                    var frmmodal = $('#frmnew');
                } else {
                    var frmmodal = $('#frmstore');
                }

                var productmainID = $('#select-productmain').val();
                var productlistID = $('#select-productlist').val();

                $('.selectpicker').selectpicker('refresh')

                var qty = $('#productqty').val();
                var result = [{
                    "html": "#select-productmain",
                    "th": "เมนูหลัก"
                }, {
                    "html": "#select-productlist",
                    "th": "รายการเมนู"
                }, {
                    "html": "#productqty",
                    "th": "จำนวน"
                }];


                async function runcheck() {
                    let result1 = await checkValidateProductList(result);

                    if (result1.error_code == 0) {
                        let datasetting = {
                            plist_id: $('#select-productlist').val(),
                            pqty: $('#productqty').val(),
                            form: frmmodal
                        }

                        createListProduct(datasetting);
                    } else {
                        $('.modal-footer .htmlvalidate').html('<span class="text-validate text-danger float-left"> โปรดระบุ ' + result1.txt + '</span>');
                    }
                }
                runcheck();
            });

            function checkValidateProductList(result) {
                // let param;
                let error = 0;
                let cal = "unknow";

                $.each(result, function(key, value) {
                    param = $(value.html).val();

                    if (!param) {
                        error = 1;
                        cal = value.th;
                        return false;
                    }
                });

                return new Promise((resolve, reject) => {
                    resolve({
                        error_code: error,
                        txt: cal
                    })
                })
            }


            function createListProduct(dataarray) {
                // console.log(dataarray);
                var data = new FormData();

                data.append("pid", dataarray.plist_id);
                data.append("pqty", dataarray.pqty);

                fetch('get_product', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {

                        let totalprice = resp.qty * resp.price;

                        let billdetail = [{
                            list: resp.list,
                            product_name: resp.name_th,
                            product_price: resp.price,
                            product_qty: resp.qty,
                            product_totalprice: formatMoney(totalprice),
                            prolist: resp.id,
                            promain: resp.main
                        }]
                        formDataDetailAppend(billdetail, dataarray.form);

                        //  close modal
                        $('.bd-example-modal-lg').modal('hide');
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`);
                    })

            }

            function formDataDetailAppend(billdetail, frm) {
                let hmtl = "";

                let indexval = frm.find('.tabledetail tbody tr').length;

                billdetail.forEach(function(key, val) {
                    let index = indexval + 1;
                    let product_name = key.product_name;
                    let product_price = formatMoney(key.product_price);
                    let product_qty = key.product_qty;
                    let product_totalprice = key.product_totalprice;
                    let promain = key.promain;
                    let prolist = key.prolist;
                    let list = key.list;

                    hmtl += '<tr data-row="' + index + '">';
                    hmtl += '<td class=""><button type="button" class="btn btn-sm btn-danger btn-del"><i class="fa fa-close"></i></button></td>';
                    hmtl += '<td class="index">' + index + '</td>';
                    hmtl += '<td class="name"> ' + product_name + ' </td>';
                    hmtl += '<td class="price">' + product_price + '</td>';
                    hmtl += '<td class="qty">';
                    hmtl += '<input type="text" value="' + product_qty + '" class="w-100 input-qty" data-promain="' + promain + '" data-prolist="' + prolist + '" data-list="' + list + '" data-price="' + product_price + '" OnKeyPress="return checkNumber(this)">';
                    hmtl += '</td>';
                    hmtl += '<td class="totalprice text-right">' + product_totalprice + '</td>';
                    hmtl += '</tr>';
                })

                frm.find('.tabledetail tbody').append(hmtl);

                calculateBill();
            }

            function add_BillNew() {
                //  loader
                let loading = '<div class="col-sm-12 text-center loading"><div class="spinner-border text-info"></div></div>';
                let rowtoolbtn = $('.row-form-tool-btn');
                let toolbtn = $('.form-tool-btn');

                rowtoolbtn.append(loading);
                toolbtn.addClass('d-none');

                let loop = document.querySelectorAll('#frmnew [data-loop]');
                let product = document.querySelectorAll('#frmnew .tabledetail tbody tr');
                // console.log(product.children.getElementsByTagName('td'));
                let array = [];
                var data = new FormData();

                let pd_promain = 0;
                let pd_prolist = 0;
                let pd_list = 0;
                let pd_name = 0;
                let pd_qty = 0;
                let pd_price = 0;
                let pd_totalprice = 0;

                let error = 0;
                //	product
                product.forEach(function(key, index) {
                    pd_promain = key.getElementsByTagName('input')[0].getAttribute('data-promain');
                    pd_prolist = key.getElementsByTagName('input')[0].getAttribute('data-prolist');
                    pd_list = key.getElementsByTagName('input')[0].getAttribute('data-list');
                    pd_name = key.getElementsByClassName('name')[0].innerHTML;
                    pd_qty = key.getElementsByClassName('qty')[0].getElementsByTagName('input')[0].value;
                    pd_price = key.getElementsByClassName('price')[0].innerHTML;
                    pd_totalprice = key.getElementsByClassName('totalprice')[0].innerHTML;

                    if (pd_qty < 0 || pd_qty == "") {
                        swal.fire('ข้อมูลผิดพลาด', 'กรุณากรอกจำนวนสินค้า', 'warning');
                        error = 1;
                    }

                    data.append('item[' + index + '][promain]', pd_promain);
                    data.append('item[' + index + '][prolist]', pd_prolist);
                    data.append('item[' + index + '][list]', pd_list);
                    data.append('item[' + index + '][name]', pd_name);
                    data.append('item[' + index + '][qty]', pd_qty);
                    data.append('item[' + index + '][price]', pd_price);
                    data.append('item[' + index + '][totalprice]', pd_totalprice);
                })

                //	amount total
                loop.forEach(function(key, index) {
                    data.append(key.getAttribute('data-name'), key.value);
                    // array.push(key.value);
                })

                data.append('bill_date', document.querySelectorAll('#frmnew #order_date')[0].value);
                data.append('bill_delivery', document.querySelectorAll('#frmnew #deliveryid')[0].value);
                data.append('bill_method', document.querySelectorAll('#frmnew #methodid')[0].value);

                data.append('cust_name', document.querySelectorAll('#frmnew #cust_name')[0].value);
                data.append('cust_tel', document.querySelectorAll('#frmnew #cust_tel')[0].value);
                data.append('cust_zipcode', document.querySelectorAll('#frmnew #cust_zipcode')[0].value);
                data.append('cust_textcode', document.querySelectorAll('#frmnew #cust_textcode')[0].value);
                data.append('cust_address', document.querySelectorAll('#frmnew #cust_address')[0].value);
                data.append('cust_textnumber', document.querySelectorAll('#frmnew #cust_textnumber')[0].value);

                data.append('billstatus', document.querySelectorAll('#frmnew .billstatus .active')[0].getAttribute('data-value'));
                data.append('statuscomplete', document.querySelectorAll('#frmnew #statuscomplete')[0].value);
                data.append('billnew', 1);

                data.append('bankid', document.querySelectorAll('#frmnew #bankid')[0].value);
                data.append('transfereddate', document.querySelectorAll('#frmnew #transfereddate')[0].value);
                data.append('transferedtime', document.querySelectorAll('#frmnew #transferedtime')[0].value);
                data.append('amount', document.querySelectorAll('#frmnew #amount')[0].value);
                data.append('transferedremark', document.querySelectorAll('#frmnew #transferedremark')[0].value);

                data.append('remark', document.querySelectorAll('#frmnew #remark')[0].value);
                data.append('bill_id', document.querySelectorAll('#frmnew #bill_id')[0].value);
                data.append('slipold', document.querySelectorAll('#frmnew #slipold')[0].checked);

                data.append('bill_code', document.querySelectorAll('#frmnew #bill_code')[0].value);

                // image
                var image_file = $('#frmnew #image_file');
                if (image_file[0].files.length > 0) {
                    var length = (image_file[0].files.length - 1);
                    for (var i = 0; i <= length; i++) {
                        data.append('file[]', image_file[0].files[i]);
                    }

                }

                if (error == 0) {
                    let url = 'add_BillNew';
                    let options = {
                        method: 'POST',
                        body: data
                    };
                    // delete options.headers['Content-Type'];

                    fetch(url, options)
                        .then(res => res.json())
                        .then(resp => {
                            // console.log(resp);
                            if (resp.error_code != 0) {
                                swal.fire('ข้อมูลผิดพลาด', resp.txt, 'warning');
                            } else {
                                //  success
                                swal.fire({
                                    type: 'success',
                                    title: 'บันทึกรายการทำเสร็จ',
                                    text: resp.txt
                                }).then((result) => {
                                    $(".modal_add_bill  button[data-dismiss=modal]").trigger({
                                        type: "click"
                                    });

                                    //  refresh
                                    getCountCreditBill();
                                    getCountReceiveBill();

                                    let rowtoolbtn = $('.row-form-tool-btn');
                                    let toolbtn = $('.form-tool-btn');


                                })
                            }

                            $('.loading').remove();
                            toolbtn.removeClass('d-none');
                        })
                        .catch((error) => {
                            console.log(`error : ${error}`);
                            // modaladdbill.find('.modal-body').text('เกิดข้อผิดพลาดกรุณาแจ้งเจ้าหน้าที่');
                        })
                }

            }

            function add_Creditnote() {
                //  loader
                let loading = '<div class="col-sm-12 text-center loading"><div class="spinner-border text-info"></div></div>';
                let rowtoolbtn = $('.row-form-tool-btn');
                let toolbtn = $('.form-tool-btn');

                rowtoolbtn.append(loading);
                toolbtn.addClass('d-none');

                let loop = document.querySelectorAll('#frm [data-loop]');
                let product = document.querySelectorAll('#frm .tabledetail tbody tr');
                // console.log(product.children.getElementsByTagName('td'));
                let array = [];
                var data = new FormData();

                let pd_promain = 0;
                let pd_prolist = 0;
                let pd_list = 0;
                let pd_name = 0;
                let pd_qty = 0;
                let pd_price = 0;
                let pd_totalprice = 0;

                let error = 0;
                //	product
                product.forEach(function(key, index) {
                    pd_promain = key.getElementsByTagName('input')[0].getAttribute('data-promain');
                    pd_prolist = key.getElementsByTagName('input')[0].getAttribute('data-prolist');
                    pd_list = key.getElementsByTagName('input')[0].getAttribute('data-list');
                    pd_name = key.getElementsByClassName('name')[0].innerHTML;
                    pd_qty = key.getElementsByClassName('qty')[0].getElementsByTagName('input')[0].value;
                    pd_price = key.getElementsByClassName('price')[0].innerHTML;
                    pd_totalprice = key.getElementsByClassName('totalprice')[0].innerHTML;

                    if (pd_qty < 0 || pd_qty == "") {
                        swal.fire('ข้อมูลผิดพลาด', 'กรุณากรอกจำนวนสินค้า', 'warning');
                        error = 1;
                    }

                    data.append('item[' + index + '][promain]', pd_promain);
                    data.append('item[' + index + '][prolist]', pd_prolist);
                    data.append('item[' + index + '][list]', pd_list);
                    data.append('item[' + index + '][name]', pd_name);
                    data.append('item[' + index + '][qty]', pd_qty);
                    data.append('item[' + index + '][price]', pd_price);
                    data.append('item[' + index + '][totalprice]', pd_totalprice);
                })

                //	amount total
                loop.forEach(function(key, index) {
                    data.append(key.getAttribute('data-name'), key.value);
                    // array.push(key.value);
                })

                data.append('loss', document.getElementById('select_return').value);
                data.append('remark', document.querySelectorAll('#frm #remark')[0].value);
                data.append('bill_id', document.querySelectorAll('#frm #bill_id')[0].value);
                data.append('bill_code', document.querySelectorAll('#frm #bill_code')[0].value);

                // image
                var image_file = $('#frm #image_file');
                if (image_file[0].files.length > 0) {
                    var length = (image_file[0].files.length - 1);
                    for (var i = 0; i <= length; i++) {
                        data.append('file[]', image_file[0].files[i]);
                    }

                }

                if (error == 0) {
                    let url = 'add_Creditnote';
                    let options = {
                        method: 'POST',
                        body: data
                    };
                    // delete options.headers['Content-Type'];

                    fetch(url, options)
                        .then(res => res.json())
                        .then(resp => {
                            // console.log(resp);
                            if (resp.error_code != 0) {
                                swal.fire('ข้อมูลผิดพลาด', resp.txt, 'warning');
                            } else {
                                //  success
                                swal.fire({
                                    type: 'success',
                                    title: 'บันทึกรายการทำเสร็จ',
                                    text: resp.txt
                                }).then((result) => {
                                    $(".modal_add_creditnote  button[data-dismiss=modal]").trigger({
                                        type: "click"
                                    });

                                    //  refresh
                                    getCountCreditBill();
                                    getCountReceiveBill();

                                    let rowtoolbtn = $('.row-form-tool-btn');
                                    let toolbtn = $('.form-tool-btn');

                                    $('.loading').remove();
                                    toolbtn.removeClass('d-none');
                                })
                            }
                        })
                        .catch((error) => {
                            console.log(`error : ${error}`);
                            // modaladdbill.find('.modal-body').text('เกิดข้อผิดพลาดกรุณาแจ้งเจ้าหน้าที่');
                        })
                }


            }

            function add_Store() {
                //  loader
                let loading = '<div class="col-sm-12 text-center loading"><div class="spinner-border text-info"></div></div>';
                let rowtoolbtn = $('.row-form-tool-btn');
                let toolbtn = $('.form-tool-btn');

                rowtoolbtn.append(loading);
                toolbtn.addClass('d-none');

                let loop = document.querySelectorAll('#frmstore [data-loop]');
                let product = document.querySelectorAll('#frmstore .tabledetail tbody tr');
                // console.log(product.children.getElementsByTagName('td'));
                let array = [];
                var data = new FormData();

                let pd_promain = 0;
                let pd_prolist = 0;
                let pd_list = 0;
                let pd_name = 0;
                let pd_qty = 0;
                let pd_price = 0;
                let pd_totalprice = 0;

                let error = 0;
                //	product
                product.forEach(function(key, index) {
                    pd_promain = key.getElementsByTagName('input')[0].getAttribute('data-promain');
                    pd_prolist = key.getElementsByTagName('input')[0].getAttribute('data-prolist');
                    pd_list = key.getElementsByTagName('input')[0].getAttribute('data-list');
                    pd_name = key.getElementsByClassName('name')[0].innerHTML;
                    pd_qty = key.getElementsByClassName('qty')[0].getElementsByTagName('input')[0].value;
                    pd_price = key.getElementsByClassName('price')[0].innerHTML;
                    pd_totalprice = key.getElementsByClassName('totalprice')[0].innerHTML;

                    if (pd_qty < 0 || pd_qty == "") {
                        swal.fire('ข้อมูลผิดพลาด', 'กรุณากรอกจำนวนสินค้า', 'warning');
                        error = 1;
                    }

                    data.append('item[' + index + '][promain]', pd_promain);
                    data.append('item[' + index + '][prolist]', pd_prolist);
                    data.append('item[' + index + '][list]', pd_list);
                    data.append('item[' + index + '][name]', pd_name);
                    data.append('item[' + index + '][qty]', pd_qty);
                    data.append('item[' + index + '][price]', pd_price);
                    data.append('item[' + index + '][totalprice]', pd_totalprice);
                })

                //	amount total
                loop.forEach(function(key, index) {
                    data.append(key.getAttribute('data-name'), key.value);
                    // array.push(key.value);
                })

                data.append('billtype', 2);

                data.append('remark', document.querySelectorAll('#frmstore #remark')[0].value);
                data.append('bill_id', document.querySelectorAll('#frmstore #bill_id')[0].value);

                data.append('bill_code', document.querySelectorAll('#frmstore #bill_code')[0].value);

                // image
                var image_file = $('#frmstore #image_file');
                if (image_file[0].files.length > 0) {
                    var length = (image_file[0].files.length - 1);
                    for (var i = 0; i <= length; i++) {
                        data.append('file[]', image_file[0].files[i]);
                    }

                }

                if (error == 0) {
                    let url = 'add_Store';
                    let options = {
                        method: 'POST',
                        body: data
                    };
                    // delete options.headers['Content-Type'];

                    fetch(url, options)
                        .then(res => res.json())
                        .then(resp => {
                            // console.log(resp);
                            if (resp.error_code != 0) {
                                swal.fire('ข้อมูลผิดพลาด', resp.txt, 'warning');
                            } else {
                                //  success
                                swal.fire({
                                    type: 'success',
                                    title: 'บันทึกรายการทำเสร็จ',
                                    text: resp.txt
                                }).then((result) => {
                                    $(".modal_add_store  button[data-dismiss=modal]").trigger({
                                        type: "click"
                                    });

                                    //  refresh
                                    getCountCreditBill();
                                    getCountReceiveBill();

                                    let rowtoolbtn = $('.row-form-tool-btn');
                                    let toolbtn = $('.form-tool-btn');


                                })
                            }

                            $('.loading').remove();
                            toolbtn.removeClass('d-none');
                        })
                        .catch((error) => {
                            console.log(`error : ${error}`);
                            // modaladdbill.find('.modal-body').text('เกิดข้อผิดพลาดกรุณาแจ้งเจ้าหน้าที่');
                        })
                }

            }

            function getCountCreditBill() {
                fetch('countcreditnote', {
                        method: 'GET'
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.error_code == 0) {
                            $('.badge_creditnote').removeClass('d-none');
                            $('.badge_creditnote').html(resp.data.count);

                            if (resp.data.count == 0) {
                                $('.badge_creditnote').addClass('d-none');
                            }
                        }
                    })
                    .catch((error) => {
                        console.log(`error : ${error}`);
                        // modaladdbill.find('.modal-body').text('เกิดข้อผิดพลาดกรุณาแจ้งเจ้าหน้าที่');
                    })
            }

            function getCountReceiveBill() {
                fetch('countReceive', {
                        method: 'GET'
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.error_code == 0) {
                            $('.badge_receive').removeClass('d-none');
                            $('.badge_receive').html(resp.data.count);

                            if (resp.data.count == 0) {
                                $('.badge_receive').addClass('d-none');
                            }
                        }
                    })
                    .catch((error) => {
                        console.log(`error : ${error}`);
                        // modaladdbill.find('.modal-body').text('เกิดข้อผิดพลาดกรุณาแจ้งเจ้าหน้าที่');
                    })
            }

            //	notification product return
            $(document).on('change', '#select_return', function() {
                let select_return = $(this);

                if (select_return.val() == 1) {
                    $('.htmltext-loss').removeClass('d-none');
                    $('.htmltext-return').addClass('d-none');

                    forceZero();
                } else {
                    $('.htmltext-return').removeClass('d-none');
                    $('.htmltext-loss').addClass('d-none');

                    forceZeroFalse();
                    $('#totalzero').prop("disabled", true);
                    $('*[for=totalzero]').hide();
                }
            })

            function forceZero() {
                // $('#totalzero').prop('checked', true);
                $('*[for=totalzero]').show();
                $('#totalzero').prop("disabled", false);
                $('#totalzero').prop("checked", true).trigger('change');
            }

            function forceZeroFalse() {
                // $('#totalzero').prop('checked', false);

                $('#totalzero').prop("checked", false).trigger('change');
            }

            //	notification product return
            $(document).on('change', '#totalzero', function(e) {
                e.stopPropagation;
                e.stopImmediatePropagation();

                calculateCreditBill();
            })

            //	quantity
            $(document).on('keyup', '.input-qty', function() {
                calculateProduct(this, $(this).val());
            })

            //	cal price
            $(document).on('keyup', '#frmnew .billnumber', function() {
                calculateBill();
            })

            //	cal price
            $(document).on('click', '#frmnew .btn-del', function() {
                let tr_id = $(this).parents('tr').attr('data-row');
                $('tr[data-row=' + tr_id + ']').remove();

                //	bill amount
                calculateBill();
            })

            //	cal price
            $(document).on('keyup', '#frm .billnumber', function() {
                calculateCreditBill();
            })

            //	cal price
            $(document).on('click', '#frm .btn-del', function() {
                let tr_id = $(this).parents('tr').attr('data-row');
                $('tr[data-row=' + tr_id + ']').remove();

                //	bill amount
                calculateCreditBill();
            })

            //	cal price
            $(document).on('click', '#frmstore .btn-del', function() {
                let tr_id = $(this).parents('tr').attr('data-row');
                $('tr[data-row=' + tr_id + ']').remove();

                //	bill amount
                calculateBill();
            })

            function calculateProduct(ele, number) {

                let form = $(ele).parents('form').attr('id');
                let formelement = $('#' + form + ' .input-qty');
                let qty = number;
                let price = ele.getAttribute('data-price');

                let result = qty * price;

                let html_price = $(ele).parents('tr').children('td.totalprice');

                //	return
                html_price.text(formatMoney(result));

                //	total amount
                if (form == 'frmnew') {
                    calculateBill();
                }

                if (form == 'frm') {
                    calculateCreditBill();
                }
            }

            function calculateBill() {
                let productamount = 0;
                let resultamount = 0;
                let loop = document.querySelectorAll('#frmnew .billnumber');
                let productprice = document.querySelectorAll('#frmnew .input-qty');

                let pd_qty = 0;
                let pd_price = 0;
                let pd_total = 0;

                //	product total
                productprice.forEach(function(key, index) {
                    pd_qty = key.value;
                    pd_price = key.getAttribute('data-price');

                    pd_total = pd_qty * pd_price;

                    if (parseInt(pd_total)) {
                        productamount += parseInt(pd_total);
                    }
                })
                $('#frmnew #bill_price').val(formatMoney(productamount));

                //	bill amount
                loop.forEach(function(key, index) {
                    if (parseFloat(key.value)) {
                        resultamount += parseFloat(key.value);
                        // console.log(resultamount+" + "+parseInt(key.value));
                    }
                })
                resultamount += productamount;
                $('#frmnew #totalamount').val(formatMoney(resultamount));

                //  check force zero
                let checkzero = $('#totalzero');
                if (checkzero.prop('checked')) {
                    $('#frmnew #totalamount').val(0);
                }
            }

            function calculateCreditBill() {
                let productamount = 0;
                let resultamount = 0;
                let loop = document.querySelectorAll('#frm .billnumber');
                let productprice = document.querySelectorAll('#frm .input-qty');

                let pd_qty = 0;
                let pd_price = 0;
                let pd_total = 0;

                //	product total
                productprice.forEach(function(key, index) {
                    pd_qty = key.value;
                    pd_price = key.getAttribute('data-price');

                    pd_total = pd_qty * pd_price;

                    if (parseInt(pd_total)) {
                        productamount += parseInt(pd_total);
                    }
                })
                $('#frm #bill_price').val(formatMoney(productamount));

                //	bill amount
                loop.forEach(function(key, index) {
                    if (parseFloat(key.value)) {
                        resultamount += parseFloat(key.value);
                        // console.log(resultamount+" + "+parseInt(key.value));
                    }
                })
                resultamount += productamount;
                $('#frm #totalamount').val(formatMoney(resultamount));

                //  check force zero
                let checkzero = $('#totalzero');
                if (checkzero.prop('checked')) {
                    $('#frm #totalamount').val(0);
                }
            }

            //  image 
            $(document).on('change', 'input[type=file]', function(event) {
                let image_file = $(this);
                var length = (image_file[0].files.length - 1);
                var html = "";
                for (var i = 0; i <= length; i++) {

                    if (image_file[0].files[i]) {

                        //  check extension
                        let fileName = image_file[0].files[i].name,
                            idxDot = fileName.lastIndexOf(".") + 1,
                            extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
                        if (extFile == "jpg" || extFile == "jpeg" || extFile == "png") {
                            //TO DO
                            html += '<img id="img-' + i + '" src="' + window.URL.createObjectURL(image_file[0].files[i]) + '" class="img" >';
                            $('.thumbnail-image').html(html);
                        } else {
                            //  error
                            swal.fire('ข้อมูลไม่ถูกต้อง', 'บันทึกเฉพาะไฟล์รูปภาพ', 'warning');
                            clearImage(); // Reset the input so no files are uploaded

                            return false;
                        }
                    }
                }
            });
            /**
             * ========================================================================================
             * จบส่วนการทำงานของปุ่มเอกสารเพิ่มเติม End
             * ========================================================================================
             */


            // ประวัติเอกสาร
            $('.modal_history').on('show.bs.modal', function() {
                let modalbody = $(".modal_history .modal-body");

                //  reset 
                modalbody.html('');

                async function runHistory() {
                    let result1 = await awaiFindBillRef();
                    let result2 = await awaiFindBillNew();
                    let result3 = await awaiFindHistory();
                    let result4 = await awaiFindReceive();

                }
                /* runHistory().then((data) =>{
                    console.log(data);
                }); */
                runHistory();


            });

            function awaiFindBillRef() {

                let modalbody = $(".modal_history .modal-body");

                let url = 'findBillRef';

                const form = new FormData();
                form.append('bill_id', billID);

                let option = {
                    method: 'POST',
                    body: form
                }

                fetch(url, option)
                    .then(res => res.json())
                    .then((resp) => {
                        let errorcode = 1;
                        let documentorder = resp.data.listorder;
                        for (var k in documentorder) {
                            let typeloss = documentorder[k].type ? "<font class='text-success'>" + documentorder[k].type + "</font>" : "";

                            let date_ob = new Date(documentorder[k].date);
                            let date = ("0" + date_ob.getDate()).slice(-2);
                            let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
                            let year = date_ob.getFullYear();
                            let newDate = date + "/" + month + "/" + year;

                            let datetime = documentorder[k].date ? newDate : "";
                            let net = documentorder[k].net ? documentorder[k].net : 0;

                            modalbody.append(`<a target=_blank href=<?php echo site_url('mod_retailcreateorder/ctl_createorder/viwecreatebill') ?>?id=${documentorder[k].id}&mdl=mdl_createorder><p>อิงจาก : ${documentorder[k].code} ยอด ${net} บาท
                            ${datetime}
                            ${typeloss}
                            </p></a>`);

                            errorcode = 0;
                        }
                    })
                    .catch(error => console.log(`Error : ${error}`))

                return 'bbbb';
                /* return new Promise((resolve, reject) => {
                    resolve({
                        error_code: errorcode,
                    })
                }) */

            }

            function awaiFindBillNew() {

                let modalbody = $(".modal_history .modal-body");

                let url = 'findBillNew';

                const form = new FormData();
                form.append('bill_id', billID);

                let option = {
                    method: 'POST',
                    body: form
                }

                fetch(url, option)
                    .then(res => res.json())
                    .then((resp) => {
                        let errorcode = 1;
                        let documentorder = resp.data.listorder;
                        for (var k in documentorder) {
                            let typeloss = documentorder[k].type ? "<font class='text-success'>" + documentorder[k].type + "</font>" : "";

                            let date_ob = new Date(documentorder[k].date);
                            let date = ("0" + date_ob.getDate()).slice(-2);
                            let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
                            let year = date_ob.getFullYear();
                            let newDate = date + "/" + month + "/" + year;

                            let datetime = documentorder[k].date ? newDate : "";
                            let net = documentorder[k].net ? documentorder[k].net : 0;

                            modalbody.append(`<a target=_blank href=<?php echo site_url('mod_retailcreateorder/ctl_createorder/viwecreatebill') ?>?id=${documentorder[k].id}&mdl=mdl_createorder><p>บิลใหม่ : ${documentorder[k].code} ยอด ${net} บาท
                            ${datetime}
                            ${typeloss}
                            </p></a>`);

                            errorcode = 0;
                        }
                    })
                    .catch(error => console.log(`Error : ${error}`))

                return 'bbbb';
                /* return new Promise((resolve, reject) => {
                    resolve({
                        error_code: errorcode,
                    })
                }) */

            }

            function awaiFindHistory() {

                let modalbody = $(".modal_history .modal-body");

                let url = 'findHistory';

                const form = new FormData();
                form.append('bill_id', billID);

                let option = {
                    method: 'POST',
                    body: form
                }

                fetch(url, option)
                    .then(res => res.json())
                    .then((resp) => {
                        let errorcode = 1;
                        let documentorder = resp.data.listorder;
                        for (var k in documentorder) {
                            let typeloss = documentorder[k].type ? "<font class='text-danger'>(" + documentorder[k].type + ")</font>" : "";

                            let date_ob = new Date(documentorder[k].date);
                            let date = ("0" + date_ob.getDate()).slice(-2);
                            let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
                            let year = date_ob.getFullYear();
                            let newDate = date + "/" + month + "/" + year;

                            let datetime = documentorder[k].date ? newDate : "";
                            let net = documentorder[k].net ? documentorder[k].net : 0;

                            modalbody.append(`<a target=_blank href=<?php echo site_url('mod_retailcreditnote/ctl_creditnote/viewbill') ?>?id=${documentorder[k].id}><p>ใบลดหนี้ : ${documentorder[k].code} ยอด ${net} บาท
                                ${datetime}
                                ${typeloss}
                                </p></a>`);

                            errorcode = 0;
                        }
                    })
                    .catch(error => console.log(`Error : ${error}`))

                return 'aaaa';
                /* return new Promise((resolve, reject) => {
                    resolve({
                        error_code: errorcode,
                    })
                }) */

            }

            function awaiFindReceive() {

                let modalbody = $(".modal_history .modal-body");

                let url = 'findReceive';

                const form = new FormData();
                form.append('bill_id', billID);

                let option = {
                    method: 'POST',
                    body: form
                }

                fetch(url, option)
                    .then(res => res.json())
                    .then((resp) => {
                        let errorcode = 1;
                        let documentorder = resp.data.listorder;
                        for (var k in documentorder) {
                            let date_ob = new Date(documentorder[k].date);
                            let date = ("0" + date_ob.getDate()).slice(-2);
                            let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
                            let year = date_ob.getFullYear();
                            let newDate = date + "/" + month + "/" + year;

                            let datetime = documentorder[k].date ? newDate : "";

                            modalbody.append(`<a target=_blank href=<?php echo site_url('mod_retailreceive/ctl_receive/viewbill') ?>?id=${documentorder[k].id}><p>บิลรับของ :${documentorder[k].code}
            ${datetime}
            </p></a>`);

                            errorcode = 0;
                        }
                    })
                    .catch(error => console.log(`Error : ${error}`))

                return 'bbbb';
                /* return new Promise((resolve, reject) => {
                    resolve({
                        error_code: errorcode,
                    })
                }) */

            }

            $(document).on('click', '.btn_loadbillvat ', function(event) {
                $('input#listorderid').val($('#bill_ID').val());

                var reporttype = "bill_vat";
                $('#table').val(reporttype);

                var d = document;
                d.frmreport.submit();
            });

            // DELETE ORDER //
            $(document).on('click', '#deleteorder', function(event) {
                $('#hdfdeleteorder').val(this.value);
                $('#remark').val('');
            });
            $(document).on('click', '#confirmdelete', function(event) {
                var val = $('#hdfdeleteorder').val();
                var remark = $('#remark').val();
                $.post("deleteorder", {
                    id: val,
                    remark: remark
                }, function(result) {
                    var obj = jQuery.parseJSON(result);
                    Toast.fire({
                        type: 'success',
                        title: obj.txt
                    });
                    var getid = $('#hdfdeleteorder').val();
                    setTimeout(function() {
                        window.location.replace('viwecreatebill?id=' + getid);
                    }, 1500);
                });
            });
        });

        // เช็ค TextCode ซ้ำ //
        function checkTextcode() {
            var textcode = $('#cust_textcode').val();
            $.ajax({
                url: "ajaxchecktextcode",
                type: 'POST',
                data: {
                    textcode: textcode
                },
                success: function(results) {
                    var obj = jQuery.parseJSON(results);
                    if (obj > 0) {
                        $("#TextCode").addClass('is-invalid');
                        $("#TextCode").focus();
                        alert('ผิดผลาด! กรุณาตรวจสอบใหม่ Text Code นี้เคยมีการสร้างออเดอร์ไปแล้ว');
                        $('#TextCode').val('');
                        $("#TextCode").removeClass('is-invalid');
                    } else {
                        $("#TextCode").removeClass('is-invalid');
                    }
                }
            });
        }
    </script>
    <script>
        function checkNumber(ele) {
            var vchar = String.fromCharCode(event.keyCode);
            // console.log(vchar);
            if (vchar < '0' || vchar > '9') {
                return false
            }

        }

        function checkPrice(ele) {
            var vchar = event.keyCode;

            let arraydetail = [45, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58];

            let search = arraydetail.find(res => res == vchar);
            if (!search) {
                return false;
            }
        }

        function formatMoney(number, decPlaces, decSep, thouSep) {
            decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
                decSep = typeof decSep === "undefined" ? "." : decSep;
            thouSep = typeof thouSep === "undefined" ? "," : thouSep;
            var sign = number < 0 ? "-" : "";
            var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
            var j = (j = i.length) > 3 ? j % 3 : 0;

            return sign +
                (j ? i.substr(0, j) + thouSep : "") +
                i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
                (decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
</body>

</html>