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
                    <th>Address</th>
                    <th>Address To</th>
                    <th>Reference</th>
                    <th>Currency</th>
                    <th>Started at</th>
                    <th>Completed at</th>
                    <th>Status</th>
                    <th>Message</th>
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
                        <td><a target="_blank" href="http://courierplus.local/admin/viewcompany?id=<?= Calypso::getDisplayValue($data, 'company_id', '') ?>"><?= Calypso::getDisplayValue($data, 'company_id', 'N/A') ?></a></td>
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
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>


<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>