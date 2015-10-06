<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// only show if user is admin
$this->params['graph_stats'] = $this->render('../elements/dashboard/choose_branch');

?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>

<form id="date-filter-form" class="dashboard-stats-title">
    <strong class="text-muted text-uppercase">STATISTICS: <?= 'TODAY'; ?></strong>
    <div class="dashboard-stats-title-date-wrap">
        <select name="date" class="form-control-transparent text-muted disguise-as-link">
            <option value="">Change</option>
            <option value="0d">Today</option>
            <option value="-1w">Last week</option>
            <option value="-2w">Last 2 weeks</option>
            <option value="-1m">Last month</option>
            <option value="-2m">Last 2 months</option>
            <option value="-3m">Last 3 months</option>
            <option value="-6m">Last 6 months</option>
            <option value="-1y">Last year</option>
            <option value="custom">Custom</option>
        </select>
        <div class="dashboard-stats-title-custom-date-wrap clearfix">
            <div class="pull-left">
                <label>From:</label>
                <input type="text" name="from_date" class="form-control input-sm" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>
            <div class="pull-left">
                <label>To:</label>
                <input type="text" name="to_date" class="form-control input-sm" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>
            <div class="pull-left">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-sm btn-default">Submit</button>
            </div>
        </div>
    </div>

</form>

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


<?php
    $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile('@web/js/dashboard.js?ver0.0.1', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>