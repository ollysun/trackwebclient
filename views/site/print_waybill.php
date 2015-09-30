<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;

$copies = ["Sender's Copy", " EC Copy", "Ack. Copy", "Recipient's Copy"];
$this->title = 'Waybill '.$parcelData['waybill_number'];
?>
<?= Html::cssFile('@web/css/compiled/print-waybill.css') ?>

<div id="main_holder">
<?php for ($i=0; $i < count($copies); $i++) { ?>
<div class="waybill-wrap">
    <?= Html::img('@web/img/waybill-bg.png', ['class' => 'waybill-bg']) ?>
    <div class="copy">
        <?= $copies[$i]; ?>
    </div>
    <div class="waybill-barcode">
        <div id="" class="barcode"></div>
    </div>
    <br/>
    <div class="waybill-no">
        <?= $parcelData['waybill_number']; ?>
    </div>


    <div class="user user--sender">
        <div class="user__inner">
            <div class="user__name">
                <?= strtoupper($parcelData['sender']['firstname'] . ' ' . $parcelData['sender']['lastname']) ?>
            </div>
            <div class="user__tel"><?= $parcelData['sender']['phone'] ?></div>
            <div class="user__address">
                <?= $parcelData['sender_address']['street_address1'] . '<br/>' . $parcelData['sender_address']['street_address2'] . '<br/>';?>
                <?php
                    if (!empty($sender_location)) {
                        echo ucwords($sender_location['name']) . ', ' . ucwords($sender_location['state']['name']);
                    }else {
                        echo $parcelData['sender_address']['city_id'] . ', ' . $parcelData['sender_address']['state_id'];
                    }
                ?>
            </div>
            <div class="user__country">
                <?php
                    if (!empty($sender_location)) {
                        echo strtoupper($sender_location['country']['name']);
                    }
                    else {
                        echo $parcelData['sender_address']['country_id'];
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="user user--receiver">
        <div class="user__inner">
            <div class="user__name">
                <?= strtoupper($parcelData['receiver']['firstname'] . ' ' . $parcelData['receiver']['lastname']) ?>
            </div>
            <div class="user__tel"><?= $parcelData['receiver']['phone'] ?></div>
            <div class="user__address">
                <?= $parcelData['receiver_address']['street_address1'] . '<br/>' . $parcelData['receiver_address']['street_address2'] . '<br/>';?>
                <?php
                    if (!empty($receiver_location)) {
                        echo ucwords($receiver_location['name']) . ', ' . ucwords($receiver_location['state']['name']);
                    }else {
                        echo $parcelData['receiver_address']['city_id'] . ', ' . $parcelData['receiver_address']['state_id'];
                    }
                ?>
            </div>
            <div class="user__country">
                <?php
                    if (!empty($receiver_location)) {
                        echo strtoupper($receiver_location['country']['name']);
                    }
                    else {
                        echo $parcelData['receiver_address']['country_id'];
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="shipped-date">
        <div class="shipped-date__dd"><?= date('d', strtotime($parcelData['created_date'])); ?></div>
        <div class="shipped-date__mm"><?= date('m', strtotime($parcelData['created_date'])); ?></div>
        <div class="shipped-date__yy"><?= date('y', strtotime($parcelData['created_date'])); ?></div>
    </div>

    <div class="reference-no">
        <?= !is_null(Calypso::getValue($parcelData, 'reference_number')) ? Calypso::getValue($parcelData, 'reference_number')  : '';?>
    </div>

    <div class="code">
        <div class="code__origin">
            <?php
                if (!empty($sender_location)) {
                    echo strtoupper($sender_location['state']['code']);
                }
            ?>
        </div>
        <div class="code__destination">
            <?php
                if (!empty($receiver_location)) {
                    echo strtoupper($receiver_location['state']['code']);
                }
            ?>
        </div>
    </div>

    <div class="shipment">
        <div class="shipment__packages"><?= $parcelData['no_of_package']; ?></div>
        <div class="shipment__actual-weight"><?= Calypso::getInstance()->formatWeight($parcelData['weight']); ?>Kg</div>
        <div class="shipment__dimensional-weight"></div>
    </div>

    <div class="service-type">
        <?php
            if(isset($serviceType) && !empty($serviceType)) {
                foreach ($serviceType as $item) {
        ?>
            <div class="service-type__inner<?php if($item['id'] == $parcelData['shipping_type']){echo ' is-active';}?>"><span><?= ucwords($item['name']) ?></span></div>
        <?php
                }
            }
        ?>
    </div>

    <div class="parcel-type">
        <?php
            if(isset($parcelType) && !empty($parcelType)) {
                foreach ($parcelType as $item) {
                    if($item['id'] == $parcelData['parcel_type']) {
                        echo ucwords($item['name']);
                    }
                }
            }
        ?>
    </div>

    <div class="cod">
        <div class="cod__inner">
            <div class="cod__yes <?= $parcelData['cash_on_delivery'] == '1' ? 'is-active' : '' ?> "></div>
            <div class="cod__no <?= $parcelData['cash_on_delivery'] == '1' ? '' : 'is-active' ?>"></div>
            <?php if($parcelData['cash_on_delivery']) { echo '<div class="cod__amt">'.Calypso::getInstance()->formatCurrency($parcelData['delivery_amount']).'</div>'; } ?>
        </div>
    </div>

    <div class="other-info">
        <?= $parcelData['other_info']; ?>
    </div>
</div>
<?php if (!($i & 1)) { echo '<div class="waybill-divider"></div>'; } } ?>
</div>
<script type="text/javascript">
    var waybill = "<?= strtoupper($parcelData['waybill_number']); ?>";
</script>
<?php $this->registerJsFile('@web/js/libs/jquery-barcode.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/html2canvas.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/print.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>

