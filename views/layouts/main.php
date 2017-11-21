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
    <title>CourierPlus &bull; <?= Html::encode($this->title) ?></title>

    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>

    <?php $this->head() ?>

    <!--[if lt IE 9]>
        <?= Html::jsFile('@web/js/libs/html5shiv.js') ?>
        <?= Html::jsFile('@web/js/libs/respond.min.js') ?>
    <![endif]-->
    <script type="text/javascript">
        function getAsyncResponse(code, payload, callback) {
            if(callback.trim().length > 0)
            {
                var fxn = callback.split(".");
                if(fxn.length > 0){
                    if(typeof window[fxn[0]] != 'undefined'){
                        var f = window[fxn[0]];
                        if(typeof f !='undefined' && typeof fxn[1] !='undefined' && typeof f[fxn[1]] == 'function') {
                            f[fxn[1]](code,payload);
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="theme-amethyst fixed-header fixed-leftmenu fixed-footer">
<iframe id="async_frame" name="async_frame" style="display: none; visibility: hidden; border: 0; position: absolute; top: -99999px; left: -99999px"></iframe>
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
