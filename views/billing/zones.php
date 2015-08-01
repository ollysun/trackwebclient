<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Billing: Zones';
$this->params['breadcrumbs'] = array(
	array(
		'label' => 'Billing',
		'url' => ['billing/']
	),
	array('label'=> 'Zones')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add a new zone</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
	<div class="main-box-header">
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover ">
				<thead>
					<tr>
						<th style="width: 20px">S/N</th>
						<th>Code</th>
						<th>Name</th>
						<th>Type</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if (isset($zones) && is_array(($zones))):
					$row = 1;
					foreach ($zones as $zone) {
						?>
						<tr>
							<td><?= $row++; ?></td>
							<td class="c<?= $zone['id']; ?>"><?= $zone['code']; ?></td>
							<td class="n<?= $zone['id']; ?>"><?= $zone['name']; ?></td>
							<td class="d<?= $zone['id']; ?>"><?= $zone['description']; ?></td>
							<td>
								<button type="button" class="btn btn-default btn-xs" data-toggle="modal"
										data-target="#editModal" data-id="<?= $zone['id']; ?>"><i
										class="fa fa-edit"></i> Edit
								</button>
							</td>
						</tr>
						<?php
					}
				endif;
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="" method="post">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a new Zone</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group">
					<label for="">Name</label>
					<input type="text" class="form-control" name="zone_name">
				</div>
				<div class="form-group">
					<label for="">Code</label>
					<input type="text" class="form-control" name="zone_code">
				</div>
				<div class="form-group">
					<label for="">Description</label>
					<textarea class="form-control" name="zone_desc"></textarea>
				</div>
				<div class="form-group">
					<label for="">Type</label>
					<select class="form-control" disabled="disabled" name="zone_type">
						<option>Custom</option>
					</select>
				</div>
	      </div>
	      <div class="modal-footer">
			  <input type="hidden" name="task" value="create">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Add Zone</button>
	      </div>
	    </div>
	  	</form>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="" method="post">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Edit Zone</h4>
	      </div>
			<div class="modal-body">
				<div class="form-group">
					<label for="">Name</label>
					<input type="text" class="form-control" name="zone_name">
				</div>
				<div class="form-group">
					<label for="">Code</label>
					<input type="text" class="form-control" name="zone_code">
				</div>
				<div class="form-group">
					<label for="">Description</label>
					<textarea class="form-control" name="zone_desc"></textarea>
				</div>
				<div class="form-group">
					<label for="">Type</label>
					<select class="form-control" disabled="disabled" name="zone_type">
						<option>Custom</option>
					</select>
				</div>
			</div>
	      <div class="modal-footer">
			  <input type="hidden" name="task" value="edit">
			  <input type="hidden" name="id" value="">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Save changes</button>
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
<?php $this->registerJsFile('@web/js/zone.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
