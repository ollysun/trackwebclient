<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\TrackingAsset;

/* @var $this \yii\web\View */
/* @var $content string */

TrackingAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>CourierPlus T&amp;T &bull; <?= Html::encode($this->title) ?></title>

    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>

    <?php $this->head() ?>

    <!--[if lt IE 9]>
        <?= Html::jsFile('@web/js/libs/html5shiv.js') ?>
        <?= Html::jsFile('@web/js/libs/respond.min.js') ?>
    <![endif]-->

</head>
<body class="theme-amethyst fixed-footer">

<?php $this->beginBody() ?>
    <header class="navbar" id="header-navbar">
        <div class="container">
            <div id="logo" class="navbar-brand navbar-brand-transparent">
                <?= Html::img('@web/img/tnt-cp-logo-color.png', ['class' => 'normal-logo', 'alt' => 'CourierPlus Logo']) ?>
            </div>
            <div class="navbar-text text-muted">TRACKING PORTAL</div>
            <form action="" class="navbar-right navbar-form">
                <input type="text" class="form-control header-track-no-search" placeholder="Enter Waybill / Tracking no">
                <button type="submit" class="btn btn-primary btn-sm">Track</button>
            </form>
        </div>
    </header>
    <br>
    <div class="container">
        <?= $content ?>
    </div>

    <br><br><br><br>

    <div id="footer-bar" class="footer-transparent">
        <p id="footer-copyright" class="">&copy; 2015<?php if(date('Y') > 2015): echo " &ndash; ".date('Y'); endif; ?> CourierPlus. All Rights Reserved.</p>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
