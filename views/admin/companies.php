<?php
use yii\helpers\Html;
use Adapter\Util\Calypso;
use yii\helpers\Url;
use yii\helpers\Json;
/* @var $this yii\web\View */
$this->title = 'Companies Registration';
$this->params['breadcrumbs'] = array(
    /*array(
    'url' => ['site/managebranches'],
    'label' => 'Administrator'
    ),*/
    array('label' => 'Company Registration')
);
$submitted_data = Calypso::getInstance()->getPageData();
?>


<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm"
data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add Company</button>
<button type="button" class="btn btn-success btn-sm"
data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Download Company</button>';
?>

<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>

<?php echo ($submitted_data) ? '':Calypso::showFlashMessages(); ?>

    <div class="main-box">
        <div class="main-box-header table-search-form">
            <div class="clearfix">
                <div class="pull-right clearfix">
                    <form method="get" enctype="application/x-www-form-urlencoded"
                          class="table-search-form form-inline clearfix">
                        <div class="pull-left form-group">
                            <label for="searchInput">Search</label><br>

                            <div class="input-group input-group-sm input-group-search">
                                <input id="searchInput" type="text" name="search" placeholder="Company name"
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
                <?php if (count($companies) > 0) { ?>
                    <table id="table" class="table table-hover dataTable">
                        <thead>
                        <tr>
                            <th style="width: 20px">S/N</th>
                            <th>Company name</th>
                            <th>Email</th>
                            <th>Phone number</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = $offset; foreach ($companies as $company): ?>
                            <tr>
                                <td><?= ++$i; ?></td>
                                <td><?= strtoupper(Calypso::getValue($company, 'name')); ?></td>
                                <td><?= Calypso::getValue($company, 'email'); ?></td>
                                <td><?= Calypso::getValue($company, 'phone_number'); ?></td>
                                <td><?= Calypso::getValue($company, 'address'); ?></td>
                                <td>
                                    <?php
                                        echo "<span class='label label-{$company['status_details']['class']}'>{$company['status_details']['label']}</span>";
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= Url::toRoute(['/admin/viewcompany?id=' . Calypso::getValue($company, 'id')]); ?>"
                                       class="btn btn-xs btn-default"><i
                                            class="fa fa-eye">&nbsp;</i> View</a>
                                    <button
                                        data-id="<?= Calypso::getValue($company, 'id'); ?>"
                                        data-email="<?= Calypso::getValue($company, 'email'); ?>"
                                        data-name="<?= Calypso::getValue($company, 'name'); ?>"
                                        data-phone_number="<?= Calypso::getValue($company, 'phone_number'); ?>"
                                        data-address="<?= Calypso::getValue($company, 'address'); ?>"
                                        data-state_id="<?= Calypso::getValue($company, 'city.state_id'); ?>"
                                        data-city_id="<?= Calypso::getValue($company, 'city_id'); ?>"
                                        data-reg_no="<?= Calypso::getValue($company, 'reg_no'); ?>"
                                        data-relations_officer_staff_id="<?= Calypso::getValue($company, 'relations_officer.staff_id'); ?>"
                                        data-business_manager_staff_id="<?= Calypso::getValue($company, 'business_manager_staff_id') == '0'?'':Calypso::getValue($company, 'business_manager_staff_id'); ?>"
                                        data-business_zone_id="<?= !Calypso::getValue($company, 'business_zone_id')?'':Calypso::getValue($company, 'business_zone_id'); ?>"
                                        data-relations_officer_id="<?= Calypso::getValue($company, 'relations_officer_id'); ?>"
                                        data-account_type_id="<?= Calypso::getValue($company, 'account_type_id'); ?>"
                                        data-discount="<?= Calypso::getValue($company, 'discount'); ?>"
                                        data-extra_info="<?= Calypso::getValue($company, 'extra_info'); ?>"
                                        type="button" class="btn btn-default btn-xs editCompany" data-toggle="modal"
                                            data-target="#editModal"><i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button data-status="<?php echo Calypso::getValue($company, 'status'); ?>" data-id="<?= Calypso::getValue($company, 'id'); ?>"
                                       class="btn btn-xs btn-default activation"><i
                                            class="fa fa-<?php echo $company['status_details']['icon']; ?>">&nbsp;</i><?php echo $company['status_details']['action']; ?></button>
                                      <span data-toggle="tooltip" title="Reset company admin password">
                                        <button
                                            data-company-id="<?= Calypso::getValue($company, 'id'); ?>"
                                            type="button" class="btn btn-default btn-xs resetPassword" data-toggle="modal"
                                            data-target="#reset"><i class="fa fa-refresh"></i>
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
                <?php } else { ?>
                    <div class="alert alert-info text-center" role="alert">
                        <p><strong>No Companies created</strong></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form class="validate-form" method="post" id="createCompanyForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add a New Company</h4>
                    </div>
                    <div class="modal-body">
                        <?php echo ($submitted_data) ? Calypso::showFlashMessages() : ''; ?>
                        <fieldset>
                            <legend>Company Details</legend>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Name</label>
                                    <input type="text" name="company[name]" class="form-control validate required name" value="<?= Calypso::getDisplayValue($submitted_data,'company.name'); ?>">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Registration No</label>
                                    <input type="text" name="company[reg_no]" class="form-control" value="<?= Calypso::getDisplayValue($submitted_data,'company.reg_no'); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Email address</label>
                                    <input type="text" name="company[email]"
                                           class="form-control validate required email" value="<?= Calypso::getDisplayValue($submitted_data,'company.email'); ?>">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Phone number</label>
                                    <input type="text" name="company[phone_number]"
                                           class="form-control validate required phone" value="<?= Calypso::getDisplayValue($submitted_data,'company.phone_number'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="company[address]" class="form-control validate required" value="<?= Calypso::getDisplayValue($submitted_data,'company.address'); ?>">
                            </div>
                            <div class="row">
                                <div class="col-xs-4 form-group">
                                    <label for="">State</label>
                                    <select id="state" name="company[state_id]" data-state data-target="city" class="form-control validate required"  data-selected="<?= Calypso::getDisplayValue($submitted_data,'company.state_id'); ?>">
                                        <option value="" selected>Select State</option>
                                        <?php foreach (Calypso::getValue($locations, 'states', []) as $state): ?>
                                            <option
                                                value="<?= Calypso::getValue($state, 'id', '') ?>"><?= strtoupper(Calypso::getValue($state, 'name', '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-xs-4 form-group">
                                    <label for="">City</label>
                                    <select name="company[city_id]" id="city" class="form-control validate required" data-selected="<?= Calypso::getDisplayValue($submitted_data,'company.city_id'); ?>">
                                        <option value="" selected>Select a State</option>
                                    </select>
                                </div>

                                <div class="col-xs-4 form-group">
                                    <label for="">Account Type</label>
                                    <select name="company[account_type]" class="form-control">
                                        <?php foreach ($account_types as $account_type) {?>
                                            <option value="<?= $account_type['id']?>"><?= $account_type['code'].'-'.$account_type['acronym']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                        <br>

                        <div class="row">
                            <fieldset class="col-xs-4">
                                <legend>Relationship Officer</legend>
                                <div class="row">
                                    <div class="col-xs-4 form-group">
                                        <label for="">Staff ID</label>
                                        <input type="text" id="staff" class="form-control validate required" name="staff" value="<?= Calypso::getDisplayValue($submitted_data,'staff'); ?>">
                                        <input id="staffId" type="hidden" name="company[relations_officer_id]" value="<?= Calypso::getDisplayValue($submitted_data,'company.relations_officer_id'); ?>"/>
                                    </div>
                                    <div class="col-xs-4 form-group">
                                        </br>
                                        <button type="button" data-staff="staff" data-staff_id="staffId" data-staff_name="staffName" data-load_staff="true" class="btn btn-primary btn-xs">Load
                                        </button>
                                    </div>
                                    <div class="col-xs-4 form-group">
                                        </br>
                                        <p id="staffName"></p>
                                    </div>
                                </div>
                            </fieldset>


                            <fieldset class="col-xs-4">
                                <legend>Business Zone</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">Region</label>
                                        <select id="newRegionId" class="form-control">
                                            <option>Select Region</option>
                                            <?php foreach($regions as $region){?>
                                                <option value="<?= $region['id'] ?>"><?= $region['name']?></option>
                                            <?php }?>
                                            
                                        </select>

                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Zone</label>

                                        <select id="newBusinessZoneId" name="company[business_zone_id]" class="form-control"></select>

                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="col-xs-4">
                                <legend>Business Offers</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group hide">
                                        <label for="">Credit Limit <span class="currency naira"></span></label>
                                        <input type="text" class="form-control" name="credit_limit">
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label for="">Discount (%)</label>
                                        <input type="text" class="form-control" name="discount">
                                    </div>
                                </div>
                            </fieldset>

                             <fieldset class="col-xs-4">
                                <legend>Credit Limit</legend>
                                    <div class="form-group  form-inline">
                                        <div class="col-xs-3">
                                            <span class="currency naira"></span>
                                        </div>
                                        <div class="col-xs-9 pull-left">
                                            <input type="text" class="form-control number" name="company[credit_limit]">
                                        </div>
                                    </div>
                            </fieldset>

                            <fieldset class="col-xs-4">
                                <legend>Extra Info</legend>
                                <textarea class="form-control" name="company[extra_info]" id="extra_info"></textarea>
                            </fieldset>

                        </div>
                        <br>


                        <div class="row hide">
                            <fieldset class="col-xs-6">
                                <legend>Billing Plans</legend>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Plan name</th>
                                        <th>Is Default</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody id="new_billing_plans_list" data-bind="foreach: plans">
                                        <tr>
                                            <td><span data-bind="text: name"></span></td>
                                            <td><span data-bind="text: is_default_plan"></span></td>
                                            <td>
                                                <button class="btn btn-primary btn-xs" data-bind="click: function(){$root.markAsDefaultPlan(id)}">Make Default</button>
                                                <button class="btn btn-danger btn-xs" data-bind="click function(){$root.romvePlan(id)}">Remove</button>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </fieldset>
                            <fieldset class="col-xs-6">
                                <legend>New Billing Plan</legend>
                                <div class="row">
                                    <div class="col-xs-5 form-group">
                                        <select data-bind="options: all_plans, optionsValue:'id', optionsText:'name', value: plan_id"
                                                id="new_billing_plan_id" class="form-control validate required">
                                            <option value="">Select Billing Plan</option>
                                            <?php foreach ($billing_plans as $billing_plan): ?>
                                                <option
                                                    value="<?= Calypso::getValue($billing_plan, 'id'); ?>"><?= strtoupper(Calypso::getValue($billing_plan, 'name')) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-xs-5 form-group">

                                        <select data-model="value: is_default_plan" id="new_is_default" class="form-control validate required">
                                            <option>Set as Default Plan</option>
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                    <div class="col-xs-2">
                                        <button data-bind="click: addPlan" type="button" class="btn btn-danger" id="new_btn_add_plan">Add</button>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <br/>

                        <div class="row">
                            <fieldset class="col-xs-6">
                                <legend>Primary Contact</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">First name</label>
                                        <input name="primary_contact[firstname]" type="text"
                                               class="form-control validate required name" value="<?= Calypso::getDisplayValue($submitted_data,'primary_contact.firstname'); ?>">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Last name</label>
                                        <input name="primary_contact[lastname]" type="text"
                                               class="form-control validate required name" value="<?= Calypso::getDisplayValue($submitted_data,'primary_contact.lastname'); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label for="">Email address</label>
                                        <input name="primary_contact[email]" type="text"
                                               class="form-control validate required email" value="<?= Calypso::getDisplayValue($submitted_data,'primary_contact.email'); ?>">
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label for="">Phone number</label>
                                        <input name="primary_contact[phone_number]" type="text"
                                               class="form-control validate required phone" value="<?= Calypso::getDisplayValue($submitted_data,'primary_contact.phone_number'); ?>">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="col-xs-6">
                                <legend>Secondary Contact
                                    <small>(optional)</small>
                                </legend>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <label for="enableSecondaryContact">Secondary Contact Available?</label>
                                        <input type="checkbox" id="enableSecondaryContact" <?= is_null(Calypso::getValue($submitted_data, 'secondary_contact')) ? '':'checked'; ?>/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">First name</label>
                                        <input name="secondary_contact[firstname]" data-secondary_contact <?= is_null(Calypso::getValue($submitted_data, 'secondary_contact')) ? 'disabled':''; ?>
                                               type="text" class="form-control name" value="<?= Calypso::getDisplayValue($submitted_data,'secondary_contact.firstname'); ?>">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Last name</label>
                                        <input name="secondary_contact[lastname]" data-secondary_contact <?= is_null(Calypso::getValue($submitted_data, 'secondary_contact')) ? 'disabled':''; ?>
                                        type="text" class="form-control name" value="<?= Calypso::getDisplayValue($submitted_data,'secondary_contact.lastname'); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label for="">Email address</label>
                                        <input name="secondary_contact[email]" data-secondary_contact <?= is_null(Calypso::getValue($submitted_data, 'secondary_contact')) ? 'disabled':''; ?>
                                               type="text" class="form-control email" value="<?= Calypso::getDisplayValue($submitted_data,'secondary_contact.email'); ?>">
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label for="">Phone number</label>
                                        <input name="secondary_contact[phone_number]" data-secondary_contact <?= is_null(Calypso::getValue($submitted_data, 'secondary_contact')) ? 'disabled':''; ?>
                                               type="text" class="form-control phone" value="<?= Calypso::getDisplayValue($submitted_data,'secondary_contact.phone_number'); ?>">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form class="" id="editCompanyForm" method="post" action="<?= Url::to("/admin/editcompany")?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit Company</h4>
                    </div>
                    <div class="modal-body">
                        <fieldset>
                            <legend>Company Details</legend>
                            <div class="row">
                                <input type="hidden" name="company[id]"/>
                                <div class="col-xs-6 form-group">
                                    <label for="">Name</label>
                                    <input name="company[name]" type="text" class="form-control validate required name">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Registration No</label>
                                    <input name="company[reg_no]" type="text" class="form-control validate required">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Email address</label>
                                    <input name="company[email]" type="text" class="form-control validate required email">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Phone number</label>
                                    <input name="company[phone_number]" type="text" class="form-control validate required phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input name="company[address]" type="text" class="form-control validate required">
                            </div>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">State</label>
                                    <select name="company[state]" data-state data-target="edit_city" class="form-control validate required">
                                        <option value="" selected>Select State</option>
                                        <?php foreach (Calypso::getValue($locations, 'states', []) as $state): ?>
                                            <option
                                                value="<?= Calypso::getValue($state, 'id', '') ?>"><?= strtoupper(Calypso::getValue($state, 'name', '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">City</label>
                                    <select name="company[city_id]" id="edit_city" class="form-control validate required">
                                        <option value="" selected>Select a State</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hidden">
                                <label for="">Account Type</label>
                                <input name="company[account_type_id]">
                            </div>
                        </fieldset>
                        <br>

                        <div class="row">
                            <fieldset class="col-xs-4">
                                <legend>Relationship Officer</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">Staff ID</label>
                                        <input type="text" name="company[relations_officer_staff_id]" id="editStaff" class="form-control validate required">
                                        <input id="editStaffId" type="hidden" name="company[relations_officer_id]"/>
                                    </div>
                                    <div class="col-xs-2 form-group">
                                        </br>
                                        <button type="button" data-staff="editStaff" data-staff_id="editStaffId" data-staff_name="editStaffName" data-load_staff="true" class="btn btn-primary btn-xs">Load
                                        </button>
                                    </div>
                                    <div class="col-xs-4 form-group">
                                        </br>
                                        <p id="editStaffName"></p>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="col-xs-4">
                                <legend>Business Zone</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">Region</label>
                                        <select id="editRegionId" class="form-control">
                                            <option>Select Region</option>
                                            <?php foreach($regions as $region){?>
                                            <option value="<?=$region['id']?>"><?= $region['name'] ?></option>
                                            <?php } ?>
                                        </select>


                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Zone</label>

                                        <select name="company[business_zone_id]" id="editBusinessZoneId" class="form-control"></select>

                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="col-xs-4">
                                <legend>Business Offers</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group hide">
                                        <label for="">Credit Limit <span class="currency naira"></span></label>
                                        <input type="text" class="form-control number">
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label for="">Discount (%)</label>
                                        <input type="text" class="form-control number" name="company[discount]">
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="col-xs-4">
                                <legend>Extra Info</legend>
                                <textarea class="form-control" name="company[extra_info]" id="extra_info"></textarea>
                            </fieldset>
                        </div>

                        <div class="row hide">
                            <fieldset class="col-xs-6">
                                <legend>Billing Plans</legend>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Plan name</th>
                                            <th>Is Default</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="billing_plans_list">

                                    </tbody>
                                </table>
                            </fieldset>
                            <fieldset class="col-xs-6">
                                <legend>New Billing Plan</legend>
                                <div class="row">
                                    <div class="col-xs-5 form-group">
                                        <select id="edit_billing_plan_id" class="form-control validate required">
                                            <option value="">Select Billing Plan</option>
                                            <?php foreach ($billing_plans as $billing_plan): ?>
                                                <option
                                                    value="<?= Calypso::getValue($billing_plan, 'id'); ?>"><?= strtoupper(Calypso::getValue($billing_plan, 'name')) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-xs-5 form-group">
                                        <select id="edit_is_default" class="form-control validate required">
                                            <option>Set as Default Plan</option>
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                    <div class="col-xs-2">
                                        <button type="button" class="btn btn-danger" id="edit_btn_add_plan">Add</button>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="reset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                        <input name="company_id" type="hidden"/>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- this page specific scripts -->
    <script type="text/javascript">
        <?= "var previous_data = ". ($submitted_data ? 1: 0).";";?>
    </script>

<script type="text/javascript">
    <?= "var billing_plans = " . Json::encode($billing_plans) . ";";?>
</script>


<script>
    var regions = <?= Json::encode($regions) ?>;
    var businessZones = <?= Json::encode($business_zones) ?>;
</script>

<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/companies.js?v=1.0.3', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
