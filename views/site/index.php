<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// only show if user is admin
$this->params['graph_stats'] = $this->render('../elements/dashboard/choose_branch');

?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>

<form method="post" id="date-filter-form" class="dashboard-stats-title">
    <strong class="text-muted text-uppercase">STATISTICS: <span id="relative-date-text"></span></strong>
    <div class="dashboard-stats-title-date-wrap">
        <select name="date" class="form-control-transparent text-muted disguise-as-link"></select>
        <div class="dashboard-stats-title-custom-date-wrap clearfix">
            <div class="pull-left">
                <label>From:</label>
                <input type="text" name="from" class="form-control input-sm" value="<?= date('Y/m/d', strtotime($from_date)); ?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>
            <div class="pull-left">
                <label>To:</label>
                <input type="text" name="to" class="form-control input-sm" value="<?= date('Y/m/d', strtotime($to_date)); ?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
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
        <div class="infographic-box merged merged-top pull-left">
            <i class="fa fa-gift purple-bg"></i>
            <span class="value purple">25</span>
            <span class="headline">NO OF SHIPMENTS</span>
        </div>
        <div class="infographic-box merged merged-top merged-right pull-left">
            <i class="fa fa-gift green-bg"></i>
            <span class="value green">12</span>
            <span class="headline">DUE FOR SWEEP (ECOMMERCE)</span>
        </div>
    </div>
    <div class="clearfix">
        <div class="infographic-box merged pull-left">
            <i class="fa fa-gift yellow-bg"></i>
            <span class="value yellow">13</span>
            <span class="headline">DUE FOR SWEEP (OTHERS)</span>
        </div>
        <div class="infographic-box merged merged-right pull-left">
            <i class="fa fa-truck red-bg"></i>
            <span class="value red">28</span>
            <span class="headline">DUE FOR DELIVERY</span>
        </div>
    </div>
</div>

<!-- HUB STAFF -->
<div class="main-box">
    <div class="clearfix">
        <div class="infographic-box merged merged-top pull-left">
            <i class="fa fa-gift purple-bg"></i>
            <span class="value purple">25</span>
            <span class="headline">SHIPMENT RECEIVED</span>
        </div>
        <div class="infographic-box merged merged-top merged-right pull-left">
            <i class="fa fa-gift emerald-bg"></i>
            <span class="value emerald">12</span>
            <span class="headline">READY FOR SORTING</span>
        </div>
    </div>
    <div class="clearfix">
        <div class="infographic-box merged pull-left">
            <i class="fa fa-gift gray-bg"></i>
            <span class="value gray">13</span>
            <span class="headline">READY FOR SORTING (GROUNDSMAN)</span>
        </div>
        <div class="infographic-box merged merged-right pull-left">
            <i class="fa fa-gift red-bg"></i>
            <span class="value red">28</span>
            <span class="headline">SORTED BUT STILL AT THE HUB</span>
        </div>
    </div>
    <div class="clearfix">
        <div class="infographic-box merged pull-left">
            <i class="fa fa-truck yellow-bg"></i>
            <span class="value yellow">13</span>
            <span class="headline">IN TRANSIT TO CUSTOMER</span>
        </div>
        <div class="infographic-box merged merged-right pull-left">
            <i class="fa fa-check green-bg"></i>
            <span class="value green">28</span>
            <span class="headline">DELIVERED</span>
        </div>
    </div>
</div>

<script type="text/javascript">
    var filter_date = '<?= $date; ?>',
        filter_from = new Date('<?= $from_date; ?>'),
        filter_to = new Date('<?= $to_date; ?>');
</script>

<?php
    $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile('@web/js/dashboard.js?ver0.0.1', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>