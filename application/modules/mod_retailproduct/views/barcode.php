<!DOCTYPE html>
<html lang="en">
   	<head> 
        <?php include("structer/backend/head.php"); ?>
        <link rel="stylesheet" href="<?php echo $base_bn;?>frontend/bootstrap-select/css/bootstrap-select.css">
        <!--    DataTable    -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('asset/plugin/datatablebutton').'/datatables.min.css';?>"/>

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
						<h1><?php echo $submenu; ?></h1>
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
				<div class="container-fluid">
					<div class="row">
						  
						<section class="col-lg-12 connectedSortable">
 							<div class="card">
								<div class="card-header">
									<h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i> Table <?php echo $submenu; ?> </h3>
 								</div> 
								<div class="card-body">
                                    <!-- 
                                        class tool-table for calculate element height table
                                    -->
                                    <!-- <div class="tool-table form">
                                        <form id="form" enctype="multipart/form-data" accept-charset="utf-8"  method="post">
                                            <div class="form-row d-flex">

                                                <div class="form-group col-md">
                                                    <span for="productname" class="col-form-label"> ชื่อสินค้า</span>
                                                    <select name="productname" id="productname" class="form-control form-control-sm select2" style="width:100%">
                                                        <option value="" selected="selected" >เลือกสินค้า</option>
                                                     
                                                    </select>
                                                </div>

                                                <div class="form-group col-md">
                                                    <span for="lot" class="col-form-label"> Lot id </span>
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" id="productlot" name="productlot" class="form-control form-control-sm" >
                                                        <div class="input-group-append">
                                                            <div id="manual-lotid" class="input-group-text"><i class="fas fa-pen" ></i></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md">
                                                    <span for="total" class="col-form-label"> จำนวน </span>
                                                    <input type="text" id="productqty" name="productqty" class="form-control form-control-sm" >
                                                </div>
                                                
                                            </div>
                                        </form>
                                    </div> -->

                                    <div class="table-responsive"> 
                                        <table id="ex1" class="table table-bordered">  
                                            <thead>  
                                                <tr>  
                                                    <th width="5%">#</th>  
                                                    <th width="70px">รหัส mac</th> 
                                                    <th width="70px">รหัส Online</th> 
                                                    <th>รายการ</th>  
                                                    <th>ประเภท</th>  
                                                    <th>Barcode MAC</th>  
                                                    <th>Barcode Online</th>  
                                                    <th width="40px">Action</th>  
                                                </tr>  
                                            </thead>  
                                        </table>  
                                    </div>
                                    
                                    <!-- modal Peview -->
                                    <div id="modalaction" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" >
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content" >
                                                <div class="titel text-left col-md-12"> <i class="fa fa-file-text-o" aria-hidden="true"></i> Product</div>
                                                
                                                <form action="" id="frm" >
                                                    <input type="hidden" id="productid" name="productid" class="form-control form-control-sm" value="" >
                                                    
                                                    <div class="form-group">
                                                        <label for="">รหัส mac :</label>
                                                        <input type="number" id="codemac" name="codemac" class="form-control form-control-sm" value="" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="">รหัส online :</label>
                                                        <input type="number" id="code" name="code" class="form-control form-control-sm" value="" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="">ประเภท :</label>
                                                        <input type="text" id="catalog" name="catalog" class="form-control form-control-sm" value="" disabled >
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="">ชื่อสินค้า :</label>
                                                        <input type="text" id="productname" name="productname" class="form-control form-control-sm" value="" disabled >
                                                    </div>
                                                    <div class="text-right">
                                                        <button type="button" id="btn-submit" class="btn btn-primary" >
                                                            บันทึก
                                                        </button>
                                                        <button type="button" id="btn-cancel" class="btn btn-default">
                                                            ยกเลิก
                                                        </button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
 
								</div> 
							</div>
						</section>
 
					</div>
				</div> 
			</section>
		 
		</div>
            <?php include("structer/backend/footer.php"); ?>
            <?php include("structer/backend/script.php"); ?>
            <?php include("structer/backend/script_tablelayout.php"); ?>
            
            <script src="<?php echo $base_bn;?>frontend/bootstrap-select/js/bootstrap-select.js"></script>
            <script src="<?php echo $base_bn;?>plugins/sweetalert2/sweetalert2.min.js"></script>
            
            <!--    DataTable    -->
            <script type="text/javascript" src="<?php echo base_url('asset/plugin/datatablebutton').'/datatables.min.js';?>"></script>
            
        </div>
        <script>
            $(function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                dataList();
                var table = $('#ex1').DataTable();

                var inputproductid = $('input#productid');
                var inputcode = $('input#code');
                var inputcodemac = $('input#codemac');
                var inputcatalog = $('input#catalog');
                var inputproduct = $('input#productname');
                //----------------------------filter--------------------------//

                $(document).on('click', '#refresh_page', function(event) {
                    table.destroy();
                    dataList();  
                });

                $(document).on('click','#btn-cancel', function(event) {
                    $('#modalaction').modal('hide');
                });

                $(document).on('click','.btn-edit', function(event) {
                    event.stopPropagation();
                    
                    product_id = $(this).attr('data-id');
                    tr_id = $(this).parents('tr').index();
                    tr_id = tr_id+1;
                    var data = table.row( 'tr:nth-child('+tr_id+')' ).data();

                    //
                    //  data return resault array
                    //  0   = order table
                    //  1   = product code mac 
                    //  2   = product code online 
                    //  3   = product name
                    //  4   = product catalog
                    //  5   = image barcode max
                    //  6   = image barcode online
                    //
                    inputcodemac.val(data[1]);
                    inputcode.val(data[2]);
                    inputproductid.val($(this).attr('data-id'));
                    inputcatalog.val(data[4]);
                    inputproduct.val(data[3]);

                    $('#modalaction').modal().show();
                });

                //
                //  submit form 
                //
                $(document).on('click', '#btn-submit', function(e) {
                    event.stopPropagation();

                    $.post("updateProductlist",
                    {
                        id : inputproductid.val(),
                        code : inputcode.val(),
                        codemac : inputcodemac.val()
                    })
                    .done(function(data, status, error){ 
                        if(error.status == 200){	//	status complete
                            var obj = jQuery.parseJSON(data); 
                            if(obj.error_code !='0'){
                                swal("พบความผิดปกติ ระบบจะทำการ Reload", '<span class="spinner-border spinner-border-sm"></span>', "warning ");
                            }else{
                                //	async dtaa table
                                Swal.fire({
                                    type: 'success',
                                    title: 'อัพเดตข้อมูล',
							        text: obj.txt,
                                    timer: 1000,
                                }).then((result) => {
                                    $('#modalaction').modal('hide');
                                })

                                // $('#ex1').DataTable().ajax.reload();
                                $('#ex1').DataTable().ajax.reload(null, false);
                            }
                        }

                    })
                    .fail(function(xhr, status, error) {
                        // error handling
                        alert('พบความผิดปกติ ระบบจะทำการ Reload');
                        window.location.reload();
                    });
                });

                //
                //  cache key enter
                //
                $(document).on('keypress', 'body', function(event) {
                    if($('#modalaction').hasClass('show') && !$('.swal2-container').hasClass('swal2-shown')){
                        checkKey(event); 
                    }
                });

                function checkKey(e) {
                    if(e.keyCode == 13)
                    {
                        $('#btn-submit').trigger('click');
                        return false;
                    }
                    else
                    {
                        return true;
                    }
                }
                
                function dataList(status, keyword, selectproductmain) {
                    //  function on script_tablelayout
                    var moniter = tableLayout();

                    if(status == 0){ status = 'off'; }

                    var dataTable = $('#ex1').DataTable({  
                        "processing":false,  
                        "serverSide":false,           

                        "order":[],  
                        "ajax":{  
                                url:"<?php echo base_url() . 'mod_retailproduct/ctl_retailbarcode/fetch_product'; ?>",  
                                type:"POST",
                                data:{
                                    status: status, keyword: keyword, selectproductmain: selectproductmain
                                },
                                error: function (xhr, error, code)
                                {
                                    //  xhr return array status async
                                    if(xhr.status != 200){
                                        alert('พบข้อผิดพลาด กรุณาแจ้งเจ้าหน้าที่');
                                    }
                                } 
                        },
                        dom:
                            "<'row'<'col-sm-6 btn-sm'B><'col-sm-6'f>>" +
                            "<'row'<'col-sm-12 small'tr>>" +
                            "<'row'<'col-sm-4 small'i><'col-sm-4 text-center d-flex justify-content-center small'l><'col-sm-4 small'p>>",
                        buttons: [
                            {
                                extend: 'collection',
                                text: '<i class="far fa-eye"></i>',
                                titleAttr: 'view',
						        tag: 'span',
                                buttons: [ 'columnsToggle', 'colvisRestore' ],
                                fade: true
                            },
                            {
                                extend: 'collection',
                                text: '<i class="fas fa-file-download"></i>',
                                titleAttr: 'export',
						        tag: 'span',
                                buttons: [ 'excel', 'pdf', 'copy' ],
                                fade: true
                            },
                            {
                                extend: 'print',
                                text: '<i class="fas fa-print"></i>',
                                titleAttr: 'print'
                            },
                            {   
                                text: '<i class="fas fa-redo-alt"></i>',
                                className :'',
                                titleAttr: 'reload',
                                action: function ( e, dt, node, config ) {
                                    dt.ajax.reload();
                                }
                            }
                        ],
                        "columnDefs":[  
                                {  
                                    // "targets":0,  
                                    "orderable":false,  
                                },  
                        ],
                        "initComplete": function( settings, json ) {
                            // console.log(json);
        
                        },
						
                        "scrollY": moniter +'px',  
                        "scrollCollapse": false,
                        rowReorder: true
                    }); 
                }
        
            })
        </script>
        
	</body>
</html>
 