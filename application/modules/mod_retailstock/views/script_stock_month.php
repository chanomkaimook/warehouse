<script>
    const queryString = decodeURIComponent(window.location.search);
    const params = new URLSearchParams(queryString);
    let getDate = params.get("m");
    var dataTable;
    var fixedColumns;
    $(function() {
        //  form table
        let formTable;
        formTable = `<table id="exam" class="table table-bordered table-centered mb-0 table-sm" >
        <thead>
        <tr class="tr-1">
            <th rowspan=2 >#</th>

            <th rowspan=2 >ID</th>
            <th rowspan=2 >สินค้า</th>
            <th rowspan=2 >คงคลัง</th>
            <th rowspan=2 class="text-info">รวมขาย</th>
            <th rowspan=2 class="text-success">รวมเข้า</th>
            <th rowspan=2 class="text-secondary">รวมเบิก</th>
            <th rowspan=2 >รวมเหลือ</th>
        </tr>
        <tr class="tr-2">

        </tr>
        </thead>
        <tbody>
            <td colspan=8 class="text-center" >Loading...</td>		
        </tbody>
        </table>
        `;
        $('.tables').html(formTable);

        //
        //	run process
        //  1. create table infomation
        //  2. if create success going to create datatable
        //

        async function creatDataTable() {
            event_loading();

            let result1 = await dataStockMonth();
            if (result1.error_code == 0) {
                dataTable = $('#exam').DataTable({
                    scrollY: "500px",
                    scrollX: true,
                    scrollCollapse: true,

                    // fixedColumns:   true,
                    /* fixedColumns: {
                        left: 8,
                        right: 0
                    }, */
                    paging: false,
                    autoWidth: false,
                    dom: "<'row'<'col-sm-3 btn-sm'i><'col-sm-6 btn-sm toolbar text-center d-flex justify-content-center align-items-center'><'col-sm-3'f>>" +
                        "<'row'<'col-sm-12 small'tr>>" +
                        "<'row'<'col-sm-4 small'i><'col-sm-4 text-center d-flex justify-content-center smalll'><'col-sm-4 small'p>>",
                    "rowCallback": function(row, data, index) {
                        // console.log(data);
                        if ($('td', row).eq(7).text() < 0) {
                            $('td', row).eq(7).addClass('text-danger');
                        }
                    },
                    "initComplete": function(settings, json) {
                        event_ready();
                        let m = getDate.split('-');
                        let monthnumber = parseInt(m[1] - 1);
                        let monthNames = [
                            "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน",
                            "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม.",
                            "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                        ];
                        let year = parseInt(m[0]) + 543;

                        let databegin = params.get("begin");
                        let datalength = params.get("length");
                        let selected_begin,selected_length;
                        const calendar_length = $('#calendar_length').val();

                        if (databegin && datalength) {
                            selected_begin = databegin;
                            selected_length = datalength;
                        } else {
                            selected_begin = 1;
                            selected_length = calendar_length;
                        }

                        //  create select
                        let htmlOption_begin;
                        let htmlOption_length;
                        let htmlOption_begin_select;
                        let htmlOption_length_select;
                        if(calendar_length){
                            for(var i=1;i<=calendar_length;i++){
                                (selected_begin == i ? htmlOption_begin_select = 'selected' : htmlOption_begin_select='');
                                htmlOption_begin += '<option value="'+i+'" '+htmlOption_begin_select+' >'+i+'</option>';

                                (selected_length == i ? htmlOption_length_select = 'selected' : htmlOption_length_select='');
                                htmlOption_length += '<option value="'+i+'" '+htmlOption_length_select+' >'+i+'</option>';
                            }
                        }

                        let select_begin = `<div class="form-group row">
                        <label class="col-sm-2 col-form-label">ตั้งแต่</label>
                        <div class="col-sm-10">
                            <select id="select_begin" class="custom-select custom-select-sm select_date">
                            ${htmlOption_begin}
                            </select>
                        </div>
                        </div>`;

                        let select_length = `<div class="form-group row">
                        <label class="col-sm-2 col-form-label">ถึง</label>
                        <div class="col-sm-10">
                            <select id="select_length" class="custom-select custom-select-sm select_date">
                            ${htmlOption_length}
                            </select>
                        </div>
                        </div>`;

                        let textHead = '<div class="flex-fill"><b>ข้อมูลเดือน ' + monthNames[monthnumber] + ' ' + year + '</b></div>';
                        textHead += '<div class="form-inline"> ' + select_begin + ' ' + select_length + ' </div>';

                        $('.tables .toolbar').html(textHead);
                    }
                });

                fixedColumns = new $.fn.dataTable.FixedColumns(dataTable, {
                    // leftColumns: 5
                    leftColumns: 8
                });

            }
        }
        creatDataTable();

        function event_loading() {
            document.getElementById('field-bill').setAttribute('disabled', 'disabled');
            document.getElementById('field-receive').setAttribute('disabled', 'disabled');
            document.getElementById('field-issue').setAttribute('disabled', 'disabled');

            //  button
            document.getElementById('listmonth').setAttribute('disabled', 'disabled');
            document.getElementById('select_listmonth').setAttribute('disabled', 'disabled');
        }

        function event_ready() {
            document.getElementById('field-bill').removeAttribute('disabled', 'disabled');
            document.getElementById('field-receive').removeAttribute('disabled', 'disabled');
            document.getElementById('field-issue').removeAttribute('disabled', 'disabled');

            //  button
            document.getElementById('listmonth').removeAttribute('disabled', 'disabled');
            document.getElementById('select_listmonth').removeAttribute('disabled', 'disabled');
        }

        $(document).on('click', '.check-field-column', function() {
            if (!$.fn.DataTable.isDataTable('#exam')) {
                console.log('error');
                return false;
            }

            //  CDN 4.0.1 fixedcolummns not support code when event dataTable destroy()
            //  ใช้ลบทั้งบล็อค สร้าง html ใหม่
            $('.tables').html(formTable);
            creatDataTable();
        })

        $(document).on('change', '.select_date', function() {
            let urlParams = new URLSearchParams(window.location.search);

            let date_begin = document.getElementById('select_begin').value;
            let date_length = document.getElementById('select_length').value;
            urlParams.set('begin', date_begin);
            urlParams.set('length', date_length);

            if(date_begin && date_length){
                window.location.search = urlParams;
            }
        })

        function dataStockMonth() {
            let getDate = params.get("m");
            (getDate ? datesearch = getDate : datesearch = $('input#date').val());

            const formData = new FormData();
            formData.append('date', datesearch);
            formData.append('date_cut', $('#date_cut').val());
            formData.append('dataField[bill]', document.getElementById("field-bill").checked);
            formData.append('dataField[receive]', document.getElementById("field-receive").checked);
            formData.append('dataField[issue]', document.getElementById("field-issue").checked);

            formData.append('date_begin', (params.get("begin") ? params.get("begin") : null));
            formData.append('date_length', (params.get("length") ? params.get("length") : null));

            let url = 'fetch_productMonth';
            let method = {
                method: 'POST',
                body: formData
            }
            return new Promise((resolve, reject) => {
                fetch(url, method)
                    .then(res => res.json())
                    .then((resp) => {
                        // console.log(resp);
                        let tbody_row = "";
                        let thead_1 = $('.table thead .tr-1');
                        let thead_2 = $('.table thead .tr-2');
                        let tbody = $('.table tbody');

                        let thead_th = '<th class="text-info">ขาย</th>';
                        thead_th += '<th class="text-success">เข้า</th>';
                        thead_th += '<th class="text-secondary">เบิก</th>';
                        thead_th += '<th>เหลือ</th>';

                        tbody.html('');

                        let urlParams = new URLSearchParams(window.location.search);
                        let today = (urlParams.get('begin') ? urlParams.get('begin') : 1);
                        var x = 1; //  paramiter for break column 

                        if (resp) {
                            $.each(resp, function(key, value) {
                                // console.log(key+"--"+value.data.length);
                                tbody_row = '<td></td>';
                                tbody_row += '<td>' + value.id + '</td>';
                                tbody_row += '<td>' + value.name + '</td>';
                                tbody_row += '<td>' + value.stock + '</td>';

                                tbody_row += '<td class="text-info">' + value.bill_net + '</td>';
                                tbody_row += '<td class="text-success">' + value.receive_net + '</td>';
                                tbody_row += '<td class="text-secondary">' + value.issue_net + '</td>';
                                tbody_row += '<td>' + value.total_net + '</td>';

                                if (value.data) {
                                    //  add column 
                                    if (thead_1.length) {

                                        for (x; x <= value.data.length; x++) {
                                            thead_1.append('<th colspan=4 class="text-center">' + today + '</th>');
                                            thead_2.append(thead_th);

                                            //  remove class
                                            if (x == value.data.length) {
                                                thead_1.removeClass('tr-1');
                                            }

                                            today++;
                                        }
                                    }

                                    $.each(value.data, function(keyin, valuein) {

                                        // console.log(keyin+"--"+valuein.date_today);
                                        tbody_row += '<td class="text-info" >' + (valuein.bill ? valuein.bill : "") + '</td>';
                                        tbody_row += '<td class="text-success" >' + (valuein.receive ? valuein.receive : "") + '</td>';
                                        tbody_row += '<td class="text-secondary" >' + (valuein.issue ? valuein.issue : "") + '</td>';
                                        tbody_row += '<td>' + (valuein.total ? valuein.total : "") + '</td>';

                                        // keyin_index++;
                                        // thead_1.append('<th colspan=4 class="text-center">'+keyin_index+'</th>');
                                        // thead_2.append(thead_th);
                                    })
                                }
                                tbody.append('<tr>' + tbody_row + '</tr>');

                            })
                        }

                        resolve({
                            error_code: 0
                        })

                    })
            })
        }
    })
</script>