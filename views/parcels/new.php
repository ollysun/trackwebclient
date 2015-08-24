<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Create a New Shipment';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['site/parcels'],
        'label' => 'Shipment',
    ),
    array('label' => $this->title),
);
?>


<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>

<?php echo Calypso::showFlashMessages(); ?>

<form action="#" method="post" enctype="multipart/form-data" class="validate-form" data-keyboard-submit>

    <div id="newParcelForm" class="l-new-parcel-form carousel slide">
        <ol class="carousel-indicators hidden">
            <li data-target="#newParcelForm" data-slide-to="0" class="active"></li>
            <li data-target="#newParcelForm" data-slide-to="1"></li>
            <li data-target="#newParcelForm" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="main-box item">
                <div class="row">
                    <div class="col-xs-12 col-lg-6">
                        <div class="main-box-header">
                            <h2>Consignor Information</h2>
                        </div>
                        <div class="main-box-body">
                            <?= $this->render('../elements/new_parcel_user_information', ['prefix' => 'shipper', 'countries' => $countries]) ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-lg-6">
                        <div class="main-box-header">
                            <h2>Consignee Information</h2>
                        </div>
                        <div class="main-box-body">
                            <?= $this->render('../elements/new_parcel_user_information', ['prefix' => 'receiver', 'countries' => $countries]) ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix main-box-body main-box-button-wrap">
                    <a href="#newParcelForm" data-slide="next" class="pull-right btn btn-default">Continue <i
                            class="fa fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="main-box item active">
                <div class="main-box-header">
                    <h2>Parcel/Shipment Information</h2>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="main-box-body">
                            <div class="form-group">
                                <label>Send parcel to Hub?</label>

                                <div>
                                    <div class="radio-inline">
                                        <input id="sendToHubYes" type="radio" name="send_to_hub" value="1"
                                               checked="checked"> <label for="sendToHubYes" class="">Yes</label>
                                    </div>
                                    <div class="radio-inline">
                                        <input id="sendToHubNo" type="radio" name="send_to_hub" value="0"> <label
                                            for="sendToHubNo" class="">No</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Parcel Type</label>

                                <select name="parcel_type" id="" class="form-control validate required">
                                    <option value="">Please select</option>
                                    <?php if (isset($parcelType) && is_array($parcelType['data'])) {
                                        foreach ($parcelType['data'] as $item) { ?>
                                            <option
                                                value="<?= $item['id'] ?>"><?= strtoupper($item['name']); ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 form-group">
                                    <label>No. of Packages</label>
                                    <input name="no_of_packages" class="form-control validate required non-zero-integer">
                                </div>
                                <div class="col-xs-12 col-sm-4 form-group">
                                    <label>Total weight</label>

                                    <div class="input-group">
                                        <input name="parcel_weight" class="form-control validate required non-zero-number" id="weight">
                                        <span class="input-group-addon">Kg</span>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-5 form-group">
                                    <label>Parcel value</label>

                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <select name="currency" id="currencySelect" class="selectpicker"
                                                    data-width="70px" data-style="btn-default"
                                                    title="Please choose a currency">
                                                <option title="NGN" value="NGN" selected="selected">Naira</option>
                                                <option title="USD" value="USD">United States Dollars</option>
                                                <option title="EUR" value="EUR">Euro</option>
                                                <option title="GBP" value="GBP">British Pounds</option>
                                            </select>
                                        </div>
                                        <input name="parcel_value" type="text" class="form-control required non-zero-number">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Delivery Type</label>

                                <div class='validate'>
                                    <div class="radio-inline">
                                        <input id="deliveryAtAddress" type="radio" name="delivery_type" value="2"
                                            > <label for="deliveryAtAddress"
                                                                         class="">Dispatch</label>
                                    </div>
                                    <div class="radio-inline">
                                        <input id="deliveryAtCentre" type="radio" name="delivery_type" value="1"> <label
                                            for="deliveryAtCentre" class="">Pickup</label>
                                    </div>
                                </div>
                            </div>
                            <div id="pickUpWrap" class="form-group hidden">
                                <label for="">Pickup Centre</label>
                                <select name="pickup_centres" id="" class="form-control">
                                    <option value="">Choose One</option>
                                    <?php
                                    if (isset($centres) && is_array(($centres))):
                                        foreach ($centres as $centre) {
                                            ?>
                                            <option value="<?=$centre['id'];?>"><?=ucwords($centre['name']);?></option>
                                            <?php
                                        } endif;
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Service Type</label>
                                <select name="shipping_type" id="" class="form-control validate required">
                                    <option value="">Please select</option>
                                    <?php if (isset($ShipmentType) && is_array($ShipmentType['data'])) {
                                        foreach ($ShipmentType['data'] as $item) { ?>
                                            <option
                                                value="<?= $item['id'] ?>"><?= strtoupper($item['name']); ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="main-box-body">
                            <div class="form-group">
                                <label>Merchant?</label>

                                <div>
                                    <div class="radio-inline">
                                        <input id="merchantNew" type="radio" name="merchant" value="new"> <label
                                            for="merchantNew" class="">New</label>
                                    </div>
                                    <div class="radio-inline">
                                        <input id="merchantOld" type="radio" name="merchant" value="old">
                                        <label for="merchantOld" class="">Existing</label>
                                    </div>
                                    <div class="radio-inline">
                                        <input id="merchantNone" type="radio" name="merchant" checked="checked"
                                               value="none">
                                        <label for="merchantNone" class="">Not applicable</label>
                                    </div>
                                </div>
                            </div>
                            <div id="bank-account-details" class="hidden">

                                <input type="hidden" name="account_id" class="form-control" id="account_id">

                                <div class="form-group">
                                    <label for="">Account Name</label>
                                    <input name="account_name" class="form-control" id="account_name">
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-lg-5 form-group">
                                        <label>Account No</label>
                                        <input name="account_no" class="form-control" data-validate-limit="10" id="account_no">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-lg-7 form-group">
                                        <label>Bank</label>
                                        <select name="bank" class="form-control" id="bank">
                                            <?php
                                            if (isset($Banks) && is_array($Banks['data'])) {
                                                foreach ($Banks['data'] as $item) {
                                                    ?>
                                                    <option
                                                        value="<?= $item['id'] ?>"><?= strtoupper($item['name']); ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-lg-5 form-group">
                                        <label>Cash on Delivery?</label><br>

                                        <div class="radio-inline">
                                            <input id="cODYes" type="radio" name="cash_on_delivery" value="true"> <label
                                                for="cODYes" class="">Yes</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="cODNo" type="radio" name="cash_on_delivery" checked="checked"
                                                   value="false">
                                            <label for="cODNo" class="">No</label>
                                        </div>
                                    </div>
                                    <div id="CODAmountWrap" class="col-xs-12 col-sm-6 col-lg-7 form-group hidden">
                                        <label>Amount to be collected</label>

                                        <div class="input-group">
                                            <span class="input-group-addon currency naira"></span>
                                            <input name="CODAmount" id="CODAmount" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Corporate lead?</label><br>

                                <div class="radio-inline">
                                    <input id="cLeadYes" type="radio" name="corporate_lead" value="true"> <label
                                        for="cLeadYes" class="">Yes</label>
                                </div>
                                <div class="radio-inline">
                                    <input id="cLeadNo" type="radio" name="corporate_lead" value="false"
                                           checked="checked"> <label for="cLeadNo" class="">No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Other Information</label>
                                <textarea name="other_info" class="form-control validate limit max word" data-validate-limit="50"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="clearfix main-box-body main-box-button-wrap">
                    <a href="#newParcelForm" data-slide="prev" class="btn btn-default pull-left"><i
                            class="fa fa-arrow-left"></i> Back</a>
                    <a href="#newParcelForm" data-slide="next" class="btn btn-default pull-right" data-calculate-amount="true">Continue <i
                            class="fa fa-arrow-right"></i></a>
                </div>

            </div>
            <div class="item">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-sm-push-3">
                        <div class="main-box">
                            <div class="main-box-header">
                                <h2>Payment Information</h2>
                            </div>
                            <div class="main-box-body">
                                <div class="form-group amount-due-wrap">
                                    <label for="">Amount Due</label>

                                    <div class="amount-due currency naira">3,045.00</div>
                                    <input type="hidden" name="amount" id="amount" />
                                </div>
                                <div class="form-group">
                                    <label for="">Payment Method</label>

                                    <div>
                                        <div class="radio-inline">
                                            <input id="paymentMethodCash" type="radio" name="payment_method" value="1"
                                                   checked="checked"> <label for="paymentMethodCash"
                                                                             class="">Cash</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="paymentMethodPOS" type="radio" name="payment_method" value="2">
                                            <label for="paymentMethodPOS" class="">POS</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="paymentMethodCashPOS" type="radio" name="payment_method"
                                                   value="3"> <label for="paymentMethodCashPOS" class="">Cash &amp;
                                                POS</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="cashPOSAmountWrap" class="row hidden">
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="">Amount paid in Cash</label>
                                            <input name="amount_in_cash" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="">Amount via POS</label>
                                            <input name="amount_in_pos" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div id="POSIDWrap" class="hidden">
                                    <div class="form-group">
                                        <label for="">POS Transaction ID</label>
                                        <input name="pos_transaction_id" class="form-control">
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="clearfix main-box-body main-box-button-wrap">
                                <a href="#newParcelForm" data-slide="prev" class="btn btn-default pull-left"><i
                                        class="fa fa-arrow-left"></i> Back</a>
                                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-check"></i>
                                    Save &amp; Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php $this->registerJsFile('@web/js/libs/bootstrap-select.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/new_parcel_form.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
