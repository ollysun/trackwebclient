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
    <title>CourierPlus &bull; <?= Html::encode($this->title) ?></title>

    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet'
          type='text/css'>

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
            <?= Html::img('@web/img/courier-logo.png', ['class' => 'normal-logo', 'alt' => 'CourierPlus Logo']) ?>
        </div>
        <div class="navbar-text text-muted"><a href="track">TRACKING PORTAL</a></div>
        <?php if(!isset($this->params['hide-tracking-header-form'])): ?>
        <form id="track-search-form" class="navbar-right navbar-form">
            <div class="form-group" style="width: 500px;">
                <span class="pull-right">
                    <input name="query" type="text" class="form-control header-track-no-search"
                           placeholder="Enter Waybill / Tracking no"
                           value="<?=  implode(',', preg_split('/\r\n|[\r\n]/', Yii::$app->request->getQueryParam('query', '')))  ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Track</button>
                </span>
                <span class="clearfix"></span>
            </div>
        </form>
        <?php endif; ?>
    </div>
</header>
<br>

<div class="container">
    <?= $content ?>
</div>

<br><br><br><br>
<?php $this->registerJsFile('@web/js/track-search.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
