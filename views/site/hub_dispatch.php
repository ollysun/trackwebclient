<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Shipments: Dispatched';
$this->params['breadcrumbs'] = array(
	/*array(
	'url' => ['site/managebranches'],
	'label' => 'Manage Branches'
	),*/
	array('label'=> 'Dispatched Shipments')
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
			<div class="pull-left">
				<form class="clearfix">
					<div class="pull-left form-group form-group-sm">
						<label for="">From:</label><br>
						<input name="from" id="" class="form-control date-range" value="" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
					</div>
					<div class="pull-left form-group form-group-sm">
						<label for="">To:</label><br>
						<input name="to" id="" class="form-control date-range"  value="" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
					</div>
				</form>
			</div>

			<div class="pull-right clearfix">
				<form class="table-search-form form-inline clearfix">
					<div class="pull-left form-group">
						<label for="searchInput">Filter next location</label><br>
						<select class="form-control input-sm">
							<option>Ibadan</option>
							<option>Lagos</option>
							<option>Kaduna</option>
						</select>
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
						<th>Sweeper</th>
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
						<td></td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>
</div>


<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>