<?php
/**
 * Author: Adeyemi Olaoye <yemi@cottacush.com>
 * Date: 11/01/2016
 * Time: 1:41 PM
 */
use Adapter\Util\Calypso;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Bulk Shipment Tasks';
$this->params['breadcrumbs'] = array(
    array(
        'label' => 'Bulk Shipment Tasks'
    )
);
?>
<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-body">
        <?php if ($tasks): ?>
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Company</th>
                    <th>Billing Plan</th>
                    <th>Created By</th>
                    <th>Created at</th>
                    <th>Started at</th>
                    <th>Completed at</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = $offset;
                foreach ($tasks as $task):
                    $i++;
                    $jobData = json_decode(Calypso::getValue($task, 'job_data', '{}'));
                    ?>
                    <tr>
                        <td><?= Calypso::getValue($task, 'id', '') ?></td>
                        <td><?= strtoupper(Calypso::getValue($jobData, 'company.name', '')) ?></td>
                        <td><?= strtoupper(Calypso::getValue($jobData, 'billing_plan.name', '')) ?></td>
                        <td><?= ucwords(Calypso::getValue($jobData, 'creator.fullname', '')) ?></td>
                        <td><?= strtoupper(Calypso::getValue($task, 'created_at', '')) ?></td>
                        <td><?= strtoupper(Calypso::getDisplayValue($task, 'started_at', 'N/A')) ?></td>
                        <td><?= strtoupper(Calypso::getDisplayValue($task, 'completed_at', 'N/A')) ?></td>
                        <td>
                            <?php if (Calypso::getValue($task, 'status', '') == 'queued'): ?>
                                <span
                                    class="label label-info"><?= strtoupper(Calypso::getValue($task, 'status', '')) ?></span>
                            <?php elseif (Calypso::getValue($task, 'status', '') == 'in_progress'): ?>
                                <span class="label label-warning">IN PROGRESS</span>
                            <?php elseif (Calypso::getValue($task, 'status', '') == 'failed'): ?>
                                <span
                                    class="label label-danger"><?= strtoupper(Calypso::getValue($task, 'status', '')) ?></span>
                            <?php elseif (Calypso::getValue($task, 'status', '') == 'success'): ?>
                                <span
                                    class="label label-success"><?= strtoupper(Calypso::getValue($task, 'status', '')) ?></span>
                            <?php else: ?>
                                <?= strtoupper(Calypso::getValue($task, 'status', '')) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-default"
                               href="<?= Url::to('/shipments/bulk?task_id=' . Calypso::getValue($task, 'id', '')) ?>"><i
                                    class="glyphicon glyphicon-eye-open"></i> View Details
                            </a>
                            <?php if(Calypso::getValue($task, 'status', '') == 'success'):?>
                            <button class="print_btn btn btn-primary"
                                    data-status="<?= strtoupper(Calypso::getValue($task, 'status', '')) ?>"
                                    data-create_task_url="<?= '/shipments/printbulkshipment?task_id='. Calypso::getValue($task, 'id', '') ?>"
                                    data-print_url="<?= $s3_base_url . 'waybills_task_' . Calypso::getValue($task, 'id', '') . '.pdf' ?>">
                                <i class="glyphicon glyphicon-print"></i> Print
                            </button>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/utils.js?v1.1.0', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/bulk_shipment_tasks.js?v1.1.1', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>


