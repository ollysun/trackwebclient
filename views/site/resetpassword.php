<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use Adapter\Util\Calypso;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Reset Password';
?>
<div class="l-sigin-page">
    <div class="card card-signin">
        <?php echo Calypso::showFlashMessages(); ?>
        <div class="card-header">
            <h3 class="card-title">Reset Password</h3>
        </div>

        <?php if(is_bool($showForm)):?>
        <form action="" method="post">
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" required name="password" type="password" class="form-control" />
            </div>
            <div class="form-group">
                <label for="c_password">Confirm Password</label>
                <input id="c_password" required name="c_password" type="password" class="form-control" />
            </div>
            <p id="errorMsg" style="color: red; display: none;" class="text-center">Passwords don't match</p>
            <button id="resetPwdBtn" type="submit" class="btn btn-primary btn-block">Change Password</button>
        </form>
        <?php endif; ?>
    </div>
    <p class="text-center"><a class="forgot-link" href="<?= \yii\helpers\Url::base(true);?>">&larr; Go back</a></p>

    <div id="footer-bar" class="row">
        <p id="footer-copyright" class="col-xs-12">&copy; 2015<?php if(date('Y') > 2015): echo " &ndash; ".date('Y'); endif; ?> CourierPlus. All Rights Reserved.</p>
    </div>
</div>
<?php $this->registerJsFile('@web/js/resetpassword.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>