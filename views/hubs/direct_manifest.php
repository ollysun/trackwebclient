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
    array('label' => 'Direct Manifest')
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
            <div class="pull-left hide">
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

            <?= $this->render('../elements/parcel_records_filter', ['page_width' => $page_width]) ?>

            <div class="pull-right">
                <br/>
                <button class="btn btn-primary generateManifest" id="manifest">Generate Manifest</button>
            </div>
        </div>
    </div>

    <div class="main-box-body">
        <?php if (!empty($parcel_next)) { ?>
            <div class="table-responsive">
                <input type="hidden" id="form_branch_type" name="branch_type"/>
                <input type="hidden" id="form_branch_name" name="branch"/>
                <input type="hidden" name="return_to_origin" value="1"/>
                <table id="next_dest" class="table table-hover next_dest dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px;" class="datatable-nosort">
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
                        <?php if ($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
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
                                           class='chk_next'/><label
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
                            <td><?= Calypso::getValue($parcels, 'return_reason.comment'); ?></td>
                            <td><?= Calypso::getValue($parcels, 'weight') ?></td>
                            <?php if ($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                                <td><?= strtoupper(Calypso::getValue($parcels, "created_branch.name")) ?></td>
                                <td><?= ParcelAdapter::getCurrentLocation($parcels); ?></td>
                            <?php } ?>
                            <td></td>
                            <td>
                                <?= $this->render('../elements/parcel/partial_return_button', ['parcel' => $parcels, 'reasons_list' => $reasons_list]) ?>
                                <?= $this->render('../elements/parcel/partial_cancel_button', ['waybill_number' => $parcels['waybill_number'], 'status' => $parcels['status']]) ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'total_count' => $total_count, 'page_width' => $page_width]) ?>

        <?php } else { ?>
            <p>No record to display.</p>
        <?php } ?>
    </div>
</div>






<!-- Modal -->
<div class="modal fade" id="genManifest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Generate Dispatch Manifest</h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-xs-6">
                            <div class="pull-left form-group">
                                <label for="branch_name" id="hub_branch_label">Branch Name</label><br>
                                <select id="branch_name" required class="form-control input-sm" name="to_branch_id">
                                    <option value="">Select Name...</option>
                                    <?php foreach($branches as $branch): ?>
                                        <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Staff ID</label>

                                <div class="input-group">
                                    <input class="form-control" id="staff">

                                    <div class="input-group-btn">
                                        <button type="button" id="btn_staff" class="btn btn-default"><i
                                                class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="staff_info" style="display: none;">
                        <hr/>
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
                    <hr/>
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
                    <input id="dm_held_by_id" name="held_by_id" type="hidden">
                    <input id="dm_waybill_numbers" name="waybill_numbers" type="hidden">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnGenerate">Generate</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="createBag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create Bag from Items</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="payload" name="payload"/>

                <div class="row">
                    <div class="col-xs-6 form-group">
                        <label for="parcels_destination">Parcels Destination</label>

                        <div class="form-control-static"><strong id="parcels_destination"></strong></div>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label for="seal_id">SEAL ID</label>
                        <input class="form-control" id="seal_id"/>
                    </div>
                </div>
                <br>

                <p>Set Bag Destination</p>

                <div class="row">
                    <div class="col-xs-6 form-group">
                        <label for="branch_type">Branch type</label><br>
                        <select id="branch_type" class="form-control input-sm branch_type" name="btype">
                            <option value="exp">Express Centres</option>
                            <option value="hub">Hub</option>
                        </select>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label for="to_branch" id="hub_branch_label">Branch Name</label><br>
                        <select id="to_branch" class="form-control input-sm branch_name" name="bid">
                            <option>Select Name...</option>
                        </select>
                    </div>
                </div>
                <hr/>
                <table class="table table-bordered table-condensed" id="bag_parcels_table">
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnBag">Create a Bag</button>
            </div>
        </div>
    </div>
</div>





<?= $this->render('../elements/parcel/partial_cancel_shipment_form') ?>
<?= $this->render('../elements/parcel/partial_return_form') ?>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/direct_manifest.js?v1.0.2', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/return.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>


