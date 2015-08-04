<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Manage Hubs';
$this->params['breadcrumbs'] = array(
	array(
	'url' => ['site/managebranches'],
	'label' => 'Manage Branches'
	),
	array('label'=> 'Hubs')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages();?>
<div class="main-box">
	<div class="main-box-header table-search-form">
		<form class="form-inline clearfix" id="state_filter" method="post">
			<div class="pull-left">
				<?= $this->render('../elements/branch_type_filter', ['branch_type'=>'hub']) ?>
			</div>
			<div class="pull-right clearfix">
				<div class="form-group pull-left">
					<label for="">Filter by State</label><br>
					<select class="form-control input-sm" name="filter_state_id" id="filter_state_id">
						<option value="">All States</option>
                        <?php
                        if(isset($States) && is_array(($States))):
                            foreach($States as $state){
                        ?>
                            <option value="<?= $state['id'] ?>"<?= ($state['id']==$filter_state_id) ? 'selected':''; ?>><?= strtoupper($state['name']); ?></option>
                        <?php
                            }
                        endif;
                        ?>
					</select>
					<input type="hidden" name="task" value="filter">
				</div>
				<div class="pull-left">
					<label for="">&nbsp;</label><br>
					<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add Hub</button>
				</div>
			</div>
		</form>
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover dataTable">
				<thead>
					<tr>
						<th style="width: 20px">S/N</th>
						<th>Hub Name</th>
						<th>Hub Code</th>
						<th>State</th>
						<th>Address</th>
						<th>Created Date</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
                    <?php

                    if(isset($hubs) && is_array(($hubs))):
                        $count=1; foreach($hubs as $hub){
                    ?>
					<tr class="text-center">
						<td><?= $count++; ?></td>
						<td class="n<?= $hub['id']; ?>"><?= $hub['name']; ?></td>
						<td><?= strtoupper($hub['code']); ?></td>
						<td><?= strtoupper($hub['state']['name']); ?></td>
						<td class="a<?= $hub['id']; ?>"><?= $hub['address']; ?></td>
						<td><?= date('Y/m/d @ H:m',strtotime($hub['created_date'])); ?></td>
						<td><?= ($hub['status']==ServiceConstant::ACTIVE?'Active':'Inactive'); ?></td>
						<td data-id="<?= $hub['id']; ?>"data-state-id="<?= $hub['state']['id']; ?>" data-status="<?= $hub['status']; ?>"><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i> Edit</button> <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#status" data-id="<?= $hub['id']; ?>"><i class="fa fa-edit"></i> Change Status</button></td>
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


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="validate" method="post" action="#">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a New Hub</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group">
					<label>Hub name</label>
					<input class="form-control" name="name">
				</div>
				<div class="form-group">
					<label>State</label>
					<select class="form-control" name="state_id">
                        <?php
                        if(isset($States) && is_array(($States))):
                            foreach($States as $state){
                        ?>
                            <option value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
                        <?php
                            }
                        endif;
                        ?>
					</select>
				</div>
				<div class="form-group">
					<label>Address</label>
					<textarea class="form-control" name="address" rows="2"></textarea>
				</div>
				<div class="form-group">
					<label>Status</label>
					<select class="form-control" name="status">
						<option value="<?= ServiceConstant::ACTIVE?>">Active</option>
						<option value="<?= ServiceConstant::INACTIVE?>">Inactive</option>
					</select>
				</div>
	      </div>
	      <div class="modal-footer">
			  <input type="hidden" name="task" value="create">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Create Hub</button>
	      </div>
	    </div>
	  	</form>
  </div>
</div>


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<form class="" method="post" action="">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Edit a Hub</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Hub name</label>
						<input class="form-control" name="name">
					</div>
					<div class="form-group">
						<label>State</label>
						<select class="form-control" name="state_id">
							<?php
							if(isset($States) && is_array(($States))):
								foreach($States as $state){
									?>
									<option value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
									<?php
								}
							endif;
							?>
						</select>
					</div>
					<div class="form-group">
						<label>Address</label>
						<textarea class="form-control" name="address" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="id">
					<input type="hidden" name="task" value="edit">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</form>
	</div>
</div>


<div class="modal fade" id="status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<form class="" method="post" action="">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Change Hub Status</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Status</label>
						<select class="form-control" name="status">
							<option value="<?= ServiceConstant::ACTIVE?>">Active</option>
							<option value="<?= ServiceConstant::INACTIVE?>">Inactive</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="id">
					<input type="hidden" name="task" value="status">
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
<?php $this->registerJsFile('@web/js/manage_branches.js', ['depends' => [\app\assets\AppAsset::className()]])?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>


