<script>
    //	setting
    let frm = $('form#frm');

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

        function clearImage() {
            $("#imagedledetail").text("Choose file");
            $("#image_file").val(null);

            $('.thumbnail-image').html('');
        }

        //	submit form
        $(document).on('click', '#submitform', function() {

            add_Bill();
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

    function add_Bill() {
        //  loader
        let loading = '<div class="col-sm-12 text-center loading"><div class="spinner-border text-info"></div></div>';
        let rowtoolbtn = $('.row-form-tool-btn');
        let toolbtn = $('.form-tool-btn');

        rowtoolbtn.append(loading);
        toolbtn.addClass('d-none');

        let loop = document.querySelectorAll('[data-loop]');
        let product = document.querySelectorAll('.bill_sup_product tbody tr');
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
            pd_price = 0;
            pd_totalprice = 0;

            if (pd_qty <= 0 || pd_qty == "") {
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

        data.append('remark', document.getElementById('remark').value);
        
        if (error == 0) {
            let url = 'add_Bill';
            let options = {
                method: 'POST',
                body: data
            };
            
            fetch(url, options)
                .then(res => res.json())
                .then(resp => {

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

    //----------------------------function--------------------------//
    function tableGetData() {
        let table = $('#ex1');
        table.DataTable().ajax.reload();

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

    }

    $(document).ready(function() {
        bsCustomFileInput.init();
    });
</script>