<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use Adapter\Util\Calypso;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = '';
$data = Calypso::getInstance()->getPageData();
?>
<div class="l-sigin-page">

    <div class="card card-signin">
        <div class="card-header">
            <!-- <h3 class="card-title">Sign In</h3> -->
            <h3 class="card-title">Access Denied</h3>
        </div>
        <div class="card-body">
            <?php if($data): ?>
                <div class="alert alert-danger">
                    <?= $data; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer text-muted text-center">
            <a href="#" onclick="window.history.back()">Go Back</a>
        </div>
    </div>

    <div id="footer-bar" class="row">
        <p id="footer-copyright" class="col-xs-12">&copy; 2015<?php if(date('Y') > 2015): echo " &ndash; ".date('Y'); endif; ?> CourierPlus. All Rights Reserved.</p>
    </div>
</div>
