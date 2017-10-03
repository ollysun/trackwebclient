<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Billing: Pricing';
$this->params['breadcrumbs'] = array(
	array(
		'label' => 'Billing',
		'url' => ['billing/']
	),
	array('label'=> 'Pricing')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" id="add_billing"><i class="fa fa-plus"></i> Add Billing Price</button>';
?>

<div class="main-box">
	<div class="main-box-header">
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-bordered">
				<thead>
					<tr>
						<th style="width: 20px">S/N</th>
						<th>Weight (Kg)</th>
						<th>Zone</th>
						<th>Service Type</th>
						<th>Price</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(isset($billingMatrix)) {

						$row = 1;
						$billingRow = 1;
						foreach ($billingMatrix as $matrix) {
							$billingCount = count($matrix['billing']);
							$billingRow = 1;
							foreach ($matrix['billing'] as $billing) {
								echo '<tr>';
								if($billingRow == 1) {
									echo "<td rowspan='{$billingCount}'>{$row}</td>";
									echo "<td rowspan='{$billingCount}'>{$matrix['weight']['min_weight']} - {$matrix['weight']['max_weight']}</td>";
								}
								//$basePercentage = (float) $billing['base_percentage'] * 100;
								//$incrPercentage = (float) $billing['increment_percentage'] * 100;
								$zone = $billing['zone']['code'];
								$service_type = $billing['parcel_type']['name'];
								echo "<td>{$zone}</td>";
								echo "<td>{$service_type}</td>";
								echo "<td>{$billing['base_amount']}</td>";
								//echo "<td>{$basePercentage} %</td>";
								//echo "<td>{$incrPercentage} %</td>";
								echo "<td>
										<button type='button' data-weight-billing-id='{$billing['id']}'
										class='btn btn-default btn-xs edit_billing'><i class='fa fa-edit'></i></button>&nbsp;&nbsp;
										<button type='button' data-weight-billing-id='{$billing['id']}'
										class='btn btn-danger btn-xs del_billing'>&nbsp;<i class='fa fa-trash-o'></i>&nbsp;</button>
									  </td>";
								echo '</tr>';
								$billingRow++;
							}

							$row++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_pricing" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form id="billing-form" class="validate-form">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a Billing Pricing</h4>
	      </div>
	      <div class="modal-body">
				<div class="row">
					<div class="form-group col-xs-4">
						<label for="weight_range">Select Weight Range</label>
						<select name="weight_range" id="weight_range" class="form-control validate required">
							<option value="">Select a weight range</option>
							<?php
							if(!empty($weightRanges)) {
								foreach ($weightRanges as $weightRange) {
									echo "<option value='{$weightRange['id']}'>{$weightRange['min_weight']} - {$weightRange['max_weight']}</option>";
								}
							}
							?>
						</select>
					</div>
					<div class="form-group col-xs-4">
						<label for="zone">Zones</label>
						<select name="zone" id="zone" class="form-control validate required">
							<option value="">Select a zone</option>
							<?php
							if(!empty($zones)) {
								foreach ($zones as $zone) {
									echo "<option value='{$zone['id']}'>{$zone['code']}</option>";
								}
							}
							?>
						</select>
					</div>

					<div class="form-group col-xs-4">
						<label for="zone">Service Type</label>
						<select name="parcel_type" id="parcel_type" class="form-control validate required">
							<option value="">Select a Parcel Type</option>

                            <option value="5">Express Documents Exp</option>
                            <option value="6">Express Non-Documents Exp</option>
                            <option value="7">Economy Express Exp</option>

                            <option value="8">Express Documents Imp</option>
                            <option value="9">Express Non-Documents Imp</option>
                            <option value="10">Economy Express Imp</option>

						</select>
					</div>
				</div>
				<div class="add-billing-pricing-wrap">
					<!--<h5 id="zone_name">City Express (CE)</h5>-->
					<input type="hidden" id="id" />
					<div style="height: 20px;"></div>
					<div class="row">
						<div class="form-group col-xs-12">
							<label for="base_cost">Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control validate required number" id="base_cost">
							</div>
						</div>
					</div>
				</div>

			  <div style="height: 20px;"></div>
			  <div class="row">
			  	<button type="button" id="save_billing" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
			  </div>

			  <div style="height: 20px;"></div>
			  <h4>Saved Billings</h4>
			  <hr />
			  <table id="dlg_tbl_pricing" class="table table-hover dataTable no-footer" role="grid" aria-describedby="table_info">
				  <thead>
				  <tr role="row">
					  <th>Weight Range</th>
					  <th>Zone</th>
					  <th>Price</th>
				  </thead>
				  <tbody>
				  </tbody>
			  </table>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary" id="refresh">Refresh Page</button>
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
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/intl_pricing.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
