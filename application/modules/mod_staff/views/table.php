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
                            <h1>รายชื่อผู้ใช้งาน</h1>
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

            <section class="container-fluid">
                <!-- Left content -->
                <style>
                    .d-flex {
                        overflow: hidden;
                    }

                    .flex-content-left {
                        flex: 50;
                    }

                    .flex-content-right {
                        flex: 50;
                    }

                    .scroll-col {
                        overflow: auto;
                        /*height: 100%;  used h-100 class instead*/
                    }

                    .table {
                        cursor: pointer;
                    }
                </style>

                <div class="card" style="height:calc(100vh - 200px)">
                    <div class="card-body d-flex flex-md-row flex-column mh-100">
                        <!-- BEGIN Left content -->
                        <div class="scroll-col flex-content-left pr-4">

                            <div class="form d-flex justify-content-between justify-content-sm-end ">
                                <div class="form-group ml-1">
                                    <button class="btn btn-success btn-sm" id="btn_adduser">+ เพิ่มผู้ใช้</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="dataTableUser" class="table display w-100">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>ชื่อ-นามสกุล</th>
                                            <th>username</th>
                                            <th>สาขา</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <!-- END Left content -->
                        <!-- BEGIN Right content -->
                        <div class="bg-light scroll-col flex-content-right p-4">

                            <div class="card">
                                <div id="sec_manage" class="card-body d-none">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#datainformation">ข้อมูล</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#permit">สิทธิ์</a>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane container active" id="datainformation">

                                            <form id="frmInfo" class="form-horizontal">

                                                <input type="hidden" id="userid" name="userid" value="">

                                                <div class="form-group row pt-4">
                                                    <label for="name" class="col-sm-2 col-form-label small">ชื่อ(US)</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="John">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="lastname" class="col-sm-2 col-form-label">สกุล(US)</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Doh">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="name" class="col-sm-2 col-form-label">ชื่อ(TH)</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="name_th" name="name_th" placeholder="จอห์น">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="lastname" class="col-sm-2 col-form-label">สกุล(TH)</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="lastname_th" name="lastname_th" placeholder="โด">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="username" name="username" placeholder="Doh">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="password" name="password" placeholder="password">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="franshine_id" class="col-sm-2 col-form-label">สาขา</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="franshine_text" placeholder="สาขา">

                                                        <select name="franshine_id" id="franshine_id" class="form-control d-none">
                                                            <option value="">ไม่ระบุ</option>
                                                            <?php
                                                            $sql = $this->db->from('retail_methodorder')
                                                                ->where('status', 1);
                                                            $q = $sql->get();
                                                            $num = $q->num_rows();
                                                            if ($num) {
                                                                foreach ($q->result() as $row) {
                                                                    echo "<option value='" . $row->ID . "'>" . $row->TOPIC . "</option>";
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="card-footer">
                                                <button type="button" class="btn btn-warning" id="edit">แก้ไข</button>
                                                <button type="button" class="btn btn-info d-none" id="submit">บันทึก</button>
                                                <button type="button" class="btn btn-default float-right" id="back">ย้อนกลับ</button>
                                            </div>

                                        </div>
                                        <div class="tab-pane container fade" id="permit">B</div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- END Right content -->
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
        var protocol = window.location.protocol;
        var hostname = window.location.hostname;
        var pathname = window.location.pathname;
        var pathname_name = pathname.split("/");
        var pathname_new;

        //  set time loader
        let timeLoad = 400;

        //  set paramiter form
        let arrayInput = [{
                input: '#userid',
                arraykey: 'id'
            },
            {
                input: '#name',
                arraykey: 'name'
            },
            {
                input: '#lastname',
                arraykey: 'lastname'
            },
            {
                input: '#name_th',
                arraykey: 'name_th'
            },
            {
                input: '#lastname_th',
                arraykey: 'lastname_th'
            },
            {
                input: '#username',
                arraykey: 'username'
            },
            {
                input: '#password',
                arraykey: 'password'
            },
            {
                input: '#franshine_text',
                arraykey: 'franshine_text'
            },
            {
                input: '#franshine_id',
                arraykey: 'franshine_id'
            }
        ];

        if (hostname == "localhost") {
            hostname = hostname + "/" + pathname_name[1];
        } else {
            hostname = hostname;
        }

        $(function() {
            tableList();

            function tableList() {

                var dataTable = $('#dataTableUser').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "scrollY": $('.flex-content-left').height() - 200 + 'px',
                    "scrollCollapse": false,

                    dom: "<'row'<'col-sm-4 btn-sm'f><'col-sm-4 btn-sm toolbar text-center'><'col-sm-4'>>" +
                        "<'row'<'col-sm-12 small'tr>>" +
                        "<'row'<'col-sm-4 small'i><'col-sm-4 text-center d-flex justify-content-center smalll'><'col-sm-4 small 'p>>",

                    "ajax": {
                        url: "./fetch_list",
                        type: "POST",
                        data: {
                            /* status: status,
                            keyword: keyword,
                            selectproductmain: selectproductmain */
                        }
                    },
                    "createdRow": function(row, data, dataIndex) {

                        $(row).attr('data-id', data['id']);
                        $(row).attr('data-info', data['data-info']);

                        if (data['franshine'] != null) {
                            // console.log(hostname);

                            $('td', row).eq(0).prepend('<li class="list-inline-item"><img alt="Avatar" class="rounded-circle" style="width:25px" src="//' + hostname + '/asset/backendmake/assets/dist/img/avatar5.png"></li> ');
                        }
                    },
                    "columnDefs": [{
                        "targets": 0,
                        "orderable": false,
                    }],
                    "columns": [{
                            "data": "no"
                        },
                        {
                            "data": "name"
                        },
                        {
                            "data": "account"
                        },
                        {
                            "data": "franshine"
                        },
                    ],
                });
            }

            async function async_formViews(userid) {
                const result1 = await new Promise((resolve, reject) => formViews(dataViews(userid)))
            }

            $(document).on('click', '#btn_adduser', function() {
                console.log($(this));
            })

            //  view information user
            $(document).on('click', '#dataTableUser > tbody > tr', function() {
                loadData();

                let userid = $(this).attr('data-id');

                //  show user data
                setTimeout(() => {
                    async_formViews(userid);
                }, timeLoad);
            })

            //  edit form
            $(document).on('click', '#edit', function() {
                let userid = $(this).attr('data-id');

                //  edit user data
                formEdit();
            })

            //  submit form information
            $(document).on('click', '#submit', function() {
                let form = $('form').serializeArray();
                console.log(form);

                fetch(
                        '../../api/staff', {
                            headers: {
                                'API-KEY': 'XOGgx6vzY2yIj7li4tS1PMrqckh8dmE5FVQRZGeL',
                            },
                            method: 'POST',
                            body: form
                        })
                    .then(res => res.json())
                    .then(resp => {
                        console.log('res :' + resp.data);
                    })
                    .catch(function(error) {
                        console.log(`Error : $(error)`);
                    })

                //  open button submit
                toolActionView();
            })

            //  back to view information user
            $(document).on('click', '#back', function() {
                let userid = $('#userid').val();

                //  show user data
                async_formViews(userid);
            })

            /* ======================================================== */
            /*  FUNCTION 
            /* ======================================================== */

            function formViews(dataarray) {
                let frm = $('form');

                let elementId = "";
                arrayInput.forEach(function(key, index) {
                    elementId = frm.find(arrayInput[index].input);

                    elementId.val(dataarray[arrayInput[index].arraykey]);
                    elementId.removeClass('form-control').addClass('form-control-plaintext').attr('readonly', 'readonly');
                })

                //  open button submit
                toolActionView();

                //
                loadHide();
            }

            function formEdit() {
                let frm = $('form');

                let elementId = "";
                arrayInput.forEach(function(key, index) {
                    elementId = frm.find(arrayInput[index].input);

                    elementId.addClass('form-control').removeClass('form-control-plaintext').removeAttr('readonly');
                })

                //  open button submit
                toolActionEdit();
            }

            function dataViews(userid) {
                let token = $('tr[data-id=' + userid + ']').attr('data-info');
                let userdata = parseJwt(token);
                console.log(userdata);

                return userdata;
            }

            function toolActionView() {
                $('button#submit').addClass('d-none');
                $('button#edit').removeClass('d-none');
            }

            function toolActionEdit() {
                $('button#submit').removeClass('d-none');
                $('button#edit').addClass('d-none');
            }

            let secterManage = '#sec_manage';

            function loadData() {
                let elementId = secterManage;
                let loaderBlock = $('div').find('.loader');
                if (loaderBlock.length) {
                    return false;
                }

                let loader = `
                <div class="loader d-flex justify-content-center w-100 align-items-center h-100" style="position:absolute" role="status">
                    <div class="spinner-border" style="width:3rem;height: 3rem;" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                `;

                if (elementId) {
                    //  remove for show on first load
                    $(secterManage).removeClass('d-none');

                    $(elementId).addClass('invisible');
                    $(elementId).parent().prepend(loader);
                }
            }

            function loadHide() {
                let elementId = secterManage;
                $('.loader').remove();
                $(elementId).removeClass('invisible');
            }

        })
    </script>
</body>

</html>