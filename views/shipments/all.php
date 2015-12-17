<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


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
                <?= $this->render('../elements/parcels_filter',['from_date'=>$from_date,'to_date'=>$to_date,'page_width'=>$page_width,'filter'=>$filter, 'hideStatusFilter'=>true]) ?>
            </div>
            <div class="pull-right clearfix">

                <form class="table-search-form form-inline clearfix">
                    <div class="pull-left">
                        <label for="searchInput">Search</label><br>
                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="search" placeholder="Waybill Number" class="search-box form-control">
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
        <?php if(count($parcels)) : ?>
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Shipper</th>
                    <th>Shipper Phone</th>
                    <th>Receiver</th>
                    <th>Request Type</th>
                    <th>Created Date</th>
                    <th>Pieces</th>
                    <th>Return Status</th>
                    <th>Shipment Status</th>
                    <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                        <th>Originating Branch</th>
                        <th>Current Location</th>
                    <?php } ?>
                    <th>Shipment Age</th>
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
                            <td><?= ++$i; ?></td>
                            <td><?= strtoupper($parcel['waybill_number']); ?></td>
                            <td><?= strtoupper($parcel['sender']['firstname'].' '. $parcel['sender']['lastname']) ?></td>
                            <td><?= $parcel['sender']['phone'] ?></td>
                            <td><?= strtoupper($parcel['receiver']['firstname'].' '. $parcel['receiver']['lastname']) ?></td>
                            <td><?= ServiceConstant::getRequestType($parcel['request_type']) ?></td>
                            <td><?= date(ServiceConstant::DATE_TIME_FORMAT,strtotime($parcel['created_date'])); ?></td>
                            <td><?= $parcel['no_of_package']; ?></td>
                            <td><?= ServiceConstant::getReturnStatus($parcel); ?></td>
                            <td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
                            <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                                <td><?= strtoupper(Calypso::getValue($parcel, "created_branch.name")) ?></td>
                                <td><?= ParcelAdapter::getCurrentLocation($parcel); ?></td>
                            <?php } ?>
                            <td><?= ParcelAdapter::getAgeAnalysis($parcel); ?></td>
                            <td><a href="<?= Url::toRoute(['/shipments/view?waybill_number='.$parcel['waybill_number']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
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

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]])?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]])?>

