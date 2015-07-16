<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Manage Hubs';
$this->params['breadcrumbs'] = array(
	array(
		'url' => ['site/managebranches'],
		'label' => 'Manage Branches',
	),
	array('label' => 'Hubs'),
);
?>
<!-- this page specific styles -->
<?=Html::cssFile('@web/css/libs/dataTables.fixedHeader.css')?>
<?=Html::cssFile('@web/css/libs/dataTables.tableTools.css')?>

<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');

?>
<div class="main-box">
	<div class="main-box-header table-search-form">
		<form class="form-inline clearfix">
			<div class="pull-left">
<?=$this->render('../elements/branch_type_filter', ['branch_type' => 'hub'])?>
</div>

			<div class="pull-right clearfix">
				<div class="form-group pull-left">
					<label for="">Filter State</label><br>
					<select class="form-control input-sm">
<?php if (isset($states) && is_array($states['data'])) {
	foreach ($states['data'] as $item) {?>
						<option value="<?=$item['id']?>"><?=strtoupper($item['name']);?></option>
<?php }}?>
</select>
				</div>
				<div class="pull-left">
					<label for="">&nbsp;</label><br>
					<button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Hub</button>
				</div>
			</div>
		</form>
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover ">
				<thead>
					<tr>
						<th style="width: 20px">S/N</th>
						<th>Hub Code</th>
						<th>Hub Name</th>
						<th>State</th>
						<th>State Code</th>
						<th>Address</th>
						<th>Status</th>
						<th>Action</th>
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
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>2</td>
						<td></td>
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
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>


<script type="text/javascript">

</script>