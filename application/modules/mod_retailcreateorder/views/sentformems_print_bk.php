
<!DOCTYPE html>
<html lang="en">
   	<head> 
        <?php include("structer/backend/head.php"); ?>
		<style>
			.div-ems {
                padding: 1rem;
                margin: 0.5rem;
                border: 1px solid;
                border-radius: 20px;
                min-height: 300px;
				max-height:300px;
				font-size: 1.3rem;
            }
         </style>
   	</head>
   	<body>
		
	   <input type="hidden" id="getval" name="getval" value="<?php echo $this->input->post('select-order'); ?>">
	   	<div class="container">
			<div class="">
				<div class="col-md-12">
					<h1> ออกใบส่ง EMS </h1>
				</div>
				<div id="container" >
					<div class="row">
				<?php if($Query){
						$i=0;
						foreach($Query AS $row){ 
							$product = $this->mdl_sentformems->get_codeProduct($row->ID);
							
							$calc = $i % 8;
							if($calc==0 && $i != 0){
								// echo '<div style="page-break-after: always"></div>';
								// echo '<div class="pagebreak"> </div>';
							}
				?>
					<div class="col-md-6">
						<div class="div-ems">
							<?php
								$PHONENUMBER = '';
								if($row->PHONENUMBER != ''){ $PHONENUMBER = 'ติดต่อ '.$row->PHONENUMBER; } 
								echo '<b>ชื่อที่อยู่ผู้รับ/Address</b><br>';
								echo $row->NAME."<br>";
								echo $row->ADDRESS." ".$row->ZIPCODE."<br>";
								echo $PHONENUMBER;
								echo $PHONENUMBER."<br>";
								echo "<small>".$row->CODE."</small><br>";
								echo "<small>".$product."</small><br>";
							?>
						</div>
					</div>
				<?php 	
							$i++;
						}
				
					} else { ?>
						<div class="col-md-12 text-center">
							<h1> --- โปรดเลือกออเดอร์ที่จะปริ้น ! --- </h1>
						</div>
				<?php }?>
			 
					</div>	<!-- row -->
				</div>	<!-- container print -->
			</div>
		</div>
	      
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script>
		var getval = $('#getval').val();
		if(getval != ''){
			// window.print();
			// return false;
			PrintDiv();
		}
		
		function PrintDiv() {
			// window.print();
			var divToPrint = document.getElementById('container'); // เลือก div id ที่เราต้องการพิมพ์
			var html =  '<html>'+ // 
						'<head>'+
							'<link href="<?php echo base_url("asset"); ?>/print.css" rel="stylesheet" type="text/css">'+
						'</head>'+
							'<body onload="window.print()">' + divToPrint.innerHTML + '</body>'+
						'</html>';
			
			var popupWin = window.open();
			popupWin.document.open();
			popupWin.document.write(html); //โหลด print.css ให้ทำงานก่อนสั่งพิมพ์
			popupWin.document.close();	
		}
		
	</script>
	</body>
</html>
 