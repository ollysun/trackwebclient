<?php
use yii\helpers\Url;
use yii\helpers\Html;
use Adapter\Util\Calypso;

$session_data = Calypso::getInstance()->session('user_session');
?>
<header class="navbar" id="header-navbar">
	<div class="container">
		<a href="<?= Url::to(['site/index']) ?>" id="logo" class="navbar-brand">
			<?= Html::img('@web/img/logo.png', ['class' => 'normal-logo logo-white']) ?>
			<?= Html::img('@web/img/logo-black.png', ['class' => 'normal-logo logo-black']) ?>
			<?= Html::img('@web/img/logo-small.png', ['class' => 'small-logo hidden-xs hidden-sm hidden']) ?>
		</a>

		<div class="clearfix">

			<div class="nav-no-collapse pull-right" id="header-nav">
				<ul class="nav navbar-nav pull-right">
					<li class="dropdown profile-dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<?= Html::img('@web/img/avatar.png', ['alt' => '']) ?>
							<span class="hidden-xs"><?= $session_data['fullname']; ?></span> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="#"><i class="fa fa-user"></i>Profile</a></li>
							<li><a href="#"><i class="fa fa-cog"></i>Settings</a></li>
							<li><a href="#"><i class="fa fa-envelope-o"></i>Messages</a></li>
							<li><a href="<?= Url::to(['site/logout']) ?>"><i class="fa fa-power-off"></i>Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</header>