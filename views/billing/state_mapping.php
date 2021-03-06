<?php
use Adapter\Globals\ServiceConstant;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Billing: State - Region Mapping';
$this->params['breadcrumbs'] = array(
	array(
		'label' => 'Billing',
		'url' => ['billing/']
	),
	array('label'=> 'State - Region Mapping')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add a State Mapping</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box" id="state-mapping">
	<div class="main-box-header">
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-bordered ">
				<thead>
					<tr>
						<th style="width: 20px">S/N</th>
						<th>Region</th>
						<th>State</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php $a = 1;foreach($output as $arg) {
					for($i=0; $i < count($arg['states']); $i++) {
						?>
					<tr>
						<td><?=$a++?></td>
						<?php if($i==0) { ?><td rowspan="<?= count($arg['states']);?>"><?= ucwords($arg['region']);?></td><?php } ?>
						<td><?=ucwords($arg['states'][$i]['name']);?></td>
						<td><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal" data-id="<?=$arg['states'][$i]['id'];?>" data-region-id="<?=$arg['states'][$i]['region']['id'];?>"><i class="fa fa-edit"></i> Edit</button></td>
					</tr>
					<?php } }
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="" method="post" action="#">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a State Mapping</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group">
					<label>State</label>
					<select class="form-control" name="state_id">
						<?php
						if(isset($states) && is_array(($states))):
							foreach($states as $state){
								?>
								<option value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
								<?php
							}
						endif;
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Region</label>
					<select class="form-control" name="region_id">
						<?php
						if(isset($regions) && is_array(($regions))):
							foreach($regions as $region){
								?>
								<option value="<?= $region['id'] ?>"><?= strtoupper($region['name']); ?></option>
								<?php
							}
						endif;
						?>
					</select>
				</div>
	      </div>
	      <div class="modal-footer">
			  <input type="hidden" name="task" value="create">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Add State Mapping</button>
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
	        <h4 class="modal-title" id="myModalLabel">Edit State Mapping</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group">
					<label>State</label>
					<select class="form-control" disabled name="state">
						<?php
						if(isset($states) && is_array(($states))):
							foreach($states as $state){
								?>
								<option value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
								<?php
							}
						endif;
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Region</label>
					<select class="form-control" name="region_id">
						<?php
						if(isset($regions) && is_array(($regions))):
							foreach($regions as $region){
								?>
								<option value="<?= $region['id'] ?>"><?= strtoupper($region['name']); ?></option>
								<?php
							}
						endif;
						?>
					</select>
				</div>
	      </div>
	      <div class="modal-footer">
			  <input type="hidden" name="state_id">
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
<?php $this->registerJsFile('@web/js/regions.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>