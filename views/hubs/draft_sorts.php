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
    .table.next_dest tbody > tr > td {
        text-align: center;
    }
</style>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>


<div class="main-box">
    <div class="main-box-body">
        <?php if ($draft_sorts): ?>
            <div class="table-responsive">
                <table id="next_dest" class="table table-hover next_dest">
                    <thead>
                    <tr>
                        <th style="width: 20px;">
                            <div class='checkbox-nice'>
                                <input id='chk_all' type='checkbox' class='chk_all'><label for='chk_all'></label>
                            </div>
                        </th>
                        <th style="width: 20px">S/N</th>
                        <th>Sort Number</th>
                        <th>Waybill Number</th>
                        <th>Next Destination</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $row = $offset;
                    foreach ($draft_sorts as $draft_sort):
                        ++$row;
                        ?>
                        <tr data-sortnumber='<?= Calypso::getValue($draft_sort, 'sort_number') ?>'>
                            <td>
                                <div class='checkbox-nice'>
                                    <input id='chk_<?= $row; ?>' type='checkbox'
                                           class='chk_next'><label
                                        for='chk_<?= $row; ?>'></label>
                                </div>
                            </td>
                            <td><?= $row; ?></td>
                            <td><?= Calypso::getValue($draft_sort, 'sort_number') ?></td>
                            <td>
                                <a href='/shipments/view?waybill_number=<?= Calypso::getValue($draft_sort, 'waybill_number'); ?>'><?= Calypso::getValue($draft_sort, 'waybill_number') ?></a>
                            </td>

                            <td><?= ucwords(Calypso::getValue($draft_sort, 'to_branch.name', '')) ?></td>
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
<?php $this->registerJsFile('@web/js/expected_shipments.js', ['depends' => [JqueryAsset::className()]]); ?>
