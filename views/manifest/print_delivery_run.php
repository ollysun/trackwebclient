<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;

?>

<?= Html::cssFile('@web/css/compiled/print-manifest.css') ?>

<div class="manifest">
	<div class="manifest-header">
		<?= Html::img('@web/img/logo.jpg', ['class' => 'logo pull-left']) ?>
		<h3 class="pull-right manifest-title">SHIPMENT DELIVERY RECORD</h3>
	</div>
	<div class="manifest-header-box text-uppercase clearfix">
		<div class="row">
			<div class="col-xs-4 clearfix">
				<div class="pull-left inline-underline-title">Delivery Date </div>
				<div class="inline-underline pull-left inline-underline-sm"></div>
			</div>
			<div class="col-xs-4 clearfix">
				<div class="inline-underline pull-right inline-underline-sm"></div>
				<div class="pull-right inline-underline-title">Station</div>
			</div>
			<div class="col-xs-4 clearfix">
				<div class="inline-underline pull-right inline-underline-xs"></div>
				<div class="pull-right inline-underline-title">Sheet number</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-4 clearfix">
				<div class="pull-left inline-underline-title">Courier Route </div>
				<div class="inline-underline"></div>
			</div>
			<div class="col-xs-4 pull-right clearfix">
				<div class="inline-underline pull-right inline-underline-xs"></div>
				<div class="pull-right inline-underline-title">Total Sheets</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-8 clearfix">
				<div class="pull-left inline-underline-title">Courier Name </div>
				<div class="inline-underline inline-underline-full"></div>
			</div>
			<div class="col-xs-4 clearfix">
				<div class="inline-underline pull-right inline-underline-xs"></div>
				<div class="pull-right inline-underline-title">Total Shipments</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-5 clearfix">
				<div class="pull-left inline-underline-title">Checked by (Name) </div>
				<div class="inline-underline inline-underline-full"></div>
			</div>
			<div class="col-xs-3 clearfix">
				<div class="inline-underline pull-right inline-underline-sm"></div>
				<div class="pull-right inline-underline-title">Date</div>
			</div>
			<div class="col-xs-4 clearfix">
				<div class="inline-underline pull-right inline-underline-xs"></div>
				<div class="pull-right inline-underline-title">Total Pieces</div>
			</div>
		</div>
	</div>
	<br>
	<div class="manifest-body rotate-90">
		<div class="row">
			<div class="col-xs-8"></div>
			<div class="col-xs-4">
				<h3 style="margin: 10px 0 5px !important;">NO: 002819128</h3>
			</div>
		</div>
		<table class="table table-bordered delivery-run-table table-condensed">
			<thead>
				<tr>
					<th class="show-only-right-cell-border" rowspan="2"></th>
					<th colspan="4">
						SHIPMENT INFORMATION
						<span><br>TO BE ENTERED AT STATION</span>
					</th>
					<th colspan="3">DELIVERY INFORMATION</th>
					<th></th>
				</tr>
				<tr>
					<th width="220px;">CONSIGNEE NAME</th>
					<th width="170px;">AIRWAY BILL NUMBER</th>
					<th width="140px;">ORIGIN CODE</th>
					<th width="110px;">NO PCS.</th>
					<th width="120px;">TIME</th>
					<th width="90px;">CD</th>
					<th width="250px;">NAME OF PERSON <br> RECEIVING SHIPMENT</th>
					<th width="180px;">SIGNATURE</th>
				</tr>
			</thead>
			<tbody>
			<?php for ($i=1; $i <= 24 ; $i++) {  ?>
				<tr>
					<td class="show-only-right-cell-border"><?= ($i < 10)?'0'.$i:''.$i; ?></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>