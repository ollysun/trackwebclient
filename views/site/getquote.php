<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 10/29/2016
 * Time: 7:38 PM
 */

use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;

$this->title = 'Get Quote';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['finance'],
        'label' => 'finance',
    ),
    array('label' => $this->title),
);
?>

<div class="main-box">
    <div class="main-box-body">

        <div class="row">
            <div class="col-md-6 col-xs-12">
                <label>Company</label>
                <select id="company" class="form-control billing_plan">
                    <option value="" selected>Select Company</option>
                    <?php foreach ($companies as $company): ?>
                        <option
                            value="<?= Calypso::getValue($company, 'id') ?>"><?= strtoupper(Calypso::getValue($company, 'name')) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xs-12">
                <label>Billing Plan</label>
                <select id="billing_plan" name="billing_plan"
                        class="form-control billing_plan">
                    <option value="">Select Company</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-3 form-group">
                <label>No. of Packages</label>
                <input name="no_of_packages"
                       class="form-control validate required non-zero-integer"
                       type="text">
            </div>

            <div class="col-xs-12 col-sm-4 form-group">
                <label> Metric</label>
                <select class="form-control" id="metric-select" name="qty_metrics">
                    <option value="<?= ServiceConstant::QTY_METRICS_WEIGHT ?>"> Weight</option>
                    <option value="<?= ServiceConstant::QTY_METRICS_PIECES ?>"> Pieces</option>
                </select>
            </div>
            <div class="col-xs-12 col-sm-5 form-group" id="metric-group">
                <label> Total weight </label>

                <div class="input-group">
                    <input name="parcel_weight"
                           class="form-control validate required non-zero-number" id="weight">
                    <span class="input-group-addon"> Kg</span>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Originating State</label>
                    <select class="form-control validate required" id="originating_state">
                        <option>Select state</option>
                        <?php foreach ($states as $state): ?>
                            <option
                                value="<?= Calypso::getValue($state, 'id', '') ?>">
                                <?= strtoupper(Calypso::getValue($state, 'name', '')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Originating City</label>
                    <select name="originating_city" id="originating_city" class="form-control validate required" disabled="disabled"></select>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Destination State</label>
                    <select class="form-control validate required" id="destination_state">
                        <option>Select state</option>
                        <?php foreach ($states as $state): ?>
                            <option
                                value="<?= Calypso::getValue($state, 'id', '') ?>">
                                <?= strtoupper(Calypso::getValue($state, 'name', '')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Destination City</label>
                    <select id="destination_city" name="destination_city" class="form-control validate required" disabled="disabled"></select>
                </div>
            </div>

        </div>


        <div class="clearfix main-box-body main-box-button-wrap">

            <div id="auto_billing" class="form-group amount-due-wrap">
                <div id="calculating_info" class="hide">calculating</div>

                <div class="row" id="quote">
                    <div class="col-md-2">
                        <label for="">Amount</label>

                        <div id="total_amount" class="amount-due currency naira">0.00</div>
                    </div>
                    <div class="col-md-2">
                        <label for="">Discount (<span id="discount_percentage">0.00</span>%)</label>

                        <div id="discount" class="amount-due currency naira">0.00</div>
                    </div>

                    <div class="col-md-3">
                        <label for="">Net Amount</label>

                        <div id="gross_amount" class="amount-due currency naira">0.00</div>
                    </div>

                    <div class="col-md-2">
                        <label for="">VAT</label>

                        <div id="vat" class="amount-due currency naira">0.00</div>
                    </div>

                    <div class="col-md-3">
                        <label for="">Amount Due</label>

                        <div id="amount_due" class="amount-due currency naira">0.00</div>
                    </div>
                </div>

            </div>

            <button id="btncalculate" class="pull-right btn btn-default">Get Quote <i
                    class="fa fa-arrow-right"></i></button>
        </div>


    </div>
</div>



<?php $this->registerJsFile('@web/js/libs/bootstrap-select.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/form-watch-changes.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/select2.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/utils.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/finance/get_quote.js?1.0.4', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<script type="text/javascript">
    <?= "var billingPlans = " . \yii\helpers\Json::encode($billingPlans) . ";";?>
</script>
