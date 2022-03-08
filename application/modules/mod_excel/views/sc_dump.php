<script>
    $(function() {

        $(document).ready(function() {
            let page = $('input#import').val();
            $('#sel_dataupdate').val(page);
        })

        $(document).on('change', '#sel_dataupdate', function(event) {
            event.stopPropagation;

            ajaxDataUpdate();
        })

        function blockHTML(obj) {
            let result;

            result = '<div class="table-responsive">';
            result += '<table id="table" class="table ">';
            result += '<thead><tr>';
            result += '<th> </th>';
            result += '<th>Order</th>';
            result += '<th>อ้างอิง</th>';
            result += '<th>ลูกค้า</th>';
            result += '<th>ช่องทาง</th>';
            result += '<th>ขนส่ง</th>';
            result += '<th>ยอดขาย</th>';
            result += '<th>รวมสุทธิ</th>';
            result += '<th>ชำระเงิน</th>';
            result += '<th>สถานะ</th>';
            result += '<th>เนื้อหา</th>';
            result += '</tr></thead>';


            let detail = obj.resultdetail;

            if (obj.resulttable) {
                $.each(obj.resulttable, function(key, val) {

                    let trdetail = "";
                    let searchval = val.code;
                    let searchresult = detail.filter(function(res) {
                        return res.code == searchval
                    });

                    //	detail
                    $.each(searchresult, function(keyin, valin) {
                        let index = keyin + 1;

                        trdetail += '<tr class=\'detail bg-secondary\' data-id=\'' + key + '\'>';
                        trdetail += '<td></td>';
                        trdetail += '<td>' + index + '</td>';
                        trdetail += '<td colspan=\'2\'>' + valin.rp_name + '</td>';
                        trdetail += '<td><p>ราคาสินค้า</p> ' + valin.rp_price + '</td>';
                        trdetail += '<td><p>จำนวน</p> ' + valin.rtd_qty + '</td>';
                        trdetail += '<td colspan=\'5\'><p>รวมขาย</p> ' + valin.rtd_total + '</td>';

                        trdetail += '</tr>';
                    })


                    result += '<tr id="' + key + '" data-detail="' + trdetail + '">';
                    result += '<td class="delId text-danger" data-billid="' + val.id + '" > <i class="fas fa-trash-alt"></i> </td>';
                    result += '<td>' + val.code + '</td>';
                    result += '<td>' + val.ref + '</td>';
                    result += '<td>' + val.custname + '</td>';
                    result += '<td>' + val.receipt_name + '</td>';
                    result += '<td>' + val.shipping + '</td>';
                    result += '<td>' + val.total_price + '</td>';
                    result += '<td>' + val.net_total + '</td>';
                    result += '<td>' + val.billstatus + '</td>';
                    result += '<td>' + val.conflict + '</td>';
                    result += '<td>' + val.total + '</td>';
                    result += '</tr>';
                })
            }

            result += '</table>';
            result += '</div>';

            return new Promise((resolve, reject) => {

                // resolve({error_code:obj.error_code,txt:obj.txt})
                resolve({
                    error_code: '11',
                    respone: result,
                });

            })
        }

        function blocksuccess(respone) {
            $('.dataimport').html(respone)
        }

        //
        //	show record
        $(document).on('click', 'a[href="#dataupdate"]', function(event) {
            event.stopPropagation();

            if ($('#table').length) {
                return false;
            }

            ajaxDataUpdate();
        });

        function ajaxDataUpdate() {
            let dataimport = ".datatoday";
            let textloading = '<div class="spinner-border text-info"></div>';

            $.ajax({
                    method: "get",
                    beforeSend: function() {
                        $(dataimport).html(textloading);
                    },
                    data: {
                        method: $('#sel_dataupdate').val()
                    },
                    url: "get_datatoday",
                    success: function(result) {
                        let obj = jQuery.parseJSON(result);
                        let resultTable = obj.resulttable;
                        let resultDetail = obj.resultdetail;
                        // console.log(obj.resultall);
                        let divhtml;

                        createHtml(obj);
                        async function createHtml(obj) {
                            let result1 = await blockHTML(obj);
                            let respon = result1.respone;

                            if (respon) {
                                blocktodaysuccess(respon);
                            }
                        }
                    },

                    error: function(error) {
                        alert(error);
                    }
                })
                .fail(function(xhr, status, error) {
                    // error handling
                    // window.location.reload();
                });
        }

        function blocktodaysuccess(respone) {
            $('.datatoday').html(respone);
            optionDataTable();
        }

        function optionDataTable() {
            $('#table').DataTable({
                dom: "<'row'<'col-sm-6 btn-sm tool-detail'><'col-sm-6 form-control-sm'f>>" +
                    "<'row'<'col-sm-12 small'tr>>" +
                    "<'row'<'col-sm-4 small'i><'col-sm-4 d-flex justify-content-center small'l><'col-sm-4 small'p>>",
            });

            let totalamount = 1950;

            let toolDetail = "";
            let text = "<a id='deleteall' class='text-primary' style='cursor:pointer'>ลบข้อมูล " + $('#sel_dataupdate').val() + " วันนี้ทั้งหมด <i class='fas fa-trash-alt'></i></a>";
            

            toolDetail = text;

            $.post("get_sumTotalAmount",
			{
				//  paramiter
                method: $('#sel_dataupdate').val()
			})
			.done(function(data, status, error){ 
				let obj = jQuery.parseJSON(data);
                let textreport = "<a class='float-right'>ยอดรวม <span class='totalamount text-success text-bold'>" + formatMoney(obj.totalamount) + "</span> บาท</a>";
               
                toolDetail += textreport;
                $('.tool-detail').html(toolDetail);
			})
			.fail(function(xhr, status, error) {
				// error handling
				console.log(`err : ${error}`);
			});
        }

        $(document).on('click', 'tr td button', function(event) {
            event.stopPropagation();

            let trID = $(this).attr('data-id');
            let table = $('#table tr#' + trID);

            let htmlTr = table.attr('data-detail');
            let trdetail = 'tr.detail';

            let detailrow = $(trdetail + '[data-id=' + trID + ']');
            if (detailrow.length) {

                // trdetail.remove();
                detailrow.remove();
                table.removeClass('bg-navy');
            } else {
                table.after(htmlTr);
                table.addClass('bg-navy');
            }
        })

        $(document).on('click', 'a#deleteall', function(event) {
            // Swal.showLoading();
            const swaltheme = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            })

            swaltheme.fire({
                type: 'warning',
                title: 'ต้องการลบข้อมูล ' + $('#sel_dataupdate').val() + ' วันนี้ทั้งหมด',
                // timer: 2000,
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ไม่ทำ',
            }).then((result) => {
                //
                // 
                if (result.value) {
                    Swal.fire({
                        title: 'Wait ...',
                        allowOutsideClick: false,
                        async onOpen(r) {

                            let resp = await running();

                            if (resp) {
                                Swal.fire({
                                    type: resp.icon,
                                    title: resp.topic,
                                    // timer: 2000,
                                    showConfirmButton: true,
                                    text: resp.txt,
                                }).then((result) => {
                                    //
                                    $('#table').DataTable().destroy();
                                    $('#table tbody').children('tr').remove();
                                    optionDataTable();
                                })
                            } else {
                                alert('error');
                                window.location.reload();
                            }
                        },
                        onBeforeOpen() {
                            Swal.showLoading()
                        },
                        onAfterClose() {
                            // Swal.hideLoading()
                        }
                    })
                }
            })

        })

        $(document).on('click', 'td.delId', function(event) {

            let id = $(this).attr('data-billid');

            const swaltheme = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            })

            swaltheme.fire({
                type: 'warning',
                title: 'ต้องการลบข้อมูล 1 รายการ',
                // timer: 2000,
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ไม่ทำ',
            }).then((result) => {
                //
                // 
                if (result.value) {
                    Swal.fire({
                        title: 'Wait ...',
                        allowOutsideClick: false,
                        async onOpen(r) {

                            let resp = await runningID(id);

                            if (resp) {
                                Swal.fire({
                                    type: resp.icon,
                                    title: resp.topic,
                                    // timer: 2000,
                                    showConfirmButton: true,
                                    text: resp.txt,
                                }).then((result) => {
                                    //
                                    $('#table').DataTable().destroy();
                                    $('#table tr td[data-billid=' + id + ']').parent('tr').remove();
                                    optionDataTable();

                                })
                            } else {
                                alert('error');
                                window.location.reload();
                            }
                        },
                        onBeforeOpen() {
                            Swal.showLoading()
                        },
                        onAfterClose() {
                            // Swal.hideLoading()
                        }
                    })
                }
            })

        })

        function running() {
            return new Promise((resolve, reject) => {

                $.ajax({
                        method: 'post',
                        url: 'cancelBillDump',
                        data: {
                            'method': $('#sel_dataupdate').val(),
                            'type': 'all'
                        }
                    })
                    // .then(res => res.json())
                    .then((data) => {

                        var obj = jQuery.parseJSON(data);

                        let obj_icon, obj_topic;
                        if (obj.error_code == 1) {
                            obj_icon = 'warning';
                            obj_topic = 'ไม่มีการทำรายการ';
                        }
                        if (obj.error_code == 0) {
                            obj_icon = 'success';
                            obj_topic = 'ทำรายการสำเร็จ';
                        }

                        resolve({
                            error_code: obj.error_code,
                            icon: obj_icon,
                            topic: obj_topic,
                            txt: obj.txt
                        })

                    })
                    .fail(error => {
                        console.log(`error ${error}`)
                    })

            })
        }

        function runningID(id) {
            return new Promise((resolve, reject) => {

                $.ajax({
                        method: 'post',
                        url: 'cancelBillDump',
                        data: {
                            'method': $('#sel_dataupdate').val(),
                            'type': 'id',
                            'id': id
                        }
                    })
                    // .then(res => res.json())
                    .then((data) => {

                        var obj = jQuery.parseJSON(data);

                        let obj_icon, obj_topic;
                        if (obj.error_code == 1) {
                            obj_icon = 'warning';
                            obj_topic = 'ไม่มีการทำรายการ';
                        }
                        if (obj.error_code == 0) {
                            obj_icon = 'success';
                            obj_topic = 'ทำรายการสำเร็จ';
                        }

                        resolve({
                            error_code: obj.error_code,
                            icon: obj_icon,
                            topic: obj_topic,
                            txt: obj.txt
                        })

                    })
                    .fail(error => {
                        console.log(`error ${error}`)
                    })

            })
        }

        $(document).on('page.dt', '#table', function() {
            $('#table tr').removeClass('bg-navy');
        });

        //	format number and float (.00) return string!! 
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

    })
</script>