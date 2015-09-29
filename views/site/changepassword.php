<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use Adapter\Util\Calypso;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Change Password';
$data = Calypso::getInstance()->getPageData();

?>
<div class="l-sigin-page">

    <div class="card card-signin">

        <?php echo Calypso::showFlashMessages(); ?>
        <div class="card-header">
            <h3 class="card-title">Change Password</h3>
        </div>
        <form action="changepassword" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="old_password">Old Password</label>
                <input id="old_password" name="old_password" type="password" class="form-control" />
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input id="new_password" name="new_password" type="password" class="form-control" />
            </div>
            <div class="form-group">
                <label for="password">Confirm Password</label>
                <input id="password" name="password" type="password" class="form-control" />
            </div>
            <input type="hidden" name="task" value="change">
            <button type="submit" class="btn btn-primary btn-block">Change Password</button>
        </form>
    </div>
    <p class="text-center"><a class="forgot-link" href="<?= \yii\helpers\Url::base(true);?>">&larr; Go back</a></p>

    <div id="footer-bar" class="row">
        <p id="footer-copyright" class="col-xs-12">&copy; 2015<?php if(date('Y') > 2015): echo " &ndash; ".date('Y'); endif; ?> CourierPlus. All Rights Reserved.</p>
    </div>
</div>
