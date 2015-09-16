<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

$this->title = 'Manifest: #'.$id;
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['manifest/index'],
        'label' => 'Manifests'
    ),
    array('label'=> 'View '.$this->title)
);

$this->params['content_header_button'] = '<a href="'.Url::to(['manifest/print?id='.$id]).'" class="btn btn-primary"><i class="fa fa-print"></i> Print Manifest</a>';

?>
<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>



<div class="main-box no-header">
    <div class="main-box-body">
        <div class="row">
            <div class="col-xs-4">
                <label for="">Origin Station</label>
                <div class="form-control-static"><?= strtoupper(Calypso::getValue($manifest, 'from_branch.code'));?> </div>
            </div>
            <div class="col-xs-4">
                <label for="">Destination</label>
                <div class="form-control-static"><?= strtoupper(Calypso::getValue($manifest, 'to_branch.code'));?> </div>
            </div>
            <div class="col-xs-4">
                <label for="">Created Date</label>
                <div class="form-control-static"><?= date(ServiceConstant::DATE_TIME_FORMAT, strtotime(Calypso::getValue($manifest, 'created_date'))); ?></div>
            </div>
            <div class="col-xs-4">
                <label for="">Driver</label>
                <div class="form-control-static"><?= ucwords(Calypso::getValue($manifest, 'holder.fullname'));?> (<?= Calypso::getValue($manifest, 'holder.staff_id');?>)</div>
            </div>
            <div class="col-xs-4">
                <label for="">Driver Phone no</label>
                <div class="form-control-static"><?= Calypso::getValue($manifest, 'holder.phone');?></div>
            </div>
            <div class="col-xs-4">
                <label for="">Prepared by</label>
                <div class="form-control-static"><?= ucwords(Calypso::getValue($manifest, 'sender_admin.fullname'));?> (<?=Calypso::getValue($manifest, 'sender_admin.staff_id');?>)</div>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th width="16%">WAYBILL NO</th>
                    <th width="16%">DESTINATION</th>
                    <th width="8%">PCS</th>
                    <th width="8%">WT</th>
                    <th width="16%">SHIPPER</th>
                    <th width="35%">DESCRIPTION OF SHIPMENT(S)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2N0000000001</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2N0000000001</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2N0000000001</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2N0000000001</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2N0000000001</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2N0000000001</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2N0000000001</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2N0000000001</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td style="border-left-color: transparent; border-bottom-color: transparent;" colspan="2">TOTAL</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


