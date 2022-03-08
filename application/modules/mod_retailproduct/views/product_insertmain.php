
<!DOCTYPE html>
<html lang="en">
   	<head> 
        <?php include("structer/backend/head.php"); ?>
        <link rel="stylesheet" href="<?php echo $base_bn;?>frontend/bootstrap-select/css/bootstrap-select.css">
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
									<h3 class="card-title"> <i class="fa fa-bars" aria-hidden="true"></i> Manage <?php echo $submenu; ?> </h3>
 								</div> 
								<div class="card-body">
                                      
                                <form id="demo2" name="demo2" class="demo"  enctype="multipart/form-data" accept-charset="utf-8"  method="post">
                                    <input type="hidden" id="promain_id" name="promain_id" value="<?php echo $UPproductmain->ID; ?>">
                                    <div class="titel text-left"> <i class="fa fa-database" aria-hidden="true"></i>  Data Management </div>
                                    <div class="form-row">

                                        <label class="form-group col-md-3 text-right" for="name_th"> เลือกเมนูเพื่อแก้ไข </label>
                                        <div class="form-group col-md-9 ">
                                            <select id="select-productmain" name="select-productmain" class="selectpicker" data-live-search="true">
                                                       
                                                <?php if($UPproductmain->ID){
                                                        foreach($Query_productmain->result() AS $row){ ?>
                                                            <option <?php if($UPproductmain->ID == $row->ID){ echo 'selected';} ?> value="<?php echo $row->ID ?>"><?php echo $row->NAME_TH; ?></option>
                                                <?php   }  
                                                    } else {
                                                        echo ' <option  value=""> -- โปรดเลือกเมนู -- </option>';
                                                        foreach($Query_productmain->result() AS $row){ ?>
                                                            <option value="<?php echo $row->ID ?>"><?php echo $row->NAME_TH; ?></option>
                                                <?php   
                                                        }  
                                                    }?>
                                            </select>
                                        </div>

                                        <label class="form-group col-md-3 text-right" for="name_th"> ชื่อเมนู | TH</label>
                                        <div class="form-group col-md-9 ">
                                            <input type="text" class="form-control " name="name_th" id="name_th" placeholder="ชื่อเมนู" value="<?php echo $UPproductmain->NAME_TH; ?>">
                                        </div>

                                        <label class="form-group col-md-3 text-right" for="name_us"> ชื่อเมนู | US </label>
                                        <div class="form-group col-md-9 ">
                                            <input type="text" class="form-control " name="name_us" id="name_us" placeholder="ชื่อเมนู" value="<?php echo $UPproductmain->NAME_US; ?>">
                                        </div>

                                         
                                        <?php if($this->input->get('promain_id') != ''){ ?>
                                            <label class="form-group col-md-3 text-right" for="status"> สถานะการแสดงผล</label>
                                            <div class="form-group col-md-9">
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio" id="status1" name="status" <?php if($UPproductmain->STATUS == 1){ echo 'checked';} ?>>
                                                    <label for="status1" class="custom-control-label">Enabler</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio" id="status2" name="status" <?php if($UPproductmain->STATUS == 0){ echo 'checked';} ?>>
                                                    <label for="status2" class="custom-control-label">Disabler</label>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <label class="form-group col-md-3 text-right" for="status"> สถานะการแสดงผล</label>
                                            <div class="form-group col-md-9">
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio" id="status1" name="status"  checked="">
                                                    <label for="status1" class="custom-control-label">Enabler</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio" id="status2" name="status" >
                                                    <label for="status2" class="custom-control-label">Disabler</label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    </div>
                                      
                                    <hr>
                                    <div class="row">
                                        <label class="form-group col-md-3"> </label>
                                        <div class="col-md-9 ">
                                            <?php if($this->input->get('promain_id') != ''){  
                                                echo '<button type="button" class="btn btn-default btn-sm" id="Save"><li class="fa fa-floppy-o"> </li> Update</button>';
                                            } else {
                                                echo '<button type="button" class="btn btn-default btn-sm" id="Save"><li class="fa fa-floppy-o"> </li> Save </button>';
                                            } ?>
                                            <button type="button" class="btn btn-default btn-sm" id="cancel"><li class="fa fa-angle-double-left"> </li> Back Main</button>
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
        </div>
        <script >
             
            $(document).ready(function(){
                 
                $('#select-productmain').on('change', function(e) {
                    var val = $('#select-productmain').val();
                    window.location.replace('product_insertmain?promain_id='+val);  
                });

                $("#cancel").on("click", function (e) {
                    window.location.replace('product');   
                }); 
                 
                $("#Save").on("click", function (e) {
                        var result = ["name_th", "name_us"];
                        for(var x=0;x<result.length;x++){
                            if(document.forms["demo2"][result[x]].value == ''){
                                swal("เกิดข้อผิดผลาด", "กรอกข้อมูลให้ครบถ้วน / please insert data", "warning"); 
                                document.getElementById(result[x]).focus();
                                return false;
                            }
                        }
                        dataform();
                });
                
                function dataform() {
                        
                        if(document.getElementById("status1").checked == true){
                            var status = "1";
                        }else{
                            var status = "0";
                        }
                           
                        var data = new FormData();
                        var d = document;
                        var val = $('#select-productmain').val();
                        var promain_id = '';
                        if(val != ''){
                            promain_id = val
                        }
                        data.append("promain_id", promain_id);
                        data.append("name_th",d.getElementById('name_th').value);
                        data.append("name_us",d.getElementById('name_us').value);
                        
                        data.append("status",status);
                         
                        var settings2 = {
                            "crossDomain": true,
                            "url": "ajaxdataForm",	 
                            "method": "POST",
                            "type": "POST",
                            "processData": false,
                            "contentType": false,
                            "mimeType": "multipart/form-data",
                            "data": data
                        }
                        $.ajax(settings2).done(function(response) {
                            var obj = jQuery.parseJSON(response); 
                                if(obj.error_code == 1){
                                    swal("ผิดผลาด", obj.txt, "warning");
                                }else{
                                    swal("บันทึกข้อมูลเรียบร้อย", obj.txt, "success");
                                    $(".swal-button").on("click", function (e) {
                                        window.location.replace('product_insertmain');   
                                    });
                                   
                                }
                            })
                        .fail(function(response) {
                                console.log('Error : ' + response);
                        })
                }
 
    
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                bsCustomFileInput.init();
            });
        </script>
         
	</body>
</html>
 