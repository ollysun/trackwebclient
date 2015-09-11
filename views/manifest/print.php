<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;

?>

<?= Html::cssFile('@web/css/compiled/print-manifest.css') ?>

<div class="manifest">
	<div class="manifest-header">
		<?= Html::img('@web/img/logo.jpg', ['class' => 'big-logo']) ?>
		<div class="clearfix">
			<h3 class="pull-left manifest-title big-logo-title">SHUTTLE CONTROL SHEET</h3>
			<div class="pull-right clearfix">
				<span class="pull-left text-uppercase">Date: </span>
				<div class="inline-underline pull-right"></div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-xs-3">
			<hr class="manifest-hr">
			Origin Station
		</div>
		<div class="col-xs-3 col-xs-offset-1">
			<hr class="manifest-hr">
			Destination
		</div>
		<div class="col-xs-4 col-xs-offset-1">
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
					<td class="total-cell" colspan="2">TOTAL</td>
					<td></td>
					<td></td>
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
				<div class="inline-underline"></div>
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
