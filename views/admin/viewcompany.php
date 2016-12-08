<?php
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Company Details';
$this->params['page_title'] = strtoupper(Calypso::getValue($company, 'name', ''));
$this->params['breadcrumbs'][] = 'Company Details';
?>

<?= Calypso::showFlashMessages(); ?>

<div class="row">
    <div class="col-md-8 col-md-offset-4">
        <div class="main-box no-header">
            <div class="main-box-body">
                <div class="row">
                    <div class="col-md-5">
                        <a class="btn btn-primary" data-toggle="modal" data-target="#manage_application_access">Manage Application Access</a>
                    </div>
                    <div class="col-md-3">
                        <a class="btn btn-primary" data-toggle="modal" data-target="#send_private_key">Send Private Key</a>
                    </div>
                    <div class="col-md-4">
                        <a class="btn btn-primary" data-toggle="modal" data-target="#reset_admin_password">Reset Admin Password</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="main-box no-header">
    <div class="main-box-body row">
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Company Detail</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Name</label>

                        <div
                            class="form-control-static"><?= strtoupper(Calypso::getValue($company, 'name', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Reg No</label>

                        <div class="form-control-static"><?= Calypso::getValue($company, 'reg_no', 'N/A'); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Email Address</label>

                        <div class="form-control-static"><?= Calypso::getValue($company, 'email', ''); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Phone number</label>

                        <div class="form-control-static"><?= Calypso::getValue($company, 'phone_number', ''); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-12">
                        <label for="">Address</label>

                        <div class="form-control-static"><?= Calypso::getValue($company, 'address', ''); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-4">
                        <label>City</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($company, 'city.name', '')); ?></div>
                    </div>
                    <div class="col-xs-4">
                        <label>State</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($company, 'state.name', '')); ?></div>
                    </div>
                    <div class="col-xs-4">
                        <label>Account Type</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($company, 'account_type.code', '') . '-' . Calypso::getValue($company, 'account_type.acronym', '') ); ?></div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Primary Contact</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Name</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'primary_contact.firstname', '')); ?>
                            <?= ucwords(Calypso::getValue($company, 'primary_contact.lastname', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Email Address</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'primary_contact_auth.email', '')); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Phone Number</label>

                        <div
                            class="form-control-static"><?= Calypso::getValue($company, 'primary_contact.phone_number', ''); ?></div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Relations Officer</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Name</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'relations_officer.fullname', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Staff ID</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'relations_officer.staff_id', '')); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Email</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'relations_officer_auth.email', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Phone Number</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'relations_officer.phone', '')); ?></div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Creation Information</legend>
                <div class="form-group">
                    <label>Date &amp; Time</label>

                    <div
                        class="form-control-static"><?= Util::convertDateTimeToTime(Calypso::getValue($company, 'created_date', '')); ?></div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Billing Plans</legend>
                <div class="form-group">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Is Default</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sno = 1; foreach($billing_plans as $plan):?>
                                <tr>
                                    <td><?= ($sno++)?></td>
                                    <td style="text-align: left"><?= ucwords($plan['name']) ?></td>
                                    <td style="text-align: left"><?= ($plan['is_default'] == 1?'Yes':'No')?></td>
                                    <td>
                                        <a href="/billing/markplanasdefault?company_id=<?= $company['id']?>&billing_plan_id=<?=$plan['id']?>" class="btn btn-xs btn-primary">Make Default</a>
                                        <a href="/billing/removecompanyfromplan?company_id=<?= $company['id']?>&billing_plan_id=<?=$plan['id']?>" class="btn btn-xs btn-danger">Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <a class="btn btn-success" data-toggle="modal" data-target="#addPlan">Add Plan</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
            <br><br>
        </div>


    </div>
</div>


<div class="modal fade" id="reset_admin_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post" id="resetCompanyAdminPasswordForm" action="resetcompanyadminpassword">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Company Admin Password Reset</h4>
                </div>
                <div class="modal-body">
                    <p>Please fill the following information carefully. <strong>All fields are required.</strong></p>
                    <div class="form-group">
                        <label>New Password</label>
                        <input name="password" type="password" class="form-control validate required">
                    </div>
                </div>
                <div class="modal-footer">
                    <input name="company_id" type="hidden" value="<?=  Calypso::getValue($company, 'id', '');  ?>"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="manage_application_access" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post" action="manageapplicationaccess">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Manage Application Access</h4>
                </div>
                <div class="modal-body">
                    <p>Please fill the following information carefully. <strong>All fields are required.</strong></p>
                    <div class="form-group">
                        <label>Allow Portal Login</label>
                        <select name="allow_portal_login" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Allow API Call</label>
                        <select name="allow_api_call" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>API Express Centre</label>
                        <select name="branch_id" class="form-control">
                            <?php foreach ($ecs as $ec) : ?>
                            <option value="<?= $ec['branch']['id']?>"><?= $ec['branch']['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input name="registration_number" type="hidden" value="<?=  Calypso::getValue($company, 'reg_no', '');  ?>"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="send_private_key" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post" action="sendprivatekey">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Send Private Key</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Create New Key</label>
                        <select name="create_new_key" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input name="company_id" type="hidden" value="<?=  Calypso::getValue($company, 'id', '');  ?>"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="addPlan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post" action="/billing/index">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Link Company To Billing Plan</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="">Billing Plan</label>
                        <select name="billing_plan_id" class="form-control validate required">
                            <option value="">Select Plan</option>
                            <?php foreach ($all_billing_plans as $plan): ?>
                                <option
                                    value="<?= Calypso::getValue($plan, 'id'); ?>"><?= strtoupper(Calypso::getValue($plan, 'name')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Set as Default Plan</label>
                        <select name="is_default" class="form-control validate required">
                            <option>Select option</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="company_id" value="<?= Calypso::getValue($company, 'id')?>">
                    <input type="hidden" name="task" value="link_company">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add to Company</button>
                </div>
            </div>
        </form>
    </div>
</div>