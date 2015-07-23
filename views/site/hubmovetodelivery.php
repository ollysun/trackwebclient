<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Shipment: Generate Manifest for Delivery';
$this->params['breadcrumbs'] = array(
    /*array(
    'url' => ['site/managebranches'],
    'label' => 'Manage Branches'
    ),*/
    array('label'=> 'For Delivery')
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

<form class="clearfix" method="post">
    <div class="main-box">
        <div class="main-box-header table-search-form">
            <div class="clearfix">
                <div class="pull-right clearfix">
                    <form class="table-search-form form-inline clearfix">
                        <div class="pull-left form-group">
                            <label for="searchInput">&nbsp;</label><br>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">Generate Manifest</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="main-box-body">
            <div class="table-responsive">
                <table id="next_dest" class="table table-hover next_dest">
                    <thead>
                    <tr>
                        <th style="width: 20px;"></th>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No</th>
                        <th>Origin</th>
                        <th>Next Destination</th>
                        <th>Final Destination</th>
                        <th>Weight (Kg)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    if(isset($parcel_delivery)) {
                        $row = 1;
                        foreach ($parcel_delivery as $parcels) {

                            echo "<tr data-waybill='{$parcels['waybill_number']}'>";
                            echo "<td>
											<div class='checkbox-nice'>
												<input name='waybills[]' id='chk_{$row}' type='checkbox' class='chk_next'><label for='chk_{$row}'></label>
											</div>
										  </td>";
                            echo "<td>{$row}</td>";
                            echo "<td><a href='/site/viewwaybill?id=" . Calypso::getValue($parcels, 'id') . "'>" . Calypso::getValue($parcels, 'waybill_number') . "</a></td>";
                            echo "<td>" . ucwords(Calypso::getValue($parcels, 'sender_address.city') . ', ' . Calypso::getValue($parcels, 'sender_address.state.name')) . "</td>";
                            echo "<td>" . strtoupper(Calypso::getValue($parcels, 'to_branch.name')) ."</td>";
                            echo "<td>" . ucwords(Calypso::getValue($parcels, 'receiver_address.city') . ', ' . Calypso::getValue($parcels, 'receiver_address.state.name')) . "</td>";
                            echo "<td>" . Calypso::getValue($parcels, 'weight') . "</td>";
                            echo "</tr>";
                            $row++;
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Generate Dispatch Manifest</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Location</label>
                                <select class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Staff ID</label>
                                <input class="form-control">
                            </div>
                        </div>
                    </div>
                    <br>
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Final Destination</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Generate</button>
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
<?php //$this->registerJsFile('@web/js/next_destination.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
