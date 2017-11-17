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
        <form action="" method="post">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" required name="email" type="email" class="form-control" />
            </div>
            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
        </form>
    </div>
    <p class="text-center"><a class="forgot-link" href="<?= \yii\helpers\Url::base(true);?>">&larr; Go back</a></p>


</div>

