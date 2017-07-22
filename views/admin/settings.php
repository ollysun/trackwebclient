<?php
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Notification';
$this->params['page_title'] = 'Admin Settings';
$this->params['breadcrumbs'][] = 'Settings';
?>

<?= Calypso::showFlashMessages(); ?>

<div class="row">
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"> <i class="fa fa-cogs"> </i> Credit Limit Settings</div>
                    <div class="row" style="padding: 10px;">
                        <form >
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Admin Email</label>
                                <input name="" type="email" class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label data-toggle="Test" title="The Percentage credit limit reached such that a mail will be triggered">
                                    Percentage to Trigger Alert <i class="fa fa-question-circle"> </i>
                                </label> <input name="" type="number" maxlength="3" max="100" min="0" class="form-control"> <br>
                            </div>
                            <input type="checkbox" checked name=""> Check to Alert Clients Also
                        </div>
                        <div class="col-md-4">
                            <label>When Limit is Reached (Warning Level)</label>
                            <div class="form-group">
                            <button class="btn <?= !empty($status["email"])?"btn-primary":""; ?>  btn-xs" data-toggle="modal"
                                    data-target="#editModal<?= isset($status["id"])?$status["id"]:''; ?>"
                                    data-id="<?= isset($status["id"])?$status["id"]:''; ?>">
                                <i class="fa fa-envelope-o"> </i>  Mail to Client
                            </button>

                            <button class="btn <?= !empty($status["email"])?"btn-primary":""; ?>  btn-xs" data-toggle="modal"
                                    data-target="#editModal<?= isset($status["id"])?$status["id"]:''; ?>"
                                    data-id="<?= isset($status["id"])?$status["id"]:''; ?>">
                                <i class="fa fa-envelope-o"> </i>  Mail to Admin
                            </button>
                            </div>

                            <label>Attempt to Exceed Limit</label>
                            <div class="form-group">
                                <button class="btn <?= !empty($status["email"])?"btn-primary":""; ?>  btn-xs" data-toggle="modal"
                                        data-target="#editModal<?= isset($status["id"])?$status["id"]:''; ?>"
                                        data-id="<?= isset($status["id"])?$status["id"]:''; ?>">
                                    <i class="fa fa-envelope-o"> </i>  Mail to Client
                                </button>

                                <button class="btn <?= !empty($status["email"])?"btn-primary":""; ?>  btn-xs" data-toggle="modal"
                                        data-target="#editModal<?= isset($status["id"])?$status["id"]:''; ?>"
                                        data-id="<?= isset($status["id"])?$status["id"]:''; ?>">
                                    <i class="fa fa-envelope-o"> </i>  Mail to Admin
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
                <div class="panel-body">

                </div>
            </div>
        </div>
    </div>
</div>