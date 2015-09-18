<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use Adapter\Util\Calypso;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Tracking Portal';
$data = Calypso::getInstance()->getPageData();
?>


<?php
    // some logic to ensure that tracking data is set.
    // if multiple tracking dataset exists, please use the actionTracksearchdetails function on the site controller
    $trackingData = true;
?>


<?php if(!empty($trackingData)){ ?>
    <div class="clearfix">
        <h1 class="pull-left">Tracking for #2N0000123934023</h1>
        <h4 class="pull-right text-muted">Status: Out for Delivery</h4>
    </div>
    <br>
    <div class="row text-center text-uppercase">
        <div class="col-xs-4">
            <label class="tracking-info-label">Receiver's name</label>
            <div class="tracking-info-value">Olajide Oye</div>
        </div>
        <div class="col-xs-4">
            <label class="tracking-info-label">Pieces</label>
            <div class="tracking-info-value">2</div>
        </div>
        <div class="col-xs-4">
            <label class="tracking-info-label">Weight</label>
            <div class="tracking-info-value">2.5Kg</div>
        </div>
    </div>

    <!-- Uncomment below to see other possible tracking map status -->
    <!-- <div class="tracking-location-wraps">
        <div class="tracking-location arrived-in">
            <div class="fa fa-home tracking-logo"></div>
            <div class="tracking-name">Ikeja EC</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner">Parcel created</span>
            </div>
        </div>

        <div class="tracking-location">
            <i class="fa fa-user tracking-logo"></i>
            <div class="tracking-name">You</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
            </div>
        </div>
    </div>
    <div class="tracking-location-wraps">
        <div class="tracking-location arrived-in">
            <div class="fa fa-home tracking-logo"></div>
            <div class="tracking-name">Ikeja EC</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">10:04AM</span>
            </div>
        </div>
        <div class="tracking-location in-transit">
            <i class="fa fa-building-o tracking-logo"></i>
            <div class="tracking-name">Lagos Hub</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner ">In transit to Lagos Hub</span>
            </div>
        </div>
        <div class="tracking-location">
            <i class="fa fa-user tracking-logo"></i>
            <div class="tracking-name">You</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
            </div>
        </div>
    </div>
    <div class="tracking-location-wraps">
        <div class="tracking-location arrived-in">
            <div class="fa fa-home tracking-logo"></div>
            <div class="tracking-name">Ikeja EC</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">10:04AM</span>
            </div>
        </div>
        <div class="tracking-location arrived-in">
            <i class="fa fa-building-o tracking-logo"></i>
            <div class="tracking-name">Lagos Hub</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">1:18PM</span>
            </div>
        </div>

        <div class="tracking-location in-transit">
            <i class="fa fa-building-o tracking-logo"></i>
            <div class="tracking-name">Benin Hub</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner">In transit to Benin</span>
            </div>
        </div>
        <div class="tracking-location">
            <i class="fa fa-user tracking-logo"></i>
            <div class="tracking-name">You</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
            </div>
        </div>
    </div>
    <div class="tracking-location-wraps">
        <div class="tracking-location arrived-in">
            <div class="fa fa-home tracking-logo"></div>
            <div class="tracking-name">Ikeja EC</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">10:04AM</span>
            </div>
        </div>
        <div class="tracking-location arrived-in">
            <i class="fa fa-building-o tracking-logo"></i>
            <div class="tracking-name">Lagos Hub</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">1:18PM</span>
            </div>
        </div>

        <div class="tracking-location arrived-in">
            <i class="fa fa-building-o tracking-logo"></i>
            <div class="tracking-name">Benin Hub</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">7:32PM</span>
            </div>
        </div>
        <div class="tracking-location">
            <i class="fa fa-user tracking-logo"></i>
            <div class="tracking-name">You</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
            </div>
        </div>
    </div> -->
    <div class="tracking-location-wraps">
        <div class="tracking-location arrived-in">
            <div class="fa fa-home tracking-logo"></div>
            <div class="tracking-name">Ikeja EC</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">10:04AM</span>
            </div>
        </div>
        <div class="tracking-location arrived-in">
            <i class="fa fa-building-o tracking-logo"></i>
            <div class="tracking-name">Lagos Hub</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">1:18PM</span>
            </div>
        </div>
        <div class="tracking-location arrived-in">
            <i class="fa fa-building-o tracking-logo"></i>
            <div class="tracking-name">Benin Hub</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">17 Sept. 2015</span>
                <span class="tracking-status-inner time">7:32PM</span>
            </div>
        </div>
         <div class="tracking-location arrived-in">
            <i class="fa fa-building-o tracking-logo"></i>
            <div class="tracking-name">Port-Harcourt Hub</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner date">18 Sept. 2015</span>
                <span class="tracking-status-inner time">9:59AM</span>
            </div>
        </div>
        <div class="tracking-location in-transit">
            <i class="fa fa-user tracking-logo"></i>
            <div class="tracking-name">You</div>
            <div class="tracking-circle"></div>
            <div class="tracking-bar"></div>
            <div class="tracking-status">
                <span class="tracking-status-inner">On it's way to you</span>
            </div>
        </div>
    </div>
<?php } else { ?>
<div class="row empty-tracking-no">
    <div class="col-xs-6 col-xs-offset-3 text-center">
        <h1>Waybill / Tracking Number not found</h1>
        <p class="text-muted">The waybill / tracking number you entered could not be found on our system. Please search with another number.</p>
    </div>
</div>
<?php } ?>