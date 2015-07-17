<?php
use yii\helpers\Html;
use yii\helpers\Url;

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

<div class="main-box">
	<div class="main-box-header table-search-form">
		<form class="form-inline clearfix">
			<div class="pull-left">
				<?= $this->render('../elements/branch_type_filter', ['branch_type'=>'hub']) ?>
			</div>

			<div class="pull-right clearfix">
				<div class="form-group pull-left">
					<label for="">Filter by State</label><br>
					<select class="form-control input-sm">
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
				<div class="pull-left">
					<label for="">&nbsp;</label><br>
					<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add Hub</button>
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
						<td><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i> Edit</button></td>
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


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a New Hub</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group">
					<label>Hub name</label>
					<input class="form-control">
				</div>
				<div class="form-group">
					<label>State</label>
					<select class="form-control"></select>
				</div>
				<div class="form-group">
					<label>Address</label>
					<input class="form-control">
					<input class="form-control address-line-1">
				</div>
				<div class="form-group">
					<label>Activate Hub?</label>
					<select class="form-control">
						<option>Yes</option>
						<option>No</option>
					</select>
				</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Create Hub</button>
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
	        <h4 class="modal-title" id="myModalLabel">Edit a Hub</h4>
	      </div>
	      <div class="modal-body">
				<div class="form-group">
					<label>Hub name</label>
					<input class="form-control">
				</div>
				<div class="form-group">
					<label>State</label>
					<select class="form-control"></select>
				</div>
				<div class="form-group">
					<label>Address</label>
					<input class="form-control">
					<input class="form-control address-line-1">
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
