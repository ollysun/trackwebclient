<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
$this->title = 'Draft Sortings';
$this->params['breadcrumbs'] = array(
    array('label' => 'Draft Sortings')
);

?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>
<style>
    .table tbody > tr > td {
        text-align: center;
    }
</style>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>

<?php
$this->params['content_header_button'] = '<button type="button" id="create_draft_bag" class="btn btn-default btn-sm"><i class="fa fa-suitcase"></i> Create Draft Bag</button> <button type="button" id="confirm_sorting" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Confirm</button>
<button type="button" class="btn btn-danger btn-sm" id="discard_sorting"><i class="fa fa-times"></i> Discard</button>
';
?>

<div class="main-box">
    <div class="main-box-body">
        <?php if ($draft_sorts): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th style="width: 20px;">
                            <div class='checkbox-nice'>
                                <input id='chk_all' type='checkbox' class='chk_all'><label for='chk_all'></label>
                            </div>

                        </th>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill Number</th>
                        <th>Origin</th>
                        <th>Next Destination</th>
                        <th>Final Destination</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $row = $offset;
                    foreach ($draft_sorts as $draft_sort):
                        ++$row;
                        ?>
                        <tr data-sortnumber='<?= Calypso::getValue($draft_sort, 'sort_number') ?>'
                            data-waybill='<?= Calypso::getValue($draft_sort, 'waybill_number') ?>'
                            data-nextdestination='<?= ucwords(Calypso::getDisplayValue($draft_sort, 'to_branch.name', '')) ?>'
                            data-tobranchid='<?= Calypso::getValue($draft_sort, 'to_branch.id', '') ?>'>
                            <td>
                                <?php if (Calypso::getValue($draft_sort, 'waybill_number', false)): ?>
                                    <div class='checkbox-nice'>
                                        <input id='chk_<?= $row; ?>' type='checkbox'
                                               class='chk_next'><label
                                            for='chk_<?= $row; ?>'></label>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= $row; ?></td>

                            <?php if (Calypso::getValue($draft_sort, 'waybill_number', false)): ?>
                                <td>
                                    <a href='/shipments/view?waybill_number=<?= Calypso::getDisplayValue($draft_sort, 'waybill_number', ''); ?>'><?= Calypso::getValue($draft_sort, 'waybill_number') ?></a>
                                </td>
                            <?php else: ?>
                                <td><?= Calypso::getValue($draft_sort, 'sort_number') ?></td>
                            <?php endif; ?>

                            <td><?= ucwords(Calypso::getDisplayValue($draft_sort, 'from_branch.name', '')) ?></td>
                            <td><?= ucwords(Calypso::getDisplayValue($draft_sort, 'to_branch.name', '')) ?></td>
                            <td>
                                <?php if (Calypso::getValue($draft_sort, 'waybill_number', false)): ?>
                                    <?= (Calypso::getDisplayValue($draft_sort, 'receiver_address.street_address1', false) ? ucwords(Calypso::getDisplayValue($draft_sort, 'receiver_address.street_address_1')) . ', ' : '') . (Calypso::getDisplayValue($draft_sort, 'receiver_address.street_address2', false) ? ucwords(Calypso::getDisplayValue($draft_sort, 'receiver_address.street_address_2')) . ', ' : '') . Calypso::getDisplayValue($draft_sort, 'receiver_address_city.name', '') . ', ' . Calypso::getDisplayValue($draft_sort, 'receiver_address_state.name', '') ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!Calypso::getValue($draft_sort, 'waybill_number', false)): ?>
                                    <button class="btn btn-sm btn-primary confirm-bag-action-btn">Confirm</button>
                                    <button class="btn btn-sm btn-danger discard-draft-bag-btn">Discard</button>
                                <?php endif; ?>
                            </td>
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


<div class="modal fade" id="create_draft_bag_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Create Draft Bag</h4>
            </div>
            <div class="modal-body">
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


                <div class="seal-details hide">
                    <p>Seal ID</p>
                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <input class="form-control" type="text" id="seal_id"/>
                        </div>
                    </div>
                </div>

                <hr/>
                <table class="table table-bordered table-condensed" id="bag_parcels_table">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Waybill Number</th>
                        <th>Next Destination</th>
                    </tr>
                    </thead>
                    <tbody id="draft_items"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="create_draft_bag_btn">Create Draft Bag</button>
                <button type="submit" class="btn btn-primary hide" id="confirm_draft_bag_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>


<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/table_util.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/hub_delivery.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/expected_shipments.js', ['depends' => [JqueryAsset::className()]]); ?>
