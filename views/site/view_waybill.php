<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Waybill No: '.strtoupper($parcelData['waybill_number']);
$this->params['breadcrumbs'][] = 'Waybill';
?>

<?php
	$this->params['content_header_button'] = '<span class="label label-success">CONFIRMED DELIVERY</span>';
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
					<div class="form-control-static"><?= date('d M Y',strtotime($parcelData['created_date'])); ?></div>
				</div>
				<div class="form-group">
					<label>Shipper Information</label>
					<div class="form-control-static">
                        <?= strtoupper($parcelData['sender']['firstname'].' '.$parcelData['sender']['firstname']); ?>
						<span style="padding-left: 80px"><?= $parcelData['sender']['phone']; ?></span>
					</div>
					<address>
                        <?= $parcelData['sender_address']['street_address1'].'</br>'.$parcelData['sender_address']['street_address2']; ?>
					</address>
				</div>
				<div class="form-group">
					<label>Parcel type</label>
					<div class="form-control-static">Document</div>
				</div>
				<div class="row">
					<div class="form-group col-xs-6">
						<label>No of packages</label>
						<div class="form-control-static"><?= $parcelData['no_of_package']; ?></div>
					</div>
					<div class="form-group col-xs-6">
						<label>Total actual weight</label>
						<div class="form-control-static"><?= $parcelData['weight']; ?>Kg</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group">
					<label>Delivery date</label>
					<div class="form-control-static">23 June 2015</div>
				</div>
				<div class="form-group">
					<label>Receiver Information</label>
					<div class="form-control-static">
                        <?= strtoupper($parcelData['receiver']['firstname'].' '.$parcelData['receiver']['firstname']); ?>
						<span style="padding-left: 80px"><?= $parcelData['receiver']['phone']; ?></span>
					</div>
					<address>
                        <?= $parcelData['receiver_address']['street_address1'].'</br>'.$parcelData['receiver_address']['street_address2']; ?>
					</address>
				</div>
				<div class="form-group">
					<label>Shipment type</label>
					<div class="form-control-static">Priority Shipping (Air Mail)</div>
				</div>
				<div class="form-group">
					<label>Delivery type</label>
					<div class="form-control-static">Pickup at Allen Office</div>
				</div>
                <div class="form-group">
					<label>Waybill Bar Code</label>
					<div id="barcode" class="form-control-static">Pickup at Allen Office</div>
				</div>
                <div class="form-group">
                   <button onclick="javascript:window.open('<?= Url::to(['site/printwaybill?id='.$id]) ?>', '_blank', 'toolbar=yes, scrollbars=yes, resizable=yes, top=10, left=50%, width=1100, height=800');" class="btn btn-primary">Print Waybill</button>
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
<?php $this->registerJsFile('@web/js/barcode.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>


