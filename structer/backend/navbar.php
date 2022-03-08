		<?php 
			$siteurl = site_url('mod_admin');
		?>
		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav sidebar-collapse">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" id="datatable"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
            
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $siteurl; ?>/ctl_login/logout">
                        <i class="fa fa-power-off" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link openFullscreen" onclick="openFullscreen();">
                        <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link closeFullscreen" onclick="closeFullscreen();">
                        <i class="fa fa-compress" aria-hidden="true"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->
 