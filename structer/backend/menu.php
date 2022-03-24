<?php
$siteurl = site_url('mod_admin');
$permisspage = $this->permiss->get_PagePermiss($this->session->userdata('useradmin'));
?>
<style>
	nav li.has-treeview,
	ul.nav-treeview li {
		display: none;
	}

	[class*=sidebar-dark-] {
		background-color: #426c52 !important;
	}
</style>
<input id="mainmenu" name="mainmenu" type="hidden" value="<?php echo $mainmenu; ?>">
<input id="submenu" name="submenu" type="hidden" value="<?php echo $submenu; ?>">
<input id="permisspage" name="permisspage" type="hidden" value="<?php echo $permisspage; ?>">
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<!-- Brand Logo -->
	<a href="<?php echo $siteurl . "/ctl_admin/backend_main"; ?>" class="brand-link">
		<img src="<?php echo $base_bn; ?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
		<span class="brand-text font-weight-light">WAREHOUSE</span>
	</a>

	<!-- Sidebar -->
	<div class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			<div class="image">
				<img src="<?php echo $base_bn; ?>dist/img/avatar6@2x.png" class="img-circle elevation-2" alt="User Image">
			</div>
			<div class="info">
				<a href="#" class="d-block"><?php echo $this->session->userdata('useradminname'); ?></a>
			</div>
		</div>

		<!-- Sidebar Menu -->
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

				<li class="nav-header">Manage basic information
				<li>




				<li id="retail" class="nav-item has-treeview">
					<a href="#" class="nav-link">
						<img class="nav-icon" src="<?php echo $basepic . 'front/retail/icon/food-delivery.png'; ?>" style="filter: invert(1);width: 24px;height: 23px;margin-right: 0.4rem;">
						<p> จัดการคลัง <i class="fas fa-angle-left right"></i> </p>
					</a>
					<ul class="nav nav-treeview">
						<li id="dashboard" class="nav-item">
							<a href="<?php echo site_url('mod_retaildashboard/ctl_dashboard/dashboard') ?>" class="nav-link">
								<i class="nav-icon fa fa-tachometer " aria-hidden="true"></i>
								<p>Dashboard</p>
							</a>
						</li>
						<li id="stock" class="nav-item">
							<a href="<?php echo site_url('mod_retailstock') ?>/ctl_retailstock/stock" class="nav-link">
								<i class="nav-icon fa fa-home " aria-hidden="true"></i>
								<p>Stock</p>
							</a>
						</li>
						<li id="product" class="nav-item">
							<a href="<?php echo site_url('mod_retailproduct') ?>/ctl_retailproduct/product" class="nav-link">
								<i class="nav-icon fas fa-utensils " aria-hidden="true"></i>
								<p>สินค้า</p>
							</a>
						</li>

						<li id="barcode" class="nav-item">
							<a href="<?php echo site_url('mod_retailproduct') ?>/ctl_retailbarcode/barcode" class="nav-link">
								<i class="nav-icon fa fa-barcode " aria-hidden="true"></i>
								<p>บาร์โค้ดสินค้า</p>
							</a>
						</li>

						<li id="createbill" class="nav-item">
							<a href="<?php echo site_url('mod_retailcreateorder') ?>/ctl_createorder/bill" class="nav-link">
								<i class="nav-icon fa fa-file-text " aria-hidden="true"></i>
								<p>รายการสั่งสินค้า</p>
								<?php
								if (countorder() != 0) {
									echo '<span class="badge bg-warning">' . countorder() . '</span>';
								}
								?>
							</a>
						</li>

						<li id="retailreceive" class="nav-item">
							<a href="<?php echo site_url('mod_retailreceive') ?>/ctl_receive/receive" class="nav-link">
								<i class="nav-icon fa fa-file-alt " aria-hidden="true"></i>
								<p>รับเข้าคลัง</p>
								<?php
								echo '<span class="badge bg-warning badge_receive">' . (countBillreceive() != 0 ? countBillreceive() : "") . '</span>';
								?>
							</a>
						</li>

						<li id="retailsupplier" class="nav-item">
							<a href="<?php echo site_url('mod_retailsupplier') ?>/ctl_supplier/supplier" class="nav-link">
								<i class="nav-icon fa fa-file-alt " aria-hidden="true"></i>
								<p>ใบ supplier</p>
								<?php
								$countSupplier = countSupplier();
								if ($countSupplier['num'] != 0) {
									echo '<span class="badge bg-secondary">' . ($countSupplier != 0 ?  $countSupplier['num']  : "") . '</span>';
								}
								?>
							</a>
						</li>

						<li id="retailissue" class="nav-item">
							<a href="<?php echo site_url('mod_retailissue') ?>/ctl_issue/issue" class="nav-link">
								<i class="nav-icon fa fa-file-alt " aria-hidden="true"></i>
								<p>ใบเบิก</p>
								<?php
								$countIssue = countIssue();
								if ($countIssue['num'] != 0) {
									echo '<span class="badge bg-secondary">' . ($countIssue != 0 ?  $countIssue['num']  : "") . '</span>';
								}
								?>
							</a>
						</li>

						<li id="retailnote" class="nav-item">
							<a href="<?php echo site_url('mod_retailnote') ?>/ctl_note/note" class="nav-link">
								<i class="nav-icon fa fa-exclamation-circle " aria-hidden="true"></i>
								<p>Note!</p>
								<?php
								$countnote = countNote();
								if ($countnote['num'] != 0) {
									echo '<span class="badge bg-secondary">' . ($countnote != 0 ?  $countnote['num']  : "") . '</span>';
								}
								?>
							</a>
						</li>

						<li id="report" class="nav-item">
							<a href="<?php echo site_url('mod_retailreport') ?>/ctl_report/retailreport" class="nav-link">
								<i class="nav-icon fa fa-bar-chart " aria-hidden="true"></i>
								<p>รายงาน</p>
							</a>
						</li>

						<li id="staff" class="nav-item">
							<a href="<?php echo site_url('mod_staff/ctl_staff/staff') ?>" class="nav-link">
								<i class="nav-icon fa fa-user" aria-hidden="true"></i>
								<p>ผู้ใช้งาน</p>
							</a>
						</li>

					</ul>
				</li>



			</ul>
		</nav>
		<!-- /.sidebar-menu -->
	</div>
	<!-- /.sidebar -->
</aside>