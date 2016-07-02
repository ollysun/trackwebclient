<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'View Waybill: ' . strtoupper($parcelData['waybill_number']);
$this->params['page_title'] = 'Waybill No: <strong>' . strtoupper($parcelData['waybill_number']) . '</strong>';
$this->params['breadcrumbs'][] = 'Waybill';
?>

<?php
$status = '' . strtoupper(ServiceConstant::getStatus($parcelData['status'])) . '';
if (!in_array(Calypso::getValue($sessionData, 'role_id'), [ServiceConstant::USER_TYPE_COMPANY_ADMIN, ServiceConstant::USER_TYPE_COMPANY_OFFICER])) {
    $this->params['content_header_button'] = $status . ' <button onclick="javascript:window.open(\'/site/printwaybill?id=' . $parcelData['id'] . '\', \'_blank\', \'toolbar=yes, scrollbars=yes, resizable=yes, top=10, left=50%, width=1100, height=800\');" class="btn btn-primary">Print Waybill
                    </button>';
}
?>

<div class="main-box no-header">
    <div class="main-box-body row">
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Sender Information</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Name</label>

                        <div
                            class="form-control-static"><?= ucwords($parcelData['sender']['firstname'] . ' ' . $parcelData['sender']['lastname']); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label for="">Email address</label>

                        <div class="form-control-static"><?= $parcelData['sender']['email'] ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Phone number</label>

                        <div class="form-control-static"><?= $parcelData['sender']['phone'] ?></div>
                    </div>

                    <div class="col-xs-6">
                        <label>Address</label>

                        <div class="form-control-static">
                            <?= $parcelData['sender_address']['street_address1'] ?>
                            <?php if ($parcelData['sender_address']['street_address2']) { ?>
                                <br><?= $parcelData['sender_address']['street_address2'] ?>
                            <?php } ?>
                            <br>
                            <?php
                            if (isset($senderLocation, $senderLocation['data']) && is_array($senderLocation['data'])) {
                                $data = $senderLocation['data'];
                                echo ucwords($data['name']) . ', ' . ucwords($data['state']['name']) . ', ' . ucwords($data['country']['name']);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Receiver Information</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Name</label>

                        <div
                            class="form-control-static"><?= ucwords($parcelData['receiver']['firstname'] . ' ' . $parcelData['receiver']['lastname']); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label for="">Email address</label>

                        <div class="form-control-static"><?= $parcelData['receiver']['email'] ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Phone number</label>

                        <div class="form-control-static"><?= $parcelData['receiver']['phone'] ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Address</label>

                        <div class="form-control-static">
                            <?= $parcelData['receiver_address']['street_address1'] ?>
                            <?php if ($parcelData['receiver_address']['street_address2']) { ?>
                                <br><?= $parcelData['receiver_address']['street_address2'] ?>
                            <?php } ?>
                            <br>
                            <?php
                            if (isset($receiverLocation, $receiverLocation['data']) && is_array($receiverLocation['data'])) {
                                $data = $receiverLocation['data'];
                                echo ucwords($data['name']) . ', ' . ucwords($data['state']['name']) . ', ' . ucwords($data['country']['name']);
                            }
                            ?>
                        </div>

                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12">
            <fieldset>
                <legend>Shipment Information</legend>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="row form-group">
                            <div class="col-xs-6">
                                <label>Parcel type</label>

                                <div class="form-control-static">
                                    <?= ServiceConstant::getParcelType($parcelData['parcel_type']) ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label><?= ($parcelData['qty_metrics'] == ServiceConstant::QTY_METRICS_WEIGHT ? 'Shipment Weight' : 'No. of Pieces'); ?></label>

                                <div class="form-control-static">
                                    <?= $parcelData['weight'] . ($parcelData['qty_metrics'] == ServiceConstant::QTY_METRICS_WEIGHT ? 'Kg' : ''); ?>
                                </div>
                            </div>
                            <div class="col-xs-6 hidden">
                                <label>Send parcel to Hub?</label>

                                <div class="form-control-static">Yes</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-xs-6">
                                <label>No. of packages</label>

                                <div class="form-control-static">
                                    <?= $parcelData['no_of_package']; ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label>Shipment Value</label>

                                <div class="form-control-static">
                                    <span class="currency naira"></span><?= $parcelData['package_value']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-xs-6">
                                <label>Delivery Type</label>

                                <div class="form-control-static">
                                    <?= ucwords(ServiceConstant::getDeliveryType($parcelData['delivery_type'])); ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label>Service type</label>

                                <div class="form-control-static">
                                    <?= ucwords(ServiceConstant::getShippingType($parcelData['shipping_type'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Parcel Description</label>

                            <div><?= $parcelData['other_info']; ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="row form-group">

                            <div class="col-xs-6">
                                <label>Sender is a Merchant?</label>

                                <div class="form-control-static">
                                    <?= (empty($senderMerchant)) ? 'No' : 'Yes'; ?>
                                </div>
                            </div>

                           <div class="col-xs-6">
                               <?php if (!empty(Calypso::getValue($parcelData, 'reference_number', ''))): ?>
                                   <div class="row">
                                       <label>Reference Number(s)</label>

                                       <div class="form-control-static">
                                           REF:<?= Calypso::getValue($parcelData, 'reference_number', ''); ?>
                                       </div>
                                   </div>
                               <?php endif; ?>

                               <?php if (!empty(Calypso::getValue($parcelData, 'order_number', ''))): ?>
                                   <div class="row">
                                       <label>Order Number</label>

                                       <div class="form-control-static">
                                           NO:<?= Calypso::getValue($parcelData, 'order_number', ''); ?>
                                       </div>
                                   </div>
                               <?php endif; ?>
                           </div>
                        </div>
                        <?php if (!empty($senderMerchant)) { ?>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label>Account Number</label>

                                    <div class="form-control-static"><?= $senderMerchant['account_no']; ?></div>
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label>Bank</label>

                                    <div
                                        class="form-control-static"><?= ucwords($senderMerchant['bank']['name']); ?></div>
                                </div>
                                <div class="col-xs-12 form-group">
                                    <label>Account Name</label>

                                    <div class="form-control-static"><?= $senderMerchant['account_name']; ?></div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row form-group">
                            <div class="col-xs-6">
                                <label>Cash on Delivery?</label>

                                <div
                                    class="form-control-static"><?= ($parcelData['cash_on_delivery']) ? 'Yes' : 'No'; ?></div>
                            </div>
                            <?php if ($parcelData['cash_on_delivery']) { ?>
                                <div class="col-xs-6">
                                    <label>Amount to be collected</label>

                                    <div class="form-control-static">
                                        <span
                                            class="currency naira"></span><?= $parcelData['delivery_amount'] + ($parcelData['is_freight_included'] ? $parcelData['amount_due'] : 0); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-10 col-sm-3">
            <fieldset>
                <legend>Billing Information</legend>
                <div class="form-group">
                    <label>Billed Amount</label>

                    <div class="form-control-static">
                        <span class="currency naira"></span><?= $parcelData['base_price']; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Manual Billing</label>

                    <div class="form-control-static">
                        <span
                            class=""></span><?= Calypso::getValue($parcelData, 'is_billing_overridden', 0) == 1 ? 'Yes' : 'No' ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>

                    <div
                        class="form-control-static"><?= ServiceConstant::getPaymentMethod($parcelData['payment_type']); ?></div>
                </div>
                <div class="row">
                    <?php
                    $cash = false;
                    $pos = false;
                    switch ($parcelData['payment_type']) {
                        case ServiceConstant::REF_PAYMENT_METHOD_CASH:
                            $cash = true;
                            break;

                        case ServiceConstant::REF_PAYMENT_METHOD_POS:
                            $pos = true;
                            break;

                        case ServiceConstant::REF_PAYMENT_METHOD_CASH_POS:
                            $cash = true;
                            $pos = true;
                            break;
                    }
                    ?>
                    <?php if ($cash) { ?>
                        <div class="col-xs-6 form-group">
                            <label> Amount collected in cash</label>

                            <div class="form-control-static"><span
                                    class="currency naira"></span><?= $parcelData['cash_amount']; ?></div>
                        </div>
                    <?php } ?>
                    <?php if ($pos) { ?>
                        <div class="col-xs-6 form-group">
                            <label> Amount collected via POS</label>

                            <div class="form-control-static"><span
                                    class="currency naira"></span><?= $parcelData['pos_amount']; ?></div>
                        </div>
                        <div class="col-xs-6 form-group">
                            <label>POS Transaction ID</label>

                            <div class="form-control-static"><?= $parcelData['pos_trans_id']; ?></div>
                        </div>
                    <?php } ?>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-4">

            <fieldset>
                <legend>Extra charges</legend>
                <div class="col-xs-6 form-group">
                    <label> Insurance</label>

                    <div class="form-control-static"><span
                            class="currency naira"></span><?= $parcelData['insurance']; ?></div>
                </div>

                <div class="col-xs-6 form-group">
                    <label> storage/Demurrage</label>

                    <div class="form-control-static"><span
                            class="currency naira"></span><?= $parcelData['storage_demurrage']; ?></div>
                </div>

                <div class="col-xs-6 form-group">
                    <label> Handling charge</label>

                    <div class="form-control-static"><span
                            class="currency naira"></span><?= $parcelData['handling_charge']; ?></div>
                </div>

                <div class="col-xs-6 form-group">
                    <label> Duty Charge</label>

                    <div class="form-control-static"><span
                            class="currency naira"></span><?= $parcelData['duty_charge']; ?></div>
                </div>

                <div class="col-xs-6 form-group">
                    <label> Cost of Crating</label>

                    <div class="form-control-static"><span
                            class="currency naira"></span><?= $parcelData['cost_of_crating']; ?></div>
                </div>

                <div class="col-xs-6 form-group">
                    <label> others</label>

                    <div class="form-control-static"><span
                            class="currency naira"></span><?= $parcelData['others']; ?></div>
                </div>


            </fieldset>
        </div>
        <div class="col-xs-12 col-sm-4">
            <fieldset>
                <legend>Creation Information</legend>
                <div class="form-group">
                    <label> Created By </label>

                    <div class="form-control-static">
                        <?= ucwords($parcelData['created_by']['fullname']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Originating Center</label>

                    <div class="form-control-static">
                        <?= ucwords($parcelData['created_branch']['name']); ?><br>
                        <?= $parcelData['created_branch']['address']; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Date &amp; Time</label>

                    <div
                        class="form-control-static"><?= date(ServiceConstant::DATE_TIME_FORMAT, strtotime($parcelData['created_date'])); ?></div>
                </div>
            </fieldset>
            <br><br>
        </div>
    </div>
</div>



<div class="main-box no-header">
    <div class="main-box-body row">
        <div class="col-sm-12">
            <fieldset>
                <legend>Parcel History</legend>
                <table class="table">
                    <tr>
                        <th>S/No</th>
                        <th>From Branch</th>
                        <th>To Branch</th>
                        <th>Activity</th>
                        <th>Actor</th>
                        <th>Date</th>
                    </tr>
                    <?php
                    $sn = 0;
                    foreach ($histories as $history) {
                        ?>
                        <tr>
                            <td style="text-align: left;"><?= (++$sn) ?></td>
                            <td style="text-align: left;"><?= ucwords($history['from_branch']['name']) ?></td>
                            <td style="text-align: left;"><?= ucwords($history['to_branch']['name']) ?></td>
                            <td style="text-align: left;"><?= ucfirst($history['description']) ?></td>
                            <td style="text-align: left;"><?= ucwords($history['sender_admin']['fullname']) ?></td>
                            <td style="text-align: left;"><?= $history['created_date'] ?></td>
                        </tr>
                    <?php } ?>
                </table>

            </fieldset>
            <br><br>
        </div>
    </div>
</div>