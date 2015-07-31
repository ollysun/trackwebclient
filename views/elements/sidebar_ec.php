<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Util\Calypso;
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
										<a href="<?= Url::to(['billing/matrix']) ?>">View Matrix</a>
									</li>
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
										<a href="<?= Url::to(['billing/exceptions']) ?>">Exceptions</a>
									</li>
									<li>
										<a href="<?= Url::to(['billing/onforwarding']) ?>">Onforwarding Charges</a>
									</li>
								</ul>
							</li>
						</ul>
					</li>
					<li>
						<a href="#" class="dropdown-toggle">
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
								<a href="<?= Url::to(['hubs/hubdispatch']) ?>">Dispatched Shipments</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="<?= Url::to(['site/customerhistory']) ?>">
							<i class="fa fa-user"></i>
							<span>Customer History</span>
						</a>
					</li>
					<li>
						<a href="<?= Url::to(['finance/']) ?>" class="dropdown-toggle">
							<i class="fa fa-money"></i>
							<span>Reconciliations</span>
							<i class="fa fa-angle-right drop-icon"></i>
						</a>
						<ul class="submenu">
							<li>
								<a href="<?= Url::to(['finance/customersall']) ?>">Customers</a>
							</li>
							<li>
								<a href="<?= Url::to(['finance/merchantsdue']) ?>">Merchants</a>
							</li>
						</ul>
					</li>
					<?php
/*					$permission = Calypso::getInstance()->permissionMap();
					$menus = Calypso::getInstance()->getMenus();

					foreach($menus as $k => $v) {
						//var_dump($k);continue;
						*/?><!--
						<li>
							<a href="<?/*= !is_array($v)? Url::to([$v]):'' */?>" class="dropdown-toggle">
								<i class="fa fa-money"></i>
								<span><?/*= Calypso::getInstance()->normaliseLinkLabel($k); */?></span>
								<i class="fa fa-angle-right drop-icon"></i>
							</a>
							<?php
/*							if(is_array($menus[$k])){
							*/?>
							<ul class="submenu">
								<?php
/*								//print_r($menus[$k]);
								foreach($menus[$k] as $key => $value){
								*/?>
								<li>
									<a href="<?/*= Url::to(['finance/customersall']) */?>"><?/*= Calypso::getInstance()->normaliseLinkLabel('Fake_Test'); */?></a>
								</li>
								<?php /*} */?>
							</ul>
							<?php /*} */?>
						</li>
						--><?php
/*					}
					*/?>
				</ul>
			</div>
		</div>
	</section>
	<div id="nav-col-submenu"></div>
</div>