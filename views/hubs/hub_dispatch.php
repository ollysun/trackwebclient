<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;

/* @var $this yii\web\View */
$this->title = 'Shipments: Dispatched';
$this->params['breadcrumbs'] = array(
    /*array(
    'url' => ['site/managebranches'],
    'label' => 'Manage Branches'
    ),*/
    array('label' => 'Dispatched Shipments')
);

$user_data = $this->context->userData;
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
//$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add New Staff</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header table-search-form">
        <div class="clearfix">
            <div class="pull-left">
                <form class="clearfix">
                    <div class="pull-left form-group form-group-sm">
                        <label for="">From:</label><br>

                        <div class="input-group input-group-date-range">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input name="from" id="" class="form-control date-range"
                                   value="<?= date('Y/m/d', strtotime($from_date)); ?>" data-provide="datepicker"
                                   data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                        </div>
                    </div>

                    <div class="pull-left form-group form-group-sm">
                        <label for="">To:</label><br>

                        <div class="input-group input-group-date-range">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input name="to" id="" class="form-control date-range"
                                   value="<?= date('Y/m/d', strtotime($to_date)); ?>" data-provide="datepicker"
                                   data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                        </div>
                    </div>
                    <div class="pull-left">
                        <label>&nbsp;</label><br>
                        <button class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="pull-right clearfix">
                <form class="table-search-form form-inline clearfix" method="post" id="filter">
                    <div class="pull-left form-group">
                        <label for="">Filter</label><br>
                        <select class="form-control input-sm" id="to_branch_id" name="to_branch_id">
                            <option value="">All Hubs & ECs</option>
                            <?php
                            if (isset($hubs) && is_array(($hubs))):
                                foreach ($hubs as $hub) {
                                    ?>
                                    <option
                                        value="<?= $hub['id']; ?>" <?= ($hub['id'] == $branch_id) ? 'selected' : ''; ?>><?= ucwords($hub['name']) . " (" . strtoupper($hub['code']) . ")"; ?></option>
                                <?php }
                            endif
                            ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <?php if (count($parcels) > 0) { ?>
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No</th>
                        <th>Reference No</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Weight/Piece</th>
                        <th>Sweeper</th>
                        <th>Return Status</th>
                        <?php if ($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                            <th>Originating Branch</th>
                            <th>Current Location</th>
                        <?php } ?>
                        <th>Age analysis</th>
                        <th style="width: 30px;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($parcels)) {
                        $row = 1;
                        foreach ($parcels as $parcel) {
                            ?>
                            <tr data-waybill='<?= $parcel['waybill_number'] ?>'>
                                <td><?= $row++; ?></td>
                                <td><?= $parcel['waybill_number']; ?></td>
                                <td><?= $parcel['reference_number']; ?></td>
                                <td><?= ucwords($parcel['from_branch']['name']); ?></td>
                                <td><?= ucwords($parcel['to_branch']['name']); ?></td>
                                <td><?= $parcel['weight']; ?> KG</td>
                                <td><?= ucwords($parcel['holder']['fullname']); ?></td>
                                <td><?= $parcel['return_reason']['comment']?></td>
                                <?php if ($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                                    <td><?= strtoupper(Calypso::getValue($parcel, "created_branch.name")) ?></td>
                                    <td><?= ParcelAdapter::getCurrentLocation($parcel); ?></td>
                                <?php } ?>
                                <td></td>
                                <td>
                                    <a href="<?= Url::toRoute(['/shipments/view?waybill_number=' . $parcel['waybill_number']]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View
                                    </a>
                                    <button title="Clone this shipment"
                                            data-href="<?= Url::toRoute(['/parcels/new?id=' . $parcel['id']]) ?>"
                                            class="btn btn-xs btn-info btnClone"><i class="fa fa-copy"></i></button>
                                    <?= $this->render('../elements/parcel/partial_return_button', ['parcel' => $parcel, 'reasons_list' => $reasons_list]) ?>
                                    <?= $this->render('../elements/parcel/partial_cancel_button', ['waybill_number' => $parcel['waybill_number'], 'status' => $parcel['status']]) ?>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No dispatched shipments found</p>
            <?php } ?>
        </div>
    </div>
</div>

<?php
$ex = '
    $("select#to_branch_id").on("change", function (event) {
        $("form#filter").submit();
    });';
$this->registerJs($ex, View::POS_READY);
?>

<?= $this->render('../elements/parcel/partial_cancel_shipment_form') ?>

<?= $this->render('../elements/parcel/partial_return_form') ?>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?><?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/submit_teller.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
