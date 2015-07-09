<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'New Parcel';
$this->params['breadcrumbs'][] = $this->title;
?>

<form action="#">
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
							<?php
								$prefix = 'shipper';
								include(dirname(__DIR__).'../elements/new_parcel_user_information.php');
								unset($prefix);
							?>

						</div>
					</div>
					<div class="col-xs-12 col-lg-6">
						<div class="main-box-header">
							<h2>Receiver Information</h2>
						</div>
						<div class="main-box-body">
							<?php
								$prefix = 'receiver';
								include(dirname(__DIR__).'../elements/new_parcel_user_information.php');
								unset($prefix);
							?>
						</div>
					</div>
				</div>
				<div class="clearfix main-box-body main-box-button-wrap">
					<a href="#newParcelForm" data-slide="next" class="pull-right btn btn-default">Continue <i class="fa fa-arrow-right"></i></a>
				</div>
			</div>

			<div class="main-box item">
				<div class="main-box-header">
					<h2>Other Information</h2>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="main-box-body">
							<div class="form-group">
								<label for="">Parcel Type</label>
								<div>
									<div class="radio-inline">
										<input id="parcelTypeDoc" type="radio" name="parcel_type" value="doc" checked="checked"> <label for="parcelTypeDoc" class="">Document</label>
									</div>
									<div class="radio-inline">
										<input id="parcelTypeNonDoc" type="radio" name="parcel_type" value="non-doc"> <label for="parcelTypeNonDoc" class="">Non-Document</label>
									</div>
									<div class="radio-inline">
										<input id="parcelTypeHighValue" type="radio" name="parcel_type" value="high-value"> <label for="parcelTypeHighValue" class="">High Value</label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-4 form-group">
									<label>Parcel weight</label>
									<div class="input-group">
										<input class="form-control">
										<span class="input-group-addon">Kg</span>
									</div>
								</div>
								<div class="col-xs-12 col-sm-8 form-group">
									<label>Parcel value</label>
									<div class="input-group">
										<div class="input-group-btn">
											<select id="currencySelect" class="selectpicker" data-width="70px" data-style="btn-default" title="Please choose a currency">
												<option title="NGN" value="NGN" selected="selected">Naira</option>
												<option title="USD" value="USD">United States Dollars</option>
												<option title="EUR" value="EUR">Euro</option>
												<option title="GBP" value="GBP">British Pounds</option>
											</select>
										</div>
										<input type="text" class="form-control">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="">Delivery Type</label>
								<div>
									<div class="radio-inline">
										<input id="deliveryAtCentre" type="radio" name="delivery_type" value="centre"> <label for="deliveryAtCentre" class="">Centre Pickup</label>
									</div>
									<div class="radio-inline">
										<input id="deliveryAtAddress" type="radio" name="delivery_type" value="address" checked="checked"> <label for="deliveryAtAddress" class="">Address delivery</label>
									</div>
								</div>
							</div>
							<div id="pickUpWrap" class="form-group hidden">
								<label for="">Pickup Centre</label>
								<select name="" id="" class="form-control"></select>
							</div>
							<div class="form-group">
								<label for="">Shipping Type</label>
								<select name="" id="" class="form-control"></select>
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
									<input class="form-control">
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-6 col-lg-5 form-group">
										<label>Account No</label>
										<input class="form-control">
									</div>
									<div class="col-xs-12 col-sm-6 col-lg-7 form-group">
										<label>Bank</label>
										<select class="form-control"></select>
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
											<input id="CODAmount" class="form-control">
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
								<textarea class="form-control"></textarea>
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
											<input id="paymentMethodCash" type="radio" name="payment_method" value="cash" checked="checked"> <label for="paymentMethodCash" class="">Cash</label>
										</div>
										<div class="radio-inline">
											<input id="paymentMethodPOS" type="radio" name="payment_method" value="pos"> <label for="paymentMethodPOS" class="">POS</label>
										</div>
										<div class="radio-inline">
											<input id="paymentMethodCashPOS" type="radio" name="payment_method" value="cash_pos"> <label for="paymentMethodCashPOS" class="">Cash &amp; POS</label>
										</div>
									</div>
								</div>
								<div id="cashPOSAmountWrap" class="row hidden">
									<div class="col-xs-12 col-sm-6">
										<div class="form-group">
											<label for="">Amount paid in Cash</label>
											<input class="form-control">
										</div>
									</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group">
											<label for="">Amount via POS</label>
											<input class="form-control">
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

<?= $this->registerJsFile('@web/js/libs/bootstrap-select.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/new_parcel_form.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
