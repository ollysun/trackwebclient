<?php
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\web\View;

$this->title = (empty($bag)) ? '' : 'Bag: #' . $waybill_number;
?>

<?= Html::cssFile('@web/css/compiled/print-manifest.css') ?>

<?php echo Calypso::showFlashMessages(); ?>

<?php if (!empty($bag)): ?>
    <div class="manifest">
        <div class="manifest-header">
            <?= Html::img('@web/img/tnt-cp-logo.png', ['class' => 'big-logo']) ?>
            <div class="clearfix">
                <h3 class="pull-left manifest-title big-logo-title">BAG SHEET</h3>

                <div class="pull-right clearfix">
                    <span class="pull-left text-uppercase">Date: </span>

                    <div class="inline-underline pull-right">
                        <?= date(ServiceConstant::DATE_TIME_FORMAT, strtotime(Calypso::getValue($bag, 'created_date'))); ?>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-xs-3">
                <?= strtoupper(Calypso::getValue($bag, 'from_branch.name')); ?>
                <hr class="manifest-hr">
                Origin Station
            </div>
            <div class="col-xs-3 col-xs-offset-1">
                <?= strtoupper(Calypso::getValue($bag, 'to_branch.name')); ?>
                <hr class="manifest-hr">
                Destination
            </div>
            <div class="col-xs-3 col-xs-offset-1">
                <?= Calypso::getDisplayValue($bag, 'seal_id', 'N/A'); ?>
                <hr class="manifest-hr">
                SEAL ID
            </div>

        </div>
        <br>

        <div class="manifest-body">
            <table class="table table-bordered table-condensed">
                <thead>
                <tr>
                    <th width="16%">WAYBILL NO</th>
                    <th width="8%">PCS</th>
                    <th width="8%">WT</th>
                    <th width="35%">DESCRIPTION OF SHIPMENT(S)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $totalWeight = 0;
                $totalNumber = 0;
                foreach (Calypso::getValue($bag, 'parcels', array()) as $parcel):
                    $totalNumber += (int)Calypso::getValue($parcel, 'no_of_package');
                    $totalWeight += (int)Calypso::getValue($parcel, 'weight');
                    ?>
                    <tr>
                        <td><?= Calypso::getValue($parcel, 'waybill_number') ?></td>
                        <td><?= Calypso::getValue($parcel, 'no_of_package') ?></td>
                        <td><?= Calypso::getValue($parcel, 'weight') ?> KG</td>
                        <td><?= Calypso::getValue($parcel, 'other_info') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td style="border-left-color: transparent; border-bottom-color: transparent;" colspan="1">TOTAL</td>
                    <td><?= $totalNumber ?></td>
                    <td><?= $totalWeight ?> KG</td>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
    </div>
    <?php $this->registerJs("window.print();", View::POS_READY, 'print'); ?>
<?php endif; ?>
