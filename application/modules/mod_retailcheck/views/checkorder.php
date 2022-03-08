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
						<h1>retail</h1>
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
                                    
                                    <!-- <button id="btn-dialog" class="btn btn-default" data-toggle="modals" data-target="#modalstocks" >dialog</button>
                                    <button id="btn-alert" class="btn btn-default" >alert</button> -->
                                   
                                    <div class="row">

                                        <div class="col blocklist">
                                            <h5>รายการสินค้า</h5>
                                            <?php
                                                //
                                                //
                                                $array_order = array();
                                                $array_detail = array();
                                                if(count($billid) > 0){
                                                    $i = 1;
                                                    //  paramiter get for set order choose
                                                    if($this->input->get('billid') ? $url_getbillid = $this->input->get('billid') : $url_getbillid = $billid[0]);
                                                    foreach($billid as $id){
                                                        $array_order = array();
                                                        $array_detail = array();
                                                        $netqty = 0;
                                                        //
                                                        //  set array
                                                        $query = $this->mdl_retailcheck->fetch_Billdetail($id);
                                                        if($query){
                                                            foreach($query->result() as $r){
                                                                $netqty = $netqty  + $r->bill_qty;
                                                                $array_order = array('id' => $r->bill_code,'orderid' => $r->bill_id,'name' => $r->bill_name);
                                                                array_push($array_detail,array('pid' => $r->pd_id,'plistid' => $r->pd_listid,'name' => $r->pd_nameth,'total' => $r->bill_qty));
                                                            }
                                                        }

                                            ?>
                                                        
                                            <div class="">
                                                <a href="<?php echo "#cl-".$i;?>" class="" data-toggle="collapse" data-id="<?php echo "cl-".$i;?>" data-index="<?php echo $i;?>" data-orderid="<?php echo $id;?>" >
                                                    <span class="d-flex text-danger">
                                                    <span class="head"><?php echo $array_order['id']."</span>
                                                    <span> - ".$array_order['name'];?></span>
                                                    <span class="statusname"><span class="ml-4" style="color:#888" >รอ</span></span>
                                                    </span>
                                                </a>
                                            </div>    
                                            <div  id="<?php echo "cl-".$i;?>" class="collapse" data-id="<?php echo "cl-".$i;?>" data-orderid="<?php echo $id;?>" data-netqty="<?php echo $netqty;?>" >
                                            <?php
                                                //
                                                //  display 
                                                foreach($array_detail as $data){
                                                    if($data['plistid']){
                                                        $pid = $data['plistid'];
                                                        $pro = $data['pid'];
                                                    }else{
                                                        $pid = $data['pid'];
                                                        $pro = "";
                                                    }
                                                    // <i class="fas fa-check-circle text-success"></i><span class="badge badge-secondary">5</span>
                                                    echo '<div class="d-flex justify-content-between" data-productid="'.$pid.'" data-promotion="'.$pro.'" data-total="'.$data['total'].'" data-count="0" data-status="0" >';
                                                    echo '<div class="col-8"><h3>'.$data['name'].'</h3></div>';
                                                    echo '<div class=""><h3>'.$data['total'].'</h3></div>';
                                                    echo '<div class=""><h3 class="insertTotal"> ... </h3></div>';
                                                    echo '</div>';
                                                }
                                            ?>
                                            <hr>
                                            </div>                     
                                            <?php                
                                                        $i++;
                                                   }   //  end foreach
                                                }
                                            ?>
                                        </div>
                                                
                                        <div class="col pl-5">
                                            <div class="">
                                                <span>เลขที่สินค้า</span>
                                                <div class="">
                                                    <div class="input-group">
                                                        <input type="text" id="bill-code" name="bill-code" value="" class="form-control form-control-sm" disabled >
                                                        <div class="input-group-append">
                                                            <button class="btn btn-secondary btn-sm" type="button" id="icon-search" ><i class="fa fa-search"></i></button>
                                                        </div>
                                                    </div>
                                                    <p class="text-secondary">ช่องกรอกเลขที่ order คลิกปุ่ม <i class="fa fa-search"></i> เพื่อค้นหารายการ</p>
                                                </div>
                                            </div>

                                            <div class="d-flex">

                                                <div class="">
                                                    <span>รหัสสินค้า</span>
                                                    <div class="">
                                                        <input type="text" id="product-barcode" name="product-barcode" value="" class="form-control form-control-sm bg-danger" data-orderid="" >
                                                        <p class="text-secondary">ช่องกรอกรหัส barcode ค้นหาได้ทั้งรหัส mac5 และรหัสสินค้า</p>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <span>จำนวน</span>
                                                    <div class="">
                                                        <input type="number" id="product-total" name="product-total" value="1" class="form-control form-control-sm" >
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="">
                                                <button class="btn btn-success btn-sm" id="btn-skip" type="button" >ข้ามรายการต่อไป</button>
                                            </div>

                                        </div>

                                    </div>
                                    
                                    

                                    <!-- modal stock Peview -->
                                    <div id="modalstock" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content" >
                                                <div class="titel text-left col-md-12"> <i class="fa fa-file-text-o" aria-hidden="true"></i> ข้อมูล </div>
                                                
                                                <div id="content" class="block">
                                                </div>

                                                <div class="text-right">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal" >
														ปิด
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- End stock   -->
 
								</div> 

							</div>
						</section>
 
					</div>
				</div> 
			</section>
		 
		</div>
            <?php include("structer/backend/footer.php"); ?>
            <?php include("structer/backend/script.php"); ?>
            <script src="<?php echo $base_bn;?>plugins/sweetalert2/sweetalert2.min.js"></script>
            <script src="<?php echo $base_bn;?>plugins/toastr/toastr.min.js"></script>
            <script src="<?php echo $base_bn;?>frontend/bootstrap-select/js/bootstrap-select.js"></script>
            
        </div>
        <script>
            $(function () {
				var heightBlocklist = $(window).height() - (60+250);
				// console.log(heightBlocklist);
				$('.blocklist').css({'height':heightBlocklist+'px','overflow':'auto'});
                //  
                //  setting
                var divCollapse = $('.collapse');
                var inputTotal = $('input#product-total');

                $('[data-toggle="tooltip"]').tooltip();

                //
                //  status input barcode
                $(document).on('focus','#product-barcode', function(event) {
                    $(this).removeClass('bg-danger');
                });
                $(document).on('focusout','#product-barcode', function(event) {
                    $(this).addClass('bg-danger');
                });

                $(document).on('click','#btn-dialog', function(event) {
                    event.stopPropagation();
                    dialogInputCode();
                });

                $(document).on('click','#btn-alert', function(event) {
                    event.stopPropagation();

                    array = {
                        "type":"error",
                        "desc":"Are you the six fingered man?"
                    };
                    dialogAlert(array);
                });

                //====================================================================
                
                //
                //  if find not collaspe show div collaspe first to show 
                //
                setDefaultCollapse();
                function setDefaultCollapse(){
                    if(divCollapse.hasClass('show') === false){
                        showCollaspe(0);
                    }
                }
                
                //
                //  clear collaspe
                //
                function clearCollaspe(){
                    if(divCollapse.hasClass('show')){
                        let divId = $('.show').attr('data-id');
                        divCollapse.removeClass('show');
                        $('a[data-id='+divId+'] span.head').unwrap();
                    }
                }
                
                //
                //  check order collaspe
                //
                function checkCollaspe(){
                    let divElement = 0;
                    let result;

                    divCollapse.each(function (index){
                        if($(this).hasClass('show')){
                            divElement = index;
                        }
                        
                    });
                    
                    return divElement;
                }

                //
                //  check order collaspe
                //
                function nextCollaspe(divIndex){
                    let divNumber = divCollapse.length;
                    // 
                    //  next index
                    divIndex++;
                    if(divIndex >= divNumber){
                        divIndex = 0
                    }

                    return divIndex;
                }

                //
                //  collaspe to show
                //
                function showCollaspe(index){
                    divCollapse.eq(index).addClass('show');
                    if(divCollapse.hasClass('show')){
                        let dataId = divCollapse.eq(index).attr('data-id');
                        let adata = $('a[data-id='+dataId+'] span');
                        let textOrder;

                        //
                        //  find order name 
                        if(adata.contents("span.head").html() == null){
                            textOrder = adata.contents("h1").children("span.head").html();
                        }else{
                            textOrder = adata.contents("span.head").html();
                        }

                        //
                        //  set paramiter before tranfer method
                        $('input#bill-code').val(textOrder);
                        $('input#product-barcode').attr('data-orderid',$('a[data-id='+dataId+']').attr('data-orderid'));
                        $('input#product-barcode').val('');
                        inputTotal.val(1);

                        //
                        //  wrap tag h1 for order name
                        adata.contents("span.head").wrap("<h1>");

                        //
                        //  run to focus input barcode
                        $('input#product-barcode').focus();
                    }
                }

                $(document).on('click','a[data-toggle=collapse]', function(event) {
                    event.stopPropagation();
                    let divId = $(this).attr('data-id');
                    let divIndex = $(this).attr('data-index');
                    //
                    //  find index collaspe show
                    clearCollaspe();

                    //  
                    // set collaspe show
                    divIndex = divIndex-1;  //  for array eq
                    showCollaspe(divIndex);
                    
                });

                $(document).on('click','#btn-skip', function(event) {
                    event.stopPropagation();
                    //
                    //  find index collaspe show
                    ajax_openCollapse();
                    
                });

                //
                //  function open collapse to element click
                //  class collapse enother will close; 
                //  paramiter method
                //  @param this     @text = show collapse element this to click
                //  @param next     @text = show collapse next element this to click
                //
                async function ajax_openCollapse() {
                    let result1 =  await awaite_checkCollaspe();
                    
                    try {
                        if(result1.error_code == 0){
                            //
                            //  next index for show
                            let num = result1.txt;
                            let result;
                            //
                            //  next index for show
                            clearCollaspe();

                            // 
                            //  next index
                            result = nextCollaspe(num);
                            
                            //  
                            // set collaspe show
                            showCollaspe(result);

                        } 
                    } catch (err) {
                        console.error(err);
                    }

                }

                function awaite_checkCollaspe(){
                    let num = checkCollaspe();

                    return new Promise((resolve,reject) => {
                        resolve({error_code:0,txt:num})
                    })
                }

                //
                //  shortcut to scanbarcode
                //
                $(document).on('keypress','body', function(event) {
                    if(event.which == 32){
                        $('input#product-barcode').focus();
                    }
                });

                var delayID = null;
                $(document).on('keyup','#product-barcode', function(event) {
                    event.stopPropagation();

                    //
                    //  prevent spacebar
                    if(event.which == 32){
                        return false;
                    }
                    
                    let billId = $(this).attr('data-orderid');
                    let textInput = $(this).val();

                    let lengthBarcode = $(this).val().length;

                    // if(lengthBarcode >= 6){
                        arraybill = {
                            "billid" : billId,
                            "search" : textInput
                            // "search" : '5500079'
                        };
                        // ajax_inputCode(arraybill);

                    // }
               
                       /*  setInterval(() => {
                            if(lengthBarcode >= 2){
                                console.log('hh');
                            }
                        }, 1500); */

                        if(delayID == null){
                            delayID =            
                            setTimeout(() => {
                                var input_data = ajax_inputCode(arraybill);
                                
                                delayID = null;
                            }, 500);                
                        }else{
                            if(delayID){
                                clearTimeout(delayID);
                                delayID = 
                                setTimeout(() => {
                                    var input_data = ajax_inputCode(arraybill);
                                   
                                    delayID = null;
                                }, 500);                        
                            }  
                        }
                    
                });

            //----------------------------function--------------------------//
                //
                //  Toastr show on webpage
                //  paramiter array
                //  @param type     @text = type toastr 
                //  @param descrip  @text = descripttion toastr 
                //
                function dialogAlert(array){
                    let toastr_type = array["type"];
                    let toastr_desc = array["desc"];

                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "timeOut": "4000"
                    }

                    toastr[toastr_type](toastr_desc);
                }

                //
                //  change status process calculate total product
                //  @param div      @text = element name
                //
                function changStatus(div,total,orderid){
                    let datatotal = div.attr('data-total');
                    let datacount = div.attr('data-count');
                    let adivStatus = $('a[data-orderid='+orderid+'] span.d-flex .statusname');
                    let adiv = $('a[data-orderid='+orderid+'] span.d-flex');
                    let nettotal = 0;

                    if(datatotal == datacount){
                        div.find('div h3.insertTotal').html('<i class="fas fa-check-circle text-success"></i>');

                        let divcount = $('*.show[data-orderid='+orderid+']').children('div');
                        let divnetqty = $('*.show[data-orderid='+orderid+']');
                        let netqty = parseInt(divnetqty.attr('data-netqty'));

                        if(divcount.length > 0){
                            divcount.each(function(){
                                nettotal = nettotal + parseInt($(this).attr('data-count'));
                            });
                            //
                            //  check for write status order
                            if(netqty == nettotal){
                                adivStatus.html('<span class="ml-4" style="color:#28a745" >สำเร็จ</span>');
                                adiv.removeClass('text-danger');
                                adiv.addClass('text-success');
								
								Swal.fire({
									type: 'success',
									title: 'รายการสินค้าครบ',
									timer: 2000,
									allowOutsideClick: false,
									showConfirmButton: false
									// text: 'Something went wrong!',
								})
								
                                //	auto next order
	                            $("#btn-skip").trigger('click');
                            }
                        }
                       
                    }
                }

				//
                //  get async product from input barcode
                // 
                async function ajax_inputCode(array) {
                    let result1 =  await awaite_checkOrderBarcode(array);
                    
                    try {
                        // console.log(result1.txt);
                        if(result1.txt){
                            // insertTotalOrder();
                            let orderid = result1.txt['rt_id'];
                            let productid = result1.txt['rtp_id'];
                            let dataDiv = $('*[data-orderid='+orderid+'] div[data-productid='+productid+'][data-promotion=""][data-status=0]').eq(0);
                            let dataDivpro = $('*[data-orderid='+orderid+'] div[data-productid='+productid+'][data-promotion!=""][data-status=0]').eq(0);

                            let datatotal = dataDiv.attr('data-total');
                            let datacount = dataDiv.attr('data-count');
							
							let dataprototal = dataDivpro.attr('data-total');
                            let dataprocount = dataDivpro.attr('data-count');
                            
                            let total = inputTotal.val();
                            let netTotal = parseInt(total) + parseInt(datacount);
                            let netproTotal = parseInt(total) + parseInt(dataprocount);
                            // console.log(orderid+"--"+productid+" = ("+datatotal+"||"+datacount+") = net:"+netTotal);
							
							if(dataDiv.length == 0 && dataDivpro.length == 0 ){
								console.log(dataDiv.length+' || '+dataDivpro.length);
								notMatchResult();
								return false;
							}
							
							if(total <= 0){
								notMatchResult();
								return false;
							}
							
							//	promotion
							if(netproTotal > 0){
								//
                                //  check product id promotion
                                if(dataDivpro.length > 0 && netproTotal <= dataprototal){
									console.log('Promotion');
									
									arrayAlert = {
										"type":"success",
										"desc":"เพิ่มรายการ "+result1.txt['rtp_name']+" "+total+" หน่วย"+" สำเร็จ"
									};
									dialogAlert(arrayAlert);
									//
									//  insert number to html
									// let divProduct = $('*[data-orderid='+orderid+'] div[data-productid='+productid+'][data-promotion=""][data-status=0]');
							
									dataDivpro.find('div h3.insertTotal').html(netproTotal);
									dataDivpro.attr('data-count',netproTotal);
									//
									//	update status to next product
									if(netproTotal == dataprototal){
										dataDivpro.attr('data-status',1);
									}
									
									//  check status process
									changStatus(dataDivpro,netproTotal,orderid);
									
									//  when method success will clear value input barcode
									$('input#product-barcode').val('');
									return false;
								}else{
									notMatchResult();
								}
							}
							
							//	product
							if(netTotal > 0){
								//
                                //  check product id
                                if(dataDiv.length > 0 && netTotal <= datatotal){
									console.log('product');
									
									arrayAlert = {
										"type":"success",
										"desc":"เพิ่มรายการ "+result1.txt['rtp_name']+" "+total+" หน่วย"+" สำเร็จ"
									};
									dialogAlert(arrayAlert);
									//
									//  insert number to html
									// let divProduct = $('*[data-orderid='+orderid+'] div[data-productid='+productid+'][data-promotion=""][data-status=0]');
							
									dataDiv.find('div h3.insertTotal').html(netTotal);
									dataDiv.attr('data-count',netTotal);
									//
									//	update status to next product
									if(netTotal == datatotal){
										dataDiv.attr('data-status',1);
									}
									
									//  check status process
									changStatus(dataDiv,netTotal,orderid);
									
									//  when method success will clear value input barcode
									$('input#product-barcode').val('');
									return false;
								}else{
									notMatchResult();
								}
								
							}
               
                        }else{
							findNotResult();
							//  when method success will clear value input barcode
							$('input#product-barcode').val('');
						}
                    } catch (error) {
                        // alert(error);
                    }

                }

                function notMatchResult(){
                    Swal.fire({
                        type: 'warning',
                        title: 'จำนวนไม่ตรงกับข้อมูล',
                        timer: 800,
                        allowOutsideClick: false,
                        showConfirmButton: false
                        // text: 'Something went wrong!',
                    })
					
					//  when method success will clear value input barcode
                    $('input#product-barcode').val('');
                }
                
                function findNotResult(){
                    Swal.fire({
                        type: 'warning',
                        title: 'ไม่พบข้อมูล',
                        timer: 800,
                        allowOutsideClick: false,
                        showConfirmButton: false
                        // text: 'Something went wrong!',
                    })
					
					//  when method success will clear value input barcode
                    $('input#product-barcode').val('');
                }

                function awaite_checkOrderBarcode(array){
                    let search = array['search'];
                    let billid = array['billid'];

                    return new Promise((resolve,reject) => {
                        $.post("searchOrderBarcode",
                        {
                            //  paramiter
                            searchtext : search,
                            billid : billid
                        })
                        .done(function(data, status, error){ 
                            if(error.status == 200){	//	status complete
                                var obj = jQuery.parseJSON(data); 
                                resolve({error_code:obj.error_code,txt:obj.txt})
                            }

                        })
                        .fail(function(xhr, status, error) {
                            // error handling
                            alert('พบความผิดปกติ แจ้งเจ้าหน้าที่');
                            // window.location.reload();
                        });

                    })
                }

               /*  function dialogInputCode(){

                    Swal.fire({
                        title: 'Submit your Github username',
                        input: 'text',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'กรอกรหัส Barcode สินค้า',
                        showLoaderOnConfirm: true,
                        preConfirm: (login) => {
                            if(login){
                                return fetch("//api.github.com/users/"+login)
                                .then(response => {
                                    if (!response.ok) {
                                    throw new Error(response.statusText)
                                    }
                                    return response.json()
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(
                                        // console.log(error)
                                    "Request failed: "+error
                                    )
                                })
                            }
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.value) {
                            Swal.fire({
                                title: result.value.login+"'s avatar",
                                imageUrl: result.value.avatar_url
                            })
                        }
                    })

                } */

            })

            //----------------------------modal--------------------------// 
            $(document).on('click','.btn-lotout', function(event) {
                event.stopPropagation();
                var divid = $(this).parents('div.dflex').attr('data-id');
                var divelement = $('div.dflex[data-id='+divid+']');
                var lotinfo = $(this).parents('div.dflex').find('.selectlotout');
                // var lotinfoselect = $('option:selected','.selectlotout');
                var lotinfoselect = $(this).parents('div.dflex').find('option:selected','.selectlotout');
                // console.log(lotinfo.attr('data-lotdetailid')+"="+lotinfoselect.attr('data-lotdate')+"="+lotinfoselect.attr('data-status_full'));return false;
                
                if(lotinfoselect.val() == ""){
                    Swal.fire({
                        type: 'warning',
                        title: '"ไม่มีข้อมูล Lot id',
                        timer: 1200,
                        allowOutsideClick: false,
                        showConfirmButton: false
                        // text: 'Something went wrong!',
                    })
                    return false;
                }

                var sw_icon = 'success';
                Swal.fire({
                    title: 'Wait ...',
                    allowOutsideClick: false,
                    async onOpen (result) {
                        let result1 =  await ajax_updateLot(lotinfo.attr('data-lotdetailid'),lotinfo.val(),lotinfoselect.attr('data-lotdate'),lotinfoselect.attr('data-status_full'));
                        
                        if(result1.error_code == 1){
                            sw_icon = 'warning';
                        }else{
                            sw_icon = 'success';
                        }

                        Swal.fire({
                            type: sw_icon,
                            title: result1.txt,
                            timer: 1200,
                            showConfirmButton: false
                            // text: 'Something went wrong!',
                        }).then((result) => { 
                            divelement.remove();
                            $('#ex1').DataTable().ajax.reload(null, false);
                            
                        })
                    },
                    onBeforeOpen () {
                        Swal.showLoading ()
                    },
                    onAfterClose () {
                        // Swal.hideLoading()
                    }
                })
            });

            //
            //  @param prooductid @int = product id 
            function ajax_updateLot(lotdetailid,stockdetailid,stockdate,statusfull){
                // console.log(lotdetailid+"--"+stockdetailid+"--"+stockdate+" -- "+statusfull);
                return new Promise((resolve,reject) => {
                    $.post("updatelot",
                    {
                        //  paramiter
                        lotdetailid : lotdetailid,
                        stockdetailid : stockdetailid,
                        stockdate : stockdate,
                        statusfull : statusfull
                    })
                    .done(function(data, status, error){ 
                        if(error.status == 200){	//	status complete
                            var obj = jQuery.parseJSON(data); 
                            resolve({error_code:obj.error_code,txt:obj.txt})
                        }

                    })
                    .fail(function(xhr, status, error) {
                        // error handling
                        alert('พบความผิดปกติ ระบบจะทำการ Reload');
                        window.location.reload();
                    });

                })
                
            }
        </script>
        
	</body>
</html>
 