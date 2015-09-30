<?php
use yii\helpers\Url;
use yii\helpers\Html;
use Adapter\Util\Calypso;

$session_data = Calypso::getInstance()->session('user_session');
?>
<header class="navbar" id="header-navbar">
	<div class="container">
		<a href="<?= Url::toRoute(['/site/index']) ?>" id="logo" class="navbar-brand">
			<?= Html::img('@web/img/logo.png', ['class' => 'normal-logo logo-white']) ?>
			<?= Html::img('@web/img/logo-black.png', ['class' => 'normal-logo logo-black']) ?>
			<?= Html::img('@web/img/logo-small.png', ['class' => 'small-logo hidden-xs hidden-sm hidden']) ?>
		</a>

		<div class="clearfix">
			<button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
				<span class="sr-only">Toggle navigation</span>
				<span class="fa fa-bars"></span>
			</button>
			<div class="nav-no-collapse navbar-left hidden-sm hidden-xs pull-left" id="header-nav-left">
				<ul class="nav navbar-nav pull-left">
					<li>
						<a class="btn" id="make-small-nav" data-toggle="tooltip" data-placement="bottom" title="Toggle Sidebar">
								<i class="fa fa-bars"></i>
							</a>
					</li>
				</ul>
			</div>
			<?php if(!empty($session_data['branch']) && !empty($session_data['branch']['name'])): ?>
			<div class="nav-no-collapse pull-left navbar-text text-muted text-uppercase">
				<?= $session_data['branch']['name']; ?>
			</div>
			<?php endif; ?>
			<div class="nav-no-collapse pull-right" id="header-nav">
				<ul class="nav navbar-nav pull-right">
					<li class="dropdown profile-dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<?= Html::img('@web/img/avatar.png', ['alt' => '']) ?>
							<div class="profile-details hidden-xs">
								<span class="name"><?= $session_data['fullname']; ?></span>
								<span class="designation"><?= $session_data['role']['name']; ?></span>
							</div>
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu dropdown-menu-right">
							<!-- <li><a href="#"><i class="fa fa-user"></i>Profile</a></li> -->
							<li><a href="<?= Url::toRoute(['/site/gerraout']) ?>" data-method="post"><i class="fa fa-power-off"></i>Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</header>