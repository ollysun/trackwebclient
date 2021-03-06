<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 10/14/2016
 * Time: 9:03 AM
 */
use Adapter\Util\Util;

$this->title = 'Tracking Portal';
?>


<div class="tracking-wrap">

    <div class="tracking_item">

        <?php if(is_array($tracking_info) && count($tracking_info) > 0):
            extract($tracking_info)?>
        <div class="row">
            <h2 style="text-align: center">Tracking Number: <?= $tracking_number ?></h2>
            <h4 style="text-align: center">Status: <?= $last_status ?></h4>

            <div class="col-md-6 col-md-offset-3" style="background-color: #ffffff; padding: 10px; border-radius: 10px; min-height: 130px;">
                <h4 class="text-center">Parcel History</h4>

                <table>
                    <?php foreach($histories as $history):?>

                        <tr>
                            <td style="width: 120px;">
                                <?= Util::convertToTrackingDateFormat(\Adapter\Util\Calypso::getValue($history, 'date', '')) ?><br/>
                                <?= Util::convertDateTimeToTime(\Adapter\Util\Calypso::getValue($history, 'date', '')) ?>

                            </td>
                            <td>
                                <p><?= $history['text']?></p>
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
