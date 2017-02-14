<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Intlbilling: Weight Ranges';
$this->params['breadcrumbs'] = array(
	array(
		'label' => 'Intlbilling',
		'url' => ['intlbilling/']
	),
	array('label'=> 'Weight Ranges')
);
?>


<?php
	$this->params['content_header_button'] =
        '<div class="col-md-4">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus"></i> Add Range
            </button>
        </div>
        <div class="col-md-8">
            <form method="post" action="/billing/deleteweightranges">
                <input type="hidden" name="range_ids" id="rangeIds">
                <div class="col-md-4">
                    <input value="1" name="force_delete" type="checkbox"><label
                                        for="force_delete"> Force </label>
                </div>
                
                <div class="col-md-8">
                    <button id="deleteRangeBtn" type="submit" class="btn btn-danger">
                        <i class="fa fa-times"></i> Delete Ranges
                    </button>
                </div>
            </form>
        </div>  
        ';
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
                        <th style="width: 20px" class="datatable-nosort">
                            <div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label
                                        for="chbx_w_all"> </label></div>
                        </th>
						<th style="width: 20px">S/N</th>

						<th>Minimum Weight (Kg)</th>
						<th>Maximum Weight (Kg)</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if (isset($ranges)) {
					$row = 1;
					foreach ($ranges as $range) {
						?>
						<tr>
                            <td>
                                <div class="checkbox-nice">

                                    <input id="chbx_w_<?= $row; ?>" class="checkable"
                                           data-id="<?= $range['id']; ?>"
                                           type="checkbox"><label
                                            for="chbx_w_<?= $row; ?>"> </label>
                                </div>
                            </td>

							<td><?= $row++; ?></td>

							<td class="l<?=$range['id'];?>"><?= $range['min_weight']; ?></td>
							<td class="m<?=$range['id'];?>"><?= $range['max_weight']; ?></td>
							<td>
								<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal" data-id="<?= $range['id']; ?>"><i class="fa fa-edit"></i> Edit</button>
								<form method="post" action="<?= Url::to("/intlbilling/deleteweightrange");?>">
									<input type="hidden" value="<?= $range['id']; ?>" name="range_id" />
									<button type="button" class="btn btn-danger btn-xs deleteWeightRange"><i class="fa fa-trash-o"></i> Delete</button>
								</form>
							</td>
						</tr>
					<?php }
				} ?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="validate-form" method="post">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a Weight Range</h4>
	      </div>
	      <div class="modal-body">
				<div class="row">
					<div class="form-group col-xs-4">
						<label for="">Min Weight</label>
						<div class="input-group">
							<input type="text" class="form-control validate required number" name="min_weight">
							<span class="input-group-addon">Kg</span>
						</div>
					</div>
					<div class="form-group col-xs-4">
						<label for="">Incremental Weight</label>
						<div class="input-group">
							<input type="text" class="form-control validate required number" name="increment_weight">
							<span class="input-group-addon">Kg</span>
						</div>
					</div>
					<div class="form-group col-xs-4">
						<label for="">Max Weight</label>
						<div class="input-group">
							<input type="text" class="form-control validate required number" name="max_weight">
							<span class="input-group-addon">Kg</span>
						</div>
					</div>
				</div>
	      </div>
	      <div class="modal-footer">
			  <input type="hidden" name="task" value="create">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Add Weight Range</button>
	      </div>
	    </div>
	  	</form>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="validate-form" method="post">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Edit Weight Range</h4>
	      </div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group col-xs-4">
						<label for="">Min Weight</label>
						<div class="input-group">
							<input type="text" class="form-control validate required number" name="min_weight">
							<span class="input-group-addon">Kg</span>
						</div>
					</div>
					<div class="form-group col-xs-4">
						<label for="">Incremental Weight</label>
						<div class="input-group">
							<input type="text" class="form-control validate required number" name="increment_weight">
							<span class="input-group-addon">Kg</span>
						</div>
					</div>
					<div class="form-group col-xs-4">
						<label for="">Max Weight</label>
						<div class="input-group">
							<input type="text" class="form-control validate required number" name="max_weight">
							<span class="input-group-addon">Kg</span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="id" value="">
				<input type="hidden" name="task" value="edit">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save Changes</button>
			</div>
	    </div>
	  	</form>
  </div>
</div>

<!-- this page specific scripts -->
<?php //$this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php //$this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php //$this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php //$this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/weights.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

