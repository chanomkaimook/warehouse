
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
				font-size: 1.5rem;
				display:inline-table;
            }
			@media print {
			  .div-ems {
				width: 101.6mm;
				height: 101.6mm
			  }
			  /* ... the rest of the rules ... */
			}
         </style>
   	</head>
   	<body>
		
	   <input type="hidden" id="getval" name="getval" value="<?php echo $this->input->post('select-order'); ?>">
	   	<div id="container" class="container">
			<!--<div class="" style="width:21.0cm;height:auto;">-->
				<div class="" style="">
				<!--
				<div class="col-md-12">
					<h1> ออกใบส่ง EMS </h1>
				</div>
				-->
				<?php if($Query){
						$i=0;
						foreach($Query AS $row){ 
							$product = $this->mdl_sentformems->get_codeProduct($row->ID);
				?>
					
						
						<?php
							$calc = $i % 2;
							if($calc==0 && $i != 0){
								// echo '<div style="page-break-after: always"></div>';
								echo '<div class="pagebreak"> </div>';
							}
						?>
		
						<div class="div-ems" style="width:101.6mm;height:101.6mm;" >
							<?php
								$PHONENUMBER = '';
								if($row->PHONENUMBER != ''){ $PHONENUMBER = 'ติดต่อ '.$row->PHONENUMBER; } 
								echo '<h4><small>ชื่อที่อยู่ผู้รับ/Address</small></h4>';
								echo $row->NAME."<br>";
								echo $row->ADDRESS." ".$row->ZIPCODE."<br>";
								echo $PHONENUMBER."<br>";
								echo "<small>".$row->CODE."</small><br>";
								echo "<small>".$product."</small><br>";
							?>
						</div>

					
				<?php 	
							$i++;
						}
				
					} else { ?>
						<div class="col-md-12 text-center">
							<h1> --- โปรดเลือกออเดอร์ที่จะปริ้น ! --- </h1>
						</div>
				<?php }?>
			 
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
 