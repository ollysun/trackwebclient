<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Login';
?>
<div class="l-sigin-page">

    <div class="card card-signin">
        <div class="card-header">
            <!-- <h3 class="card-title">Sign In</h3> -->
            <h3 class="card-title">Staff Sign In</h3>
        </div>
        <form action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="text" class="form-control" />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-control" />
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>
        <div class="card-footer text-muted text-center">
            Not yet registered? <a href="#">Sign Up</a>
        </div>
    </div>
    <p class="text-center"><a class="forgot-link" href="#">Forgot password?</a></p>

    <div id="footer-bar" class="row">
        <p id="footer-copyright" class="col-xs-12">&copy; 2015<?php if(date('Y') > 2015): echo " &ndash; ".date('Y'); endif; ?> CourierPlus. All Rights Reserved.</p>
    </div>
</div>
