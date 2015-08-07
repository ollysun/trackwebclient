<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'View Waybill: '.strtoupper($parcelData['waybill_number']);
$this->params['page_title'] = 'Waybill No: <strong>'.strtoupper($parcelData['waybill_number']).'</strong>';
$this->params['breadcrumbs'][] = 'Waybill';
?>

<?php
	//$this->params['content_header_button'] = '<span class="label label-success">CONFIRMED DELIVERY</span>';
//var_dump($parcelData);
?>

<div class="main-box">
    <div class="main-box-header">
        <h2>Placeholder Waybill</h2>
    </div>
    <div class="main-box-body">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Shipment date</label>
                    <div class=""><?= date('d M Y', strtotime($parcelData['created_date'])); ?></div>
                </div>
                <div class="form-group">
                    <label>Shipper Name</label>
                    <div class="">
                        <?= strtoupper($parcelData['sender']['firstname'] . ' ' . $parcelData['sender']['lastname']); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Shipper Phone number</label>
                    <div class="">
                        <?= $parcelData['sender']['phone']; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Shipper Address</label>
                    <address>
                        <?= $parcelData['sender_address']['street_address1'] . '</br>' . $parcelData['sender_address']['street_address2']; ?>
                    </address>
                </div>
                <div class="form-group">
                    <label>Parcel type</label>
                    <div class="">N/A</div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-6">
                        <label>No of packages</label>
                        <div class=""><?= $parcelData['no_of_package']; ?></div>
                    </div>
                    <div class="form-group col-xs-6">
                        <label>Total actual weight</label>
                        <div class=""><?= $parcelData['weight']; ?>Kg</div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Delivery date</label>
                    <div class="">N/A</div>
                </div>
                <div class="form-group">
                    <label>Receiver Name</label>
                    <div class="">
                        <?= strtoupper($parcelData['receiver']['firstname'] . ' ' . $parcelData['receiver']['lastname']); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Receiver Phone number</label>
                    <div class="">
                        <?= $parcelData['receiver']['phone']; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Receiver Address</label>
                    <address>
                        <?= $parcelData['receiver_address']['street_address1'] . '</br>' . $parcelData['receiver_address']['street_address2']; ?>
                    </address>
                </div>
                <div class="form-group">
                    <label>Shipment type</label>

                    <div class="">N/A</div>
                </div>
                <div class="form-group">
                    <label>Delivery type</label>

                    <div class="">N/A</div>
                </div>

                <div class="form-group">
                    <button
                        onclick="javascript:window.open('<?= Url::to(['site/printwaybill?id=' . $id]) ?>', '_blank', 'toolbar=yes, scrollbars=yes, resizable=yes, top=10, left=50%, width=1100, height=800');"
                        class="btn btn-primary">Print Waybill
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var waybill = "<?= strtoupper($parcelData['waybill_number']); ?>";
</script>
<?php

?>
<?php $this->registerJsFile('@web/js/libs/jquery-barcode.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php //$this->registerJsFile('@web/js/barcode.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>


