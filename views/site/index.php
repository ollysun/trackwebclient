<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>
<strong class="text-muted text-uppercase">TODAY'S STATISTICS</strong>
<div class="row">
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="main-box infographic-box colored purple-bg">
            <i class="fa fa-gift"></i>
            <span class="headline">NEW PARCELS IN</span>
            <span class="value">15</span>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="main-box infographic-box colored yellow-bg">
            <i class="fa fa-truck"></i>
            <span class="headline">DELIVERED PARCELS</span>
            <span class="value">37</span>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="main-box infographic-box colored red-bg"> <!-- .emerald-bg -->
            <i class="fa fa-gift"></i>
            <span class="headline">CENTRE PICKUPS</span>
            <span class="value">14</span>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="main-box infographic-box colored green-bg">
            <i class="fa fa-money"></i>
            <span class="headline">MONIES COLLECTED</span>
            <span class="value currency naira">139,295</span>
        </div>
    </div>
</div>
