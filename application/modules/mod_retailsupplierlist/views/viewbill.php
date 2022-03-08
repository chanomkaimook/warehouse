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
                            <h1><?php echo "ใบรับเข้า"; ?></h1>
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
                                    <?php
                                    if (chkPermissPage('report_supplierlist') && $query->COMPLETE == 2) {
                                    ?>
                                        <!-- <div class="text-right">
                                            <a href="<?php echo site_url('mod_retailreceive/ctl_receive/doc_receive') . "?id=" . $this->input->get('id'); ?>" target=_blank type="button" id="download" class="btn btn-sm btn-warning ">download เอกสาร</a>
                                        </div> -->
                                    <?php } ?>
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
            function get_orderBill(bill_id) {
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

                        // console.log(bill);
                        formDataInsert(bill);
                    })
                    .catch(function(err) {
                        console.log(`error : ${err}`)
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


            function formDataInsert(bill) {
                let usercreate =  bill.sp_user_starts;
                let datecreate =  toThaiDateTimeString(new Date(bill.sp_date_starts), 'datetime');

                let userupdate;
                let dateupdate;
                if(bill.sp_date_update){
                    dateupdate = toThaiDateTimeString(new Date(bill.sp_date_update), 'datetime'); 
                }

                //  set style div
                frm.find('.bill_supplier').text(bill.sp_name);
                frm.find('.bill_staffcreate').text(bill.sp_user_starts);
                frm.find('.bill_datecreate').text(datecreate);
                frm.find('.bill_staffedit').text(bill.sp_user_update);
                frm.find('.bill_dateedit').text(dateupdate);

                frm.removeClass('d-none');
            }

            //----------------------------function--------------------------//
            function displayLoading(elename) {
                let loading = '<div class="spinner-border text-info loading"></div>';

                $(elename).append(loading);
            }

            function hideLoading() {
                $('.loading').addClass('d-none');
                // frm.removeClass('d-none');
            }

        })
    </script>
    <script>
        $(function() {
            const queryString = decodeURIComponent(window.location.search);
            const params = new URLSearchParams(queryString);
            let billID = params.get("id");
            //  loader
            let loading = '<div class="col-sm-12 text-center loading"><div class="spinner-border text-info"></div></div>';
            let rowtoolbtn = $('.row-form-tool-btn');
            let toolbtn = $('.form-tool-btn');

            $(document).on('click', '#btn-cancel', function() {
                Swal.fire({
                    type: 'warning',
                    title: 'ต้องการยกเลิก',
                    text: 'ยืนยันเพื่อยกเลิกรายการ',
                    showConfirmButton: true,
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {

                        rowtoolbtn.append(loading);
                        toolbtn.addClass('d-none');
                        var data = new FormData();
                        // data.append("bill_id", 16286);
                        data.append("bill_id", billID);

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