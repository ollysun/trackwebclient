<?php
/* @var $this yii\web\View */
use Adapter\BillingPlanAdapter;
use Adapter\Util\Calypso;

$this->title = 'Billing: Corporate';
$this->params['breadcrumbs'] = array(
    array(
        'label' => 'Billing',
        'url' => ['billing/']
    ),
    array('label' => 'Corporate')
);
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal"><i class="fa fa-plus"></i> Add a Billing Plan</button>';
?>

<?= Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header">
    </div>
    <div class="main-box-body">
        <?php if (count($billingPlans) > 0): ?>
            <table id="table" class="table table-bordered">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Billing Name</th>
                    <th>Associated Company</th>
                    <th>Type</th>
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
                        <td><?= strtoupper(Calypso::getValue($billingPlan, 'company.name', 'N/A')) ?></td>
                        <td><?= strtoupper(Calypso::getValue(BillingPlanAdapter::getTypes(), Calypso::getValue($billingPlan, 'type'))); ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                <p><strong>No billing plans have been created</strong></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate-form" method="post" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Billing Plan</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input id="name" class="form-control validate required" name="name" />
                    </div>
                    <div class="form-group">
                        <label for="">Associated Company</label>
                        <select  name="company_id" class="form-control validate required">
                            <option value="">Select Company</option>
                            <?php foreach ($companies as $company): ?>
                                <option
                                    value="<?= Calypso::getValue($company, 'id'); ?>"><?= strtoupper(Calypso::getValue($company, 'name')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Type</label>
                        <select  name="type" class="form-control validate required">
                            <option value="">Select Type</option>
                            <?php foreach ($billingPlanTypes as $id => $name): ?>
                                <option
                                    value="<?= $id ?>"><?= strtoupper($name) ?></option>
                            <?php endforeach; ?>
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
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>