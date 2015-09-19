<?php
use app\assets\TrackingAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use Adapter\Util\Calypso;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$session_data = Calypso::getInstance()->session('user_session');
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
<body class="theme-amethyst fixed-header fixed-leftmenu fixed-footer">

<?php $this->beginBody() ?>
    <div id="theme-wrapper">
        <?= $this->render('../elements/header') ?>

        <div id="page-wrapper" class="container">
            <div class="row">
                <?= $this->render('../elements/sidebar_ec',['session_data' => $session_data]) ?>
                <div id="content-wrapper">
                    <?= $this->render('../elements/content_header') ?>

                    <?= $content ?>
                    <?= $this->render('../elements/footer') ?>
                </div>
            </div> <!-- /.row -->
        </div> <!-- /#page-wrapper -->
    </div> <!-- /#theme-wrapper -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
