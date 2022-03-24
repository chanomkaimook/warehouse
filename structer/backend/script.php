  <!-- <script src="<?php echo $base_bn; ?>/global/plugins/jquery/jquery-1.11.1.min.js"></script>
<script src="<?php echo $base_bn; ?>/global/plugins/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="<?php echo $base_bn; ?>/global/plugins/jquery-ui/jquery-ui-1.11.2.min.js"></script> -->
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script src="<?php echo $base_bn; ?>frontend/js/vendor/jquery-2.2.4.min.js"></script>
  <script src="<?php echo $base_bn; ?>frontend/js/jquery-ui.js"></script>
  <script>
  	$.widget.bridge('uibutton', $.ui.button)
  </script>

  <!-- ChartJS -->
  <script src="<?php echo $base_bn; ?>plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="<?php echo $base_bn; ?>plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <!-- <script src="<?php echo $base_bn; ?>plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="<?php echo $base_bn; ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script> -->
  <!-- jQuery Knob Chart -->
  <!-- <script src="<?php echo $base_bn; ?>plugins/jquery-knob/jquery.knob.min.js"></script> -->
  <!-- daterangepicker -->
  <script src="<?php echo $base_bn; ?>plugins/moment/moment.min.js"></script>
  <script src="<?php echo $base_bn; ?>plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?php echo $base_bn; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="<?php echo $base_bn; ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

  <!-- overlayScrollbars -->
  <script src="<?php echo $base_bn; ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="<?php echo $base_bn; ?>dist/js/pages/dashboard.js"></script> -->
  <!-- AdminLTE for demo purposes -->
  <script src="<?php echo $base_bn; ?>dist/js/demo.js"></script>

  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <!-- SweetAlert2 -->
  <!--
<script src="<?php echo $base_bn; ?>plugins/sweetalert2/sweetalert2.min.js"></script>
-->
  <!-- Toastr -->
  <!--
<script src="<?php echo $base_bn; ?>plugins/toastr/toastr.min.js"></script>
 -->
  <!-- DataTables -->
  <script src="<?php echo $base_bn; ?>plugins/datatables/jquery.dataTables.js"></script>
  <script src="<?php echo $base_bn; ?>plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>

  <!-- Bootstrap Switch -->
  <script src="<?php echo $base_bn; ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
  <!-- bs-custom-file-input -->
  <script src="<?php echo $base_bn; ?>plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

  <!-- --------------------------------------------------- -->

  <!-- AdminLTE App -->
  <script src="<?php echo $base_bn; ?>dist/js/adminlte.js"></script>
  <!-- Ekko Lightbox -->
  <script src="<?php echo $base_bn; ?>plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
  <!-- Filterizr-->
  <!-- <script src="<?php echo $base_bn; ?>plugins/filterizr/jquery.filterizr.min.js"></script> -->
  <!-- Summernote -->
  <script src="<?php echo $base_bn; ?>plugins/summernote/summernote-bs4.min.js"></script>

  <script>
  	$(document).on('click', '.sidebar-collapse', function() {
  		let divSide = $('body').hasClass('sidebar-collapse');
  		if (divSide) {

  			// var year = 1000*60*60*24*365;		///	1 year
  			var year = 1000 * 60 * 60 * 24; ///	1 day
  			var expires = new Date((new Date()).valueOf() + year);
  			document.cookie = "cc_sidebar_collapse=yes;expires=" + expires.toUTCString();
  		} else {
  			var name_ck_sidebar = 'cc_sidebar_collapse';
  			document.cookie = name_ck_sidebar + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
  		}
  	})

  	if (getCookie('cc_sidebar_collapse')) {
  		$('body').addClass('sidebar-collapse');
  	}

  	function getCookie(username) {
  		let name = username + "=";
  		let spli = document.cookie.split(';');
  		for (var j = 0; j < spli.length; j++) {
  			let char = spli[j];
  			while (char.charAt(0) == ' ') {
  				char = char.substring(1);
  			}
  			if (char.indexOf(name) == 0) {
  				return char.substring(name.length, char.length);
  			}
  		}
  		return "";
  	}

  	// $(document).on('click', '#datatable', function(event) {
  	//   $('#ex1').DataTable().destroy();
  	// }); 
  	/*########################################*/
  	/*##########		sidebar		##########*/
  	/*########################################*/
  	if ($(".sidebar > li").hasClass('active')) { //addActiveMenu
  		$(".sidebar > li.active > a[href]").attr("href", "#");
  	} else {
  		addActiveMenu();
  	}
  	/**********		menu open	*********/
  	var order;
  	var order = $('input#permisspage[type=hidden]').val();
  	var res = order.split(",");


  	var li_id = [];
  	var li_menuselect = $('nav li.menu-open').attr('id'); //	mainmenu
  	var li_menu = $('nav li.menu-open ul').children('li'); //	submenu
  	var li_menu_arr = li_menu.toArray();
  	for (i = 0; i < li_menu_arr.length; i++) {
  		var li = li_menu.eq(i).attr('id');
  		li_id.push(li);
  	}

  	// console.log(li_menuselect);
  	// console.log(li_id);
  	// console.log("+"+res);
  	if (res.length > 0) {
  		for (var i = 0; i < res.length; i++) {
  			for (var x = 0; x < li_id.length; x++) { ///	block menu
  				// console.log(res[i]+" -- "+li_id[x]);
  				if (res[i] == li_id[x]) {
  					$("ul.nav-sidebar li#" + li_menuselect).css('display', 'block');

  				}
  			}
  			$("li#" + li_menuselect + " ul.nav-treeview li#" + res[i]).css('display', 'block');
  			////	other menu 
  			var chkli = $("ul.nav-sidebar").find("li#" + res[i]);
  			var chkliClass = $("ul.nav-sidebar li#" + res[i]).parents("li").css('display');
  			var uchildren = $("ul.nav-sidebar li#" + res[i]).parents("ul").children();
  			// if(chkli.length > 0 && chkliClass == 'none'){
  			// console.log(chkli.length+"<<<"+res[i]);
  			if (chkli.length > 0) {
  				$("ul.nav-sidebar li#" + res[i]).parents("li").css('display', 'block')
  				$("ul.nav-sidebar li#" + res[i]).css('display', 'block');
  				// console.log($("ul.nav-sidebar li#"+res[i]).css('display','block'));
  			}

  		}
  		if (res == 'all') {
  			$("ul.nav-sidebar li").css('display', 'block');
  			$("ul.children li").css('display', 'block');
  		}
  	}

  	function addActiveMenu() {
  		var d = document;
  		var mainmenu = d.getElementById('mainmenu').value;
  		var submenu = d.getElementById('submenu').value;

  		$("li#" + mainmenu).addClass("menu-open");
  		$("li#" + mainmenu + " li#" + submenu + " a").addClass("active");
  	}

  	function parseJwt(token) {
  		var base64Url = token.split('.')[1];
  		var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  		var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
  			return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  		}).join(''));

  		return JSON.parse(jsonPayload);
  	};
  </script>