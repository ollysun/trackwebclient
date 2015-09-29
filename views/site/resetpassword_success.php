<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use Adapter\Util\Calypso;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Password Reset Success';
?>
<div class="l-sigin-page">
    <div class="card card-signin">
        <p class="text-center">Your password has been reset successfully, click the link below to login.</p>
        <a class="btn btn-primary btn-block" href="<?= \yii\helpers\Url::base(true);?>">Login</a>
    </div>

    <div id="footer-bar" class="row">
        <p id="footer-copyright" class="col-xs-12">&copy; 2015<?php if(date('Y') > 2015): echo " &ndash; ".date('Y'); endif; ?> CourierPlus. All Rights Reserved.</p>
    </div>
</div>
<?php $this->registerJsFile('@web/js/resetpassword.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>