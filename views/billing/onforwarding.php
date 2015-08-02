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
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add an Onforwarding Charge</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header">
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table id="table" class="table table-hover ">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Amount (<span class="currency naira"></span>)</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($charges) && is_array(($charges))):
                    $row = 1;
                    foreach ($charges as $charge) {
                        ?>
                        <tr>
                        <td><?= $row++; ?></td>
                        <td class="n<?= $charge['id'];?>"><?=$charge['name']; ?></td>
                        <td class="c<?= $charge['id'];?>"><?=$charge['code']; ?></td>
                        <td class="d<?= $charge['id'];?>"><?=$charge['description']; ?></td>
                        <td class="a<?= $charge['id'];?>"><?=$charge['amount']; ?></td>
                        <td>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                    data-target="#editModal" data-id="<?= $charge['id']; ?>"><i class="fa fa-edit"></i>
                                Edit
                            </button>
                        </td>
                        </tr><?php
                    }
                endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate" method="post">
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
                            <input type="text" class="form-control" name="onforward_name">
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="">Onforwading Code</label>
                            <input type="text" class="form-control" name="onforward_code">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-4">
                            <label for="">Base Price (<span class="currency naira"></span>)</label>
                            <input type="text" class="form-control" name="onforward_amount">
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="">Percentage (%)</label>
                            <input type="text" class="form-control" name="onforward_percentage">
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="">Amount (<span class="currency naira"></span>)</label>
                            <input type="text" class="form-control" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="onforward_desc"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
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
        <form class="validate" method="post">
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
                                <input type="text" class="form-control required" name="onforward_name">
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="">Onforwading Code</label>
                                <input type="text" class="form-control required" name="onforward_code">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-4">
                                <label for="">Base Price (<span class="currency naira"></span>)</label>
                                <input type="text" class="form-control required number" name="onforward_amount">
                            </div>
                            <div class="form-group col-xs-4">
                                <label for="">Percentage (%)</label>
                                <input type="text" class="form-control required" name="onforward_percentage">
                            </div>
                            <div class="form-group col-xs-4">
                                <label for="">Amount (<span class="currency naira"></span>)</label>
                                <input type="text" class="form-control" readonly="readonly">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="onforward_desc"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="task" value="edit">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
        </form>
    </div>
</div>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/onforwarding.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
