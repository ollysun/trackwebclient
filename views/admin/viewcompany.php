<?php
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Company Details';
$this->params['page_title'] = strtoupper(Calypso::getValue($company, 'name', ''));
$this->params['breadcrumbs'][] = 'Company Details';
?>
<div class="main-box no-header">
    <div class="main-box-body row">
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Company Detail</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Name</label>

                        <div
                            class="form-control-static"><?= strtoupper(Calypso::getValue($company, 'name', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Reg No</label>

                        <div class="form-control-static"><?= Calypso::getValue($company, 'reg_no', 'N/A'); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Email Address</label>

                        <div class="form-control-static"><?= Calypso::getValue($company, 'email', ''); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Phone number</label>

                        <div class="form-control-static"><?= Calypso::getValue($company, 'phone_number', ''); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-12">
                        <label for="">Address</label>

                        <div class="form-control-static"><?= Calypso::getValue($company, 'address', ''); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>City</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($company, 'city.name', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>State</label>

                        <div class="form-control-static"><?= strtoupper(Calypso::getValue($company, 'state.name', '')); ?></div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Primary Contact</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Name</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'primary_contact.firstname', '')); ?>
                            <?= ucwords(Calypso::getValue($company, 'primary_contact.lastname', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Email Address</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'primary_contact_auth.email', '')); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Phone Number</label>

                        <div
                            class="form-control-static"><?= Calypso::getValue($company, 'primary_contact.phone_number', ''); ?></div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Relations Officer</legend>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Name</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'relations_officer.fullname', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Staff ID</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'relations_officer.staff_id', '')); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-6">
                        <label>Email</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'relations_officer_auth.email', '')); ?></div>
                    </div>
                    <div class="col-xs-6">
                        <label>Phone Number</label>

                        <div
                            class="form-control-static"><?= ucwords(Calypso::getValue($company, 'relations_officer.phone', '')); ?></div>
                    </div>
                </div>
            </fieldset>
            <br><br>
        </div>
        <div class="col-xs-12 col-sm-6">
            <fieldset>
                <legend>Creation Information</legend>
                <div class="form-group">
                    <label>Date &amp; Time</label>

                    <div
                        class="form-control-static"><?= Util::convertDateTimeToTime(Calypso::getValue($company, 'created_date', '')); ?></div>
                </div>
            </fieldset>
            <br><br>
        </div>
    </div>
</div>