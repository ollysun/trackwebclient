<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Util;


/* @var $this yii\web\View */
$this->title = 'All Shipments';
$this->params['breadcrumbs'][] = 'Shipments';


$link = "";
if($search){
    $fro = date('Y/m/d',strtotime($from_date));
    $to = date('Y/m/d',strtotime($to_date));
    $link = "&search=true&to=".urlencode($to)."&from=".urlencode($fro)."&page_width=".$page_width;
    if(!is_null($filter)){$link.= '&date_filter='. $filter;}
}
$user_data = $this->context->userData;
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                <?= $this->render('../../../../views/elements/parcels_filter',['from_date'=>$from_date,'to_date'=>$to_date,'page_width'=>$page_width,'filter'=>$filter, 'hideStatusFilter'=>false]) ?>
            </div>
            <div class="pull-right clearfix">

                <form class="table-search-form form-inline clearfix">
                    <div class="pull-left">
                        <label for="searchInput">Search</label><br>
                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="search" placeholder="Search by Waybill or Reference No." class="search-box form-control">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="pull-left hidden">
                    <label>&nbsp;</label><br>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Select an action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if (!empty($parcels)): ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No.</th>
                        <th>Reference No.</th>
                        <th>Receiver</th>
                        <th>Receiver Phone</th>
                        <th>Created Date</th>
                        <th>Delivery Route</th>
                        <th>Return Status</th>
                        <th>Current Status</th>
                        <th>Last Modified</th>
                        <th>Delivery Type</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = $offset;
                    if (isset($parcels) && is_array($parcels)) {
                        foreach ($parcels as $parcel) {
                            ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= strtoupper(Calypso::getValue($parcel, 'parcel_waybill_number')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($parcel, 'parcel_reference_number')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($parcel, 'receiver_firstname') . ' ' . Calypso::getValue($parcel, 'receiver_lastname')) ?></td>
                                <td><?= Calypso::getValue($parcel, 'parcel_reference_number') ?></td>
                                <td><?= Util::formatDate(ServiceConstant::DATE_TIME_FORMAT, Calypso::getValue($parcel, 'parcel_created_date')); ?></td>
                                <td><?= Calypso::getValue($parcel, 'route_name'); ?></td>
                                <td><?= Calypso::getDisplayValue($parcel, 'parcel_comment_comment'); ?></td>
                                <td><?= ServiceConstant::getStatus($parcel['parcel_status']); ?></td>
                                <td><?= Util::formatDate(ServiceConstant::DATE_TIME_FORMAT, Calypso::getValue($parcel, 'parcel_modified_date')); ?>
                                    (<?= Util::ago(Calypso::getValue($parcel, 'parcel_modified_date')); ?>)
                                </td>
                                <td><?= ucwords(ServiceConstant::getDeliveryType(Calypso::getValue($parcel, 'parcel_delivery_type'))); ?></td>
                                <td>
                                    <a href="<?= Url::toRoute(['/shipments/view?waybill_number=' . Calypso::getValue($parcel, 'parcel_waybill_number')]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i></a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        <?php else: ?>
            There are no parcels matching the specified criteria.
        <?php endif; ?>
    </div>
</div>

<?= $this->render('../../../../views/elements/parcel/partial_cancel_shipment_form') ?>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]])?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]])?>

