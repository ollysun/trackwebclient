<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 12/7/2016
 * Time: 9:56 AM
 */

use yii\helpers\Html;
use yii\web\JqueryAsset;
use app\assets\AppAsset;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use Adapter\Globals\ServiceConstant;
use yii\helpers\Url;


$this->title = 'Sales Tellers';
$this->params['breadcrumbs'] = array(
    array('label' => 'Finance')
);
?>

<?= Html::cssFile('@web/css/libs/select2.css') ?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>


<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                <form method="get">
                    <link href="/css/libs/datepicker.css" rel="stylesheet">
                    <div class="clearfix">
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Creation Date</label><br>
                            <input name="start_created_date" class="form-control date-range" data-provide="datepicker"
                                   data-date-format="yyyy/mm/dd" data-date-end-date="0d"
                                   value="<?= $start_created_date; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for=""></label><br>
                            <input name="end_created_date" class="form-control date-range" data-provide="datepicker"
                                   data-date-format="yyyy/mm/dd" data-date-end-date="0d"
                                   value="<?= $end_created_date; ?>">
                        </div>

                        <div class="pull-left form-group form-group-sm">
                            <label for="">Branch</label><br>
                            <select name="branch_id" id="" class="form-control filter-status">
                                <option value="">All</option>
                                <?php foreach ($branches as $branch) { ?>
                                    <option
                                            value="<?= Calypso::getValue($branch, 'id'); ?>"
                                        <?= $branch_id == Calypso::getValue($branch, 'id') ? 'selected' : '' ?>>
                                        <?= strtoupper(Calypso::getValue($branch, 'name')); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="pull-left form-group form-group-sm">
                            <label for="">Status</label><br>
                            <select name="status" id="" class="form-control filter-status">
                                <option value="">All</option>
                                <?php foreach ($statuses as $sta) { ?>
                                    <option
                                        value="<?= $sta; ?>" <?= $status == $sta ? 'selected' : '' ?>><?= ServiceConstant::getStatus($sta); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="pull-left">
                            <label>&nbsp;</label><br>
                            <button class="btn btn-default btn-sm" id="apply" type="submit"><i class="fa fa-filter"></i>
                                APPLY
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="pull-right clearfix">
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if (!empty($tellers)): ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Bank</th>
                        <th>Account No.</th>
                        <th>Teller No.</th>
                        <th>Amount</th>
                        <th>Created Date</th>
                        <th>Paid By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = $offset;
                    if (isset($tellers) && is_array($tellers)) {
                        foreach ($tellers as $teller) {
                            ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= strtoupper(Calypso::getValue($teller, 'bank.name')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($teller, 'account_no')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($teller, 'teller_no')); ?></td>
                                <td><span class="currency naira"></span><?= Calypso::getValue($teller, 'amount_paid') ?></td>
                                <td><?= Util::formatDate(ServiceConstant::DATE_FORMAT, Calypso::getValue($teller, 'created_date')); ?></td>
                                <td><?= Calypso::getValue($teller, 'payer.fullname') ?></td>
                                <td><?= ServiceConstant::getStatus(Calypso::getValue($teller, 'status')) ?></td>

                                <td>
                                    <?php if(Calypso::getValue($teller, 'status') != ServiceConstant::TELLER_APPROVED) :?>
                                    <a title="Approve teller" href="<?= Url::toRoute(['/finance/approvesalesteller?id=' . Calypso::getValue($teller, 'id')]) ?>"
                                       class="btn btn-xs btn-success">Approve</a>
                                    <?php endif;?>

                            <?php if(Calypso::getValue($teller, 'status') != ServiceConstant::TELLER_DECLINED) :?>
                                <a title="Reject teller" href="<?= Url::toRoute(['/finance/delinesalesteller?id=' . Calypso::getValue($teller, 'id')]) ?>"
                                       class="btn btn-xs btn-danger">Decline</a>
                            <?php endif;?>
                                    <a href="?id=<?= Calypso::getValue($teller, 'id') ?>" class="btn btn-xs btn-primary">View</a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        <?php else: ?>
            There are no tellers matching the specified criteria.
        <?php endif; ?>
    </div>
</div>

<?php $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/select2.js', ['depends' => [AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/utils.js', ['depends' => [JqueryAsset::className()]]) ?>

<?php $this->registerJsFile('@web/js/teller.js', ['depends' => [JqueryAsset::className()]]) ?>
