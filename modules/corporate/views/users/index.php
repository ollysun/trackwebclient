<?php
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Corporate User Summary';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['corporate/requests'],
        'label' => 'Corporate'
    ),
    array('label' => 'Users')
);
?>


<?php

$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add User</button>';
?>

<?php echo Calypso::showFlashMessages(); ?>

    <div class="main-box">
        <div class="main-box-header table-search-form clearfix">
            <div class=" clearfix">
                <div class="pull-left">

                </div>
                <div class="pull-right clearfix">
                    <form class="form-inline clearfix">
                        <div class="pull-left form-group">
                            <label for="searchInput">Search</label><br>

                            <div class="input-group input-group-sm input-group-search">
                                <input id="searchInput" type="text" name="search" placeholder="Email address"
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
                <?php if (count($users) > 0): ?>
                    <table id="table" class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width: 20px">No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone No.</th>
                            <th>Role</th>
                            <th>Status</th>
                            <!--                            <th>Action</th>-->
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = $offset; foreach ($users as $user): ?>
                            <tr>
                                <td><?= ++$i; ?></td>
                                <td><?= Calypso::getValue($user, 'firstname') . " " . Calypso::getValue($user, 'lastname') ?></td>
                                <td><?= Calypso::getValue($user, 'email'); ?></td>
                                <td><?= Calypso::getValue($user, 'phone_number') ?></td>
                                <td><?= Util::formatRoleName(Calypso::getValue($user, 'role.name', '')); ?></td>
                                <td><?= ServiceConstant::getStatus(Calypso::getValue($user, 'status')); ?></td>
                                <!--                                <td><a href=""-->
                                <!--                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>-->
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]); ?>
                <?php else: ?>
                    There are no users in this company.
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form data-keyboard-submit="disable" class="validate-form" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create a new user</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">First Name</label>
                                    <input name="firstname" type="text"
                                           class="form-control validate required name active-validate">

                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Last Name</label>
                                    <input type="text" name="lastname"
                                           class="form-control validate required name active-validate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Email address</label>
                                    <input type="text" name="email"
                                           class="form-control validate active-validate required email">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Phone number</label>
                                    <input type="text" name="phone_number"
                                           class="form-control validate active-validate required phone">
                                    <span class="help-block">Format: 234xxxxxxxxxx</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-6">
                                <label for="">Role</label>
                                <select name="role_id" class="form-control">
                                    <option value="<?= ServiceConstant::USER_TYPE_COMPANY_ADMIN ?>">Admin</option>
                                    <option selected value="<?= ServiceConstant::USER_TYPE_COMPANY_OFFICER ?>">Officer
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-xs-6 hide">
                                <label for="">Activate user?</label>
                                <select name="status" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>