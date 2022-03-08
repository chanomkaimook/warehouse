<script>
    //	setting
    let search_order = $('[type=search]');
    let frm = $('form#frm');
    let search_result = $('.search_result');

    $(function() {

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

        //	submit form
        $(document).on('click', '#submitform', function() {

            add_Creditnote();
        })

        //	quantity
        $(document).on('keyup', '.input-qty', function() {
            calculateProduct(this, $(this).val());
        })

        //	cal price
        $(document).on('keyup', '.billnumber', function() {
            calculateCreditBill();
        })

        //	cal price
        $(document).on('click', '.btn-del', function() {
            let tr_id = $(this).parents('tr').attr('data-row');
            $('tr[data-row=' + tr_id + ']').remove();

            //	bill amount
            calculateCreditBill();
        })
    })

    function formDataInsert(bill) {
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

    function add_Creditnote() {
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
                
                $('.loading').remove();
                toolbtn.removeClass('d-none');
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
                            $("[data-dismiss=modal]").trigger({
                                type: "click"
                            });

                            //  refresh
                            tableGetData();

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

    //----------------------------function--------------------------//
    function tableGetData() {
        let table = $('#ex1');
        table.DataTable().ajax.reload();

        // getCountCreditBill();
        get_countMenu();

    }

    function get_countMenu() {
        let url = "get_countMenu";
        fetch(url)
        .then(res => res.json())
        .then(resp => {

            if(resp['data']){
                $.each(resp['data'], function(key, value) {
                    let spanname = '.badge_'+key;
                    if($(spanname).length && value){
                        $(spanname).removeClass('d-none');
                        $(spanname).text(value);
                    }else{
                        $(spanname).addClass('d-none');
                    }
                });
            }
        })
        .catch((error) =>{
            console.log(`error : ${error}`);
        })
    }

    /* function getCountCreditBill() {
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
    } */

    function calculateProduct(ele, number) {
        let qty = number;
        let price = $(ele).attr('data-price');

        let result = qty * price;
        let html_price = $(ele).parents('tr').children('td.totalprice');

        //	return
        html_price.text(formatMoney(result));

        //	total amount
        calculateCreditBill();
    }

    function calculateCreditBill() {
        let productamount = 0;
        let resultamount = 0;
        let loop = document.querySelectorAll('.billnumber');
        let productprice = document.querySelectorAll('.input-qty');

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
        document.getElementById('bill_price').value = formatMoney(productamount);

        //	bill amount
        loop.forEach(function(key, index) {
            if (parseFloat(key.value)) {
                resultamount += parseFloat(key.value);
                // console.log(resultamount+" + "+parseInt(key.value));
            }
        })
        resultamount += productamount;
        document.getElementById('totalamount').value = formatMoney(resultamount);

        //  check force zero
        let checkzero = $('#totalzero');
        if (checkzero.prop('checked')) {
            document.getElementById('totalamount').value = 0;
        }
    }

    $(document).ready(function() {
        bsCustomFileInput.init();
    });
</script>