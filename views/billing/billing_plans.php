<?php
/* @var $this yii\web\View */
use Adapter\BillingPlanAdapter;
use Adapter\Util\Calypso;
use yii\helpers\Url;

$this->title = 'Billing: Billing Plans';
$this->params['breadcrumbs'] = array(
    /*array(
        'label' => 'Billing',
        'url' => ['billing/']
    ),*/
    array('label' => 'Billing Plans')
);
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal"><i class="fa fa-plus"></i> Add a Billing Plan</button>';
?>

<?= Calypso::showFlashMessages(); ?>
    <div class="main-box">
        <div class="main-box-header table-search-form ">
            <div class="clearfix">
                <div class="pull-right clearfix">

                    <form class="table-search-form form-inline clearfix">
                        <div class="pull-left">
                            <label for="searchInput">Search</label><br>

                            <div class="input-group input-group-sm input-group-search">
                                <input id="searchInput" type="text" name="search" placeholder="Billing plan name"
                                       class="search-box form-control" value="<?= $search; ?>">

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
            <?php if (count($billingPlans) > 0): ?>
                <table id="table" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Billing Name</th>
                        <th>Associated Companies</th>
                        <th>Type</th>
                        <th class="hidden">Discount</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $row = $offset;
                    foreach ($billingPlans as $billingPlan): ?>
                        <tr>
                            <td><?= ++$row; ?></td>
                            <td><?= strtoupper(Calypso::getValue($billingPlan, 'name')) ?></td>
                           <!-- <td><?/*= strtoupper(Calypso::getValue($billingPlan, 'company.name', 'N/A')) */?></td>-->
                            <td>
                                <?= strtoupper(Calypso::getValue($billingPlan, 'linked_companies_count', '0')) ?> Companies<br/>

                                 <a style="margin-top: 5px;" class="btn btn-xs btn-primary linkCompany" data-toggle="modal"
                                    data-target="#linkCompany"
                                   data-plan_id="<?= Calypso::getValue($billingPlan, 'id')?>" data-plan_name="<?= Calypso::getValue($billingPlan, 'name')?>">Add</a>


                                <a  data-bind='click: function () { viewCompanies(<?= Calypso::getValue($billingPlan, 'id') ?>); }' style="margin-top: 5px;" class="btn btn-xs btn-primary linkCompany" data-toggle="modal" data-target="#linkCompanies">View</a>
                            </td>
                            <td><?= strtoupper(Calypso::getValue(BillingPlanAdapter::getTypes(), Calypso::getValue($billingPlan, 'type'))); ?></td>
                            <td class="hidden"><?= Calypso::getValue($billingPlan, 'discount') ?></td>
                            <td>
                                <a data-toggle="modal" data-target="#editModal"
                                   data-discount="<?= Calypso::getValue($billingPlan, 'discount') ?>"
                                   data-name="<?= Calypso::getValue($billingPlan, 'name') ?>"
                                   data-id="<?= Calypso::getValue($billingPlan, 'id')?>"
                                   data-company_id="<?= Calypso::getValue($billingPlan, 'company.id')?>"
                                   data-type="<?= Calypso::getValue(BillingPlanAdapter::getTypes(), Calypso::getValue($billingPlan, 'type'))?>" href="#editModal"
                                   class="btn btn-xs btn-primary editbtn hidden">Edit</a>

                                <a href="<?= Url::to(["/billing/onforwarding", 'billing_plan_id' => Calypso::getValue($billingPlan, 'id')]) ?>"
                                   class="btn btn-xs btn-default">Onforwarding <br/> Charges</a>
                                <a href="<?= Url::to(["/billing/citymapping", 'billing_plan_id' => Calypso::getValue($billingPlan, 'id')]) ?>"
                                   class="btn btn-xs btn-primary">City Mapping</a>
                                <a href="<?= Url::to(["/billing/weightranges", 'billing_plan_id' => Calypso::getValue($billingPlan, 'id')]) ?>"
                                   class="btn btn-xs btn-default">Weight Ranges</a>
                                <a href="<?= Url::to(["/billing/pricing", 'billing_plan_id' => Calypso::getValue($billingPlan, 'id')]) ?>"
                                   class="btn btn-xs btn-primary">Pricing</a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
            <?php else: ?>
                <div class="alert alert-info text-center" role="alert">
                    <p><strong>No billing plans have been created</strong></p>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <div class="modal fade" id="linkCompanies" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Linked Companies</h4>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    S/No
                                </th>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody data-bind="foreach: companies">
                            <tr>
                                <td><span class="user-box-name" data-bind="text: ($index() + 1)"></span></td>
                                <td><span class="user-box-name" data-bind="text: name"></span></td>
                                <td><button class="btn btn-xs" data-bind="click: function(){$root.remove(id);}">Remove</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="linkCompany" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form class="validate-form" method="post" action="">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Link Company To Billing Plan</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Plan Name</label>
                            <input readonly id="plan_name" class="form-control validate" name="name"/>
                        </div>

                        <div class="form-group">
                            <label for="">Company</label>
                            <select name="company_id" class="form-control validate required">
                                <option value="">Select Company</option>
                                <?php foreach ($companies as $company): ?>
                                    <option
                                        value="<?= Calypso::getValue($company, 'id'); ?>"><?= strtoupper(Calypso::getValue($company, 'name')) ?></option>
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
                        <input type="hidden" name="billing_plan_id" id="plan_id">
                        <input type="hidden" name="task" value="link_company">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add to Plan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form class="validate-form" method="post" action="<?= Url::to("/billing/savecorporate") ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add Billing Plan</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" class="form-control validate required" name="name"/>
                        </div>
                        <!--<div class="form-group hide">
                            <label for="">Associated Company</label>
                            <select name="company" class="form-control validate required">
                                <option value="">Select Company</option>
                                <?php /*foreach ($companies as $company): */?>
                                    <option
                                        value="<?/*= Calypso::getValue($company, 'id'); */?>"><?/*= strtoupper(Calypso::getValue($company, 'name')) */?></option>
                                <?php /*endforeach; */?>
                            </select>
                        </div>-->
                        <div class="form-group">
                            <label class="checkbox-inline" >
                                <input type="checkbox" id="clone-billing-plan" name="clone_billing_plan">
                                Clone existing billing plan
                            </label>
                        </div>
                        <div class="form-group" id="clone-details">

                        </div>


                        <div class="form-group hidden">
                            <label for="name">Discount</label>
                            <input id="discount" value="0" class="form-control validate required" name="discount"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id">
                        <input type="hidden" name="type"
                               value="<?= BillingPlanAdapter::TYPE_WEIGHT_AND_ON_FORWARDING; ?>">
                        <input type="hidden" name="task" value="status">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form class="validate-form" method="post" action="<?= Url::to("/billing/savecorporate") ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit Billing Plan</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input readonly id="edit_name" class="form-control validate required" name="name"/>
                        </div>

                        <div class="form-group">
                            <label for="name">Discount</label>
                            <input id="edit_discount" value="0" class="form-control validate required" name="discount"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" id="edit_id">
                        <input type="hidden" name="edit_type"
                               value="<?= BillingPlanAdapter::TYPE_WEIGHT_AND_ON_FORWARDING; ?>">
                        <input type="hidden" name="edit_task" value="status">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/finance/billing_plans.js', ['depends' => [\yii\web\JqueryAsset::className(),
    \app\assets\AppAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>