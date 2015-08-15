<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use yii\web\View;


$this->title = 'Shipments: Due for Delivery';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label'=> 'Due for delivery')
);
$show_next = false;
$show_prev = false;

$link = "";
if($search){
    $fro = date('Y/m/d',strtotime($from_date));
    $to = date('Y/m/d',strtotime($to_date));
    $link = "&search=true&to=".urlencode($to)."&from=".urlencode($fro);
}

if( count($parcels) >= $page_width ){
    $show_next = true;
}else{
    $show_next = false;
}


if($offset <= 0){
    $show_prev = false;
}elseif (($offset - $page_width) >= 0){
    $show_prev = true;
}

?>
<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>


<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header clearfix">
        <div class="clearfix">
            <form class="table-search-form form-inline pull-right clearfix">
                <div class="pull-left form-group">
                    <label for="searchInput">Search</label><br>
                    <div class="input-group input-group-sm input-group-search">
                        <input id="searchInput" type="text" name="search" placeholder="Search by Waybill number" class="search-box form-control">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="pull-left">
                <label>&nbsp;</label><br>
                <?php if(count($parcels)): ?><button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#runModal">Generate Delivery Run</button><?php endif; ?>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">Receive Shipments from Hub</button>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if(count($parcels)): ?>
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px" class="datatable-nosort"><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Shipper</th>
                    <th>Shipper Phone</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($parcels) && is_array($parcels)){
                    $i = $offset;
                    foreach($parcels as $parcel){
                        ?>
                        <tr>
                            <td><div class="checkbox-nice"><input id="chbx_w_<?= ++$i; ?>" class="checkable" data-waybill="<?= strtoupper($parcel['waybill_number']); ?>" type="checkbox"><label for="chbx_w_<?= $i; ?>"> </label></div></td>
                            <td><?= $i ?></td>
                            <td><?= strtoupper($parcel['waybill_number']); ?></td>
                            <td><?= strtoupper($parcel['sender']['firstname'].' '. $parcel['sender']['lastname']) ?></td>
                            <td><?= $parcel['sender']['phone'] ?></td>
                            <td><?= strtoupper($parcel['receiver']['firstname'].' '. $parcel['receiver']['lastname']) ?></td>
                            <td><?= $parcel['receiver']['phone'] ?></td>
                            <td><?= date(ServiceConstant::DATE_TIME_FORMAT,strtotime($parcel['created_date'])); ?></td>
                            <td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
                            <td><a href="<?= Url::to(['site/viewwaybill?id='.$parcel['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                        </tr>
                    <?php
                    }}
                ?>
                </tbody>
            </table>
        </div>
        <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
        <?php else:  ?>
            There are no parcels matching the specified criteria.
        <?php endif;  ?>
    </div>
</div>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="arrived_parcels" class="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Receive Shipments from Hub</h4>
                </div>
                <div class="modal-body">

                    <form class="">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Staff ID</label>
                                    <div class="input-group">
                                        <input id="staff_no" value="98765" class="form-control">
                                        <div class="input-group-btn">
                                            <button type="button" data-branch_type="ec" id="get_arrival" class="btn btn-default">Load</button>
                                        </div>
                                    </div>
                                    <div class="input-group">
                                        <label id="loading_label"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label>Staff Name</label>
                                <div id="sweeper_name" class="form-control-static"><em>Not Available</em></div>
                                <label>Department</label><br>
                                <div id="role" class="form-control-static"><em>Not Available</em></div>
                                <label>Branch of Operation</label><br>
                                <div id="branch" class="form-control-static"><em>Not Available</em></div>
                                <input id="staff_user_id" name="staff_user_id" type="hidden">
                            </div>
                        </div>
                    </form>

                    <br>
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody id="parcel_arrival">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="arrived_parcels_btn" type="button" class="btn btn-primary">Accept</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="runModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="arrived_parcels" class="" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Generate Delivery Run Sheet</h4>
                </div>
                <div class="modal-body">
                    <p>Dispatch officer should enter the details below to authenticate the acceptance of this run sheet.</p>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Dispatcher Staff ID</label>
                                <div class="input-group">
                                    <input id="disp_id" value="DISP1" class="form-control">
                                    <div class="input-group-btn">
                                        <button type="button" id="get_details" class="btn btn-default">Load</button>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label id="loading_label"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6" id="staff_info" style="display: none;">
                            <div class="form-group">
                                <label>Staff Name</label>
                                <p id="staff_name">Staff Name</p>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <p id="staff_role">Role</p>
                            </div>
                        </div>
                    </div>
                    <div id="delivery_run">
                        <h4>Run Sheet</h4>
                        <table class="table table-bordered table-condensed" id="delivery_run">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Waybill No.</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="staff_id" id="staff_id">
                    <input type="hidden" id="waybills" name="waybills">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="generate" class="btn btn-primary" disabled="disabled">Generate Run Sheet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- this page specific scripts -->
<?= $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]])?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/shipment_delivery.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php
$ex='
$("#chbx_w_all").change(function () {
    $("input:checkbox").prop("checked", $(this).prop("checked"));
});

';
$this->registerJs($ex,View::POS_READY);
?>
