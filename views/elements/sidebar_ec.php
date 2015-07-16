<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div id="nav-col">
	<section id="col-left" class="col-left-nano">
		<div id="col-left-inner" class="col-left-nano-content">
			<div>&nbsp;</div>
			<div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
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
						<a href="<?php //echo ROOT_PATH ?>/parcels.php" class="dropdown-toggle">
							<i class="fa fa-gift"></i>
							<span>Parcels</span>
							<i class="fa fa-angle-right drop-icon"></i>
						</a>
						<ul class="submenu">
							<li>
								<a href="<?= Url::to(['site/parcels']) ?>">
									All Parcels
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['site/processedparcels']) ?>">
									New Parcels
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
                            <!--
                            <li>
                                <a href="<?/*= Url::to(['site/parcelscollected']) */?>">
                                    Collected Parcels
                                </a>
                            </li>
                            <li>
                                <a href="<?/*= Url::to(['site/parcelsintransit']) */?>">
                                    In Transit
                                </a>
                            </li>
                            <li>
                                <a href="<?/*= Url::to(['site/parcelscancelled']) */?>">
                                    Cancelled
                                </a>
                            </li>
                            <li>
                                <a href="<?/*= Url::to(['site/parcelsdelivered']) */?>">
                                    Delivered
                                </a>
                            </li> -->
							<!-- <li>
								<a href="<?= Url::to(['site/newparcel']) ?>">
									New
								</a>
							</li> -->
						</ul>
					</li>
					<!--<li>
						<a href="<?php //echo ROOT_PATH ?>/customer_history_search.php">
							<i class="fa fa-user"></i>
							<span>Customer History</span>
						</a>
					</li> -->
					<li>
						<a href="<?= Url::to(['site/managebranches']) ?>">
							<i class="fa fa-dashboard"></i>
							<span>Manage branches</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</section>
	<div id="nav-col-submenu"></div>
</div>