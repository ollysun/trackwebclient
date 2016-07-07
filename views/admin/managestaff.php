<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Manage Staff Accounts';
$this->params['breadcrumbs'] = array(
	array('label'=> 'Manage Staff Accounts')
);
$show_next = false;
$show_prev = false;

if( count($staffMembers) >= $page_width ){
    $show_next = true;
}else{
    $show_next = false;
}
$role_filter = '';$page_role = '-1';
if(!empty($role) && $role != '-1') {
    $role_filter = '&role='.$role;
    $page_role=$role;
}

if($offset <= 0){
    $show_prev = false;
}elseif (($offset - $page_width) >= 0){
    $show_prev = true;
}
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
$this->params['content_header_button'] = '<button type="button" id="addNewStaffBtn" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add New Staff</button>';
?>

<?=Html::cssFile('@web/css/libs/bootstrap-select.min.css')?>

<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form">
        <div class="clearfix">
            <div class="form-group pull-left">
                <label for="">Filter by user role</label><br>
                <select id="role_filter" class="form-control input-sm">
                    <option value="-1">Select Filter...</option>
                    <option value="-1">ALL</option>
                    <?php
                    //roles
                    if(!empty($roles) && is_array($roles)){
                        foreach($roles as $item){
                            ?>
                            <option <?= $item['id']== $page_role?'selected':'' ?> value="<?= $item['id'] ?>"><?= strtoupper($item['name']) ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="pull-right clearfix">
                <form method="get" enctype="application/x-www-form-urlencoded" class="table-search-form form-inline clearfix">
                    <div class="pull-left form-group">
                        <label for="searchInput">Search</label><br>

                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="search" placeholder="Enter Staff ID or Staff Email"
                                   class="search-box form-control">

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
            <?php if(count($staffMembers) > 0) {?>
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Name</th>
                    <th>Email address</th>
                    <th>Phone Number</th>
                    <th>Staff ID</th>
                    <th>Branch</th>
                    <th>User role</th>
                    <th>Status</th>
                    <th style="width:6%;">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(!empty($staffMembers) && is_array($staffMembers)) {
                    $i = 1;$count = $offset + 1;
                    foreach($staffMembers as $staffMember):
                    ?>
                    <tr>
                        <td><?= $count++; ?></td>
                        <td><?= strtoupper($staffMember['fullname']) ?></td>
                        <td><?= $staffMember['email'] ?></td>
                        <td><?= $staffMember['phone'] ?></td>
                        <td><?= $staffMember['staff_id'] ?></td>
                        <td><?= strtoupper($staffMember['branch']['name'].' ('.$staffMember['branch']['code'].')') ?></td>
                        <td><?= strtoupper($staffMember['role']['name']) ?></td>
                        <td><?= ServiceConstant::getStatus($staffMember['status']) ?></td>
                        <td>
                            <span data-toggle="tooltip" title="Edit">
                            <button
                                data-id="<?= Calypso::getValue($staffMember, 'id'); ?>"
                                data-fullname="<?= Calypso::getValue($staffMember, 'fullname'); ?>"
                                data-email="<?= Calypso::getValue($staffMember, 'email'); ?>"
                                data-phone="<?= Calypso::getValue($staffMember, 'phone'); ?>"
                                data-staff_id="<?= Calypso::getValue($staffMember, 'staff_id'); ?>"
                                data-branch="<?= Calypso::getValue($staffMember, 'branch.id'); ?>"
                                data-branch_type="<?= Calypso::getValue($staffMember, 'branch.branch_type'); ?>"
                                data-role="<?= Calypso::getValue($staffMember, 'role_id'); ?>"
                                data-state="<?= Calypso::getValue($staffMember, 'branch.state_id'); ?>"
                                data-status="<?= Calypso::getValue($staffMember, 'status'); ?>"
                                type="button" class="btn btn-default btn-xs editStaff" data-toggle="modal"
                                    data-target="#myModal"><i class="fa fa-edit"></i>
                            </button></span>
                            <span data-toggle="tooltip" title="Reset password">
                                <button
                                    data-auth-id="<?= Calypso::getValue($staffMember, 'user_auth_id'); ?>"
                                    type="button" class="btn btn-default btn-xs resetPassword" data-toggle="modal"
                                    data-target="#reset"><i class="fa fa-refresh"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                    <?php
                        endforeach;
                    }
                ?>
                </tbody>
            </table>
            <div class="pull-right form-group">
                <?php if($show_prev): ?>
                    <a href="<?= Url::toRoute(['/admin/managestaff?offset='.($offset - $page_width)]).$role_filter ?>" class="btn btn-primary btn-sm">Prev</a>
                <?php endif;  ?>
                <?php if($show_next): ?>
                    <a href="<?= Url::toRoute(['/admin/managestaff?offset='.($offset + $page_width)]).$role_filter ?>" class="btn btn-primary btn-sm">Next</a>
                <?php endif;  ?>
            </div>
            <?php } else {  ?>
                <div class="alert alert-info text-center" role="alert">
                    <p><strong>No staff account found</strong></p>
                </div>
            <?php }  ?>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post" id="createStaffForm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add a New Staff Account</h4>
                </div>
                <div class="modal-body">
                    <p>Please fill the following information carefully. <strong>All fields are required.</strong></p>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>First name</label>
                                <input name="firstname" class="form-control validate required name">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Last name</label>
                                <input name="lastname" class="form-control  validate required name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Email address</label>
                                <input name="email" class="form-control validate required email">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Phone no</label>
                                <input name="phone" class="form-control validate required phone">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Staff ID</label>
                        <input name="staff_id" class="form-control validate required">
                    </div>
                    <div class="row">

                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Branch type</label>
                                <select id="branch_type" name="branch_type" class="form-control validate required">
                                    <option value="">Select ...</option>
                                    <option value="1">HQ</option>
                                    <option value="2">Hub</option>
                                    <option value="4">EC</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>State</label>
                                <select id="state" name="state" class="form-control validate required">
                                    <?php
                                    if (isset($states) && is_array($states) && !empty($states)):
                                        foreach ($states as $state):
                                            ?>
                                            <option
                                                value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
                                            <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-5">
                            <div class="form-group">
                                <label>Branch</label>
                                <select id="branch" name="branch" class="form-control validate required">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>User role</label>
                                <select name="role" class="form-control validate required">
                                    <?php

                                    if (isset($roles) && is_array($roles) && !empty($roles)):
                                        foreach ($roles as $role):
                                            ?>
                                            <option
                                                value="<?= $role['id'] ?>"><?= strtoupper($role['name']); ?></option>
                                            <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Activate user?
                                    <small>(Users can be activated later)</small>
                                </label>
                                <select name="status" class="form-control  validate required">
                                    <option value="1">YES</option>
                                    <option value="2">NO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input name="id" type="hidden"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Staff Account</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="reset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post" id="resetPasswordForm" action="resetpassword">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                </div>
                <div class="modal-body">
                    <p>Please fill the following information carefully. <strong>All fields are required.</strong></p>
                    <div class="form-group">
                        <label>New Password</label>
                        <input name="password" type="password" class="form-control validate required">
                    </div>
                </div>
                <div class="modal-footer">
                    <input name="user_auth_id" type="hidden"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
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
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/staff.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>


