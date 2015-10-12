<?php
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'View Shipment Request ';
$this->params['page_title'] = 'Shipment Request';
$this->params['breadcrumbs'][] = 'Shipment Request';
?>
<div class="main-box no-header">
    <div class="main-box-body row">
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Receiver Details</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Firstname</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'receiver_firstname')?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Lastname</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'receiver_lastname')?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label for="">Email address</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'receiver_email')?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Phone number</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'receiver_phone_number')?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label for="">Company</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($request, 'copmany.name'))?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Address</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'receiver_address')?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>State</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($request, 'receiver_city.name'))?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>City</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($request, 'receiver_state.name'))?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label for="">Country</label>

                        <div class="form-control-static">Nigeria</div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Shipment Details</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Estimated Weight (Kg)</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'estimated_weight')?> Kg</div>
                    </div>
                    <div class="col-xs-6">
                        <label for="">No of packages</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'no_of_packages')?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Shipment Value</label>

                        <div class="form-control-static">NGN <?= Calypso::getValue($request, 'parcel_value')?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Parcel Description</label>

                        <div class="form-control-static">
                            <?= Calypso::getValue($request, 'description')?>
                        </div>

                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Ecommerce</legend>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="row form-group">
                            <div class="col-xs-6">
                                <label>Cash on Delivery</label>

                                <div class="form-control-static">
                                    NGN <?= Calypso::getValue($request, 'cash_on_delivery')?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label>Order Number</label>

                                <div class="form-control-static">
                                    <?= Calypso::getValue($request, 'reference_number')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Creation Information</legend>
                <div class="form-group">
                    <label>Company</label>

                    <div class="form-control-static">
                        <?= strtoupper(Calypso::getValue($request, 'company.name'))?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Created By</label>

                    <div class="form-control-static"><?= ucwords(Calypso::getValue($request, 'created_by.firtname', '') . ' ' . Calypso::getValue($request, 'created_by.lastname'));?></div>
                </div>
                <div class="form-group">
                    <label>Date &amp; Time</label>

                    <div class="form-control-static"><?= Util::formatDate(ServiceConstant::DATE_TIME_FORMAT, Calypso::getValue($request, 'created_at', ''));?></div>
                </div>
            </fieldset>
            <br><br>
        </div>
    </div>
</div>