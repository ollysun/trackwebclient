<?php
use Adapter\Util\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;

?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>


<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <br>

        <h2>There were multiple shipments found for the tracking number you entered. Please select one to continue</h2>
        <br>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table id="table" class="table table-hover">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Waybill No.</th>
                    <th>Shipper</th>
                    <th>Receiver</th>
                    <th>Created at</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 0;
                foreach ($tracking_infos as $waybill_number => $tracking_info):
                ?>
                <tr data-shipment-id="<?= Calypso::getDisplayValue($tracking_info, 'parcel.waybill_number') ?>">
                    <td><?= ++$i ?></td>
                    <td><?= Calypso::getDisplayValue($tracking_info, 'parcel.waybill_number') ?></td>
                    <td data-sender-id="<?= Calypso::getDisplayValue($tracking_info, 'parcel.sender_id') ?>"><?= ucfirst(Calypso::getDisplayValue($tracking_info, 'sender.firstname', '')) . " " . ucfirst(Calypso::getDisplayValue($tracking_info, 'sender.lastname', '')) ?></td>
                    <td data-sender-id="<?= Calypso::getDisplayValue($tracking_info, 'parcel.receiver_id') ?>"><?= ucfirst(Calypso::getDisplayValue($tracking_info, 'receiver.firstname', '')) . " " . ucfirst(Calypso::getDisplayValue($tracking_info, 'receiver.lastname', '')) ?></td>
                    <td><?= Util::formatDate('j M Y h:ma', Calypso::getValue($tracking_info, 'parcel.created_date')) ?></td>
                    <td><?= ServiceConstant::getStatus(Calypso::getValue($tracking_info, 'parcel.status')); ?></td>
                    <td>
                        <a href="<?= Url::to(['?query=' . Calypso::getDisplayValue($tracking_info, 'parcel.waybill_number')]) ?>"
                           class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                    <?php endforeach; ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
