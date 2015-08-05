<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'View Waybill: '.strtoupper($parcelData['waybill_number']);
$this->params['page_title'] = 'Waybill No: <strong>'.strtoupper($parcelData['waybill_number']).'</strong>';
$this->params['breadcrumbs'][] = 'Waybill';
?>

<?php
	$this->params['content_header_button'] = '<button onclick="javascript:window.open(\'/site/printwaybill?id='.$parcelData['id'].'\', \'_blank\', \'toolbar=yes, scrollbars=yes, resizable=yes, top=10, left=50%, width=1100, height=800\');" class="btn btn-primary">Print Waybill
                    </button>';
?>

<div class="main-box no-header">
	<div>
		<?php //var_dump($parcelData); ?>
	</div>
	<div class="main-box-body row">
		<div class="col-xs-12 col-sm-6">
			<fieldset>
				<legend>Consignor Information</legend>
				<div class="form-group">
					<label>Name</label>
					<div class="form-control-static"><?= ucwords($parcelData['sender']['firstname'].' '.$parcelData['sender']['lastname']); ?></div>
				</div>
				<div class="row form-group">
					<div class="col-xs-4">
						<label>Phone number</label>
						<div class="form-control-static"><?= $parcelData['sender']['phone'] ?></div>
					</div>
					<div class="col-xs-offset-1 col-xs-7">
						<label for="">Email address</label>
						<div class="form-control-static"><?= $parcelData['sender']['email'] ?></div>
					</div>
				</div>
				<div class="form-group">
					<label>Address</label>
					<div class="form-control-static">
						<?= $parcelData['sender_address']['street_address1'] ?>
					</div>
					<?php if($parcelData['sender_address']['street_address2']) { ?>
					<div>
						<?= $parcelData['sender_address']['street_address2'] ?>
					</div>
					<?php }?>
					<div class="form-control-static">
						<?= $parcelData['sender_address']['city'].', '.$parcelData['sender_address']['state_id'].', '.$parcelData['sender_address']['country_id'];  ?>
					</div>
				</div>
			</fieldset>
			<br><br>
		</div>
		<div class="col-xs-12 col-sm-6">
			<fieldset>
				<legend>Consignee Information</legend>
				<div class="form-group">
					<label>Name</label>
					<div class="form-control-static"><?= ucwords($parcelData['receiver']['firstname'].' '.$parcelData['receiver']['lastname']); ?></div>
				</div>
				<div class="row form-group">
					<div class="col-xs-4">
						<label>Phone number</label>
						<div class="form-control-static"><?= $parcelData['receiver']['phone'] ?></div>
					</div>
					<div class="col-xs-offset-1 col-xs-7">
						<label for="">Email address</label>
						<div class="form-control-static"><?= $parcelData['receiver']['email'] ?></div>
					</div>
				</div>
				<div class="form-group">
					<label>Address</label>
					<div class="form-control-static">
						<?= $parcelData['receiver_address']['street_address1'] ?>
					</div>
					<?php if($parcelData['receiver_address']['street_address2']) { ?>
					<div>
						<?= $parcelData['receiver_address']['street_address2'] ?>
					</div>
					<?php }?>
					<div class="form-control-static">
						<?= $parcelData['receiver_address']['city'].', '.$parcelData['receiver_address']['state_id'].', '.$parcelData['receiver_address']['country_id'];  ?>
					</div>
				</div>
			</fieldset>
			<br><br>
		</div>
		<div class="col-xs-12 row">
			<fieldset>
				<legend>Shipment Information</legend>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="row form-group">
								<div class="col-xs-6">
									<label>Parcel type</label>
									<div class="form-control-static"><?= $parcelData['parcel_type']; ?></div>
								</div>
								<div class="col-xs-6 hidden">
									<label>Send parcel to Hub?</label>
									<div class="form-control-static">Yes</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-xs-4">
									<label>No. of packages</label>
									<div class="form-control-static">
										<?= $parcelData['no_of_package']; ?>
									</div>
								</div>
								<div class="col-xs-4">
									<label>Shipment Weight</label>
									<div class="form-control-static">
										<?= $parcelData['weight']; ?>Kg
									</div>
								</div>
								<div class="col-xs-4">
									<label>Shipment Value</label>
									<div class="form-control-static">
										<span class="currency naira"></span><?= $parcelData['package_value']; ?>
									</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-xs-6">
									<label>Delivery</label>
									<div class="form-control-static"><?= $parcelData['delivery_type']; ?></div>
									<!-- Address delivery // Pickup at Opebi EC -->
								</div>
								<div class="col-xs-6">
									<label>Service type</label>
									<div class="form-control-static"><?= $parcelData['shipping_type']; ?></div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="row form-group">
								<div class="col-xs-6">
									<label>Consignor is a Merchant?</label>
									<div class="form-control-static">
										not available in api
									</div>
								</div>
								<div class="col-xs-6">
									<label>Consignor is a Corporate lead?</label>
									<div class="class-form-control-static">
										not available in api
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-5 form-group">
									<label>Account Number</label>
									<div class="form-control-static">not available in api</div>
								</div>
								<div class="col-xs-7 form-group">
									<label>Bank</label>
									<div class="form-control-static">not available in api</div>
								</div>
								<div class="col-xs-12 form-group">
									<label>Account Name</label>
									<div class="form-control-static">not available in api</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-xs-4">
									<label>Cash on Delivery?</label>
									<div class="form-control-static"><?= $parcelData['cash_on_delivery']; ?></div>
								</div>
								<div class="col-xs-8">
									<label>Amount to be collected</label>
									<div class="form-control-static">
										<span class="currency naira"></span><?= $parcelData['delivery_amount']; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
			</fieldset>
			<br><br>
		</div>
		<div class="col-xs-12 col-sm-6">
			<fieldset>
				<legend>Other Information</legend>
				<div>
					<?= $parcelData['other_info']; ?>
				</div>
			</fieldset>
			<br><br>
		</div>
		<div class="col-xs-12 col-sm-6">
			<fieldset>
				<legend>Billing Information</legend>
				<div class="form-group">
					<label>Billed Amount</label>
					<div class="form-control-static">
						<span class="currency naira"></span><?= $parcelData['amount_due']; ?>
					</div>
				</div>
				<div class="form-group">
					<label>Payment Method</label>
					<div class="form-control-static"><?= $parcelData['payment_type']; ?></div>
				</div>
				<div class="row">
					<div class="col-xs-6 form-group">
						<label> Amount collected in cash</label>
						<div class="form-control-static"><span class="currency naira"></span><?= $parcelData['cash_amount']; ?></div>
					</div>
					<div class="col-xs-6 form-group">
						<label> Amount collected via POS</label>
						<div class="form-control-static"><span class="currency naira"></span><?= $parcelData['pos_amount']; ?></div>
					</div>
					<div class="col-xs-12 form-group">
						<label>POS Transaction ID</label>
						<div class="form-control-static">not available in api</div>
					</div>
				</div>
			</fieldset>
			<br><br>
		</div>
	</div>
</div>