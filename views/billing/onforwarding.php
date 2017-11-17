<?php
use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Billing: Onforwarding Charges';
$this->params['breadcrumbs'] = array(
    array(
        'label' => 'Billing',
        'url' => ['billing/']
    ),
    array('label' => 'Onforwarding Charges')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add an Onforwarding Charge</button> <button type="button" class="btn btn-danger" data-billing_plan_id="' . $billingPlanId . '" id="reset_onforwarding_btn"><i class="fa fa-circle-thin"></i> Reset Charges To Zero</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header">
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Base Price (<span class="currency naira"></span>)</th>
                    <th>Percentage (%)</th>
                    <th>Amount (<span class="currency naira"></span>)</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $row = $offset;
                if (isset($charges) && is_array(($charges))):
                    foreach ($charges as $charge) {
                        ?>
                        <tr>
                        <td><?= ++$row; ?></td>
                        <td class="n<?= $charge['id']; ?>"><?= ucfirst($charge['name']); ?></td>
                        <td class="c<?= $charge['id']; ?>"><?= $charge['code']; ?></td>
                        <td class="d<?= $charge['id']; ?>"><?= ucfirst($charge['description']); ?></td>
                        <td class="a<?= $charge['id']; ?>"><?= $charge['amount']; ?></td>
                        <td class="p<?= $charge['id']; ?>"><?= $charge['percentage'] * 100; ?></td>
                        <td class="h<?= $charge['id']; ?>"><?= number_format((float)$charge['amount'] * (1 + floatval($charge['percentage'])), 2, '.', ''); ?></td>
                        <td>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                    data-target="#editModal" data-id="<?= $charge['id']; ?>"
                                    data-billing-plan-id="<?= $charge['billing_plan_id'] ?>"><i class="fa fa-edit"></i>
                                Edit
                            </button>
                        </td>
                        </tr><?php
                    }
                endif;
                ?>
                </tbody>
            </table>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        </div>
    </div>
</div>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add an Onforwarding Charge</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xs-6">
                            <label for="">Name</label>
                            <input type="text" class="form-control validate required" name="onforward_name">
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="">Onforwading Code</label>
                            <input type="text" class="form-control validate required" name="onforward_code">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-4">
                            <label for="">Base Price (<span class="currency naira"></span>)</label>
                            <input type="text" class="form-control validate active-validate required number"
                                   name="onforward_amount">
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="">Percentage (%)</label>
                            <input type="text" class="form-control validate active-validate required integer"
                                   name="onforward_percentage">
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="">Amount (<span class="currency naira"></span>)</label>
                            <input type="text" class="form-control" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control validate required text" name="onforward_desc"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="billing_plan_id" value="<?= $billingPlanId ?>">
                    <input type="hidden" name="task" value="create">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Onforwarding Charge</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit an Onforwarding Charge</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-xs-6">
                                <label for="">Name</label>
                                <input type="text" class="form-control validate required" name="onforward_name">
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="">Onforwading Code</label>
                                <input type="text" class="form-control validate required" name="onforward_code">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-4">
                                <label for="">Base Price (<span class="currency naira"></span>)</label>
                                <input type="text" class="form-control validate active-validate required number"
                                       name="onforward_amount">
                            </div>
                            <div class="form-group col-xs-4">
                                <label for="">Percentage (%)</label>
                                <input type="text" class="form-control validate active-validate required integer"
                                       name="onforward_percentage">
                            </div>
                            <div class="form-group col-xs-4">
                                <label for="">Amount (<span class="currency naira"></span>)</label>
                                <input type="text" class="form-control" readonly="readonly">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control validate required" name="onforward_desc"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="billing_plan_id" value="">
                        <input type="hidden" name="task" value="edit">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
        </form>
    </div>
</div>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/bootbox.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/onforwarding.js?v=1', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

