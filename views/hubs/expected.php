<?php
use Adapter\Util\Calypso;
use app\assets\AppAsset;
use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
$this->title = 'Expected Shipments';
$this->params['breadcrumbs'] = array(
    array('label' => 'Expected Shipments')
);

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
                        <option value="hub">Hub</option>
                        <option value="exp" selected>Express Centres</option>
                    </select>
                </div>
                <div class="pull-left form-group">
                    <label for="branch_name" id="hub_branch_label">Branch Name</label><br>
                    <select id="branch_name" class="form-control input-sm" name="branch">
                        <option>Select Name...</option>
                    </select>
                </div>
                <div class="pull-left">
                    <label for="">&nbsp;</label><br>
                    <button type="submit" class="btn btn-sm btn-default" id="btn_sort_shipment">Apply Draft Sort</button>
                </div>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if ($parcels): ?>
            <div class="table-responsive">
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
                            <th>Request Type</th>
                            <th>Return Status</th>
                            <th>Weight (Kg)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $row = $offset;
                        foreach ($parcels as $parcel):
                            ++$row;
                            ?>
                            <tr data-waybill='<?= $parcel['waybill_number'] ?>'>
                                <td>
                                    <div class='checkbox-nice'>
                                        <input id='chk_<?= $row; ?>' type='checkbox'
                                               class='chk_next'><label
                                            for='chk_<?= $row; ?>'></label>
                                    </div>
                                </td>
                                <td><?= $row; ?></td>
                                <td>
                                    <a href='/shipments/view?waybill_number=<?= Calypso::getValue($parcel, 'waybill_number'); ?>'><?= Calypso::getValue($parcel, 'waybill_number') ?></a>
                                </td>
                                <td><?= ucwords(Calypso::getValue($parcel, 'sender_address.city.name') . ', ' . Calypso::getValue($parcel, 'sender_address.state.name')); ?></td>
                                <td></td>
                                <td>
                                    <?php if (!is_null(Calypso::getDisplayValue($parcel, 'receiver_address.street_address1'))): ?>
                                        <?= trim(Calypso::getValue($parcel, 'receiver_address.street_address1', ''), ',') ?>
                                        <?= ', ' ?>
                                    <?php endif; ?>


                                    <?php if (!is_null(Calypso::getDisplayValue($parcel, 'receiver_address.street_address2'))): ?>
                                        <?= trim(Calypso::getValue($parcel, 'receiver_address.street_address2', ''), ',') ?>
                                        <?= ', ' ?>
                                    <?php endif; ?>

                                    <?= ucwords(Calypso::getValue($parcel, 'receiver_address.city.name') . ', ' . Calypso::getValue($parcel, 'receiver_address.state.name')); ?>
                                </td>


                                <td><?= ServiceConstant::getRequestType($parcel['request_type']) ?></td>
                                <td><?= ServiceConstant::getReturnStatus($parcel); ?></td>
                                <td><?= Calypso::getValue($parcel, 'weight') ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'total_count' => $total_count, 'page_width' => $page_width]) ?>

        <?php else: ?>
            <p>No records to display.</p>
        <?php endif; ?>
    </div>
</div>


<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/next_destination.js', ['depends' => [JqueryAsset::className()]]); ?>
