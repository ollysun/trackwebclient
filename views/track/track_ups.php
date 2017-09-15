<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 12/8/2016
 * Time: 1:47 PM
 */

/**
 * @var $tracking_info array
 * @var $tracking_number
 */
use Adapter\Util\Util;
use \Adapter\Util\Calypso;

$this->title = 'Tracking Portal';
?>



<div class="tracking-wrap">

    <div class="tracking_item">

        <?php if(!array_key_exists('Fault', $tracking_info)):
            $activities = Calypso::getValue($tracking_info, 'TrackResponse.Shipment.Package.Activity')
            ?>
            <div class="row">
                <h2 style="text-align: center">Tracking Number: <?= $tracking_number ?></h2>
<!--                <h4 style="text-align: center">Status: <?/*= Calypso::getValue($last_history, 'UpdateDescription') */?></h4>
-->
                <div class="col-md-6 col-md-offset-3" style="background-color: #ffffff; padding: 10px; border-radius: 10px; min-height: 130px;">
                    <h4 class="text-center">Parcel History</h4>

                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: left;">Status</th>
                                <th style="text-align: left;">Location</th>
                                <th style="text-align: left;">Date/Time</th>
                            </tr>
                        </thead>
                        <?php foreach($activities as $history):?>

                            <tr>
                                <td style="text-align: left;">
                                    <p><?=  str_replace('UPS', 'Courierplus', Calypso::getValue($history, 'Status.Description')) ?></p>
                                </td>
                                <td style="text-align: left;">
                                    <p><?=  \Adapter\TrackAdapter::GetUpsLocation($history) ?></p>
                                </td>
                                <td style="width: 120px;">
                                    <?= Util::convertToTrackingDateFormat(Calypso::getValue($history, 'Date', '')) ?><br/>
                                    <?= Util::convertDateTimeToTime(Calypso::getValue($history, 'Time', '')) ?>

                                </td>
                            </tr>


                        <?php endforeach;?>
                    </table>




                </div>

            </div>
        <?php else:?>
            <div class="row empty-tracking-no">
                <div class="col-xs-6 col-xs-offset-3 text-center">
                    <h1>Waybill / Tracking Number <strong><?= $tracking_number ?></strong> not found</h1>
                    <p class="text-muted" style="color:red;">WayBill Number not recognized by the system please contact
                        <a href="mailto:customerservice@courierplus-ng.com"> customerservice@courierplus-ng.com </a>
                        for immediate resolution</p>
                </div>
            </div>
        <?php endif;?>

    </div>

</div>

