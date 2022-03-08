
<!DOCTYPE html>
<html lang="en">
   	<head> 
      <?php include("structer/backend/head.php"); ?>
      <link rel="stylesheet" href="<?php echo $base_bn;?>frontend/bootstrap-select/css/bootstrap-select.css">
      <link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/wickedcss.min.css"> 
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
                .btn-app4 {
                    border-radius: 3px;
                    background-color: #f8f9fa;
                    border: 1px solid #ddd;
                    color: #6c757d;
                    font-size: 12px;
                    height: 100%;
                    min-width: 80px;
                    padding: 15px 5px;
                    position: relative;
                    text-align: center;
                }
                .modal-footer {
                    display: -ms-flexbox;
                    display: flex;
                    -ms-flex-align: center;
                    align-items: center;
                    -ms-flex-pack: end;
                    justify-content: flex-end;
                    padding: 1rem 0 0;
                    border-top: 1px solid #e9ecef;
                    border-bottom-right-radius: .3rem;
                    border-bottom-left-radius: .3rem;
                }
                .swal2-header {
                    padding: 0;
                    border-bottom: 1px solid #FFF;
                }
                .is-warning {
                    border: 1px solid #ff5434;
                }
                .div-bottom {border: 1px solid #333; padding: 1rem;}
                
                #swal2-title{
                    font-weight: 300;
                    padding: 1.5rem 0.5rem;
                    font-size: 1.5rem;
                    color: #333;
                    text-align: center;
                }
                
                .is-invalid {
                    border: 1px solid #ff5434;
                }
                .is-required{
                    margin-left: 10px;
                    font-size: 0.7rem;
                    color: #f00;
                    
                }
                .text-ImgMultiple {
                    font-size: 0.7rem;
                    font-weight: 100;
                    color: #F44336;
                }
                .img-pay001 { width: 20%; }
                .displays-Col {display: table-column;}
                @media screen and (max-width: 991px){
                    .img-pay001 { width: 100%; }
                }
        </style>
    </head>
   	<body class="hold-transition sidebar-mini">

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
				</div><!-- /.container-fluid -->
			</section>
		  
			<section class="content">
				<div class="container-fluid">
					<div class="row">
						  
						<section class="col-lg-12 connectedSortable">
							<!-- Custom tabs (Charts with tabs)-->
							<div class="card">
								<div class="card-header">
									<h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i>   </h3>
 								</div> 
								<div class="card-body">
                                    <form id="demo2" name="demo2" class="demo"  enctype="multipart/form-data" accept-charset="utf-8"  method="post">
                                            <input type="hidden" id="bill_update" name="bill_update" value="Y">
                                            <input type="hidden" id="bill_id" name="bill_id" value="<?php echo $this->input->get('id'); ?>">
                                            <input type="hidden" id="TBLtotalprice" name="TBLtotalprice" value="<?php echo $Query_billdetil['TOTALPRICE_LANG']; ?>">
                                            <input type="hidden" id="StatusComplete" name="StatusComplete" value="<?php echo $Query_billdetil['STATUSCOMPLETE']; ?>">
                                            <input type="hidden" id="BillStatus" name="BillStatus" value="<?php echo $Query_billdetil['BillStatus_Collect']; ?>">
                                            <input type="hidden" id="CheckStatus" name="CheckStatus" value="<?php echo $this->input->get('Check'); ?>">
                                             
                                            <?php if($this->input->get('Check') == 'Dis'){ $disabled = 'disabled'; } else{ $disabled = '';} ?> 
                                            <div class="titel text-left"> <i class="fa fa-file-o" aria-hidden="true"></i> แก้ไขรายการออเดอร์ </div>
                                            <div class="form-row">
                                                
                                                <div class="form-group col-md-4">
                                                    <label class="">วันที่</label>
                                                    <span class="is-required">(* กรุณาระบุข้อมูล)</span>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                        </div>
                                                        <input type="date" class="form-control " name="order_date" id="order_date" value="<?php echo date('Y-m-d',strtotime($Query_billdetil['DATE_STARTS_strtotime'])); ?>" <?php echo $disabled; ?>>
                                                
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="">เลือกเขต</label>
                                                    <span class="is-required">(* กรุณาระบุข้อมูล)</span>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-cubes"></i></span>
                                                        </div>
                                                        <select class="custom-select " name="deliveryid" id="deliveryid" <?php echo $disabled; ?>>
															<?php
																$sql = $this->db->select('*')
																->from('delivery')
																->where('status',1)
																->get();
																foreach($sql->result() as $row){
																	if($Query_billdetil['DELIVERY_FORM'] == $row->ID){
																		$selected = "selected";
																	}else{
																		$selected = "";
																	}
																	
																	echo '<option value="'.$row->ID.'" '.$selected.'> '.$row->NAME_US.' </option>';
																}
															?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="">เลือกสาขา</label>
                                                    <span class="is-required">(* กรุณาระบุข้อมูล)</span>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-paper-plane"></i></span>
                                                        </div>
                                                        <select class="custom-select " name="method_order" id="method_order" <?php echo $disabled; ?>>
                                                            <?php foreach($Query_methodorder->result() AS $row){ ?>
                                                                <option <?php if($Query_billdetil['METHODORDER_ID'] == $row->ID){ echo 'selected';} ?> value="<?php echo $row->ID; ?>"> <?php echo $row->TOPIC; ?> </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                

                                                
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="">คำอธิบายเพิ่มเติม </label>
                                                    <textarea <?php echo $disabled; ?> rows="3" class="form-control" name="remark_order" id="remark_order" placeholder="คำอธิบาย..."><?php echo $Query_billdetil['REMARKORDER']; ?></textarea>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="titel text-left"> 
                                                <i class="fa fa-file-text" aria-hidden="true"></i> รายการออเดอร์
                                                <?php 
                                                    // $billdatetime = date('Y-m-d',strtotime($Query_billdetil['DATE_STARTSYMD']));
                                                    // if($billdatetime == date('Y-m-d') || $this->session->userdata('permiss') == 8){
                                                ?>
                                                <button style="padding: 0rem .5rem; position: absolute; right: 2rem;" type="button" class="btn btn-default btn-sm modal-bill" data-toggle="modal" data-target=".bd-example-modal-lg">
                                                <i class="fa fa-plus-circle" aria-hidden="true"></i>  เพิ่ม </button> 
                                                <?php
                                                    // }
                                                ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id='table-bill'>
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 5%;text-align: center;">#</th>
                                                                    <th style="text-align: center;">รายการออเดอร์</th>
                                                                    <th style="width: 10%;text-align: center;">ราคา/บาท</th>
                                                                    <th style="width: 10%;text-align: center;">จำนวน/หน่วย</th>
                                                                    <th style="width: 10%;text-align: center;">รวมเงิน/บาท</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ORlist">
                                                                <?php $index=1; foreach($Query_billdetil['billist'] AS $row1){ 
                                                                        foreach($row1['PRONAME_LIST'] AS $row2){ 
                                                                            $buttondel = '<button type="button" class="btn btn-danger btn-sm" id="btndeleterow" value="tr-'.$index.'"> <i class="fa fa-trash-o" aria-hidden="true"></i>   </button>'; 

                                                                            if(countBillCreditnote_search($row2['BILLDETAIL_BILLID'])){
                                                                                $buttondel = "";
                                                                            }

                                                                            if(countBillreceive_search($row2['BILLDETAIL_BILLID'])){
                                                                                $buttondel = "";
                                                                            }
                                                                            
                                                                ?>
                                                                        <tr id="tr-<?php echo $index; ?>" class="each-total">
                                                                            <td style="text-align: center;"> 
                                                                                <?php
                                                                                    echo $buttondel;
                                                                                ?>
                                                                            </td>
                                                                            <input type="hidden" id="orderlist[<?php echo $index; ?>][promain]" name="orderlist[<?php echo $index; ?>][promain]" value="<?php echo $row1['PRONAME_MAINID']; ?>">
                                                                            <input type="hidden" id="orderlist[<?php echo $index; ?>][prolist]" name="orderlist[<?php echo $index; ?>][prolist]" value="<?php echo $row2['PRONAME_LISTID']; ?>">
                                                                            <input type="hidden" id="orderlist[<?php echo $index; ?>][proqty]" name="orderlist[<?php echo $index; ?>][proqty]" value="<?php echo $row2['QUANTITY']; ?>">
                                                                            <input type="hidden" id="orderlist[<?php echo $index; ?>][totalprice]" name="orderlist[<?php echo $index; ?>][totalprice]" value="<?php echo $row2['RBD_TOTALPRICE_LANG']; ?>">
                                                                            <td style="text-align: left;">  <?php  echo $row2['PRONAME_LIST']; ?> </td>
                                                                            <td style="text-align: right;"> - </td>
                                                                            <td style="text-align: right;"> <?php  echo $row2['QUANTITY']; ?></td>
                                                                            <td id="TOTALP" style="text-align: right;" lang="<?php  echo $row2['RBD_TOTALPRICE_LANG']; ?>"> - </td>
                                                                        </tr>
                                                                <?php $index++;
                                                                        }
                                                                    } 
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row justify-content-center mt-4">
                                                <div class="col-md-6 text-center">
                                                    <button type="button" class="btn btn-default btn-sm" id="Save">
                                                        <span class="text-save"> <i class="fa fa-floppy-o"></i> ยืนยันการแก้ไขบิล  </span>
                                                        <span class="text-spinner"> <i class="spinner fa fa-refresh" aria-hidden="true"></i> กรุณารอสักครู่...  </span>
                                                    </button>
                                                    <button type="button" class="btn btn-default btn-sm" id="cancel"><li class="fa fa-angle-double-left"> </li> กลับหน้ารายการ</button>
                                                </div>
                                            </div>
                                            <!-- Modal ZOOM -->
                                            
                                            <!-- // ========== Modal ============ // -->
                                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="titel text-left"> <i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่มรายการ </div>
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label class="">เลือกเมนูหลัก</label>
                                                                <select id="select-productmain" name="select-productmain" class="selectpicker" data-live-search="true">
                                                                    <option  value=""> -- โปรดเลือกเมนูหลัก -- </option>
                                                                    <?php foreach($Query_productmain->result() AS $row){ ?>
                                                                            <option value="<?php echo $row->ID ?>"><?php echo $row->NAME_TH; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-md-6" id="SLproductlist">
                                                                <label class="">เลือกรายการเมนู</label>
                                                                <select id="select-productlist" name="select-productlist" class="selectpicker selectpicker_1" data-live-search="true" disabled>
                                                                    
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label class="">จำนวน</label>
                                                                <input type="number" class="form-control " name="qty" id="qty" placeholder="จำนวน">
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <button type="button" class="btn btn-app4 btn-block" id="add-order"> <i class="fa fa-plus-circle" aria-hidden="true"></i> เพิ่มรายการ </button>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> ปิด</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
								</div> 
							</div>
						</section>
 
					</div>
				</div> 
			</section>
		 
		</div>
            <?php include("structer/backend/footer.php"); ?>
            <?php include("structer/backend/script.php"); ?>
            <script src="<?php echo $base_bn;?>frontend/bootstrap-select/js/bootstrap-select.js"></script>
            <script src="<?php echo $base_bn;?>plugins/sweetalert2/sweetalert2.min.js"></script>
        </div>
        <script>
         
            if("<?php echo $this->input->get('Check'); ?>" == "Dis"){ 
                var length = $("#table-bill #ORlist tr").length;
                for(var i = 1; i<=length; i++){
                    var Tr = $("#table-bill #ORlist tr")[i-1].id;
                    var count =  $('#'+Tr+' td').length;
                    for(var n = 1;n <= count;n++){
                        $($('#'+Tr+' td')[0]).addClass('displays-Col');
                    }
                }
            } 
			
			$('.bd-example-modal-lg').on('shown.bs.modal', function () {
                // $("#select-productmain").eq(0).prop('checked');
                $("#select-productmain").val("");
                $('.selectpicker').selectpicker('refresh')
            });

            $('.text-spinner').hide();
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            $(document).on('change', '#image_file', function(event) {
                var image_file = $('#image_file');
                var length = (image_file[0].files.length - 1);
                var html = '';
                for(var i=0; i<=length;i++){                    
                    if ( image_file[0].files[i] ){
                        html += '<img id="img-Trans'+i+'" src="'+window.URL.createObjectURL(image_file[0].files[i])+'" class="img-pay001">'
                    }
                }
                $('.fileImage').html(html);
            });
            $(document).on('click', '#statustransfere', function(event) {
                var val = this.value;
                $('#StatusComplete').val(val);
            }); 
            $("#cancel").on("click", function (e) {
                window.location.replace('bill');
            });
            $("#cancelimgxx").on("click", function (e) {
                $("#imagedle").text("Choose file");
                $("#imageInput").val("");
            });
             
            // ============================ //
            
             
            $(document).on("click", "#Save" , function() {
                var result = ["deliveryid","method_order"];
                for(var x=0;x<result.length;x++){
                    if(document.forms["demo2"][result[x]].value == ''){
                        Swal.fire('ผิดผลาด!', 'กรอกข้อมูลให้ครบถ้วน', 'warning' );
                        $("#"+result[x]).addClass('is-warning');
                        $("#"+result[x]).focus();
                        return false;
                    }else{
                        $("#"+result[x]).removeClass('is-warning');
                    }
                }
                 
                if($("#TBLtotalprice").val() == ''){
                    Swal.fire( 'ผิดผลาด!', 'กรุณาเลือกรายการออเดอร์', 'warning' );
                    return false;
                }
                $('#Save').attr('disabled','disabled');
                $('.text-save').hide();
                $('.text-spinner').show();
                dataform();
            });

            function dataform(){
                $.ajax({
                    url: "ajaxdataform",     
                    type:'POST',
                    data: $("form").serialize(),
                    success: function (results) {
                        var obj = jQuery.parseJSON(results);
                        if(obj.error_code == 1){
                            Swal.fire( 'ผิดผลาด!', 'Error', 'warning');
                        } else {
                            if(obj.error_code == 0){
                                Swal.fire('สำเร็จ!', 'แก้ไขรายการสำเร็จ ณ วันที่ <?php echo thai_date(date('Y-m-d')); ?>', 'success')
                                $(".swal2-confirm").on("click", function (e) {
                                    window.location.replace('viwecreatebill?id='+obj.getid);
                                });
                            }
                        }
                    }
                });
            }

            $(document).on("click", "#btndeleterow" , function() {
                var val = $(this).val();
                var html = "";
                $("#"+val).remove();
                if($("#table-bill #ORlist tr").length == 0){
                    html += '<tr id="tr-'+$("#table-bill #ORlist tr").length+'">';
                    html += '    <td colspan="5" class="text-center"> --- โปรดเพิ่มรายการออเดอร์ --- </td>';
                    html += '</tr>';
                    $("#table-bill #ORlist").append(html); 
                    $('#total-price').text('-');
                    $('#TBLtotalprice').val('');
                }
                TBtotalprice();
            });
            $(document).on("click", ".modal-bill" , function() {
                $('#qty').val(''); var html2 = '';
                $("#select-productmain").selectpicker('refresh');
                $("#select-productlist").html(html2).selectpicker('refresh');
            });
            $(document).on("click", "#add-order" , function() {
                
                var productmainID = $('#select-productmain').val();
                var productlistID = $('#select-productlist').val();
				
				$('.selectpicker').selectpicker('refresh')
				
                var qty = $('#qty').val();
                var result = ["select-productmain", "select-productlist", "qty"];
                for(var x=0;x<result.length;x++){
                    if(document.forms["demo2"][result[x]].value == ''){
                        Swal.fire(
                            'ผิดผลาด!',
                            'กรอกข้อมูลให้ครบถ้วน',
                            'warning'
                        )
                        $("#"+result[x]).addClass('is-warning');
                        $("#"+result[x]).focus();
                        return false;
                    }else{
                        $("#"+result[x]).removeClass('is-warning');
                    }
                }
                addrowtable(productmainID, productlistID, qty);
            });

            function addrowtable(productmainID, productlistID, qty){
                var html = '';
                $.post("ajaxaddrowtable", { 
                    PromainID: productmainID,
                    ProlistID: productlistID,
                    qty: qty,
                }, function(result){
                    var obj = jQuery.parseJSON(result);
                    $("#tr-0").remove();
                    var tbllength = ($("#table-bill #ORlist tr").length + 1);
                    html += '<tr id="tr-'+tbllength+'" class="each-total">';
                    html += '    <td style="text-align: center;"> ';
                    html += '        <button type="button" class="btn btn-danger btn-sm" id="btndeleterow" value="tr-'+tbllength+'"> <i class="fa fa-trash-o" aria-hidden="true"></i>   </button>';
                    html += '    </td>';
                    html += '    <input type="hidden" id="orderlist['+tbllength+'][promain]" name="orderlist['+tbllength+'][promain]" value="'+obj.PromainID+'">';
                    html += '    <input type="hidden" id="orderlist['+tbllength+'][prolist]" name="orderlist['+tbllength+'][prolist]" value="'+obj.ProlistID+'">';
                    html += '    <input type="hidden" id="orderlist['+tbllength+'][proqty]" name="orderlist['+tbllength+'][proqty]" value="'+obj.prolist_qty+'">';
                    html += '    <input type="hidden" id="orderlist['+tbllength+'][totalprice]" name="orderlist['+tbllength+'][totalprice]" value="'+obj.total_price+'">';
                    html += '    <td style="text-align: left;">'+obj.prolist_name+'</td>';
                    html += '    <td style="text-align: right;">-</td>';
                    html += '    <td style="text-align: right;">'+obj.prolist_qty+'</td>';
                    html += '    <td id="TOTALP" style="text-align: right;" lang="'+obj.total_price+'">-</td>';
                    html += '</tr>';
                    $("#table-bill #ORlist").append(html);
                    TBtotalprice();
                    Toast.fire({
                        type: 'success',
                        title: ' เพิ่มรายการเมนู '+obj.prolist_name+' เรียบร้อย '
                    });    
                });
            }
                
            $('#select-productlist').change(function(){
                $('#qty').val('');
            });
            $('#select-productmain').change(function(){
                var option = $(this).find('option:selected'),
                id = option.val();
                name = option.data('name');
                $("#select-productlist").empty();
                $("#select-productlist").removeAttr('disabled')
                ajaxprolist(id);
            });

            function ajaxprolist(id){
                $.ajax({
                    url: "ajaxselectproductmain",     
                    type:'POST',
                    data: {
                        action: 'my_special_ajax_call',
                        val: id 
                    },
                    success: function (results) {
                        var obj = jQuery.parseJSON(results);
                        var html = '';
                        html +=' <option value="">โปรดเลือกรายการเมนู</option>';
                        $.each(obj, function( index, value ) {
                            html +=' <option value="'+value.ID+'">'+value.NAME_TH+'</option>';
                        });
                        $("#select-productlist").html(html).selectpicker('refresh');
                    }
                });
            }

            function TBtotalprice(){
                // Total Price //
                var arr_totalprice = [];
                $('#table-bill #ORlist .each-total').each(function () {
                    var row = $(this);
                    var rowTotal = 0;
                    $(this).find('td#TOTALP').each(function () {
                        var td = $(this);
                        arr_totalprice.push(parseFloat(td.context.lang).toFixed(2));
                    });
                    arr_totalprice = arr_totalprice.map(Number);
                    rowTotal = arr_totalprice.reduce(function(a,b){  return a+b },0);
                    $('#TBLtotalprice').val(rowTotal);
                    var up_rowTotal = rowTotal;
                    rowTotal = new Intl.NumberFormat('ja-JP').format(parseFloat(rowTotal).toFixed(2));
                    $('#total-price').text(rowTotal);
                    if($('#BillStatus').val() == "C"){
                        var Total = $('#TBLtotalprice').val();
                        var sum = Total*(3/100);
                        $('#tax').val(sum); 
                    }
                    var totalParcelcost = parseFloat($('#total-Parcelcost').val());
                    var totalShippingcost = parseFloat($('#total-Shippingcost').val());
                    var shormoney = parseFloat($('#shor_money').val());
                    var tax = parseFloat($('#tax').val());
                    var discount = parseFloat($('#discount').val());
                    var rowTotal = $('#TBLtotalprice').val();
                    var Total_cost = (parseFloat(rowTotal) + totalParcelcost + totalShippingcost + shormoney + tax) - discount;
                    $('#total-cost').text(Total_cost);
					
					//
                    //  for bill free price
                    if($('#BillStatus').val() == "F"){ 
                        $('#total-price').text(0);
                        $('#total-cost').text(0);
                    }else{
                        $('#total-price').text(rowTotal);
                        $('#total-cost').text(Total_cost);
                    }
                });
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                bsCustomFileInput.init();
            });
        </script>
	</body>
</html>
 