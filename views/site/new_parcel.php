<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Create a New Parcel';
$this->params['breadcrumbs'] = array(
	array(
	'url' => ['site/parcels'],
	'label' => 'Parcels'
	),
	array('label'=> $this->title)
);
?>

<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>

<form action="#" method="post" enctype="multipart/form-data">

	<div id="newParcelForm" class="l-new-parcel-form carousel slide">
		<ol class="carousel-indicators hidden">
			<li data-target="#newParcelForm" data-slide-to="0" class="active"></li>
			<li data-target="#newParcelForm" data-slide-to="1"></li>
			<li data-target="#newParcelForm" data-slide-to="2"></li>
		</ol>
		<div class="carousel-inner">
			<div class="main-box item active">
				<div class="row">
					<div class="col-xs-12 col-lg-6">
						<div class="main-box-header">
							<h2>Shipper Information</h2>
						</div>
						<div class="main-box-body">
							<?= $this->render('../elements/new_parcel_user_information',['prefix'=>'shipper', 'countries' => $countries]) ?>
						</div>
					</div>
					<div class="col-xs-12 col-lg-6">
						<div class="main-box-header">
							<h2>Receiver Information</h2>
						</div>
						<div class="main-box-body">
							<?= $this->render('../elements/new_parcel_user_information',['prefix'=>'receiver', 'countries' => $countries]) ?>
						</div>
					</div>
				</div>
				<div class="clearfix main-box-body main-box-button-wrap">
					<a href="#newParcelForm" data-slide="next" class="pull-right btn btn-default">Continue <i class="fa fa-arrow-right"></i></a>
				</div>
			</div>

			<div class="main-box item">
				<div class="main-box-header">
					<h2>Parcel/Shipment Information</h2>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="main-box-body">
							<div class="form-group">
								<label>Send parcel to Hub?</label>
								<div>
									<div class="radio-inline">
										<input id="sendToHubYes" type="radio" name="send_to_hub" value="true" checked="checked"> <label for="sendToHubYes" class="">Yes</label>
									</div>
									<div class="radio-inline">
										<input id="sendToHubNo" type="radio" name="send_to_hub" value="false"> <label for="sendToHubNo" class="">No</label>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="">Parcel Type</label>
								<div>
									<div class="radio-inline">
										<input id="parcelTypeDoc" type="radio" name="parcel_type" value="1" checked="checked"> <label for="parcelTypeDoc" class="">NORMAL</label>
									</div>
									<div class="radio-inline">
										<input id="parcelTypeNonDoc" type="radio" name="parcel_type" value="2"> <label for="parcelTypeNonDoc" class="">RETURNS</label>
									</div>
									<div class="radio-inline">
										<input id="parcelTypeHighValue" type="radio" name="parcel_type" value="3"> <label for="parcelTypeHighValue" class="">EXPRESS</label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-3 form-group">
									<label>No. of Packages</label>
									<input name="no_of_packages" class="form-control">
								</div>
								<div class="col-xs-12 col-sm-4 form-group">
									<label>Parcel weight</label>
									<div class="input-group">
										<input name="parcel_weight" class="form-control">
										<span class="input-group-addon">Kg</span>
									</div>
								</div>
								<div class="col-xs-12 col-sm-5 form-group">
									<label>Parcel value</label>
									<div class="input-group">
										<div class="input-group-btn">
											<select name="currency" id="currencySelect" class="selectpicker" data-width="70px" data-style="btn-default" title="Please choose a currency">
												<option title="NGN" value="NGN" selected="selected">Naira</option>
												<option title="USD" value="USD">United States Dollars</option>
												<option title="EUR" value="EUR">Euro</option>
												<option title="GBP" value="GBP">British Pounds</option>
											</select>
										</div>
										<input name="parcel_value" type="text" class="form-control">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="">Delivery Type</label>
								<div>
									<div class="radio-inline">
										<input id="deliveryAtAddress" type="radio" name="delivery_type" value="2" checked="checked"> <label for="deliveryAtAddress" class="">Dispatch</label>
									</div>
									<div class="radio-inline">
										<input id="deliveryAtCentre" type="radio" name="delivery_type" value="1"> <label for="deliveryAtCentre" class="">Pickup</label>
									</div>
								</div>
							</div>
							<div id="pickUpWrap" class="form-group hidden">
								<label for="">Pickup Centre</label>
								<select name="pickup_centres" id="" class="form-control"></select>
							</div>
							<div class="form-group">
								<label for="">Shipping Type</label>
								<select name="shipping_type" id="" class="form-control">
                                    <?php
                                    if(isset($ShipmentType) && is_array($ShipmentType['data'])){
                                        foreach($ShipmentType['data'] as $item){
                                            ?>
                                            <option value="<?= $item['id'] ?>"><?= strtoupper($item['name']); ?></option>
                                        <?php
                                        }}
                                    ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="main-box-body">
							<div class="form-group">
								<label>Merchant?</label>
								<div>
									<div class="radio-inline">
										<input id="merchantNew" type="radio" name="merchant" value="new"> <label for="merchantNew" class="">New</label>
									</div>
									<div class="radio-inline">
										<input id="merchantOld" type="radio" name="merchant" value="old">
										<label for="merchantOld" class="">Existing</label>
									</div>
									<div class="radio-inline">
										<input id="merchantNone" type="radio" name="merchant" checked="checked" value="none">
										<label for="merchantNone" class="">Not applicable</label>
									</div>
								</div>
							</div>
							<div id="bank-account-details" class="hidden">
								<div class="form-group">
									<label for="">Account Name</label>
									<input name="account_name" class="form-control">
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-6 col-lg-5 form-group">
										<label>Account No</label>
										<input name="account_no" class="form-control">
									</div>
									<div class="col-xs-12 col-sm-6 col-lg-7 form-group">
										<label>Bank</label>
										<select name="bank" class="form-control">
                                            <?php
                                            if(isset($Banks) && is_array($Banks['data'])){
                                                foreach($Banks['data'] as $item){
                                            ?>
                                                <option value="<?= $item['id'] ?>"><?= strtoupper($item['name']); ?></option>
                                            <?php
                                            }}
                                            ?>
										</select>
									</div>
								</div>

								<div class="row">
									<div class="col-xs-12 col-sm-6 col-lg-5 form-group">
										<label>Cash on Delivery?</label><br>
										<div class="radio-inline">
											<input id="cODYes" type="radio" name="cash_on_delivery" value="true"> <label for="cODYes" class="">Yes</label>
										</div>
										<div class="radio-inline">
											<input id="cODNo" type="radio" name="cash_on_delivery" checked="checked" value="false">
											<label for="cODNo" class="">No</label>
										</div>
									</div>
									<div id="CODAmountWrap" class="col-xs-12 col-sm-6 col-lg-7 form-group hidden">
										<label>Amount to be collected</label>
										<div class="input-group">
											<span class="input-group-addon currency naira"></span>
											<input name="CODAmount" id="CODAmount" class="form-control">
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label>Corporate lead?</label><br>
								<div class="radio-inline">
									<input id="cLeadYes" type="radio" name="corporate_lead" value="true"> <label for="cLeadYes" class="">Yes</label>
								</div>
								<div class="radio-inline">
									<input id="cLeadNo" type="radio" name="corporate_lead" value="false" checked="checked"> <label for="cLeadNo" class="">No</label>
								</div>
							</div>
							<div class="form-group">
								<label>Other Information</label>
								<textarea name="other_info" class="form-control"></textarea>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="clearfix main-box-body main-box-button-wrap">
					<a href="#newParcelForm" data-slide="prev" class="btn btn-default pull-left"><i class="fa fa-arrow-left"></i> Back</a>
					<a href="#newParcelForm" data-slide="next" class="btn btn-default pull-right">Continue <i class="fa fa-arrow-right"></i></a>
				</div>

			</div>
			<div class="item">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-sm-push-3">
						<div class="main-box">
							<div class="main-box-header">
								<h2>Money Information</h2>
							</div>
							<div class="main-box-body">
								<div class="form-group amount-due-wrap">
									<label for="">Amount Due</label>
									<div class="amount-due currency naira">3,045.00</div>
								</div>
								<div class="form-group">
									<label for="">Payment Method</label>
									<div>
										<div class="radio-inline">
											<input id="paymentMethodCash" type="radio" name="payment_method" value="1" checked="checked"> <label for="paymentMethodCash" class="">Cash</label>
										</div>
										<div class="radio-inline">
											<input id="paymentMethodPOS" type="radio" name="payment_method" value="2"> <label for="paymentMethodPOS" class="">POS</label>
										</div>
										<div class="radio-inline">
											<input id="paymentMethodCashPOS" type="radio" name="payment_method" value="3"> <label for="paymentMethodCashPOS" class="">Cash &amp; POS</label>
										</div>
									</div>
								</div>
								<div id="cashPOSAmountWrap" class="row hidden">
									<div class="col-xs-12 col-sm-6">
										<div class="form-group">
											<label for="">Amount paid in Cash</label>
											<input name="amount_in_cash" class="form-control">
										</div>
									</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group">
											<label for="">Amount via POS</label>
											<input name="amount_in_pos" class="form-control">
										</div>
									</div>
								</div>
								<br>
							</div>
							<div class="clearfix main-box-body main-box-button-wrap">
								<a href="#newParcelForm" data-slide="prev" class="btn btn-default pull-left"><i class="fa fa-arrow-left"></i> Back</a>
								<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-check"></i> Save &amp; Print</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php $this->registerJsFile('@web/js/libs/bootstrap-select.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/new_parcel_form.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
