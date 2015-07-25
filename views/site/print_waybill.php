<?php
//var_dump($parcelData);
use Adapter\Util\Calypso;
/*
$copies = ["Sender's Copy","Recipient's Copy","Acknowledgement's Copy"," Express Centre's Copy"];
foreach($copies as $copy) {*/
    ?>
    <div class="row">
    <div class="copy">
        Sender's Copy
    </div>
    <div class="waybill-image">
        <!-- drop waybill image here, remove div below -->
        <label>Waybill Bar Code</label><br/>

        <div style="width: 100%; height: 70%; border: 1px solid black;padding: 10px;">
            <div id="barcode" class="form-control-static"></div>
        </div>
    </div>
    <br/>
    <div class="waybill-no">
        <?= $parcelData['waybill_number']; ?>
    </div>


    <div class="user user--sender">
        <div class="user__inner">
            <div
                class="user__name"><?= strtoupper($parcelData['sender']['lastname'] . ' ' . $parcelData['sender']['firstname']) ?></div>
            <div class="user__tel"><?= $parcelData['sender']['phone'] ?></div>
            <div
                class="user__address"><?= $parcelData['sender_address']['street_address1'] . '<br/>' . $parcelData['sender_address']['street_address2'] ?></div>
            <div class="user__country">Nigeria</div>
        </div>
    </div>
    <div class="user user--receiver">
        <div class="user__inner">
            <div
                class="user__name"><?= strtoupper($parcelData['receiver']['lastname'] . ' ' . $parcelData['receiver']['firstname']) ?></div>
            <div class="user__tel"><?= $parcelData['receiver']['phone'] ?></div>
            <div
                class="user__address"><?= $parcelData['receiver_address']['street_address1'] . '<br/>' . $parcelData['receiver_address']['street_address2'] ?></div>
            <div class="user__country">Nigeria</div>
        </div>
    </div>

    <div class="shipped-date">
        <div class="shipped-date__dd"><?= date('d', strtotime($parcelData['created_date'])); ?></div>
        <div class="shipped-date__mm"><?= date('m', strtotime($parcelData['created_date'])); ?></div>
        <div class="shipped-date__yy"><?= date('y', strtotime($parcelData['created_date'])); ?></div>
    </div>

    <div class="code">
        <div class="code__origin">NG-LOS</div>
        <div class="code__destination">NG-ABJ</div>
    </div>

    <div class="shipment">
        <div class="shipment__packages"><?= $parcelData['no_of_package']; ?></div>
        <div class="shipment__actual-weight"><?= $parcelData['weight']; ?>Kg</div>
        <div class="shipment__dimensional-weight"></div>
    </div>

    <div class="service-type">
        <div class="service-type__inner express"></div>
        <div class="service-type__inner ground is-active"></div>
        <div class="service-type__inner express-2"></div>
    </div>

    <div class="parcel-type">
        <?= $parcelData['other_info']; ?>
    </div>

    <div class="cod">
        <div class="cod__inner">
            <div class="cod__yes <?= $parcelData['cash_on_delivery'] == '1' ? 'is-active' : '' ?> "></div>
            <div class="cod__no <?= $parcelData['cash_on_delivery'] == '1' ? '' : 'is-active' ?>"></div>
            <div class="cod__amt"><?= Calypso::getInstance()->formatCurrency($parcelData['delivery_amount']); ?></div>
        </div>
    </div>

    <div class="other-info">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
        consequat. Duis aute irure dolor in reprehenderit in voluptate
    </div>
    </div>
    <?php
//}
?>
<script type="text/javascript">
    var waybill = "<?= strtoupper($parcelData['waybill_number']); ?>";
</script>
<?php

?>
<?php $this->registerJsFile('@web/js/libs/jquery-barcode.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/html2canvas.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/print.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>

