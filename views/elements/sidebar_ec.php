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
					<?php
					$permission = Calypso::getInstance()->permissionMap();
					$menus = Calypso::getInstance()->getMenus();
					$role = $session_data['role']['id'];
					$branch = $session_data['branch']['branch_type'];
					foreach($menus as $k => $v) {
						if(isset($v['base']) && !Calypso::canAccess($role,$v['base'].'/*'))
						{
							continue;
						}
						if(isset($v['branch']) && !in_array($branch,$v['branch']))
						{
							continue;
						}
						?>
						<li>
							<a href="<?= !is_array($v['base_link'])? Url::to([$v['base_link']]):'#' ?>" class="<?php echo is_array($v['base_link'])?'dropdown-toggle':'' ?>">
								<i class="<?= $v['class']; ?>"></i>
								<span><?= Calypso::getInstance()->normaliseLinkLabel($k); ?></span>
								<?php
								if(is_array($v['base_link'])){
								?>
								<i class="fa fa-angle-right drop-icon"></i>
								<?php } ?>
							</a>
							<?php
							if(isset($v['base_link']) && is_array($v['base_link'])){
							?>
							<ul class="submenu">
								<?php
								foreach($v['base_link'] as $key => $value){
									if(isset($value['base']) && !Calypso::canAccess($role,$value['base'].'/*')){
										continue;
									}
									if(isset($value['base_link']) && !is_array($value['base_link']) && !Calypso::canAccess($role,$value['base_link'])) {
										continue;
									}
									if(isset($value['branch']) && !in_array($branch,$value['branch']))
									{
										continue;
									}
									if(isset($value['base_link']) && !is_array($value['base_link'])){
									?>
								<li>
									<a href="<?= Url::to([$value['base_link']]) ?>">
										<i class="<?= $value['class'] ?>"></i>
										<span><?= Calypso::getInstance()->normaliseLinkLabel($key); ?></span></a>
								</li>
								<?php }else{

											?>
											<li>
												<a href="#" class="dropdown-toggle">
													<i class="<?= $value['class']; ?>"></i>
													<span><?= Calypso::getInstance()->normaliseLinkLabel($key); ?></span>
											<?php
											if(is_array($value['base_link'])){
												?>
													<i class="fa fa-angle-right drop-icon"></i>
											<?php } ?>
												</a>
												<ul class="submenu">
													<?php
												if(isset($value['base_link'])) {
													foreach ($value['base_link'] as $subkey => $subvalue) {
														if (isset($subvalue['base']) && !Calypso::canAccess($role, $subvalue['base'] . '/*')) {
															continue;
														}
														if (isset($subvalue['base_link']) && !is_array($subvalue['base_link']) && !Calypso::canAccess($role, $subvalue['base_link'])) {
															continue;
														}
														if(isset($subvalue['branch']) && !in_array($branch,$subvalue['branch']))
														{
															continue;
														}
														if (isset($subvalue['base_link']) && !is_array($subvalue['base_link'])) {
															?>
															<li>
																<a href="<?= Url::to([$subvalue['base_link']]) ?>">
																	<i class="<?= $subvalue['class'] ?>"></i>
																	<span><?= Calypso::getInstance()->normaliseLinkLabel($subkey); ?></span></a>
															</li>
														<?php }
													} }?>
												</ul>
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
					<li>
						<a href="#" class="dropdown-toggle">
							<i class="fa fa-gift"></i>
							<span>Corporate</span>
							<i class="fa fa-angle-right drop-icon"></i>
						</a>
						<ul class="submenu">
							<li>
								<a href="<?= Url::to(['corporate/requests']); ?>">
									<i class=""></i>
									<span>Requests</span>
								</a>
							</li>
							<li>
								<a href="<?= Url::to(['corporate/users']); ?>">
									<i class=""></i>
									<span>Users</span>
								</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</section>
	<div id="nav-col-submenu"></div>
</div>

