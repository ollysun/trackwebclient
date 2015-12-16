<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Bag: #00012420389238';
$this->params['breadcrumbs'] = array(
    /*array(
    'url' => ['site/managebranches'],
    'label' => 'Manage Branches'
    ),*/
    array('label'=> 'View Bag #00012420389238')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>


<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>


    <div class="main-box">

        <form class="clearfix" method="get">
            <div class="main-box-header table-search-form clearfix">
                <div class="pull-right">
                    <label>&nbsp;</label><br>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add item to bag</button>
                </div>
            </div>
        </form>

        <div class="main-box-body">
            <div class="table-responsive">
                <table id="next_dest" class="table table-hover next_dest dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px;"></th>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No</th>
                        <th>Origin</th>
                        <th>Next Destination</th>
                        <th>Final Destination</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>1</td>
                            <td>2039492034920</td>
                            <td>Lagos</td>
                            <td>Ibadan Hub</td>
                            <td>Ibadan</td>
                            <td>
                                <button class="btn btn-xs btn-default"><i class="fa fa-trash-o"></i> Remove from bag</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <form method="post" action="delivery">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add items to Bag</h4>
                </div>
                <div class="modal-body">
                    <table id="next_dest" class="table table-hover next_dest dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px;"></th>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No</th>
                        <th>Origin</th>
                        <th>Next Destination</th>
                        <th>Final Destination</th>
                        <th>Weight/Piece</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class='checkbox-nice'>
                                    <input id='' type='checkbox' class='chk_next'><label for=''></label>
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnGenerate">Add to Bag</button>
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
<?php //$this->registerJsFile('@web/js/hub_delivery.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
