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
                            <h1><?php echo "ใบรับเข้า - แก้ไข"; ?></h1>
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
                                </div>
                                <div class="card-body">

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
                fetch('get_dataBill', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        hideLoading();

                        if (!resp) {
                            $('.connectedSortable .card-body').append('<p class="aaa">ไม่มีข้อมูล</p>');
                            return false;
                        }

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
                let url = 'get_billImg?id=' + billid;
                let option = {
                    method: 'GET',
                };

                fetch(url, option)
                    .then(res => res.json())
                    .then((resp) => {

                        let dataresult = resp.data;
                        for (var i in dataresult) {
                            img = `<div >
                            <span class="btn btn-outline-danger del-img" style="position:absolute;">delete</span>
                            <img src="${dataresult[i]['path']}" data-id="${dataresult[i]['id']}" data-del="">
                            </div>`;
                            $('.bill-image').append(img);
                        }
                    })
                    .catch(error => {
                        console.log(`error : ${error}`)
                    })
            }

            //	quantity
            $(document).on('keyup', '.input-qty', function() {
                calculateProduct(this, $(this).val());
            })

            $(document).on('click', '#submitform', function(e) {
                e.stopPropagation;
                e.stopImmediatePropagation();

                add_bill();
            })

            $(document).on('click', '#btn-back', function(e) {
                e.stopPropagation;
                e.stopImmediatePropagation();

                window.location.replace('viewbill?id=' + billID);
            })

            //	cal price
            $(document).on('click', '.btn-del', function() {
                let tr_id = $(this).parents('tr').attr('data-row');
                $('tr[data-row=' + tr_id + ']').remove();

                //	bill amount
                calculateBill();
            })

            $(document).on('click', '.bill-image img', function(e) {
                e.stopPropagation;
                e.stopImmediatePropagation();

                $('.modal-image .modal-body').html(this.outerHTML);
                $('.modal-image').modal({
                    show: true
                });
            })

            $(document).on('click', '.del-img', function(e) {
                e.stopPropagation;
                e.stopImmediatePropagation();

                this.parentElement.setAttribute('class', 'd-none');
                this.parentElement.getElementsByTagName("img")[0].setAttribute('data-del', '1');
            })

            //  image 
            $(document).on('change', '#image_file', function(event) {
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

            $(document).on('click', '#cancelimgdetail', function() {
                clearImage();
            })

            function clearImage() {
                $("#imagedledetail").text("Choose file");
                $("#image_file").val(null);

                $('.thumbnail-image').html('');
            }

            function formDataInsert(bill) {
                frm.find('.bill_code').text(bill.code);
                frm.find('.bill_textcode').text(bill.retail_code);
                frm.find('.bill_datecreate').text(bill.datecreate);
                frm.find('.bill_staffcreate').text(bill.staffcreate);

                frm.find('.bill_complete').html(bill.complete);

                frm.find('.bill_name').text(bill.name);
                frm.find('.bill_tel').text(bill.tel);
                frm.find('.bill_citizen').text(bill.citizen);
                frm.find('.bill_address').text(bill.address);
                frm.find('.bill_zipcode').text(bill.zipcode);

                frm.find('.bill_price').val(formatMoney(bill.price));
                frm.find('.bill_parcel').val(bill.parcel);
                frm.find('.bill_logis').val(bill.logis);
                frm.find('.bill_shor').val(bill.shor);
                frm.find('.bill_discount').val(bill.discount);
                frm.find('.bill_tax').val(bill.tax);
                frm.find('.bill_net').val(formatMoney(bill.net));

                frm.find('#remark').text(bill.remark);

                //  check
                if (!bill.approve_store) {
                    frm.find('.bill_check').text('รอคลังตรวจสอบ');
                } else {
                    frm.find('.bill_check').text('ดำเนินการสำเร็จ');

                    //  หากบิลดำเนินการสำเร็จแล้ว ไม่ให้ยกเลิก
                    $('#btn-cancel').remove();
                }

                if (bill.loss == 0) {
                    bill.loss = "0";
                }
                $('#select_return').val(bill.loss).change();

            }

            function formDataDetailInsert(billdetail) {
                let hmtl = "";

                // console.log(billdetail.length);
                if (billdetail.length) {

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
                        hmtl += '<td class=""><button type="button" class="btn btn-sm btn-danger btn-del"><i class="fa fa-close"></i></button></td>';
                        hmtl += '<td class="index">' + index + '</td>';
                        hmtl += '<td class="name"> ' + product_name + ' </td>';
                        hmtl += '<td class="price">' + product_price + '</td>';
                        hmtl += '<td class="qty">';
                        hmtl += '<input type="text" value="' + product_qty + '" class="w-100 input-qty" data-id="' + iddetail + '" data-promain="' + promain + '" data-prolist="' + prolist + '" data-list="' + list + '" data-price="' + product_price + '" OnKeyPress="return checkNumber(this)">';
                        hmtl += '</td>';
                        hmtl += '<td class="totalprice text-right">' + product_totalprice + '</td>';
                        hmtl += '</tr>';
                    })

                    frm.find('.tabledetail tbody').html(hmtl);
                }
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

            function add_bill() {
                //  loader
                let loading = '<div class="col-sm-12 text-center loading"><div class="spinner-border text-info"></div></div>';
                let rowtoolbtn = $('.row-form-tool-btn');
                let toolbtn = $('.form-tool-btn');

                rowtoolbtn.append(loading);
                toolbtn.addClass('d-none');

                let loop = document.querySelectorAll('[data-loop]');
                let product = document.querySelectorAll('.tabledetail tbody tr');
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
                    pd_iddetail = key.getElementsByTagName('input')[0].getAttribute('data-id');
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

                    data.append('item[' + index + '][iddetail]', pd_iddetail);
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

                data.append('remark', document.getElementById('remark').value);
                data.append('bill_id', document.getElementById('bill_id').value);
                data.append('bill_code', document.getElementById('bill_code').value);

                // image
                var image_file = $('#image_file');
                if (image_file[0].files.length > 0) {
                    var length = (image_file[0].files.length - 1);
                    for (var i = 0; i <= length; i++) {
                        data.append('file[]', image_file[0].files[i]);
                    }

                }

                let imageDelete = document.querySelectorAll('img[data-del]');
                let dataid = "";
                let datadel = "";
                imageDelete.forEach(function(key, index) {
                    dataid = key.getAttribute('data-id');
                    datadel = key.getAttribute('data-del');
                    if (datadel == 1) {
                        data.append('imagedel[]', dataid);
                    }
                })


                if (error == 0) {
                    let url = 'update_bill';
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

                                    let rowtoolbtn = $('.row-form-tool-btn');
                                    let toolbtn = $('.form-tool-btn');

                                    $('.loading').remove();
                                    toolbtn.removeClass('d-none');

                                    window.location.replace('viewbill?id=' + billID);
                                })
                            }
                        })
                        .catch((error) => {
                            console.log(`error : ${error}`);
                            modaladdbill.find('.modal-body').text('เกิดข้อผิดพลาดกรุณาแจ้งเจ้าหน้าที่');
                        })
                }


            }

        })

        //----------------------------function--------------------------//
        function calculateProduct(ele, number) {
            let qty = number;
            let price = $(ele).attr('data-price');

            let result = qty * price;
            let html_price = $(ele).parents('tr').children('td.totalprice');

            //	return
            html_price.text(formatMoney(result));

            //	total amount
            calculateBill();
        }

        function calculateBill() {
            let productamount = 0;
            let resultamount = 0;
            let loop = document.querySelectorAll('.billnumber');
            let productprice = document.querySelectorAll('.input-qty');

            let pd_qty = 0;
            let pd_price = 0;
            let pd_total = 0;

            //	product total
            if (productprice.length) {
                productprice.forEach(function(key, index) {
                    pd_qty = key.value;
                    pd_price = key.getAttribute('data-price');

                    pd_total = pd_qty * pd_price;

                    if (parseInt(pd_total)) {
                        productamount += parseInt(pd_total);
                    }
                })
                document.getElementById('bill_price').value = formatMoney(productamount);
            }
            //	bill amount
            if (loop.length) {
                loop.forEach(function(key, index) {
                    if (parseInt(key.value)) {
                        resultamount += parseInt(key.value);
                        // console.log(resultamount+" + "+parseInt(key.value));
                    }
                })
                resultamount += productamount;
                document.getElementById('totalamount').value = formatMoney(resultamount);
            }

            //  check force zero
            let checkzero = $('#totalzero');
            if (checkzero.prop('checked')) {
                document.getElementById('totalamount').value = 0;
            }
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
</body>

</html>