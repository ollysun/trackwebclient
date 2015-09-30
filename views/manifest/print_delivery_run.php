<?php
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;

?>

<?= Html::cssFile('@web/css/compiled/print-manifest.css') ?>

<?php if (!empty($manifest)):

    $parcelPerSheet = 24;
    // Add 0.5 to round up number of sheets
    $numberOfSheets = round((count(Calypso::getValue($manifest, 'parcels', array())) / $parcelPerSheet) + 0.5);
    ?>

    <?php for ($j = 0; $j < $numberOfSheets; $j++): ?>
    <div class="manifest">
        <div class="manifest-header">
            <?= Html::img('@web/img/tnt-cp-logo.png', ['class' => 'logo pull-left']) ?>
            <h3 class="pull-right manifest-title">SHIPMENT DELIVERY RECORD</h3>
        </div>
        <div class="manifest-header-box text-uppercase clearfix">
            <div class="row">
                <div class="col-xs-4 clearfix">
                    <div class="pull-left inline-underline-title">Delivery Date</div>
                    <div
                        class="inline-underline pull-left inline-underline-sm"><?= date(ServiceConstant::DATE_FORMAT, strtotime($manifest['created_date'])); ?></div>
                </div>
                <div class="col-xs-4 clearfix">
                    <div
                        class="inline-underline pull-right inline-underline-sm"><?= $manifest['from_branch']['name']; ?></div>
                    <div class="pull-right inline-underline-title">Station</div>
                </div>
                <div class="col-xs-4 clearfix">
                    <div class="inline-underline pull-right inline-underline-xs"><?= ($j + 1); ?></div>
                    <div class="pull-right inline-underline-title">Sheet number</div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 clearfix">
                    <div class="pull-left inline-underline-title">Courier Route</div>
                    <div class="inline-underline"></div>
                </div>
                <div class="col-xs-4 pull-right clearfix">
                    <div class="inline-underline pull-right inline-underline-xs"><?= $numberOfSheets; ?></div>
                    <div class="pull-right inline-underline-title">Total Sheets</div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8 clearfix">
                    <div class="pull-left inline-underline-title">Courier Name</div>
                    <div
                        class="inline-underline inline-underline-full"><?= $manifest['holder']['fullname'] . ' (' . $manifest['holder']['staff_id'] . ')'; ?></div>
                </div>
                <div class="col-xs-4 clearfix">
                    <div
                        class="inline-underline pull-right inline-underline-xs"><?= $manifest['no_of_parcels']; ?></div>
                    <div class="pull-right inline-underline-title">Total Shipments</div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-5 clearfix">
                    <div class="pull-left inline-underline-title">Checked by (Name)</div>
                    <div class="inline-underline inline-underline-full"></div>
                </div>
                <div class="col-xs-3 clearfix">
                    <div class="inline-underline pull-right inline-underline-sm"></div>
                    <div class="pull-right inline-underline-title">Date</div>
                </div>
                <div class="col-xs-4 clearfix">
                    <div class="inline-underline pull-right inline-underline-xs"></div>
                    <div class="pull-right inline-underline-title">Total Pieces</div>
                </div>
            </div>
        </div>
        <br>

        <div class="manifest-body rotate-90">
            <div class="row">
                <div class="col-xs-8"></div>
                <div class="col-xs-4">
                    <h3 style="margin: 10px 0 5px !important;">NO: <?= $manifest['id']; ?></h3>
                </div>
            </div>
            <table class="table table-bordered delivery-run-table table-condensed">
                <thead>
                <tr>
                    <th class="show-only-right-cell-border" rowspan="2"></th>
                    <th colspan="4">
                        SHIPMENT INFORMATION
                        <span><br>TO BE ENTERED AT STATION</span>
                    </th>
                    <th colspan="3">DELIVERY INFORMATION</th>
                    <th></th>
                </tr>
                <tr>
                    <th width="220px;">CONSIGNEE NAME</th>
                    <th width="170px;">AIRWAY BILL NUMBER</th>
                    <th width="100px;">ORIGIN CODE</th>
                    <th width="60px;">NO PCS.</th>
                    <th width="210px;">DATE/TIME</th>
                    <th width="90px;">CD</th>
                    <th width="250px;">NAME OF PERSON <br> RECEIVING SHIPMENT</th>
                    <th width="180px;">SIGNATURE</th>
                </tr>
                </thead>
                <tbody>
                <!-- Now to pull in the parcels :) -->
                <?php for ($i = 1; $i <= 24; $i++):
                    // Calculation to determine parcel index
                    // Sheet zero index * parcelsPersheet + non zero count index
                    $parcelIndex = ($j * $parcelPerSheet) + ($i - 1);
                    $parcel = Calypso::getValue($manifest, "parcels.$parcelIndex");
                    ?>
                    <?php if(is_null($parcel)):?>
                    <tr>
                        <td class="show-only-right-cell-border"><?= ($i < 10) ? '0' . $i : '' . $i; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php else: ?>
                        <tr>
                            <td class="show-only-right-cell-border"><?= ($i < 10) ? '0' . $i : '' . $i; ?></td>
                            <td><?= Calypso::getValue($parcel, 'shipper_firstname') . " " . Calypso::getValue($parcel, 'shipper_lastname');?></td>
                            <td><?= Calypso::getValue($parcel, 'waybill_number');?></td>
                            <td><?= strtoupper(Calypso::getValue($manifest, 'from_branch.code'));?></td>
                            <td><?= Calypso::getValue($parcel, 'no_of_package');?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endfor; ?>
<?php endif; ?>