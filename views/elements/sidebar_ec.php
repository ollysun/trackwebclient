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
								<a href="<?= Url::to(['shipments/processed']) ?>">
									New Shipments
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['shipments/fordelivery']) ?>">
									For Delivery
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['shipments/dispatched']) ?>">
									Dispatched
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['shipments/delivered']) ?>">
									Delivered
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['shipments/forsweep']) ?>">
									For Sweep
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['shipments/all']) ?>">
									All Shipments
								</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="<?= Url::to(['admin/managebranches']) ?>" class="dropdown-toggle">
							<i class="fa fa-user"></i>
							<span>Administrator</span>
							<i class="fa fa-angle-right drop-icon"></i>
						</a>
						<ul class="submenu">
							<li>
								<a href="<?= Url::to(['admin/managebranches']) ?>">Manage branches</a>
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
								<a href="<?= Url::to(['hubs/hubarrival']) ?>">Shipment Arrivals</a>
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
						<a href="<?= Url::to(['shipments/customerhistory']) ?>">
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
					$permission = Calypso::getInstance()->permissionMap();
					$menus = Calypso::getInstance()->getMenus();

					foreach($menus as $k => $v) {
						//var_dump($k);continue;
						?>
						<li>
							<a href="<?= !is_array($v)? Url::to([$v]):'' ?>" class="dropdown-toggle">
								<i class="fa fa-money"></i>
								<span><?= Calypso::getInstance()->normaliseLinkLabel($k); ?></span>
								<i class="fa fa-angle-right drop-icon"></i>
							</a>
							<?php
							if(is_array($menus[$k])){
							?>
							<ul class="submenu">
								<?php
								foreach($menus[$k] as $key => $value){
									if(is_array($value) && isset($value['link'])){
									?>
								<li>
									<a href="<?= Url::to([$value['link']]) ?>">
										<i class="<?= $value['class'] ?>"></i>
										<span><?= Calypso::getInstance()->normaliseLinkLabel($key); ?></span></a>
								</li>
								<?php }else{
												?>
										<li>
											<a href="<?= !is_array($value)? Url::to([$value]):'' ?>" class="dropdown-toggle">
												<i class="fa fa-money"></i>
												<span><?= Calypso::getInstance()->normaliseLinkLabel($key); ?></span>
												<i class="fa fa-angle-right drop-icon"></i>
											</a>
											<?php if(is_array($value)): ?>
												<ul class="submenu">
													<?php
													foreach($value as $subkey => $subvalue){
														if(is_array($subvalue) && isset($subvalue['link'])){
															?>
															<li>
																<a href="<?= Url::to([$subvalue['link']]) ?>">
																	<i class="<?= $subvalue['class'] ?>"></i>
																	<span><?= Calypso::getInstance()->normaliseLinkLabel($subkey); ?></span></a>
															</li>
														<?php }else{
															print_r($subvalue);
														}
													} ?>
												</ul>
											<?php endif ?>
										</li>
											<?php

									}
								} ?>
							</ul>
							<?php } ?>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
	</section>
	<div id="nav-col-submenu"></div>
</div>

