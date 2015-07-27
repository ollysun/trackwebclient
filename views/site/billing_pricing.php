<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Billing Pricing';
$this->params['breadcrumbs'] = array(
	array('label'=> 'Billings')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add Billing Pricing</button>';
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
						<th>Base Price (<span class="currency naira"></span>)</th>
						<th>Incr. Price (<span class="currency naira"></span>)</th>
						<th>Base Percentage (%)</th>
						<th>Incr. Percentage (%)</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td rowspan="4">1</td>
						<td rowspan="4">0 - 10</td>
						<td>CE</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td rowspan="4"><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i> Edit</button></td>
					</tr>
					<tr>
						<td>IA</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>D</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>NW</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td rowspan="4">2</td>
						<td rowspan="4">10.01 - 20</td>
						<td>CE</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td rowspan="4"><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i> Edit</button></td>
					</tr>
					<tr>
						<td>IA</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>D</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>NW</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a Billing Pricing</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group row">
					<div class="col-xs-6">
						<label for="">Select Weight Range</label>
						<select name="" id="" class="form-control"></select>
					</div>
				</div>
				<div class="form-group add-billing-pricing-wrap">
					<h5>City Express (CE)</h5>
					<div class="row">
						<div class="col-xs-3">
							<label for="">Base Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Incr. Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Base Percentage</label>
							<div class="input-group">
								<input type="text" class="form-control">
								<span class="input-group-addon">%</span>
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Incr. Percentage</label>
							<div class="input-group">
								<input type="text" class="form-control">
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group add-billing-pricing-wrap">
					<h5>Area Express (IA)</h5>
					<div class="row">
						<div class="col-xs-3">
							<label for="">Base Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Incr. Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Base Percentage</label>
							<div class="input-group">
								<input type="text" class="form-control">
								<span class="input-group-addon">%</span>
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Incr. Percentage</label>
							<div class="input-group">
								<input type="text" class="form-control">
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
				</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Add Billing Pricing</button>
	      </div>
	    </div>
	  	</form>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Edit Billing Pricing</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group row">
					<div class="col-xs-6">
						<label for="">Weight Range</label>
						<select name="" id="" class="form-control"></select>
					</div>
				</div>
				<div class="form-group add-billing-pricing-wrap">
					<h5>City Express (CE)</h5>
					<div class="row">
						<div class="col-xs-3">
							<label for="">Base Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Incr. Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Base Percentage</label>
							<div class="input-group">
								<input type="text" class="form-control">
								<span class="input-group-addon">%</span>
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Incr. Percentage</label>
							<div class="input-group">
								<input type="text" class="form-control">
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group add-billing-pricing-wrap">
					<h5>Area Express (IA)</h5>
					<div class="row">
						<div class="col-xs-3">
							<label for="">Base Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Incr. Price</label>
							<div class="input-group">
								<span class="input-group-addon currency naira"></span>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Base Percentage</label>
							<div class="input-group">
								<input type="text" class="form-control">
								<span class="input-group-addon">%</span>
							</div>
						</div>
						<div class="col-xs-3">
							<label for="">Incr. Percentage</label>
							<div class="input-group">
								<input type="text" class="form-control">
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
				</div>

	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Save changes</button>
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
