<?php
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/* @var $this yii\web\View */

$this->title = 'Create a New Shipment';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['site/parcels'],
        'label' => 'Shipment',
    ),
    array('label' => $this->title),
);

$is_hub = $branch['branch_type'] == ServiceConstant::BRANCH_TYPE_HUB;
$is_admin = $branch['branch_type'] == ServiceConstant::BRANCH_TYPE_HQ;
?>


<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>
<?= Html::cssFile('@web/css/libs/select2.css') ?>

<?php echo Calypso::showFlashMessages(); ?>

<form id="shipment_create" action="#" target="async_frame" method="post" enctype="multipart/form-data"
      class="validate-form add-required-asterisks" data-keyboard-submit data-watch-changes>

    <div id="newParcelForm" class="l-new-parcel-form carousel slide">
        <ol class="carousel-indicators hidden">
            <li data-target="#newParcelForm" data-slide-to="0" class="active"></li>
            <li data-target="#newParcelForm" data-slide-to="1"></li>
            <li data-target="#newParcelForm" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="main-box item active">
                <div class="row">
                    <div class="col-xs-12 col-lg-6">
                        <div class="main-box-header">
                            <h2>Sender Information</h2>
                        </div>
                        <div class="main-box-body">
                            <?= $this->render('../elements/new_parcel_user_information', ['prefix' => 'shipper', 'countries' => $countries, 'states' => $states, 'parcel' => $parcel, 'companies' => $companies]) ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-lg-6">
                        <div class="main-box-header">
                            <h2>Receiver Information</h2>
                        </div>
                        <div class="main-box-body">
                            <?= $this->render('../elements/new_parcel_user_information', ['prefix' => 'receiver', 'countries' => $countries, 'states' => $states, 'parcel' => $parcel, 'companies' => $companies]) ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix main-box-body main-box-button-wrap">
                    <a href="#newParcelForm" data-slide="next" class="pull-right btn btn-default">Continue <i
                            class="fa fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="main-box item">
                <div class="main-box-header">
                    <h2>Parcel/Shipment Information</h2>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="main-box-body">
                            <?php $edit = Calypso::getValue($parcel, 'info.edit'); ?>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 form-group" style=<?= ($edit) ? 'display:none;' : '' ?>>
                                    <label><?= ($is_hub) ? "Forward to another branch" : "Send parcel to Hub"; ?>
                                        ?</label>

                                    <div class="validate" style=<?= ($edit) ? 'display:none;' : '' ?>>
                                        <div class="radio-inline">
                                            <!--This was done to allow the default to be sent to hub -->
                                            <input id="sendToHubYes" type="radio" name="send_to_hub" value="1"
                                                   style="display:none;"
                                                <?= (Calypso::getValue($parcel, "info.to_hub", '') == "2") ? "" : ' checked="checked"'; ?>>
                                            <label for="sendToHubYes" class="">Yes</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="sendToHubNo" type="radio" name="send_to_hub" value="0"
                                                <?= (Calypso::getValue($parcel, "info.to_hub", '') == "2") ? "checked='checked'" : ""; ?>>
                                            <label for="sendToHubNo" class="">No</label>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($is_hub || $is_admin) {
                                    ?>
                                    <div class="col-xs-12 col-sm-6 form-group" id="hubsWrap">
                                        <label>Destination</label>

                                        <div>
                                            <select class="form-control required validate" name="to_branch_id"
                                                    id="to_branch_id">
                                                <option value="">Select One</option>
                                                <?php
                                                if (isset($centres) && is_array(($centres))):
                                                    foreach ($centres as $hub) {
                                                        if ($branch['id'] != $hub['id']) {
                                                            ?>
                                                            <option
                                                                value="<?= $hub['id']; ?>"><?= strtoupper($hub['name']); ?></option>
                                                            <?php
                                                        }
                                                    }
                                                endif;
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-7 form-group">
                                    <label for="">Parcel Type</label>

                                    <select name="parcel_type" id="" class="form-control validate required">
                                        <option value="">Please select</option>
                                        <?php if (isset($parcelType) && is_array($parcelType['data'])) {
                                            $type_id = Calypso::getValue($parcel, "info.parcel_type", '');
                                            foreach ($parcelType['data'] as $item) {

                                                $selected = ($type_id == $item['id']) ? "selected" : '';
                                                echo "<option value='{$item["id"]}' {$selected}>" . strtoupper($item['name']) . "</option>";
                                            }
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-xs-12 col-sm-5 form-group">
                                    <label>Parcel value</label>

                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <select name="currency" id="currencySelect" class="selectpicker"
                                                    data-width="70px" data-style="btn-default"
                                                    title="Please choose a currency">
                                                <option title="NGN" value="NGN" selected="selected">Naira</option>
                                                <!-- <option title="USD" value="USD">United States Dollars</option>
                                                <option title="EUR" value="EUR">Euro</option>
                                                <option title="GBP" value="GBP">British Pounds</option> -->
                                            </select>
                                        </div>
                                        <input name="parcel_value" type="text"
                                               class="form-control validate non-zero-number"
                                               value="<?= Calypso::getValue($parcel, "info.package_value", ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 form-group" style=<?= ($edit) ? 'display:none;' : '' ?>>
                                    <label>No. of Packages</label>
                                    <input name="no_of_packages"
                                           class="form-control validate required non-zero-integer"
                                           type="text"
                                           value="<?= Calypso::getValue($parcel, "info.no_of_package", ''); ?>">
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
                                               class="form-control validate required non-zero-number" id="weight"
                                               value="<?= Calypso::getValue($parcel, "info.weight", ''); ?>">
                                        <span class="input-group-addon"> Kg</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for=""> Delivery Type </label>

                                <div class='validate'>
                                    <div class="radio-inline">
                                        <input id="deliveryAtAddress" type="radio" name="delivery_type" value="2"
                                            <?= (Calypso::getValue($parcel, "info.delivery_type", '') == "2") ? "checked='checked'" : ''; ?>
                                        >
                                        <label for="deliveryAtAddress" class="">Dispatch</label>
                                    </div>
                                    <div class="radio-inline">
                                        <input id="deliveryAtCentre" type="radio" name="delivery_type" value="1"
                                            <?= (Calypso::getValue($parcel, "info.delivery_type", '') == "1") ? "checked='checked'" : ''; ?>>
                                        <label for="deliveryAtCentre" class="">Pickup</label>
                                    </div>
                                </div>
                            </div>
                            <div id="pickUpWrap" class="form-group hidden">
                                <label for="">Pickup Centre</label>
                                <select name="pickup_centres" id="" class="form-control">
                                    <option value="">Choose One</option>
                                    <?php
                                    if (isset($centres) && is_array(($centres))) {
                                        $shipment_id = Calypso::getValue($parcel, "info.shipping_type", '');
                                        foreach ($centres as $centre) {

                                            $selected = ($shipment_id == $centre['id']) ? "selected" : '';
                                            echo "<option value='{$centre['id']}' {$selected}>" . ucwords($centre['name']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Service Type</label>
                                <select name="shipping_type" id="" class="form-control validate required">
                                    <option value="">Please select</option>
                                    <?php if (isset($ShipmentType) && is_array($ShipmentType['data'])) {

                                        $shipment_id = Calypso::getValue($parcel, "info.shipping_type", '');
                                        foreach ($ShipmentType['data'] as $item) {

                                            $selected = ($shipment_id == $item['id']) ? "selected" : '';
                                            echo "<option value='{$item['id']}' {$selected}>" . strtoupper($item['name']) . "</option>";
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="main-box-body">
                            <div class="form-group">
                                <label>Merchant?</label>

                                <div class="validate">
                                    <div class="radio-inline">
                                        <input id="merchantNew" type="radio" name="merchant" value="yes"
                                            <?= (Calypso::getValue($parcel, "is_merchant", null) == null) ? "" : "checked='checked'"; ?>
                                        >
                                        <label for="merchantNew" class="">Yes</label>
                                    </div>
                                    <div class="radio-inline hidden">
                                        <input id="merchantOld" type="radio" name="merchant" value="old"
                                            <?= (Calypso::getValue($parcel, "sender_merchant.id", null) == null) ? "" : "checked='checked'"; ?>>
                                        <label for="merchantOld" class="">Existing</label>
                                    </div>
                                    <div class="radio-inline">
                                        <input id="merchantNone" type="radio" name="merchant" value="no"
                                            <?= (Calypso::getValue($parcel, "sender_merchant.id", null) == null && Calypso::getValue($parcel, "is_merchant", null) == null) ? "checked='checked'" : ""; ?>>
                                        <label for="merchantNone" class="">No</label>
                                    </div>
                                </div>
                            </div>
                            <div id="bank-account-details"
                                <?= (Calypso::getValue($parcel, "sender_merchant.id", null) == null && Calypso::getValue($parcel, "is_merchant", null) == null) ? 'class="hidden"' : ""; ?>
                            >

                                <input type="hidden" name="account_id" class="form-control" id="account_id"
                                       value="<?= Calypso::getValue($parcel, "sender_merchant.id", ''); ?>">

                                <div class="form-group hidden">
                                    <label for="account_name">Account Name</label>
                                    <input name="account_name" class="form-control" id="account_name"
                                           value="<?= Calypso::getValue($parcel, "sender_merchant.account_name", ''); ?>">
                                </div>
                                <div class="row hidden">
                                    <div class="col-xs-12 col-sm-6 col-lg-5 form-group">
                                        <label>Account No</label>
                                        <input name="account_no" class="form-control" data-validate-length="10"
                                               id="account_no"
                                               value="<?= Calypso::getValue($parcel, "sender_merchant.account_no", ''); ?>">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-lg-7 form-group">
                                        <label>Bank</label>
                                        <select name="bank" class="form-control" id="bank">
                                            <?php
                                            if (isset($Banks) && is_array($Banks['data'])) {

                                                $bank_id = Calypso::getValue($parcel, "sender_merchant.bank_id", '');
                                                foreach ($Banks['data'] as $item) {

                                                    $selected = ($bank_id == $item['id']) ? "selected" : '';
                                                    echo "<option value='{$item['id']}' {$selected}>" . strtoupper($item['name']) . "</option>";
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
                                            <input id="cODYes" type="radio" name="cash_on_delivery" value="true"
                                                <?= (Calypso::getValue($parcel, "info.cash_on_delivery", '') == '1') ? 'checked="checked"' : ""; ?>>
                                            <label for="cODYes" class="">Yes</label>
                                        </div>
                                        <div class="radio-inline">
                                            <!--Ensure that this is always selected by default-->
                                            <input id="cODNo" type="radio" name="cash_on_delivery" value="false"
                                                <?= (Calypso::getValue($parcel, "info.cash_on_delivery", '') != '1') ? 'checked="checked"' : ""; ?>>
                                            <label for="cODNo" class="">No</label>
                                        </div>
                                    </div>
                                    <div id="CODAmountWrap"
                                         class="col-xs-12 col-sm-6 col-lg-7 form-group <?= (Calypso::getValue($parcel, "info.cash_on_delivery", '') == '1') ? '' : "hidden"; ?>">
                                        <label>Amount due to merchant</label>

                                        <div class="input-group">
                                            <span class="input-group-addon currency naira"></span>
                                            <input name="CODAmount" id="CODAmount" class="form-control"
                                                   value="<?= Calypso::getValue($parcel, "info.delivery_amount", ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
                                    <label>Reference Number</label>
                                    <input name="reference_number" class="form-control" id="reference_number"
                                           value="<?= Calypso::getValue($parcel, "info.reference_number", ''); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Corporate lead?</label><br>

                                <div class="validate">
                                    <div class="radio-inline">
                                        <input id="cLeadYes" type="radio" name="corporate_lead" value="true"> <label
                                            for="cLeadYes" class="">Yes</label>
                                    </div>
                                    <div class="radio-inline">
                                        <input id="cLeadNo" type="radio" name="corporate_lead" value="false"
                                               checked="checked"> <label for="cLeadNo" class="">No</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Parcel Description</label>
                                <textarea name="other_info" class="form-control validate length"
                                          data-validate-length-type='word'
                                          data-validate-max-length="50"><?= Calypso::getValue($parcel, "info.other_info", ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="clearfix main-box-body main-box-button-wrap">
                    <a href="#newParcelForm" data-slide="prev" class="btn btn-default pull-left"><i
                            class="fa fa-arrow-left"></i> Back</a>
                    <a href="#newParcelForm" data-slide="next" class="btn btn-default pull-right"
                       data-calculate-amount="true">Continue <i
                            class="fa fa-arrow-right"></i></a>
                </div>

            </div>
            <div class="item">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-sm-push-3">
                        <div class="main-box">
                            <div class="main-box-header">
                                <h2>Shipping Cost</h2>
                            </div>
                            <div class="main-box-body">
                                <div id="auto_billing" class="form-group amount-due-wrap">
                                    <label for="">Amount Due</label>

                                    <div class="amount-due currency naira">0.00</div>
                                    <input type="hidden" name="amount" id="amount"/>
                                </div>
                                <div id="corporate_billing" class="form-group amount-due-wrap"
                                     style="display: none;">
                                    <label for="">Amount Due</label>

                                    <div class="amount-due currency naira">0.00</div>

                                    <div class="row">
                                        <div class="col-md-6 col-xs-12">
                                            <label>Company</label>
                                            <select id="company" class="form-control billing_plan">
                                                <option value="" selected>Select Company</option>
                                                <?php foreach ($companies as $company): ?>
                                                    <option
                                                        value="<?= Calypso::getValue($company, 'id') ?>" <?= Calypso::getValue($parcel, 'company_id') == Calypso::getValue($company, 'id') ? 'selected' : ''; ?>><?= strtoupper(Calypso::getValue($company, 'name')) ?></option>
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
                                    <input type="hidden" name="corporate_amount" id="corporate_amount"/>
                                </div>
                                <div id="manual_billing" class="form-group amount-due-wrap" style="display: none;">
                                    <label for="">Amount Due</label>
                                    <input type="text" class="form-control" name="manual_amount"
                                           id="manual_amount"/>
                                </div>
                                <div class="form-group">
                                    <label for="">Billing Method</label>

                                    <div>
                                        <div class="radio-inline">
                                            <input id="autoBillingMethod" type="radio" name="billing_method"
                                                   value="auto"
                                                   checked="checked"> <label for="autoBillingMethod"
                                                                             class="">Auto</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="manualBillingMethod" type="radio" name="billing_method"
                                                   value="manual">
                                            <label for="manualBillingMethod" class="">Manual</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="corporateBillingMethod" type="radio" name="billing_method"
                                                   value="corporate">
                                            <label for="corporateBillingMethod" class="">Corporate</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Payment Method</label>

                                    <div>
                                        <div class="radio-inline">
                                            <input id="paymentMethodCash" type="radio" name="payment_method"
                                                   value="1"
                                                   checked="checked"
                                                <?= (Calypso::getValue($parcel, "info.payment_type", '') == '1') ? 'checked="checked"' : ""; ?>>
                                            <label for="paymentMethodCash" class="">Cash</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="paymentMethodPOS" type="radio" name="payment_method"
                                                   value="2"
                                                <?= (Calypso::getValue($parcel, "info.payment_type", '') == '2') ? 'checked="checked"' : ""; ?>>
                                            <label for="paymentMethodPOS" class="">POS</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="paymentMethodCashPOS" type="radio" name="payment_method"
                                                   value="3"
                                                <?= (Calypso::getValue($parcel, "info.payment_type", '') == '3') ? 'checked="checked"' : ""; ?>>
                                            <label for="paymentMethodCashPOS" class="">Cash &amp; POS</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="paymentMethodDeferred" type="radio" name="payment_method"
                                                   value="4"
                                                <?= (Calypso::getValue($parcel, "info.payment_type", '') == '4') ? 'checked="checked"' : ""; ?>>
                                            <label for="paymentMethodDeferred" class="">Deferred (Freight)</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="cashPOSAmountWrap"
                                     class="row <?= (Calypso::getValue($parcel, "info.payment_type", '') != '3') ? 'hidden' : ''; ?>">
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="">Amount paid in Cash</label>
                                            <input name="amount_in_cash" class="form-control"
                                                   value="<?= Calypso::getValue($parcel, "info.cash_amount", ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="">Amount via POS</label>
                                            <input name="amount_in_pos" class="form-control"
                                                   value="<?= Calypso::getValue($parcel, "info.pos_amount", ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div id="POSIDWrap"
                                     class="<?= (Calypso::getValue($parcel, "info.payment_type", '') != '2') ? 'hidden' : ''; ?>">
                                    <div class="form-group">
                                        <label for="">POS Transaction ID</label>
                                        <input name="pos_transaction_id" class="form-control"
                                               value="<?= Calypso::getValue($parcel, "info.pos_trans_id", ''); ?>">
                                    </div>
                                </div>
                                <div id="deferred_freight" class="form-group hide">
                                    <label>Add Freight Cost to Cash on Delivery?</label><br>

                                    <div class="validate">
                                        <div class="radio-inline">
                                            <input id="freightYes" type="radio" name="include_freight"
                                                   value="<?= ServiceConstant::TRUE; ?>"> <label
                                                for="freightYes" class="">Yes</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input id="freightNo" type="radio" name="include_freight"
                                                   value="<?= ServiceConstant::FALSE; ?>"
                                                   checked="checked"> <label for="freightNo" class="">No</label>
                                        </div>
                                    </div>
                                </div>
                                <br>

                                <div style="display: <?= ($is_admin && $edit) ? '' : 'none' ?>;">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 form-group">
                                            <label>Insurance</label>

                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <select name="currency" id="currencySelect" class="selectpicker"
                                                            data-width="70px" data-style="btn-default"
                                                            title="Please choose a currency">
                                                        <option title="NGN" value="NGN" selected="selected">Naira
                                                        </option>
                                                    </select>
                                                </div>
                                                <input name="insurance" type="text"
                                                       class="form-control validate number"
                                                       value="<?= (Calypso::getValue($parcel, "info.insurance", '')) ?>">
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6 form-group">
                                            <label>Duty Charge</label>

                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <select name="currency" id="currencySelect" class="selectpicker"
                                                            data-width="70px" data-style="btn-default"
                                                            title="Please choose a currency">
                                                        <option title="NGN" value="NGN" selected="selected">Naira
                                                        </option>
                                                    </select>
                                                </div>
                                                <input name="duty_charge" type="text"
                                                       class="form-control validate number"
                                                       value="<?= (Calypso::getValue($parcel, "info.duty_charge", '')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 form-group">
                                            <label>Handling Charge</label>

                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <select name="currency" id="currencySelect" class="selectpicker"
                                                            data-width="70px" data-style="btn-default"
                                                            title="Please choose a currency">
                                                        <option title="NGN" value="NGN" selected="selected">Naira
                                                        </option>
                                                    </select>
                                                </div>
                                                <input name="handling_charge" type="text"
                                                       class="form-control validate number"
                                                       value="<?= (Calypso::getValue($parcel, "info.handling_charge", '')) ?>">
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6 form-group">
                                            <label>Cost of Crating</label>

                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <select name="currency" id="currencySelect" class="selectpicker"
                                                            data-width="70px" data-style="btn-default"
                                                            title="Please choose a currency">
                                                        <option title="NGN" value="NGN" selected="selected">Naira
                                                        </option>
                                                    </select>
                                                </div>
                                                <input name="cost_of_crating" type="text"
                                                       class="form-control validate number"
                                                       value="<?= (Calypso::getValue($parcel, "info.cost_of_crating", '')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 form-group">
                                            <label>Storage/Demurrage</label>

                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <select name="currency" id="currencySelect" class="selectpicker"
                                                            data-width="70px" data-style="btn-default"
                                                            title="Please choose a currency">
                                                        <option title="NGN" value="NGN" selected="selected">Naira
                                                        </option>
                                                    </select>
                                                </div>
                                                <input name="storage_demurrage" type="text"
                                                       class="form-control validate number"
                                                       value="<?= (Calypso::getValue($parcel, "info.storage_demurrage", '')) ?>">
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6 form-group">
                                            <label>Others</label>

                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <select name="currency" id="currencySelect" class="selectpicker"
                                                            data-width="70px" data-style="btn-default"
                                                            title="Please choose a currency">
                                                        <option title="NGN" value="NGN" selected="selected">Naira
                                                        </option>
                                                    </select>
                                                </div>
                                                <input name="others" type="text"
                                                       class="form-control validate number"
                                                       value="<?= (Calypso::getValue($parcel, "info.others", '')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($parcel['pickup_request_id'])): ?>
                                <input type="hidden" name="pickup_request_id"
                                       value="<?= $parcel['pickup_request_id']; ?>">
                            <?php endif; ?>
                            <?php if (isset($parcel['shipment_request_id'])): ?>
                                <input type="hidden" name="shipment_request_id"
                                       value="<?= $parcel['shipment_request_id']; ?>">
                            <?php endif; ?>

                            <div class="clearfix main-box-body main-box-button-wrap">
                                <a href="#newParcelForm" data-slide="prev" class="btn btn-default pull-left"><i
                                        class="fa fa-arrow-left"></i> Back</a>
                                <?php if ($edit) { ?>
                                    <input type="hidden" name="parcel_id"
                                           value=<?= Calypso::getValue($parcel, 'info.id') ?>>
                                    <button id="update_parcel_btn" type="submit" class="btn btn-primary pull-right">
                                        <i
                                            class="fa fa-check"></i>
                                        UPDATE
                                    </button>
                                <?php } else { ?>
                                    <button id="create_parcel_btn" type="submit" class="btn btn-primary pull-right">
                                        <i
                                            class="fa fa-check"></i>
                                        CREATE
                                    </button>
                                <?php } ?>
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
<?php $this->registerJsFile('@web/js/form-watch-changes.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/select2.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/utils.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/new_parcel_form.js?2.6.1', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php
$this->registerJs('$(".alert").delay(5000).fadeOut(1500);', View::POS_READY);
?>
<script type="text/javascript">
    <?= "var billingPlans = " . Json::encode($billingPlans) . ";";?>
</script>
