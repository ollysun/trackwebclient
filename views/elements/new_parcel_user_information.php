<?php use Adapter\Util\Calypso;
	$prefix_map = [ 'shipper' => 'sender', 'receiver' => 'receiver' ];
?>

<div class="form-group">
	<div class="input-group <?= $prefix; ?>-cc-group customer-group">
		<input id="<?=$prefix?>SearchBox"  data-target="#<?php echo $prefix;?>SearchFlyOutPanel" type="text" class="form-control phone" placeholder="Search customers by phone number"
			   value="<?php echo Calypso::getValue($parcel, "info.{$prefix_map[$prefix]}.phone", ''); ?>">
		<div class="input-group-btn">
			<button class="btn btn-default <?=$prefix?>" id="btn_Search_<?=$prefix?>" type="button"><i class="fa fa-search"></i></button>
		</div>
	</div>
	<div class="<?= $prefix; ?>-cc-group corporate-group hide">
		<select class="form-control">
			<option>Choose a Company</option>
		</select>
	</div>
	<div>
			<label class="radio-inline-cc-group">
				<input type="radio" name="<?= $prefix; ?>_customer_corporate_shipments" value="customer" checked>
				Customer shipment
			</label>
			<label class="radio-inline-cc-group">
				<input type="radio" name="<?= $prefix; ?>_customer_corporate_shipments" value="corporate">
				Corporate shipment
			</label>
	</div>
</div>
<div id="<?php echo $prefix;?>SearchFlyOutPanelWrap" class="flyout-panel-wrap">
	<div id="<?php echo $prefix;?>SearchFlyOutPanel" class="flyout-panel">
		<div class="flyout-panel-header">
			<a class="close">&times;</a>
			<h4 class="flyout-panel-title">Search results</h4>
		</div>
		<div class="flyout-panel-body">

		</div>
	</div>
</div>
<div class="row">

	<input name="id[<?=$prefix?>]" id="id_<?=$prefix?>" type="hidden" class="form-control">

	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">First Name</label>
			<input name="firstname[<?=$prefix?>]" id="firstname_<?=$prefix?>" type="text" class="form-control validate required name active-validate"
                   value="<?php echo Calypso::getValue($parcel, "info.{$prefix_map[$prefix]}.firstname", ''); ?>">

		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Last Name</label>
			<input name="lastname[<?=$prefix?>]" id="lastname_<?=$prefix?>" type="text" class="form-control"
                   value="<?php echo Calypso::getValue($parcel, "info.{$prefix_map[$prefix]}.lastname", ''); ?>">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Email address</label>
			<input name="email[<?=$prefix?>]" id="email_<?=$prefix?>" type="text" class="form-control <?php echo $prefix;?>SearchFlyOutPanelTrigger validate active-validate email" data-target="#<?php echo $prefix;?>SearchFlyOutPanel"
                   value="<?php echo Calypso::getValue($parcel, "info.{$prefix_map[$prefix]}.email", ''); ?>">
			<span class="help-block">Format: xyz@example.com</span>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Phone number</label>
			<?php if($prefix == 'receiver') { ?>
				<input name="phone[<?=$prefix?>]" id="phone_<?=$prefix?>" type="text" class="form-control <?php echo $prefix;?>SearchFlyOutPanelTrigger" data-target="#<?php echo $prefix;?>SearchFlyOutPanel"
                    value="<?php echo Calypso::getValue($parcel, "info.{$prefix_map[$prefix]}.phone", ''); ?>">
			<?php } else { ?>
				<input name="phone[<?=$prefix?>]" id="phone_<?=$prefix?>" type="text" class="form-control <?php echo $prefix;?>SearchFlyOutPanelTrigger validate active-validate required phone" data-target="#<?php echo $prefix;?>SearchFlyOutPanel"
				value="<?php echo Calypso::getValue($parcel, "info.{$prefix_map[$prefix]}.phone", ''); ?>">
			<?php } ?>
			<span class="help-block">Format: 234xxxxxxxxxx</span>
		</div>
	</div>
</div>
<div id="<?php echo $prefix;?>SearchFlyOutPanelWrap" class="flyout-panel-wrap">
	<div id="<?php echo $prefix;?>SearchFlyOutPanel" class="flyout-panel">
		<div class="flyout-panel-header">
			<a class="close">&times;</a>
			<h4 class="flyout-panel-title">Search results</h4>
		</div>
		<div class="flyout-panel-body">

		</div>
	</div>
</div>

<div id="<?php echo $prefix;?>AddressFlyOutPanelWrap" class="flyout-panel-wrap">
	<div id="<?php echo $prefix;?>AddressFlyOutPanel" class="flyout-panel transparent">
		<div class="flyout-panel-header">
			<a class="close">&times;</a>
			<h4 class="flyout-panel-title">Select an address</h4>
		</div>
		<div class="flyout-panel-body">
			<div class="address-box-wrap">
				<div class="address-box selected-address default-address">
					<div class="address-box-inner">
						<address>
							28B, Osuntokun Avenue, Old Bodija, Ibadan NG.
						</address>
						<div class="address-box-actions">
							<a href="#" class="address-box-action-default">Set as default</a>
							<a href="#" class="address-box-action-delete">Delete</a>
						</div>
					</div>
				</div>
				<div class="address-box">
					<div class="address-box-inner">
						<address>
							17, Adeniyi Jones Street, off Oba Akran Way, Ikeja NG.
						</address>
						<div class="address-box-actions">
							<a href="#" class="address-box-action-default">Set as default</a>
							<a href="#" class="address-box-action-delete">Delete</a>
						</div>
					</div>
				</div>
				<div class="address-box">
					<div class="address-box-inner">
						<address></address>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="clearfix">
		<label for="">Address</label>
		<a id="<?php echo $prefix;?>AddressFlyOutPanelTrigger" href="#" data-target="#<?php echo $prefix;?>AddressFlyOutPanel" class="pull-right"><small>Manage addresses</small></a>
	</div>
	<input name="address[<?=$prefix?>][]" id="address_<?=$prefix?>_1" class="form-control validate required"
           value="<?php echo Calypso::getValue($parcel, "info.{$prefix_map[$prefix]}_address.street_address1", ''); ?>">
	<input name="address[<?=$prefix?>][]" id="address_<?=$prefix?>_2" class="form-control address-line-1"
           value="<?php echo Calypso::getValue($parcel, "info.{$prefix_map[$prefix]}_address.street_address2", ''); ?>">
</div>

<div class="form-group">
	<label for="country_<?=$prefix?>">Country</label>
	<select name="country[<?=$prefix?>]" class="form-control validate required" id="country_<?=$prefix?>">
		<option value=''>Select Country...</option>
<?php
    $country_id = Calypso::getValue($parcel, "{$prefix_map[$prefix]}_location.country.id", '');
    if (isset($countries) && is_array($countries['data'])) {
	    foreach ($countries['data'] as $item) {
            $selected = ($country_id == $item['id'] || $item['id'] == 1) ? "selected" : '';
?>
            <option value="<?=$item['id']?>" <?=$selected?> ><?=strtoupper($item['name']);?></option>
<?php
        }
    }
?>
	</select>
</div>

<div class="form-group">
	<label for="state_<?=$prefix?>">State</label>
	<select name="state[<?=$prefix?>]" class="form-control validate required" id="state_<?=$prefix?>"
            data-selected-id="<?php echo Calypso::getValue($parcel, "{$prefix_map[$prefix]}_location.state.id", ''); ?>">
		<?php foreach ($states as $state): ?>
			<option
				value="<?= Calypso::getValue($state, 'id', '') ?>"><?= strtoupper(Calypso::getValue($state, 'name', '')); ?></option>
		<?php endforeach; ?>
	</select>
</div>

<div class="form-group">
	<label for="city_<?=$prefix?>">City</label>
	<select name="city[<?=$prefix?>]" class="form-control validate required" disabled="disabled" id="city_<?=$prefix?>"
            data-selected-id="<?php echo Calypso::getValue($parcel, "{$prefix_map[$prefix]}_location.id", ''); ?>"></select>
</div>