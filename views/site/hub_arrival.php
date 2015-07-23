<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Shipments: Arrival';
$this->params['breadcrumbs'] = array(
	/*array(
	'url' => ['site/managebranches'],
	'label' => 'Manage Branches'
	),*/
	array('label'=> 'Shipments Arriving')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	//$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add New Staff</button>';
?>

<div class="main-box">
	<div class="main-box-header table-search-form">
		<div class="clearfix">
			<div class="pull-left hidden">
				<form class="clearfix">
					<div class="pull-left form-group">
						<label for="">Branch type</label><br>
						<select class="form-control input-sm">
							<option>Hub</option>
							<option>Express Centre</option>
							<option>Kaduna</option>
						</select>
					</div>
					<div class="pull-left form-group">
						<label for="">Branch Name</label><br>
						<select class="form-control input-sm">
							<option>Ibadan</option>
							<option>Lagos</option>
							<option>Kaduna</option>
						</select>
					</div>
					<div class="pull-left">
						<label for="">&nbsp;</label><br>
						<button type="submit" class="btn btn-sm btn-default">Apply</button>
					</div>
				</form>
			</div>

			<div class="pull-right clearfix">
				<form class="table-search-form form-inline clearfix">
					<div class="pull-left form-group">
						<label for="searchInput">&nbsp;</label><br>
						<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">Receive</button>
					</div>
            </form>
			</div>
		</div>
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover ">
				<thead>
					<tr>
						<th style="width: 20px">S/N</th>
						<th>Waybill No</th>
						<th>Origin</th>
						<th>Destination</th>
						<th>Weight</th>
						<th style="width: 30px;">Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>
							<div class="checkbox-nice">
								<input id="checkbox" type="checkbox"><label for="checkbox"></label>
							</div>
						</td>
					</tr>
					<tr>
						<td>2</td>
						<td></td>
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


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Accept Shipments into Hub</h4>
	      </div>
	      <div class="modal-body">

				<form class="">
					<div class="form-group" style="width: 300px;">
						<label>Staff ID</label>
						<div class="input-group">
							<input id="staff_no" class="form-control">
							<div class="input-group-btn">
								<button type="button" id="get_arrival" class="btn btn-default">Load</button>
							</div>
						</div>
                        <div class="input-group">
                            <label id="loading_label"></label>
                        </div>
					</div>
				</form>

				<br>
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>S/N</th>
							<th>Waybill No.</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody id="parcel_arrival">
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
	        <button type="button" class="btn btn-primary">Accept</button>
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
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]])?>
