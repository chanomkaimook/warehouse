<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("structer/backend/head.php"); ?>
    <link rel="stylesheet" href="<?php echo $base_bn; ?>frontend/bootstrap-select/css/bootstrap-select.css">
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
                            <h1><?php echo "Note - แก้ไข"; ?></h1>
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

                    </div>
                </div>

            </section>

        </div>
        <?php include("structer/backend/footer.php"); ?>
        <?php include("structer/backend/script.php"); ?>
        <script src="<?php echo $base_bn; ?>frontend/bootstrap-select/js/bootstrap-select.js"></script>
        <script src="<?php echo $base_bn; ?>plugins/sweetalert2/sweetalert2.min.js"></script>

    </div>

    <script>
        const queryString = decodeURIComponent(window.location.search);
        const params = new URLSearchParams(queryString);
        let billID = params.get("id");

        $(function() {

            //	setting
            let frm = $('form#frm');

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            //  get order bill data
            get_orderBill(billID);

            //	search order
            function get_orderBill(billID) {
                displayLoading('.content .card-body');

                var data = new FormData();
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

                        let bill = resp.data;
                        let billdetail = resp.datadetail;
                        let billreceivedetail = resp.datareceivedetail;
                        console.log(resp);
                        $('#bill_id').val(bill.tb_id);
                        $('#bill_code').val(bill.tb_code);

                        formDataInsert(bill);

                        if (billdetail[0]) {
                            formDataDetailInsert(billdetail);
                        }
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`)
                    })
            }

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

            $(document).on('click', '.btn-del', function() {
                let tr_id = $(this).parents('tr').attr('data-row');
                $('tr[data-row=' + tr_id + ']').remove();
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
                let usercreate =  bill.tb_user_starts;
                let datecreate =  toThaiDateTimeString(new Date(bill.tb_date_starts), 'datetime');

                let userupdate;
                let dateupdate;
                if(bill.tb_date_update){
                    dateupdate = toThaiDateTimeString(new Date(bill.tb_date_update), 'datetime'); 
                }

                //  set style div
                frm.find('.bill_code').text(bill.tb_code);
                frm.find('.bill_type').text(bill.tb_type);
                frm.find('.billto').text(bill.tb_billto);
                frm.find('.bill_status').html(bill.tb_complete);
                frm.find('#remark').text(bill.tb_remark);
                frm.find('.bill_staffcreate').text(bill.tb_user_starts);
                frm.find('.bill_datecreate').text(datecreate);
                frm.find('.bill_staffedit').text(bill.tb_user_update);
                frm.find('.bill_dateedit').text(dateupdate);

                if(bill.check_rc){
                    $('#btn-cancel').remove();
                }

                frm.removeClass('d-none');
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

                        let receivetotal = key.product_receive;
                        let receivewaite = key.product_receivewaite;

                        let iddetail = key.product_rowid;

                        let button_del = '<button type="button" class="btn btn-sm btn-danger btn-del"><i class="fa fa-close"></i></button>';
                        let input_qty = '<input type="text" value="' + product_qty + '" class="w-100 input-qty" data-id="' + iddetail + '" data-promain="' + promain + '" data-prolist="' + prolist + '" data-list="' + list + '" data-price="' + product_price + '" OnKeyPress="return checkNumber(this)">';

                        if(receivetotal > 0){
                            button_del = "";
                        }

                        if(billdetail.length == 1){
                            //  หากรายการสินค้าเหลือ 1 ไม่ให้ลบ
                            button_del = "";
                        }

                        hmtl += '<tr data-row="' + index + '">';
                        hmtl += '<td class="">'+ button_del +'</td>';
                        hmtl += '<td class="index">' + index + '</td>';
                        hmtl += '<td class="name">' + product_name + '</td>';
                        hmtl += '<td class="qty">' + input_qty + '</td>';
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
                    pd_price = 0;
                    pd_totalprice = 0;

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

                if (error == 0) {
                    let url = 'update_bill';
                    let options = {
                        method: 'POST',
                        body: data
                    };

                    fetch(url, options)
                        .then(res => res.json())
                        .then(resp => {
                            // console.log(resp);
                            if (resp.error_code != 0) {
                                swal.fire('ข้อมูลผิดพลาด', resp.txt, 'warning');
                                $('.loading').remove();
                                toolbtn.removeClass('d-none');
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
                        })
                }


            }

            //  ========= Begin add product   ========
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

                data.append("billid", billID);
                data.append("pid", dataarray.plist_id);
                data.append("pqty", dataarray.pqty);

                fetch('get_product', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        if(!resp){
                            $('.modal-footer .htmlvalidate').html('<span class="text-validate text-danger float-left"> มีข้อมูลอยู่แล้ว </span>');
                        }else{
                            let totalprice = resp.qty * resp.price;

                            let billdetail = [{
                                list: resp.list,
                                product_name: resp.name_th,
                                product_price: resp.price,
                                product_qty: resp.qty,
                                product_totalprice: formatMoney(totalprice),
                                prolist: resp.id,
                                promain: resp.main,
                                product_rowid: resp.id
                            }]
                            formDataDetailAppend(billdetail);

                            //  close modal
                            $('.bd-example-modal-lg').modal('hide');
                        }
                        
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`);
                    })

            }
            function formDataDetailAppend(billdetail) {
                let hmtl = "";

                // console.log(billdetail);
                let number = $('.tabledetail tbody tr').length;
                if (billdetail.length) {

                    billdetail.forEach(function(key, val) {
                        let index = number + 1;
                        let product_name = key.product_name;
                        let product_price = formatMoney(key.product_price);
                        let product_qty = key.product_qty;
                        let product_totalprice = key.product_totalprice;
                        let promain = key.promain;
                        let prolist = key.prolist;
                        let list = key.list;

                        let iddetail = key.product_rowid;

                        let button_del = '<button type="button" class="btn btn-sm btn-danger btn-del"><i class="fa fa-close"></i></button>';
                        let input_qty = '<input type="text" value="' + product_qty + '" class="w-100 input-qty" data-id="' + iddetail + '" data-promain="' + promain + '" data-prolist="' + prolist + '" data-list="' + list + '" data-price="' + product_price + '" OnKeyPress="return checkNumber(this)">';

                        hmtl += '<tr data-row="' + index + '">';
                        hmtl += '<td class="">'+ button_del +'</td>';
                        hmtl += '<td class="index">' + index + '</td>';
                        hmtl += '<td class="name">' + product_name + '</td>';
                        hmtl += '<td class="qty">' + input_qty + '</td>';
                        hmtl += '<td class="text-right"></td>';
                        hmtl += '<td class="text-right text-danger"></td>';
                        hmtl += '</tr>';
                    })

                    frm.find('.tabledetail tbody').append(hmtl);
                }
            }
            //  ========= End add product   ========

        })

        //----------------------------function--------------------------//
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