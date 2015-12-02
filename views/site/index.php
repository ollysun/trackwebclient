<?php
use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// only show if user is admin
if($branch_type != ServiceConstant::BRANCH_TYPE_EC && $user_type == ServiceConstant::USER_TYPE_ADMIN ) {
    $this->params['graph_stats'] = $this->render('../elements/dashboard/choose_branch', array('branch_type'=>$branch_type, 'user_type'=>$user_type, 'branch'=>$branch));
}

?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>

<form id="date-filter-form" class="dashboard-stats-title">
    <strong class="text-muted text-uppercase">STATISTICS: <span id="relative-date-text"></span></strong>
    <div class="dashboard-stats-title-date-wrap">
        <select name="date" class="form-control-transparent text-muted disguise-as-link"></select>
        <div class="dashboard-stats-title-custom-date-wrap clearfix">
            <div class="pull-left">
                <label>From:</label>
                <input type="text" name="from" class="form-control input-sm" value="<?= $from_date; ?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>
            <div class="pull-left">
                <label>To:</label>
                <input type="text" name="to" class="form-control input-sm" value="<?= $to_date; ?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>
            <div class="pull-left">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-sm btn-default">Submit</button>
            </div>
        </div>
    </div>

</form>

<!-- EC STAFF  -->
<div class="main-box">
    <div class="clearfix">
        <a href="<?= Url::toRoute('shipments/processed');?>" class="infographic-box merged merged-top pull-left">
            <i class="fa fa-gift purple-bg"></i>
            <span class="value purple"><?= $stats['created'];?></span>
            <span class="headline">NO OF SHIPMENTS</span>
        </a>
        <?php if($branch_type == ServiceConstant::BRANCH_TYPE_EC) : ?>
        <a  href="<?= Url::toRoute('shipments/forsweep');?>" class="infographic-box merged merged-top merged-right pull-left">
            <i class="fa fa-gift green-bg"></i>
            <span class="value green"><?= $stats['for_sweep_ecommerce'];?></span>
            <span class="headline">DUE FOR SWEEP (ECOMMERCE)</span>
        </a>
    </div>
    <div class="clearfix">
        <a href="<?= Url::toRoute('shipments/forsweep');?>" class="infographic-box merged pull-left">
            <i class="fa fa-gift yellow-bg"></i>
            <span class="value yellow"><?= $stats['for_sweep'];?></span>
            <span class="headline">DUE FOR SWEEP (OTHERS)</span>
        </a>
        <?php endif; ?>
        <a href="<?= Url::toRoute('shipments/fordelivery');?>" class="infographic-box merged merged-right pull-left">
            <i class="fa fa-truck red-bg"></i>
            <span class="value red"><?= $stats['for_delivery'];?></span>
            <span class="headline">DUE FOR DELIVERY</span>
        </a>
    </div>
</div>

<?php if($branch_type != ServiceConstant::BRANCH_TYPE_EC) : ?>
<!-- HUB STAFF -->
<div class="main-box">
    <div class="clearfix">
        <a href="<?= Url::toRoute('hubs/hubarrival');?>" class="infographic-box merged merged-top pull-left">
            <i class="fa fa-gift purple-bg"></i>
            <span class="value purple"><?= $stats['received'];?></span>
            <span class="headline">SHIPMENT RECEIVED INTO THE HUB</span>
        </a>
        <a href="<?= Url::toRoute('hubs/destination');?>" class="infographic-box merged merged-top merged-right pull-left">
            <i class="fa fa-gift emerald-bg"></i>
            <span class="value emerald"><?= $stats['ready_for_sorting'];?></span>
            <span class="headline">READY FOR SORTING</span>
        </a>
    </div>
    <div class="clearfix">
        <a href="<?= Url::toRoute('hubs/destination');?>" class="infographic-box merged pull-left">
            <i class="fa fa-gift gray-bg"></i>
            <span class="value gray"><?= $stats['groundsman'];?></span>
            <span class="headline">READY FOR SORTING (GROUNDSMAN)</span>
        </a>
        <a href="<?= Url::toRoute('hubs/delivery');?>" class="infographic-box merged merged-right pull-left">
            <i class="fa fa-gift red-bg"></i>
            <span class="value red"><?= $stats['sorted'];?></span>
            <span class="headline">SORTED BUT STILL AT THE HUB</span>
        </a>
    </div>
    <div class="clearfix">
        <a href="<?= Url::toRoute('shipments/dispatched');?>" class="infographic-box merged pull-left">
            <i class="fa fa-truck yellow-bg"></i>
            <span class="value yellow"><?= $stats['transit_to_customer']; ?></span>
            <span class="headline">IN TRANSIT TO CUSTOMER</span>
        </a>
        <a href="<?= Url::toRoute('shipments/delivered');?>" class="infographic-box merged merged-right pull-left">
            <i class="fa fa-check green-bg"></i>
            <span class="value green"><?= $stats['delivered']; ?></span>
            <span class="headline">DELIVERED</span>
        </a>
    </div>
</div>
<?php endif; ?>
<script type="text/javascript">
    var filter_date = '<?= $date; ?>',
        filter_from = new Date('<?= $from_date; ?>'),
        filter_to = new Date('<?= $to_date; ?>'),
        branch = <?= $branch ? $branch : 0;?>;
</script>

<?php
    $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile('@web/js/dashboard.js?ver0.0.1', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>