<div class="form-group">
	<div class="input-group">
		<input id="<?= $prefix ?>SearchBox"  data-target="#<?php echo $prefix; ?>SearchFlyOutPanel" type="text" class="form-control" placeholder="Search phone no or email address">
		<div class="input-group-btn">
			<button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
		</div>
	</div>
</div>
<div id="<?php echo $prefix; ?>SearchFlyOutPanelWrap" class="flyout-panel-wrap">
	<div id="<?php echo $prefix; ?>SearchFlyOutPanel" class="flyout-panel">
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
			<input type="text" class="form-control">
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Last Name</label>
			<input type="text" class="form-control">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Email address</label>
			<input type="text" class="form-control">
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<label for="">Phone number</label>
			<input type="text" class="form-control">
		</div>
	</div>
</div>

<div id="<?php echo $prefix; ?>AddressFlyOutPanelWrap" class="flyout-panel-wrap">
	<div id="<?php echo $prefix; ?>AddressFlyOutPanel" class="flyout-panel transparent">
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
		<a id="<?php echo $prefix; ?>AddressFlyOutPanelTrigger" href="#" data-target="#<?php echo $prefix; ?>AddressFlyOutPanel" class="pull-right"><small>Manage addresses</small></a>
	</div>
	<input class="form-control">
	<input class="form-control address-line-1">
</div>
<div class="form-group">
	<label for="">City</label>
	<input class="form-control">
</div>
<div class="form-group">
	<label for="">Country</label>
	<select class="form-control"></select>
</div>
<div class="form-group">
	<label for="">State</label>
	<select class="form-control" disabled="disabled"></select>
</div>
<div class="form-group">
	<label for="">LGA</label>
	<select class="form-control" disabled="disabled"></select>
</div>