<?php
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'View Shipment Request';
$this->params['page_title'] = 'Shipment Request';
$this->params['breadcrumbs'][] = 'Shipment Request';
?>
<div class="main-box no-header">
    <div class="main-box-body row">
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Pickup Detail</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Contact Name</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'pickup_name');?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Contact Phone number</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'pickup_phone_number', '');?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>State</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($request, 'pickup_state.name', ''));?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>City</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($request, 'pickup_city.name', ''));?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label for="">Address</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'pickup_address', '');?></div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Destination Detail</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Contact Name</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'destination_name');?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Contact Phone number</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'destination_phone_number');?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>State</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($request, 'destination_state.name', ''));?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>City</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($request, 'destination_city.name', ''));?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label for="">Address</label>

                        <div class="form-control-static"><?= Calypso::getValue($request, 'destination_address');?></div>
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
                        <label>Request Details</label>

                        <div class="form-control-static">
                            <?= Calypso::getValue($request, 'request_detail');?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <label>Shipment Description</label>

                        <div class="form-control-static">
                            <?= Calypso::getValue($request, 'shipment_description');?>
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
                        <?= strtoupper(Calypso::getValue($request, 'company.name'));?>
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