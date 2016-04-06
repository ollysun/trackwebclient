<?php
use Adapter\Util\Calypso;

/* @var $this yii\web\View */
$this->title = 'Bulk Invoice Task - #' . $task_id;
$this->params['breadcrumbs'] = array(
    array('label' => 'Bulk Invoice Tasks', 'url' => 'bulk'),
    array(
        'label' => 'Bulk Invoice Task - #' . $task_id
    )
);
?>

<div class="main-box">
    <div class="main-box-body">
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th class="datatable-nosort">#</th>
                    <th>Company ID</th>
                    <th>Company Name</th>
                    <th>Invoice Number</th>
                    <th>Address</th>
                    <th>Address To</th>
                    <th>Reference</th>
                    <th>Currency</th>
                    <th>Started at</th>
                    <th>Completed at</th>
                    <th>Status</th>
                    <th>Message</th>
                    <th>Parcels</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $details = Calypso::getValue($task, 'details', []);
                $serial_number = 0;
                foreach ($details as $detail):
                    $data = json_decode(Calypso::getValue($detail, 'data', '{}'));
                    ?>
                    <tr>
                        <td><?= ++$serial_number ?></td>
                        <td><a target="_blank" href="/admin/viewcompany?id=<?= Calypso::getDisplayValue($data, 'company_id', '') ?>"><?= Calypso::getDisplayValue($data, 'company_id', 'N/A') ?></a></td>
                        <td><?= Calypso::getDisplayValue($detail, 'company_name', 'N/A') ?></td>
                        <td><?= Calypso::getDisplayValue($detail, 'invoice_number', 'N/A') ?></td>
                        <td><?= Calypso::getDisplayValue($data, 'address', 'N/A') ?></td>
                        <td><?= Calypso::getDisplayValue($data, 'to_address', 'N/A') ?></td>
                        <td><?= Calypso::getDisplayValue($data, 'reference', 'N/A') ?></td>
                        <td><?= Calypso::getDisplayValue($data, 'currency', 'N/A') ?></td>
                        <td><?= Calypso::getDisplayValue($detail, 'started_at', 'N/A') ?></td>
                        <td><?= Calypso::getDisplayValue($detail, 'completed_at', 'N/A') ?></td>
                        <td>
                            <?php if (Calypso::getValue($detail, 'status', '') == 'in_progress'): ?>
                                <span class="label label-warning">IN PROGRESS</span>
                            <?php elseif (Calypso::getValue($detail, 'status', '') == 'failed'): ?>
                                <span class="label label-danger"><?= strtoupper(Calypso::getValue($detail, 'status', '')) ?></span>
                            <?php elseif (Calypso::getValue($detail, 'status', '') == 'success'): ?>
                                <span class="label label-success"><?= strtoupper(Calypso::getValue($detail, 'status', '')) ?></span>
                            <?php else: ?>
                                <?= strtoupper(Calypso::getValue($detail, 'status', '')) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= Calypso::getDisplayValue($detail, 'error_message', '') ?></td>
                        <td><a data-view-parcels href="#" class="btn btn-default" data-parcels='<?= \yii\helpers\Json::encode(Calypso::getValue($data, 'parcels', [])) ?>' data-toggle="modal" data-target="#parcelsModal">View</a> </td>
                        <?php if(is_null(Calypso::getDisplayValue($detail, 'invoice_number', 'N/A'))):?>
                            <td><a class="btn btn-primary" target="_blank" href="/finance/printinvoice?invoice_number=<?= Calypso::getDisplayValue($detail, 'invoice_number', '') ?>">Print</a></td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
<div class="modal fade" id="parcelsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Parcels</h4>
            </div>
            <div class="modal-body">
                <ul id="parcels">

                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/finance/bulk_invoice_task_details.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>