<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Manage Staff Accounts';
$this->params['breadcrumbs'] = array(
	/*array(
	'url' => ['site/managebranches'],
	'label' => 'Manage Branches'
	),*/
	array('label'=> 'Manage Staff Accounts')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add New Staff</button>';
?>

<div class="main-box">
	<div class="main-box-header table-search-form">
		<div class="clearfix">
			<div class="form-group pull-left">
					<label for="">Filter by user role</label><br>
					<select class="form-control input-sm">
						<option>role here</option>
						<option>role here</option>
					</select>
				</div>

			<div class="pull-right clearfix">
				<form class="table-search-form form-inline clearfix">
					<div class="pull-left form-group">
						<label for="searchInput">Search</label><br>
						<div class="input-group input-group-sm input-group-search">
							<input id="searchInput" type="text" name="search" placeholder="" class="search-box form-control">
							<div class="input-group-btn">
								<button class="btn btn-default" type="submit">
									<i class="fa fa-search"></i>
								</button>
						</div>
						</div>
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
						<th>Name</th>
						<th>Email address</th>
						<th>Branch</th>
						<th>User role</th>
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
					</tr>

				</tbody>
			</table>
		</div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form class="" method="post">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a New Staff Account</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label>First name</label>
							<input name="firstname" class="form-control">
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label>Last name</label>
							<input name="lastname" class="form-control">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label>Email address</label>
							<input name="email" class="form-control">
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label>Phone no</label>
							<input name="phone" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label>Staff ID</label>
					<input name="staff_id" class="form-control">
				</div>
				<div class="form-group">
					<label>State</label>
					<select id="state" name="state" class="form-control">
                        <?php
                        if(isset($states) && is_array($states) && !empty($states)):
                            foreach($states as $state):
                        ?>
                            <option value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
                        <?php
                        endforeach;
                        endif;
                        ?>
					</select>
				</div>
				<div class="form-group">
					<label>Branch type</label>
					<select id="branch_type" name="branch_type" class="form-control">
                        <option value="">Select ... </option>
                        <option value="1">HQ</option>
                        <option value="2">Hub</option>
                        <option value="4">EC</option>
					</select>
				</div>
				<div class="form-group">
					<label>Branch</label>
					<select id="branch" name="branch" class="form-control">
					</select>
				</div>
				<div class="form-group">
					<label>User role</label>
					<select name="role" class="form-control">
                        <?php

                        if(isset($roles) && is_array($roles) && !empty($roles)):
                            foreach($roles as $role):
                                ?>
                                <option value="<?= $role['id'] ?>"><?= strtoupper($role['name']); ?></option>
                            <?php
                            endforeach;
                        endif;
                        ?>
					</select>
				</div>
				<div class="form-group">
					<label>Activate user? <small>(Users can be activated later)</small></label>
					<select name="status" class="form-control">
						<option>YES</option>
						<option>NO</option>
					</select>
				</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Create Staff Account</button>
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
	        <h4 class="modal-title" id="myModalLabel">Edit a Staff Account</h4>
	      </div>
	      <div class="modal-body">
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label>First name</label>
							<input class="form-control">
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label>Last name</label>
							<input class="form-control">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label>Email address</label>
							<input class="form-control">
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label>Phone no</label>
							<input class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label>Staff ID</label>
					<input class="form-control">
				</div>
				<div class="form-group">
					<label>State</label>
					<select class="form-control">
					</select>
				</div>
				<div class="form-group">
					<label>Branch type</label>
					<select class="form-control">
					</select>
				</div>
				<div class="form-group">
					<label>Branch</label>
					<select class="form-control">
					</select>
				</div>
				<div class="form-group">
					<label>User role</label>
					<select class="form-control">
					</select>
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
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]])?>
