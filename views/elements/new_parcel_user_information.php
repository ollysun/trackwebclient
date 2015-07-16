<div class="form-group">
	<div class="input-group">
		<input id="<?=$prefix?>SearchBox"  data-target="#<?php echo $prefix;?>SearchFlyOutPanel" type="text" class="form-control" placeholder="Search phone no or email address">
		<div class="input-group-btn">
			<button class="btn btn-default <?=$prefix?>" id="btn_Search_<?=$prefix?>" type="button"><i class="fa fa-search"></i></button>
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
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">First Name</label>
			<input name="firstname[<?=$prefix?>]" id="firstname_<?=$prefix?>" type="text" class="form-control validate required">

		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Last Name</label>
			<input name="lastname[<?=$prefix?>]" id="lastname_<?=$prefix?>" type="text" class="form-control validate required">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Email address</label>
			<input name="email[<?=$prefix?>]" id="email_<?=$prefix?>" type="text" class="form-control <?php echo $prefix;?>SearchFlyOutPanelTrigger validate required email" data-target="#<?php echo $prefix;?>SearchFlyOutPanel">
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Phone number</label>
			<input name="phone[<?=$prefix?>]" id="phone_<?=$prefix?>" type="text" class="form-control <?php echo $prefix;?>SearchFlyOutPanelTrigger validate required phone" data-target="#<?php echo $prefix;?>SearchFlyOutPanel">
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
						<address>

						</address>
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
	<input name="address[<?=$prefix?>][]" id="address_<?=$prefix?>_1" class="form-control validate required">
	<input name="address[<?=$prefix?>][]" id="address_<?=$prefix?>_2" class="form-control address-line-1">
</div>
<div class="form-group">
	<label for="">City</label>
	<input name="city[<?=$prefix?>]" id="city_<?=$prefix?>" class="form-control validate required">
</div>
<div class="form-group">
	<label for="">Country</label>
	<select name="country[<?=$prefix?>]" class="form-control validate required" id="country_<?=$prefix?>">
		<option value='' selected>Select Country...</option>
<?php
if (isset($countries) && is_array($countries['data'])) {
	foreach ($countries['data'] as $item) {
		?>
				<option value="<?=$item['id']?>"><?=strtoupper($item['name']);?></option>
<?php
}}
?>
	</select>
</div>
<div class="form-group">
	<label for="state[<?=$prefix?>]">State</label>
	<select name="state[<?=$prefix?>]" class="form-control validate required" disabled="disabled" id="state_<?=$prefix?>"></select>
</div>
<!--<div class="form-group">
	<label for="lga[<?/*= $prefix */?>]">LGA</label>
	<select name="lga[<?/*= $prefix */?>]" class="form-control" disabled="disabled" id="lga_<?/*= $prefix */?>"></select>
</div>-->