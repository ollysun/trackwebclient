<?php
use yii\helpers\Html;
use Adapter\Util\Calypso;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Intlbilling: Zones';
$this->params['breadcrumbs'] = array(
	array(
		'label' => 'Intlbilling',
		'url' => ['intlbilling/']
	),
	array('label'=> 'Zones')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add a new international zone</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
	<div class="main-box-header">
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover dataTable">
				<thead>
					<tr>
						<th style="width: 20px">S/N</th>
						<th>Code</th>
						<th>Description</th>
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
							<td class="d<?= $zone['id']; ?>"><?= $zone['description']; ?></td>
							<td>
								<button type="button" class="btn btn-default btn-xs" data-toggle="modal"
										data-target="#addCountryModel" data-id="<?= $zone['id']; ?>"><i
										class="fa fa-edit"></i> Add Country
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
					<label for="">Code</label>
					<input type="text" class="form-control" name="zone_code">
				</div>
				<div class="form-group">
					<label for="">Description</label>
					<textarea class="form-control" name="zone_desc"></textarea>
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

<div class="modal fade" id="addCountryModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<form class="" method="post">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
							aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Add Country to Zone (Mapping)</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name">Zone Description</label>
						<input readonly id="zone_desc" class="form-control validate" name="zone_desc"/>
					</div>

					<div class="form-group">
						<label for="">Country</label>
						<select name="country_id" class="form-control validate required">
							<option value="">Select Country</option>
							<?php foreach ($countries as $country): ?>
								<option
									value="<?= Calypso::getValue($country, 'id'); ?>"><?= strtoupper(Calypso::getValue($country, 'name')) ?></option>
							<?php endforeach; ?>
						</select>
					</div>

				</div>
				<div class="modal-footer">
					<input type="hidden" name="zone_id" id="zone_id">
					<input type="hidden" name="task" value="addcountry">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Add Country to Zone</button>
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
<?php $this->registerJsFile('@web/js/country-to-zone.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>

