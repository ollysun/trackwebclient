<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 11/5/2016
 * Time: 5:09 PM
 */

use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

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



    <div class="main-box">
        <div class="clearfix">
            <a href="<?= Url::toRoute('shipments/processed');?>" class="infographic-box merged merged-top pull-left">
                <i class="fa fa-gift purple-bg"></i>
                <span class="value purple"><?= $stats['created'];?></span>
                <span class="headline">SHIPMENTS CREATED</span>
            </a>

            <!--<a href="<?/*= '#'//Url::toRoute('shipments/fordelivery');*/?>" class="infographic-box merged merged-right pull-left">
                <i class="fa fa-truck red-bg"></i>
                <span class="value red"><?/*= $stats['for_delivery'];*/?></span>
                <span class="headline">FOR DELIVERY</span>
            </a>-->

            <a href="<?= '#'//Url::toRoute('shipments/fordelivery');?>" class="infographic-box merged merged-right pull-left">
                <i class="fa fa-gift red-bg"></i>
                <span class="value red"><?= $stats['remittance'];?></span>
                <span class="headline">Escrow Balance</span>
            </a>
        </div>
    </div>
    <div class="main-box">
        <div class="clearfix">
            <a href="<?= '#'// Url::toRoute('shipments/processed');?>" class="infographic-box merged merged-top pull-left">
                <i class="fa fa-gift purple-bg"></i>
                <span class="value purple"><?= $stats['transit_to_customer'];?></span>
                <span class="headline">IN TRANSIT TO CUSTOMER</span>
            </a>

            <a href="<?= '#'//Url::toRoute('shipments/fordelivery');?>" class="infographic-box merged merged-right pull-left">
                <i class="fa fa-truck red-bg"></i>
                <span class="value red"><?= $stats['delivered'];?></span>
                <span class="headline">DELIVERED</span>
            </a>
        </div>
    </div>


    <div class="main-box hide">
        <div class="clearfix">
            <a href="<?= Url::toRoute('shipments/processed');?>" class="infographic-box merged merged-top pull-left">
                <i class="fa fa-gift purple-bg"></i>
                <span class="value purple"><?= 1//$stats['created'];?></span>
                <span class="headline">FUND AVAILABLE</span>
            </a>

            <a href="<?= Url::toRoute('shipments/fordelivery');?>" class="infographic-box merged merged-right pull-left">
                <i class="fa fa-truck red-bg"></i>
                <span class="value red"><?= 4// $stats['for_delivery'];?></span>
                <span class="headline">CREDIT AVAILABLE</span>
            </a>
        </div>
    </div>



    <script type="text/javascript">
        var filter_date = '<?= $date; ?>',
            filter_from = new Date('<?= $from_date; ?>'),
            filter_to = new Date('<?= $to_date; ?>'),
            branch = <?= 0 //$branch ? $branch : 0;?>;
    </script>

<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/dashboard.js?ver0.0.1', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>