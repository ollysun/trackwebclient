<?php
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\web\View;

$this->title = (empty($manifest))? '': 'Manifest'.$manifest['id'];
?>

<?= Html::cssFile('@web/css/compiled/print-manifest.css') ?>

<?php echo Calypso::showFlashMessages(); ?>

<?php if(!empty($manifest)):?>
<div class="manifest">
	<div class="manifest-header">
		<?= Html::img('@web/img/tnt-cp-logo.png', ['class' => 'big-logo']) ?>
		<div class="clearfix">
			<h3 class="pull-left manifest-title big-logo-title">SHUTTLE CONTROL SHEET</h3>
			<div class="pull-right clearfix">
				<span class="pull-left text-uppercase">Date: </span>
				<div class="inline-underline pull-right">
					<?= date(ServiceConstant::DATE_TIME_FORMAT, strtotime(Calypso::getValue($manifest, 'created_date'))); ?>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-xs-3">
			<?= strtoupper(Calypso::getValue($manifest, 'from_branch.name'));?>
			<hr class="manifest-hr">
			Origin Station
		</div>
		<div class="col-xs-3 col-xs-offset-1">
			<?=strtoupper(Calypso::getValue($manifest, 'to_branch.name'));?>
			<hr class="manifest-hr">
			Destination
		</div>
		<div class="col-xs-4 col-xs-offset-1">
			<?= ucwords(Calypso::getValue($manifest, 'sender_admin.fullname'));?> (<?=Calypso::getValue($manifest, 'sender_admin.staff_id');?>)
			<hr class="manifest-hr">
			Prepared by
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-xs-5 clearfix">
			<span class="pull-left">Vehicle KM Reading: </span>
			<div class="inline-underline pull-left"></div>
		</div>
		<div class="col-xs-5 clearfix">
			<span class="pull-left">Seal Belt No: </span>
			<div class="inline-underline pull-left"></div>
		</div>
	</div>
	<br>
	<div class="manifest-body">
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
				$totalWeight += (int) Calypso::getValue($parcel, 'weight');
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
	<br>
	<div class="manifest-footer">
		<div class="row text-uppercase">
			<div class="col-xs-6 clearfix">
				<div class="pull-left inline-underline-title">Signed out by: </div>
				<div class="inline-underline"></div>
			</div>
			<div class="col-xs-6 clearfix">
				<div class="pull-left inline-underline-title">Departure time: </div>
				<div class="inline-underline"></div>
			</div>
			<div class="col-xs-6 clearfix">
				<div class="pull-left inline-underline-title">Driver's name: </div>
				<div class="inline-underline"><?= $manifest['holder']['fullname'].' ('.$manifest['holder']['staff_id'].')'; ?></div>
			</div>
			<div class="col-xs-6 clearfix">
				<div class="pull-left inline-underline-title">Vehicle No: </div>
				<div class="inline-underline"></div>
			</div>
			<div class="col-xs-6 clearfix">
				<div class="pull-left inline-underline-title">Received by: </div>
				<div class="inline-underline"></div>
			</div>
			<div class="col-xs-6 clearfix">
				<div class="pull-left inline-underline-title">Time: </div>
				<div class="inline-underline"></div>
			</div>
		</div>
	</div>
</div>
<?php $this->registerJs("window.print();", View::POS_READY, 'print'); ?>
<?php endif; ?>
