<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Billing: Onforwarding Charges';
$this->params['breadcrumbs'] = array(
	array('label'=> 'Onforwarding Charges')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add an Onforwarding Charge</button>';
?>

<div class="main-box">
	<div class="main-box-header">
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover ">
				<thead>
					<tr>
						<th style="width: 20px">S/N</th>
						<th>Name</th>
						<th>Base Price (<span class="currency naira"></span>)</th>
						<th>Base Percentage (%)</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>Some name</td>
						<td>FA</td>
						<td>2000</td>
						<td><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i> Edit</button></td>
					</tr>
					<tr>
						<td>2</td>
						<td>Another name</td>
						<td>FB</td>
						<td>2200</td>
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
	        <h4 class="modal-title" id="myModalLabel">Add an Onforwarding Charge</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group">
					<label for="">Name</label>
					<input type="text" class="form-control">
				</div>
				<div class="row">
					<div class="form-group col-xs-6">
						<label for="">Base Price (<span class="currency naira"></span>)</label>
						<input type="text" class="form-control">
					</div>
					<div class="form-group col-xs-6">
						<label for="">Base Percentage  (%)</label>
						<input type="text" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label>Activate Onforwarding Charge?</label>
					<select class="form-control">
						<option>Yes</option>
						<option>No</option>
					</select>
				</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Add Onforwarding Charge</button>
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
	        <h4 class="modal-title" id="myModalLabel">Edit an Onforwarding Charge</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group">
					<label for="">Name</label>
					<input type="text" class="form-control">
				</div>
				<div class="row">
					<div class="form-group col-xs-6">
						<label for="">Base Price (<span class="currency naira"></span>)</label>
						<input type="text" class="form-control">
					</div>
					<div class="form-group col-xs-6">
						<label for="">Base Percentage  (%)</label>
						<input type="text" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label>Status</label>
					<select class="form-control">
						<option>Active</option>
						<option>Inactive</option>
					</select>
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
