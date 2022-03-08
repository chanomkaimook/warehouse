 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ออกใบส่ง EMS </title>
    <link href="https://fonts.googleapis.com/css?family=Sarabun&display=swap" rel="stylesheet">
<style>
    body { font-family: sarabun; font-size: 16px;}     
    table {
        border: 1px solid #333;
        border-collapse: collapse;
        width: 100%;
    }

    td {
        border: 1px solid #333;
        /* text-align: left; */
        padding: 0.4%;
    }
    th {
        border: 1px solid #333;
         text-align: left;
        padding: 0.4%;
    }
    .border-002 {
        border: 1px solid #fff;
    }
</style>  
</head>
<body>
 
<div id="container">
        
    <div id="body">
        <div class="row">
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
                                            <input type="hidden" id="bill_ID" name="bill_ID" value="<?php echo $this->input->get('id'); ?>">
                                            <div class="form-row">

                                                <div class="form-group col-md-12">
                                                    <div style="padding: 10rem;">
                                                        <div style="border: 1px solid #333;padding: 1rem; border-radius: 20px;">
                                                            <table class="table table-bordered ">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="border-002" colspan="5">  <b>บริษัท โชคชัยอินเตอร์เนชั่นเเนล จำกัด</b> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="border-002" colspan="5">  <b>ออเดอร์ที่ : </b><span> <?php echo $Query_billdetil['CODE']; ?> </span>  </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="border-002" colspan="5"> <b>ชื่อ-นามสกุล : </b> <?php echo $Query_billdetil['NAME']; ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="border-002" colspan="5"> <b>ที่อยู่ : </b> <?php echo $Query_billdetil['ADDRESS']; ?></td>
                                                                    </tr>
                                                                    
                                                                </tbody>
                                                            </table>
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
    </div>
 
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var bill_ID = $('#bill_ID').val();
    if(bill_ID != ''){
        window.print();
        // return false;
    }
     
</script>
</body>
</html>
 