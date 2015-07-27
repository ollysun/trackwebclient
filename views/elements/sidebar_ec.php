<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div id="nav-col">
	<section id="col-left" class="col-left-nano">
		<div id="col-left-inner" class="col-left-nano-content">
			<div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
				<div>&nbsp;</div>
				<ul class="nav nav-pills nav-stacked">
					<li class="nav-header nav-header-first hidden-sm hidden-xs">
						Navigation
					</li>
					<li>
						<a href="<?= Url::to(['site/index']) ?>">
							<i class="fa fa-dashboard"></i>
							<span>Dashboard</span>
						</a>
					</li>
					<li>
						<a href="#" class="dropdown-toggle">
							<i class="fa fa-gift"></i>
							<span>Shipments</span>
							<i class="fa fa-angle-right drop-icon"></i>
						</a>
						<ul class="submenu">
							<li>
								<a href="<?= Url::to(['site/processedparcels']) ?>">
									New Shipments
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['site/parcelsfordelivery']) ?>">
									For Delivery
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['site/parcelsforsweep']) ?>">
									For Sweep
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['site/parcels']) ?>">
									All Shipments
								</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="<?= Url::to(['site/managebranches']) ?>" class="dropdown-toggle">
							<i class="fa fa-user"></i>
							<span>Administrator</span>
							<i class="fa fa-angle-right drop-icon"></i>
						</a>
						<ul class="submenu">
							<li>
								<a href="<?= Url::to(['site/managebranches']) ?>">Manage branches</a>
							</li>
							<li>
								<a href="<?= Url::to(['site/managestaff']) ?>">Manage staff accounts</a>
							</li>
							<li>
								<a href="<?= Url::to(['site/billings']) ?>" class="dropdown-toggle">Billing <i class="fa fa-angle-right drop-icon"></i></a>
								<ul class="submenu">
									<li>
										<a href="<?= Url::to(['billing/zones']) ?>">Zones</a>
									</li>
									<li>
										<a href="<?= Url::to(['billing/regions']) ?>">Regions</a>
									</li>
									<li>
										<a href="<?= Url::to(['billing/statemapping']) ?>">State - Region Mapping</a>
									</li>
									<li>
										<a href="<?= Url::to(['billing/citymapping']) ?>">City - State Mapping</a>
									</li>
									<li>
										<a href="<?= Url::to(['billing/weightranges']) ?>">Weight Ranges</a>
									</li>
									<li>
										<a href="<?= Url::to(['billing/pricing']) ?>">Pricing</a>
									</li>
									<li>
										<a href="<?= Url::to(['site/billingexceptions']) ?>">Exceptions</a>
									</li>
									<li>
										<a href="<?= Url::to(['site/billingonforwarding']) ?>">Onforwarding Charges</a>
									</li>
									<li>
										<a href="<?= Url::to(['site/billingmatrix']) ?>">View Matrix</a>
									</li>
								</ul>
							</li>
						</ul>
					</li>
					<li>
						<a href="<?= Url::to(['site/managebranches']) ?>" class="dropdown-toggle">
							<i class="fa fa-building-o"></i>
							<span>Hub</span>
							<i class="fa fa-angle-right drop-icon"></i>
						</a>
						<ul class="submenu">
							<li>
								<a href="<?= Url::to(['site/hubarrival']) ?>">Shipment Arrivals</a>
							</li>
							<li>
								<a href="<?= Url::to(['hubs/destination']) ?>">Set next destination</a>
							</li>
							<li>
								<a href="<?= Url::to(['hubs/delivery']) ?>">For Delivery</a>
							</li>
							<li>
								<a href="<?= Url::to(['site/hubdispatch']) ?>">Dispatched Shipments</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="<?= Url::to(['site/customerhistory']) ?>">
							<i class="fa fa-user"></i>
							<span>Customer History</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</section>
	<div id="nav-col-submenu"></div>
</div>