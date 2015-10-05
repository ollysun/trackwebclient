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

$this->params['content_header_button'] = '<button onclick="javascript:window.open(\''.Url::toRoute(['/manifest/print?id='.$id]).'\', \'_blank\', \'toolbar=yes, scrollbars=yes, resizable=yes, top=10, left=50%, width=1100, height=800\');" class="btn btn-primary"><i class="fa fa-print"></i> Print Manifest</button>';

?>
<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php echo Calypso::showFlashMessages(); ?>

<?php if(!empty($manifest)):?>
<div class="main-box no-header">
    <div class="main-box-body">
        <div class="row">
            <div class="col-xs-4">
                <label for="">Origin Station</label>
                <div class="form-control-static"><?= strtoupper(Calypso::getValue($manifest, 'from_branch.name'));?> </div>
            </div>
            <div class="col-xs-4">
                <label for="">Destination</label>
                <div class="form-control-static"><?= strtoupper(Calypso::getValue($manifest, 'to_branch.name'));?> </div>
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
                <?php
                $totalWeight = 0;
                $totalNumber = 0;
                foreach(Calypso::getValue($manifest, 'parcels', array()) as $parcel):
                $totalNumber += (int) Calypso::getValue($parcel, 'no_of_package');
                $totalWeight += (float) Calypso::getValue($parcel, 'weight');
                    ?>
                <tr>
                    <td><?= Calypso::getValue($parcel, 'waybill_number')?></td>
                    <td><?= strtoupper(Calypso::getValue($parcel, 'destination_name'))?> (<?= strtoupper(Calypso::getValue($parcel, 'destination_code'))?>)</td>
                    <td><?= Calypso::getValue($parcel, 'no_of_package')?></td>
                    <td><?= Calypso::getValue($parcel, 'weight')?> KG</td>
                    <td><?= ucwords(Calypso::getValue($parcel, 'shipper_firstname') . ' ' .  Calypso::getValue($parcel, 'shipper_lastname'))?></td>
                    <td><?= Calypso::getValue($parcel, 'other_info')?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td style="border-left-color: transparent; border-bottom-color: transparent;" colspan="2">TOTAL</td>
                    <td><?= $totalNumber?></td>
                    <td><?= $totalWeight?> KG</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

