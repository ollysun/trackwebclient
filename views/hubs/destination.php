<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;

/* @var $this yii\web\View */
if (!isset($isGroundsman)) {
    $isGroundsman = false;
}
$this->title = 'Shipments: Next Destination' . ($isGroundsman ? ' - GroundsMan' : '');
$this->params['breadcrumbs'] = array(
    /*array(
    'url' => ['site/managebranches'],
    'label' => 'Manage Branches'
    ),*/
    array('label' => 'Ready for Sorting')
);

$user_data = $this->context->userData;
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>
<style>
    .table.next_dest tbody > tr > td {
        text-align: center;
    }
</style>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>


<div class="main-box">
    <div class="main-box-header table-search-form">
        <div class="clearfix">
            <div class="pull-left">
                    <div class="pull-left form-group">
                        <label for="branch_type">Branch type</label><br>
                        <select id="branch_type" class="form-control input-sm" name="branch_type">
                            <?php if (!$isGroundsman): ?>
                                <option value="hub">Hub</option>
                            <?php else: ?>
                                <option value="" selected>Select...</option>
                                <option value="route">Route</option>
                            <?php endif; ?>
                            <option value="exp" <?php echo($isGroundsman ? '' : 'selected') ?> >Express Centres</option>
                        </select>
                    </div>
                    <div class="pull-left form-group">
                        <label for="branch_name" id="hub_branch_label">Branch Name</label><br>
                        <select id="branch_name" class="form-control input-sm" name="branch">
                            <option value="">Select Name...</option>
                        </select>
                    </div>
                    <div class="pull-left">
                        <label for="">&nbsp;</label><br>
                        <button type="submit" class="btn btn-sm btn-default" id="btn_apply_dest">Apply</button>
                    </div>
            </div>

            <?php if ($isGroundsman): ?>
                <div class="pull-right">
                    <br/>
                    <?= $this->render('../elements/parcel/parcel_unsort_button'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="main-box-body">
        <?php if (!empty($parcel_next)) { ?>
            <div class="table-responsive">
                <form method="post" id="table_form">
                    <input type="hidden" id="form_branch_type" name="branch_type"/>
                    <input type="hidden" id="form_branch_name" name="branch"/>
                <table id="next_dest" class="table table-hover next_dest">
                    <thead>
                    <tr>
                        <th style="width: 20px;">
                            <div class='checkbox-nice'>
                                <input id='chk_all' type='checkbox' class='chk_all'><label for='chk_all'></label>
                            </div>
                        </th>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No</th>
                        <th>Origin</th>
                        <th>Next Destination</th>
                        <th>Final Destination</th>
                        <th>Reference No</th>
                        <th>Request Type</th>
                        <th>Return Status</th>
                        <th>Weight/Piece</th>
                        <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                            <th>Originating Branch</th>
                            <th>Current Location</th>
                        <?php } ?>
                        <th>Age analysis</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $row = $offset;
                    foreach ($parcel_next as $parcels) {
                        ++$row;
                        ?>
                        <tr data-waybill='<?= $parcels['waybill_number'] ?>'>
                            <td>
                                <div class='checkbox-nice'>
                                    <input name='waybills[]' id='chk_<?= $row; ?>' type='checkbox'
                                           class='chk_next'><label
                                        for='chk_<?= $row; ?>'></label>
                                </div>
                            </td>
                            <td><?= $row; ?></td>
                            <td>
                                <a href='/shipments/view?waybill_number=<?= Calypso::getValue($parcels, 'waybill_number'); ?>'><?= Calypso::getValue($parcels, 'waybill_number') ?></a>
                            </td>
                            <td><?= ucwords(Calypso::getValue($parcels, 'sender_address.city.name') . ', ' . Calypso::getValue($parcels, 'sender_address.state.name')); ?></td>
                            <td></td>
                            <td>
                                <?php if (!is_null(Calypso::getDisplayValue($parcels, 'receiver_address.street_address1'))): ?>
                                    <?= trim(Calypso::getValue($parcels, 'receiver_address.street_address1', ''), ',') ?>
                                    <?= ', ' ?>
                                <?php endif; ?>


                                <?php if (!is_null(Calypso::getDisplayValue($parcels, 'receiver_address.street_address2'))): ?>
                                    <?= trim(Calypso::getValue($parcels, 'receiver_address.street_address2', ''), ',') ?>
                                    <?= ', ' ?>
                                <?php endif; ?>

                                <?= ucwords(Calypso::getValue($parcels, 'receiver_address.city.name') . ', ' . Calypso::getValue($parcels, 'receiver_address.state.name')); ?>
                            </td>
                            <td><?= Calypso::getValue($parcels, 'reference_number') ?></td>
                            <td><?= ServiceConstant::getRequestType($parcels['request_type']) ?></td>
                            <td><?= ServiceConstant::getReturnStatus($parcels); ?></td>
                            <td><?= Calypso::getValue($parcels, 'weight') ?></td>
                            <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                                <td><?= strtoupper(Calypso::getValue($parcels, "created_branch.name")) ?></td>
                                <td><?= ParcelAdapter::getCurrentLocation($parcels); ?></td>
                            <?php } ?>
                            <td></td>
                            <td><?= $this->render('../elements/parcel/partial_return_button', ['parcel' => $parcels, 'reasons_list' => $reasons_list]) ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                    </form>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'total_count' => $total_count, 'page_width' => $page_width]) ?>

        <?php } else { ?>
            <p>No record to display.</p>
        <?php } ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Generate Dispatch Manifest</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Next Destination</label>
                                <select class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Staff ID</label>
                                <input class="form-control">
                            </div>
                        </div>
                    </div>
                    <br>
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Final Destination</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->render('../elements/parcel/partial_return_form') ?>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/next_destination.js?v1.0.2', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/return.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
