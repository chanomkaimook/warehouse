<!DOCTYPE html>
<html>
    <head>
        <!-- BEGIN META SECTION -->
        <meta charset="utf-8">
        <title>Backend Chokchaisteakhouse</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta content="" name="description" />
        <meta content="psk" name="author" />
        <link rel="shortcut icon" href="<?php echo base_url().BASE_PIC;?>front/icon/favicon.png"> 
        <!-- END META SECTION -->
        <!-- BEGIN MANDATORY STYLE -->
        <link href="<?php echo $base_bn;?>global/css/style.css" rel="stylesheet">
        <link href="<?php echo $base_bn;?>global/css/ui.css" rel="stylesheet">
        <link href="<?php echo $base_bn;?>global/plugins/bootstrap-loading/lada.min.css" rel="stylesheet">
        <!-- END  MANDATORY STYLE -->
    </head>
    <body class="account" data-page="lockscreen">
        <!-- BEGIN LOGIN BOX -->
        <div class="container" id="lockscreen-block">
            <div class="row">
                <div class="col-md-8 col-md-offset-1">
                    <div class="account-wall">
                        <div class="user-image">
							<img style="background-color:#fff8dc"
							src="<?php echo base_url('asset');?>/images/back/image/web.png" class="img-responsive img-circle" alt="friend 8">

                            <div id="loader"></div>
                        </div>
						<div class="account-form">
                        <form class="form-signin" role="form">
                            <h2>Welcome to <strong>Backend</strong>!</h2>
                            <p>Enter your password to go to dashboard.</p>

                            <div class="append-icon">
								<input type="text" name="name" id="name" autocomplete=username class="form-control form-white username" placeholder="Username" onkeypress="return checkKeyLogin(event);" required>
								<i class="icon-user"></i>
							</div>
							<div class="append-icon m-b-20">
								<input type="password" name="password" id="passwords" autocomplete=current-password class="form-control form-white password" placeholder="Password" onkeypress="return checkKeyLogin(event);" required>
								<i class="icon-lock"></i>
							</div>
							<button type="button" id="submit" class="btn btn-lg btn-primary btn-rounded ladda-button" data-style="expand-left" onclick="javascript:checkLogin();">Sign In</button>
							
                        </form>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="loader-overlay loaded">
            <div class="loader-inner">
                <div class="loader2"></div>
            </div>
        </div>
        <script src="<?php echo $base_bn;?>global/plugins/jquery/jquery-1.11.1.min.js"></script>
        <script src="<?php echo $base_bn;?>global/plugins/jquery/jquery-migrate-1.2.1.min.js"></script>
        <script src="<?php echo $base_bn;?>global/plugins/gsap/main-gsap.min.js"></script>
        <script src="<?php echo $base_bn;?>global/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo $base_bn;?>global/plugins/backstretch/backstretch.min.js"></script>
        <script src="<?php echo $base_bn;?>global/plugins/bootstrap-loading/lada.min.js"></script>
        <script src="<?php echo $base_bn;?>global/plugins/progressbar/progressbar.min.js"></script>
        <!--<script src="<?php echo $base_bn;?>global/js/pages/lockscreen.js"></script>-->
          <script src="<?php echo $base_bn;?>admin/md-layout1/material-design/js/material.js"></script>
    <script src="<?php echo $base_bn;?>admin/layout1/js/layout.js"></script>
	<script>
		function checkKeyLogin(e) {
			if(e.keyCode == 13)
			{
				checkLogin();
				return false;
			}
			else
			{
				return true;
			}
		}
		function checkLogin() {
			var xhttp = new XMLHttpRequest();
			  xhttp.onreadystatechange = function() {
				  // console.log(this.readyState+"---"+this.status);
				if (this.readyState == 4 && this.status == 200) {
					var obj = JSON.parse(this.responseText); 
					if(obj.error_code !='0'){
						alert(obj.txt);
					}else{
						window.location.replace('ctl_login/pathadmin');
					}
				}else{
					//console.log('fail111');
				}
			  };
			  xhttp.open("POST", "ctl_login/ajaxCheckLogin", true);
			  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			  xhttp.send('id='+document.getElementById('name').value+'&passw='+document.getElementById('passwords').value);
		}
		function checkLogins() {
			$.post("ctl_login/ajaxCheckLogin",
			{
				id: document.getElementById('name').value,
				passw: document.getElementById('passwords').value
			},
			function(data, status){
				var obj = jQuery.parseJSON(data); 
				if(obj.error_code !='0'){
					alert(obj.txt);
				}else{
					window.location.replace('ctl_login/pathadmin');
				}
			});
		}
		function checkLogin_forphp5() {
			var txt;
			var data = new FormData();
			data.append('id', document.getElementById('name').value);
			data.append('passw', document.getElementById('passwords').value);
			var settings = {
				  "crossDomain": true,
				  "url": "ctl_login/ajaxCheckLogin",
				  "method": "POST",
				  "type": "POST",
				  "processData": false,
				  "contentType": false,
				  "mimeType": "multipart/form-data",
				  "data": data
				 }
			$.ajax(settings).done(function(response) {
			//console.log('response : ' + response);
			var obj = jQuery.parseJSON(response); 
				if(obj.error_code !='0'){
					alert(obj.txt);
				}else{
					window.location.replace('ctl_login/pathadmin');
				}
				//$("#selectTopic").html(obj.txt);
			})
		   .fail(function(response) {
			console.log('Error : ' + response);
		   })
		}
	</script>
    <script>
      $.material.init();
    </script>
  </body>
</html>