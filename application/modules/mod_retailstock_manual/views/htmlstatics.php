<div class="row justify-content-center">

    <div class="col-md-3 col-sm-12">
        <div class="card-box widget-box-two widget-two-custom ">
            <div id="dashboard-1" class="widget-box-four-chart"></div>
            <div class="text-center">
                <h4 class="mt-0 font-16 mb-1 text-overflow text-success" title="ยอดรับเข้า">จำนวนรับเข้า</h4>
                <h3 class="my-2 text-success"><span><?php echo number_format($staticPull); ?><span></h3>
                <a href="" id="pull-detail" data-toggle="modal" data-target=".modal-detail" data><span>รายละเอียด</span> <i class="fa fa-exclamation-circle"></i></a>
                <p class="font-secondary text-muted"><?php echo thai_date($get_date); ?></p>
            </div>

        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="card-box widget-box-two widget-two-custom ">
            <div id="dashboard-1" class="widget-box-four-chart"></div>
            <div class="text-center">
                <h4 class="mt-0 font-16 mb-1 text-overflow text-danger" title="ยอดรับเข้า">จำนวนจำหน่าย</h4>
                <h3 class="my-2 text-danger"><span><?php echo number_format($staticCut); ?><span></h3>
                <a href="" id="cut-detail" data-toggle="modal" data-target=".modal-detail" data><span>รายละเอียด</span> <i class="fa fa-exclamation-circle"></i></a>
                <p class="font-secondary text-muted"><?php echo thai_date($get_date); ?></p>
            </div>

        </div>
    </div>

</div>

<div class="row justify-content-center">

    <div class="col-xl-2 col-md-3 col-sm-6">
        <div class="card-box widget-box-two widget-two-custom ">
            <div class="media">
                <div class="wigdet-two-content media-body">
                    <p class="m-0 text-uppercase font-weight-medium text-truncate text-center text-info" title="Statistics">ยอดรวมเคลมสินค้า (ชิ้น)</p>
                    <div class="text-right">
                        <h3 class="font-weight-medium my-2"> <span id="static-claim" data-plugin="counterup"><?php echo $staticClaim; ?></span></h3>
                        <p class="m-0 text-secondary"><?php echo thai_date($get_date); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end col -->
    <div class="col-xl-2 col-md-3 col-sm-6">
        <div class="card-box widget-box-two widget-two-custom ">
            <div class="media">
                <div class="wigdet-two-content media-body">
                    <p class="m-0 text-uppercase font-weight-medium text-truncate text-center text-info" title="Statistics">ยอดรวมสินค้าเสีย (ชิ้น)</p>
                    <div class="text-right">
                        <h3 class="font-weight-medium my-2"> <span id="static-loss" data-plugin="counterup"><?php echo $staticLoss; ?></span></h3>
                        <p class="m-0 text-secondary"><?php echo thai_date($get_date); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end col -->
    <div class="col-xl-2 col-md-3 col-sm-6">
        <div class="card-box widget-box-two widget-two-custom ">
            <div class="media">
                <div class="wigdet-two-content media-body">
                    <p class="m-0 text-uppercase font-weight-medium text-truncate text-center text-info" title="Statistics">ยอดรวม repack (ชิ้น)</p>
                    <div class="text-right">
                        <h3 class="font-weight-medium my-2"> <span id="static-repack" data-plugin=""><?php echo $staticRepack; ?></span></h3>
                        <p class="m-0 text-secondary"><?php echo thai_date($get_date); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end col -->
    <div class="col-xl-2 col-md-3 col-sm-6">
        <div class="card-box widget-box-two widget-two-custom ">
            <div class="media">
                <div class="wigdet-two-content media-body">
                    <p class="m-0 text-uppercase font-weight-medium text-truncate text-center text-info" title="Statistics">ยอดรวมเคสอื่นๆ (ชิ้น)</p>
                    <div class="text-right">
                        <h3 class="font-weight-medium my-2"> <span id="static-other" data-plugin=""><?php echo $staticOther; ?></span></h3>
                        <p class="m-0 text-secondary"><?php echo thai_date($get_date); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end col -->

</div>