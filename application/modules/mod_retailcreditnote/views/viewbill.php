<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("structer/backend/head.php"); ?>
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
                            <h1><?php echo "ใบลดหนี้"; ?></h1>
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

                <div class="container">
                    <div class="row">

                        <section class="col-lg-12 connectedSortable">
                            <!-- Custom tabs (Charts with tabs)-->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i> <?php echo "Manage " . $mainmenu; ?> </h3>
                                    <?php if (chkPermissPage('report_creditnote')) { ?>
                                        <div class="text-right">
                                            <a href="<?php echo site_url('mod_retailcreditnote/ctl_creditnote/doc_creditnote') . "?id=" . $this->input->get('id'); ?>" target=_blank type="button" id="download" class="btn btn-sm btn-warning ">download เอกสาร</a>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" id="permit_approve" name="permit_approve" value="<?php echo (chkPermissPage('approve_creditnote') ? 1 : null); ?>">
                                    <input type="hidden" id="permit_returnapprove" name="permit_returnapprove" value="<?php echo (chkPermissPage('return_creditnote') ? 1 : null); ?>">
                                    <input type="hidden" id="permit_approvestore" name="permit_approvestore" value="<?php echo (chkPermissPage('approvestore_creditnote') ? 1 : null); ?>">

                                    <?php require_once('form_bill.php'); ?>

                                </div>
                            </div>
                        </section>

                    </div>
                </div>

            </section>

            <!--	Modal	-->
            <div class="modal modal-image fade bs-example-modal-center" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            ...
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php include("structer/backend/footer.php"); ?>
        <?php include("structer/backend/script.php"); ?>

        <script src="<?php echo $base_bn; ?>plugins/sweetalert2/sweetalert2.min.js"></script>

    </div>

    <script>
        const queryString = decodeURIComponent(window.location.search);
        const params = new URLSearchParams(queryString);
        let billID = params.get("id");

        $(function() {

            //	setting
            let search_order = $('[type=search]');
            let frm = $('form#frm');
            let search_result = $('.search_result');

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            //  get order bill data
            get_orderBill(billID);

            //	search order
            function get_orderBill(bill_id) {
                displayLoading('.content .card-body');

                var data = new FormData();
                // data.append("bill_id", 16286);
                data.append("bill_id", billID);

                //	วิธี fetch แบบ error handling
                fetch('get_creditnoteBill', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        hideLoading();

                        let dataresult = resp;
                        let resultSearch = "";
                        let bill = resp.data;
                        let billdetail = resp.datadetail;

                        // console.log(billdetail);
                        $('#bill_id').val(bill.id);
                        $('#bill_code').val(bill.code);

                        formDataInsert(bill);

                        if (billdetail[0]) {
                            formDataDetailInsert(billdetail);
                        }

                        //  load image
                        fetchImage(bill.id);

                        frm.removeClass('d-none');
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`)
                    })
            }

            //  load bill image
            function fetchImage(billid) {
                let html = "";
                let img = "";
                let url = 'get_creditnoteImg?id=' + billid;
                let option = {
                    method: 'GET',
                };

                fetch(url, option)
                    .then(res => res.json())
                    .then((resp) => {

                        let dataresult = resp.data;
                        for (var i in dataresult) {
                            img = `<div >
                            <img src="${dataresult[i]['path']}" data-id="${dataresult[i]['id']}" data-del="">
                            </div>`;
                            $('.bill-image').append(img);
                        }
                    })
                    .catch(error => {
                        console.log(`error : ${error}`)
                    })
            }

            $(document).on('click', '#editform', function(e) {
                e.stopPropagation;
                e.stopImmediatePropagation();

                window.location.replace('editbill?id=' + billID);
            })

            $(document).on('click', '#btn-back', function(e) {
                e.stopPropagation;
                e.stopImmediatePropagation();

                window.close();
            })

            //	cal price
            $(document).on('keyup', '.billnumber', function() {
                calculateCreditBill();
            })

            $(document).on('click', '.bill-image img', function(e) {
                e.stopPropagation;
                e.stopImmediatePropagation();

                $('.modal-image .modal-body').html(this.outerHTML);
                $('.modal-image').modal({
                    show: true
                });
            })

            function formDataInsert(bill) {
                let date_appr = new Date(bill.appr_date);
                let date_apst = new Date(bill.apst_date);

                frm.find('.bill_code').text(bill.code);
                frm.find('.bill_codereport').text(bill.codereport);
                frm.find('.bill_textcode').text(bill.retail_code);
                frm.find('.bill_datecreate').text(bill.datecreate);
                frm.find('.bill_staffcreate').text(bill.staffcreate);
                frm.find('.bill_staffupdate').html(bill.staffupdate);

                if (bill.appr_username) {
                    frm.find('.bill_appr_user').text(bill.appr_username);
                    frm.find('.bill_appr_user').append('<br><span>(' + toThaiDateTimeString(date_appr, 'datetime') + ' )</span>');
                }
                if (bill.apst_username) {
                    frm.find('.bill_apst_user').text(bill.apst_username);
                    frm.find('.bill_apst_user').append('<br><span>(' + toThaiDateTimeString(date_apst, 'datetime') + ' )</span>');
                }
                frm.find('.bill_complete').html(bill.complete);

                frm.find('.bill_name').text(bill.name);
                frm.find('.bill_tel').text(bill.tel);
                frm.find('.bill_citizen').text(bill.citizen);
                frm.find('.bill_address').text(bill.address);
                frm.find('.bill_zipcode').text(bill.zipcode);

                frm.find('.bill_price').text(formatMoney(bill.price));
                frm.find('.bill_parcel').text(formatMoney(bill.parcel));
                frm.find('.bill_logis').text(formatMoney(bill.logis));
                frm.find('.bill_shor').text(formatMoney(bill.shor));
                frm.find('.bill_discount').text(formatMoney(bill.discount));
                frm.find('.bill_tax').text(formatMoney(bill.tax));
                frm.find('.bill_net').text(formatMoney(bill.net));

                frm.find('#remark').text(bill.remark);

                if (bill.loss) {

                    frm.find('.htmltext-loss').removeClass('d-none');
                    frm.find('.htmltext-return').addClass('d-none');
                } else {
                    frm.find('.htmltext-return').removeClass('d-none');
                    frm.find('.htmltext-loss').addClass('d-none');
                }
                let fomrbtn = "";
                let usercancel = '<span class="float-right">ยกเลิกโดย ' + bill.staffcreate + '<br>- '+ bill.remark_order +'</span>';
                let permit_approve = document.getElementById('permit_approve').value;
                let permit_returnapprove = document.getElementById('permit_returnapprove').value;
                let permit_approvestore = document.getElementById('permit_approvestore').value;

                if (bill.codecomplete != 3) {

                    if (bill.codecomplete == 2) {
                        //  หากมีการอนุมัติบิลแล้วห้ามมีการแก้ไข
                        $('#editform').remove();

                        //  หากบิลดำเนินการสำเร็จแล้ว ไม่ให้ยกเลิก
                        $('#btn-cancel').remove();

                        frm.find('.bill_check').text('ดำเนินการสำเร็จ');
                    } else {

                        if (bill.approve) {
                            //  หากมีการอนุมัติบิลแล้วห้ามมีการแก้ไข
                            $('#editform').remove();

                            //  และทาง store ยังไม่อนุมัติ
                            if (!bill.approve_store && bill.codecomplete != 2) {
                                if (permit_returnapprove == 1) {
                                    fomrbtn += '<button type="button" class="cf_finance btn btn-md btn-danger" data-value="0" >ยกเลิกอนุมัติ</button> ';
                                }
                            }
                        } else {
                            if (permit_approve == 1) {
                                fomrbtn += '<button type="button" class="cf_finance btn btn-md px-5 btn-outline-info" data-value="1" >ยืนยันยอดบิล</button> '
                            }
                        }

                        if (!bill.loss && bill.approve) {
                            if (bill.approve_store) {
                                // fomrbtn += '<button type="button" class="cf_stock btn btn-md btn-danger" data-value="0" >ยกเลิกรับสินค้า</button> ';
                            } else {
                                if (permit_approvestore == 1) {
                                    //  ไม่แสดงในใบลดหนี้
                                    // fomrbtn += '<button type="button" class="cf_stock btn btn-md px-5 btn-outline-info" data-value="1" >ยืนยันการรับสินค้า</button> '
                                }
                            }
                        }

                        //  check
                        if (!bill.approve) {
                            frm.find('.bill_check').text('รอบัญชีตรวจสอบ');
                        } else if (!bill.approve_store && !bill.loss) {
                            frm.find('.bill_check').text('รอคลังตรวจสอบ');
                        } else {
                            frm.find('.bill_check').text('ดำเนินการสำเร็จ');

                            //  หากบิลดำเนินการสำเร็จแล้ว ไม่ให้ยกเลิก
                            $('#btn-cancel').remove();
                        }

                        $('.form-tool-btn').prepend(fomrbtn);

                    }


                } else {
                    $('.form-tool-btn #submitform').remove();
                    $('.form-tool-btn #editform').remove();
                    $('.form-tool-btn #btn-cancel').remove();
                    $('.form-tool-btn').append(usercancel);
                }



            }

            function formDataDetailInsert(billdetail) {
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
                    let iddetail = key.product_rowid;

                    hmtl += '<tr data-row="' + index + '">';
                    hmtl += '<td class=""></td>';
                    hmtl += '<td class="index">' + index + '</td>';
                    hmtl += '<td class="name"> ' + product_name + ' </td>';
                    hmtl += '<td class="price">' + product_price + '</td>';
                    hmtl += '<td class="qty">';
                    // hmtl += '<input type="text" value="' + +'" class="w-100 input-qty" data-id="' + iddetail + '" data-promain="' + promain + '" data-prolist="' + prolist + '" data-list="' + list + '" data-price="' + product_price + '" OnKeyPress="return checkNumber(this)">';
                    hmtl += '<span>' + product_qty + '</span>';
                    hmtl += '</td>';
                    hmtl += '<td class="totalprice text-right">' + product_totalprice + '</td>';
                    hmtl += '</tr>';
                })

                frm.find('.tabledetail tbody').html(hmtl);
            }
            //----------------------------function--------------------------//
            function displayLoading(elename) {
                let loading = '<div class="spinner-border text-info loading"></div>';

                $(elename).append(loading);
            }

            function hideLoading() {
                $('.loading').addClass('d-none');
                frm.removeClass('d-none');
            }

        })
    </script>
    <script>
        $(function() {
            //  loader
            let loading = '<div class="col-sm-12 text-center loading"><div class="spinner-border text-info"></div></div>';
            let rowtoolbtn = $('.row-form-tool-btn');
            let toolbtn = $('.form-tool-btn');

            //  button approve bill
            $(document).on('click', '.cf_finance', function() {
                rowtoolbtn.append(loading);
                toolbtn.addClass('d-none');

                var data = new FormData();
                // data.append("bill_id", 16286);
                data.append("bill_id", billID);
                data.append("approve", this.getAttribute('data-value'));

                let url = 'confirmFinance';
                let options = {
                    method: 'POST',
                    body: data
                };

                fetch(url, options)
                    .then(res => res.json())
                    .then((resp) => {

                        if (resp.error_code == 0) {
                            window.location.reload();
                        } else {
                            swal.fire('ทำรายการไม่สำเร็จ', resp.txt, 'warning')
                                .then((result) => {
                                    window.location.reload();
                                });
                        }
                    })
                    .catch(error => {
                        console.log(`error : ${error}`);
                    })
            })

            $(document).on('click', '.cf_stock', function() {

                Swal.fire({
                    type: 'warning',
                    title: 'ยืนยันการรับสินค้า',
                    text: 'เมื่อยืนยันแล้วจะไม่สามารถยกเลิกรายการได้',
                    showConfirmButton: true,
                    showCancelButton: true
                }).then((result) => {

                    if (result.value) {
                        rowtoolbtn.append(loading);
                        toolbtn.addClass('d-none');

                        var data = new FormData();
                        // data.append("bill_id", 16286);
                        data.append("bill_id", billID);
                        data.append("approve", this.getAttribute('data-value'));

                        let url = 'confirmStore';
                        let options = {
                            method: 'POST',
                            body: data
                        };

                        fetch(url, options)
                            .then(res => res.json())
                            .then((resp) => {
                                if (resp.error_code == 0) {
                                    window.location.reload();
                                } else {
                                    swal.fire('ทำรายการไม่สำเร็จ', resp.txt, 'warning')
                                        .then((result) => {
                                            window.location.reload();
                                        });
                                }
                            })
                            .catch(error => {
                                console.log(`error : ${error}`);
                            })
                    }
                })

            })

            $(document).on('click', '#btn-cancel', function() {
                Swal.fire({
                    type: 'warning',
                    title: 'ต้องการยกเลิก',
                    text: 'กรุณาระบุเหตุผล เพื่อยกเลิกรายการ',
                    showConfirmButton: true,
                    showCancelButton: true,
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                }).then((result) => {
                    if (result.value) {
                        rowtoolbtn.append(loading);
                        toolbtn.addClass('d-none');

                        var data = new FormData();
                        // data.append("bill_id", 16286);
                        data.append("bill_id", billID);
                        data.append("remark_order", result.value);

                        let url = 'cancelBill';
                        let options = {
                            method: 'POST',
                            body: data
                        };

                        fetch(url, options)
                            .then(res => res.json())
                            .then((resp) => {
                                console.log(resp);
                                if (resp.error_code == 0) {
                                    window.location.reload();
                                } else {
                                    swal.fire('ทำรายการไม่สำเร็จ', resp.txt, 'warning')
                                        .then((result) => {
                                            window.location.reload();
                                        });
                                }
                            })
                            .catch(error => {
                                console.log(`error : ${error}`);
                            })
                    }
                });
            })

        })
    </script>
    <script>
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
        //	date
        //	@param	date	@date = date yyyy-mm-dd (2021-07-08)
        //	@param	typereturn	@text = [date , datetime]
        //	return datetime TH
        //
        function toThaiDateTimeString(date, typereturn) {
            let monthNames = [
                "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน",
                "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม.",
                "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            let year = date.getFullYear() + 543;
            let month = monthNames[date.getMonth()];
            let numOfDay = date.getDate();
            // console.log(date + "--" + typereturn);
            let hour = date.getHours().toString().padStart(2, "0");
            let minutes = date.getMinutes().toString().padStart(2, "0");
            let second = date.getSeconds().toString().padStart(2, "0");

            switch (typereturn) {
                case 'datetime':
                    return `${numOfDay} ${month} ${year} ` +
                        `${hour}:${minutes}:${second} น.`;
                    break;
                case 'date':
                    return `${numOfDay} ${month} ${year} `;
                    break;
                default:
                    return `${numOfDay} ${month} ${year} ` +
                        `${hour}:${minutes}:${second} น.`;
                    break;
            }

        }
    </script>
</body>

</html>