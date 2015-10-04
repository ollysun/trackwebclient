<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'View Waybill: '.strtoupper($parcelData['waybill_number']);
$this->params['page_title'] = 'Waybill No: <strong>'.strtoupper($parcelData['waybill_number']).'</strong>';
$this->params['breadcrumbs'][] = 'Waybill';
?>

<?php
	$status = ''.strtoupper(ServiceConstant::getStatus($parcelData['status'])).'';
	$this->params['content_header_button'] = $status.' <button onclick="javascript:window.open(\'/site/printwaybill?id='.$parcelData['id'].'\', \'_blank\', \'toolbar=yes, scrollbars=yes, resizable=yes, top=10, left=50%, width=1100, height=800\');" class="btn btn-primary">Print Waybill
                    </button>';
?>

<div class="main-box no-header">
	<div class="main-box-body row">
		<div class="col-xs-12 col-sm-6">
			<fieldset>
				<legend>Sender Information</legend>
				<div class="row form-group">
					<div class="col-xs-6">
						<label>Name</label>
						<div class="form-control-static"><?= ucwords($parcelData['sender']['firstname'].' '.$parcelData['sender']['lastname']); ?></div>
					</div>
					<div class="col-xs-6">
						<label for="">Email address</label>
						<div class="form-control-static"><?= $parcelData['sender']['email'] ?></div>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-xs-6">
						<label>Phone number</label>
						<div class="form-control-static"><?= $parcelData['sender']['phone'] ?></div>
					</div>

					<div class="col-xs-6">
						<label>Address</label>
						<div class="form-control-static">
							<?= $parcelData['sender_address']['street_address1'] ?>
							<?php if($parcelData['sender_address']['street_address2']) { ?>
								<br><?= $parcelData['sender_address']['street_address2'] ?>
							<?php }?>
							<br>
							<?php
								if (isset($senderLocation, $senderLocation['data']) && is_array($senderLocation['data'])) {
									$data = $senderLocation['data'];
									echo ucwords($data['name']).', '.ucwords($data['state']['name']).', '.ucwords($data['country']['name']);
								}
							?>
						</div>
					</div>
				</div>
			</fieldset>
			<br><br>
		</div>
		<div class="col-xs-12 col-sm-6">
			<fieldset>
				<legend>Receiver Information</legend>
				<div class="row form-group">
					<div class="col-xs-6">
						<label>Name</label>
						<div class="form-control-static"><?= ucwords($parcelData['receiver']['firstname'].' '.$parcelData['receiver']['lastname']); ?></div>
					</div>
					<div class="col-xs-6">
						<label for="">Email address</label>
						<div class="form-control-static"><?= $parcelData['receiver']['email'] ?></div>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-xs-6">
						<label>Phone number</label>
						<div class="form-control-static"><?= $parcelData['receiver']['phone'] ?></div>
					</div>
					<div class="col-xs-6">
						<label>Address</label>
						<div class="form-control-static">
							<?= $parcelData['receiver_address']['street_address1'] ?>
							<?php if($parcelData['receiver_address']['street_address2']) { ?>
								<br><?= $parcelData['receiver_address']['street_address2'] ?>
							<?php }?>
							<br>
							<?php
								if (isset($receiverLocation, $receiverLocation['data']) && is_array($receiverLocation['data'])) {
									$data = $receiverLocation['data'];
									echo ucwords($data['name']).', '.ucwords($data['state']['name']).', '.ucwords($data['country']['name']);
								}
							?>
						</div>

					</div>
				</div>
			</fieldset>
			<br><br>
		</div>
		<div class="col-xs-12">
			<fieldset>
				<legend>Shipment Information</legend>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="row form-group">
								<div class="col-xs-6">
									<label>Parcel type</label>
									<?php
										if(isset($parcelType, $parcelType['data']) && is_array($parcelType['data'])) {
											foreach ($parcelType['data'] as $item) {
												if($item['id'] == $parcelData['parcel_type']) {
													echo '<div class="form-control-static">'.ucwords($item['name']).'</div>';
												}
											}
										}
									?>
								</div>
								<div class="col-xs-6">
									<label>Shipment Weight</label>
									<div class="form-control-static">
										<?= $parcelData['weight']; ?>Kg
									</div>
								</div>
								<div class="col-xs-6 hidden">
									<label>Send parcel to Hub?</label>
									<div class="form-control-static">Yes</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-xs-6">
									<label>No. of packages</label>
									<div class="form-control-static">
										<?= $parcelData['no_of_package']; ?>
									</div>
								</div>
								<div class="col-xs-6">
									<label>Shipment Value</label>
									<div class="form-control-static">
										<span class="currency naira"></span><?= $parcelData['package_value']; ?>
									</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-xs-6">
									<label>Delivery Type</label>
									<div class="form-control-static">
										<?= ucwords(ServiceConstant::getDeliveryType($parcelData['delivery_type'])); ?>
									</div>
								</div>
								<div class="col-xs-6">
									<label>Service type</label>
									<?php
										if(isset($serviceType, $serviceType['data']) && is_array($serviceType['data'])) {
											foreach ($serviceType['data'] as $item) {
												if($item['id'] == $parcelData['shipping_type']) {
													echo '<div class="form-control-static">'.ucwords($item['name']).'</div>';
												}
											}
										}
									?>
								</div>
							</div>
							<div class="form-group">
								<label>Parcel Description</label>
								<div><?= $parcelData['other_info']; ?></div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="row form-group">
								<div class="col-xs-6">
									<label>Sender is a Merchant?</label>
									<div class="form-control-static">
										<?= (empty($senderMerchant)) ? 'No' : 'Yes'; ?>
									</div>
								</div>
								<?php if(!empty(Calypso::getValue($parcelData, 'reference_number', ''))): ?>
								<div class="col-xs-6">
									<label>Reference Number(s)</label>
									<div class="form-control-static">
										REF:<?= Calypso::getValue($parcelData, 'reference_number', ''); ?>
									</div>
								</div>
								<?php endif; ?>
							</div>
							<?php if(!empty($senderMerchant)) { ?>
							<div class="row">
								<div class="col-xs-6 form-group">
									<label>Account Number</label>
									<div class="form-control-static"><?= $senderMerchant['account_no']; ?></div>
								</div>
								<div class="col-xs-6 form-group">
									<label>Bank</label>
									<div class="form-control-static"><?= ucwords($senderMerchant['bank']['name']); ?></div>
								</div>
								<div class="col-xs-12 form-group">
									<label>Account Name</label>
									<div class="form-control-static"><?= $senderMerchant['account_name']; ?></div>
								</div>
							</div>
							<?php } ?>
							<div class="row form-group">
								<div class="col-xs-6">
									<label>Cash on Delivery?</label>
									<div class="form-control-static"><?= ($parcelData['cash_on_delivery']) ? 'Yes': 'No' ; ?></div>
								</div>
								<?php if ($parcelData['cash_on_delivery']) { ?>
								<div class="col-xs-6">
									<label>Amount to be collected</label>
									<div class="form-control-static">
										<span class="currency naira"></span><?= $parcelData['delivery_amount']; ?>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
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
                    <label>Manual Billing</label>
                    <div class="form-control-static">
                        <span class=""></span><?= Calypso::getValue($parcelData, 'is_billing_overridden', 0) == 1 ? 'Yes' : 'No'?>
                    </div>
                </div>
				<div class="form-group">
					<label>Payment Method</label>
					<div class="form-control-static"><?= ServiceConstant::getPaymentMethod($parcelData['payment_type']); ?></div>
				</div>
				<div class="row">
				<?php
					$cash = false;
					$pos = false;
					switch ($parcelData['payment_type']) {
						case ServiceConstant::REF_PAYMENT_METHOD_CASH:
							$cash = true;
							break;

						case ServiceConstant::REF_PAYMENT_METHOD_POS:
							$pos = true;
							break;

						case ServiceConstant::REF_PAYMENT_METHOD_CASH_POS:
							$cash = true;
							$pos = true;
							break;
					}
				?>
				<?php if ($cash) { ?>
					<div class="col-xs-6 form-group">
						<label> Amount collected in cash</label>
						<div class="form-control-static"><span class="currency naira"></span><?= $parcelData['cash_amount']; ?></div>
					</div>
				<?php } ?>
				<?php if ($pos) { ?>
					<div class="col-xs-6 form-group">
						<label> Amount collected via POS</label>
						<div class="form-control-static"><span class="currency naira"></span><?= $parcelData['pos_amount']; ?></div>
					</div>
					<div class="col-xs-6 form-group">
						<label>POS Transaction ID</label>
						<div class="form-control-static"><?= $parcelData['pos_trans_id']; ?></div>
					</div>
				<?php } ?>
				</div>
			</fieldset>
			<br><br>
		</div>
		<div class="col-xs-12 col-sm-6">
			<fieldset>
				<legend>Creation Information</legend>
				<div class="form-group">
					<label>Originating Center</label>
					<div class="form-control-static">
						<?= ucwords($parcelData['created_branch']['name']); ?><br>
						<?= $parcelData['created_branch']['address']; ?>
					</div>
				</div>
				<div class="form-group">
					<label>Date &amp; Time</label>
					<div class="form-control-static"><?= date(ServiceConstant::DATE_TIME_FORMAT,strtotime($parcelData['created_date'])); ?></div>
				</div>
			</fieldset>
			<br><br>
		</div>
	</div>
</div>