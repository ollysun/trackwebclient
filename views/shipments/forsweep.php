<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Shipments: Due for Sweep';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label'=> 'Due for sweep')
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

<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class="clearfix">
            <form class="form-inline pull-right clearfix">
                <div class="pull-left form-group">
                    <label for="searchInput">Search</label><br>
                    <div class="input-group input-group-sm input-group-search">
                        <input id="searchInput" type="text" name="search" placeholder="" class="search-box form-control">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <?php if(!empty($parcels)): ?>
            <div class="pull-left">
                <label>&nbsp;</label><br>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="manifest">Generate Manifest</button>
                <!--<button type="button" onclick="javascript:window.print();" class="btn btn-sm btn-default">Generate Sweep Run</button>-->
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="main-box-body">
        <?php if(!empty($parcels)): ?>
        <div class="table-responsive">
            <table id="next_dest" class="table table-hover">
                <thead>
                <tr>
                    <th style="width: 20px"><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>
                    <th style="width: 20px">No.</th>
                    <th>Waybill No.</th>
                    <th>Shipper</th>
                    <th>Receiver</th>
                    <th>Final Destination</th>
                    <th>Created Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($parcels) && is_array($parcels)){
                    $i = $offset;
                    foreach($parcels as $parcel){
                        ?>
                        <tr data-waybill='<?php echo Calypso::getValue($parcel, 'waybill_number'); ?>'
                                data-to-branch-id='<?php echo Calypso::getValue($parcel, 'to_branch.id'); ?>'
                                data-to-branch-name='<?php echo Calypso::getValue($parcel, 'to_branch.name'); ?>'
                            >
                            <td>
                                <div class='checkbox-nice'>
                                    <input name='waybills[]' value="<?= $parcel['waybill_number'] ?>" id='chk_<?php echo ++$i; ?>' type='checkbox' class='chk_next'><label for='chk_<?php echo $i; ?>'></label>
                                </div>
                            </td>
                            <td><?= $i ?></td>
                            <td><?= strtoupper($parcel['waybill_number']); ?></td>
                            <td><?= strtoupper($parcel['sender']['firstname'].' '. $parcel['sender']['lastname']) ?></td>
                            <td><?= strtoupper($parcel['receiver']['firstname'].' '. $parcel['receiver']['lastname']) ?></td>
                            <td><?= ucwords(Calypso::getValue($parcel, 'receiver_address.city.name')) . ', ' .
                                    ucwords(Calypso::getValue($parcel, 'receiver_address.state.name')); ?>
                            </td>
                            <td><?= date(ServiceConstant::DATE_TIME_FORMAT,strtotime($parcel['created_date'])); ?></td>
                            <td><a href="<?= Url::to(['site/viewwaybill?id='.$parcel['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                        </tr>
                    <?php
                    }}
                ?>
                </tbody>
            </table>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
        </div>
        <?php else:  ?>
            There are no parcels matching the specified criteria.
        <?php endif;  ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="genManifest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="forsweep">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Generate Sweep Manifest</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="payload" name="payload" />
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="dlg_location">Location</label>
                                <input class="form-control input-sm" id="dlg_location" />
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="searchInput">Sweeper Staff ID</label><br>
                                <div class="input-group input-group-sm input-group-search">
                                    <input id="staff" type="text" name="search_staff" placeholder="" class="search-box form-control">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" id="btnSearch" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="staff_info" style="display: none;">
                        <hr />
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Staff Name</label>
                                <p id="staff_name">Staff Name</p>
                            </div>
                            <div class="col-xs-6">
                                <label>Email</label>
                                <p id="staff_email">Role</p>
                            </div>
                            <div class="col-xs-6">
                                <label>Phone Number</label>
                                <p id="staff_phone">Staff Name</p>
                            </div>
                            <div class="col-xs-6">
                                <label>Role</label>
                                <p id="staff_role">Role</p>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <table class="table table-bordered table-condensed" id="tbl_manifest">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Final Destination</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnGenerate">Generate</button>
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
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php //$this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/ec_forsweeper.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>


