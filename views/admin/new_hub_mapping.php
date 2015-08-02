<?php
use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'New Hub Mapping';
$this->params['breadcrumbs'] = array(
    array('label' => 'New Hub Mapping')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
//$this->params['content_header_button'] = '';
?>

<div class="main-box">
    <div class="main-box-header">
        <h2>Hub mapping for ABK</h2>
    </div>
    <div class="main-box-body">
        <form class="table-responsive">
            <table id="table" class="table table-bordered">
                <thead>
                <tr>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Zone</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="3">ABK</td>
                        <td>IBA</td>
                        <td>
                            <select name="" id="" class="form-control input-sm"></select>
                        </td>
                    </tr>
                    <tr>
                        <td>ILR</td>
                        <td>
                            <select name="" id="" class="form-control input-sm"></select>
                        </td>
                    </tr>
                    <tr>
                        <td>LOS</td>
                        <td>
                            <select name="" id="" class="form-control input-sm"></select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="clearfix">
                <button class="pull-right btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

