<?php
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Notification';
$this->params['page_title'] = 'Notification';
$this->params['breadcrumbs'][] = 'Notification';
?>

<?= Calypso::showFlashMessages(); ?>

<div class="row">
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"> <i class="fa fa-cogs"> </i> Status Notification Settings</div>

                <div class="panel-body">
                    <table class="table table-striped table-hover table-responsive">
                        <thead>
                            <tr>
                                <td>Status</td>
                                <td colspan="2">Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($statuses as $status) {
                            ?>
                            <tr>
                                <td><?= isset($status["name"])?ucfirst(implode(' ',explode('_',$status["name"]))):''; ?></td>
                                <td>
                                    <button class="btn <?= !empty($status["email"])?"btn-primary":""; ?>  btn-xs" data-toggle="modal"
                                            data-target="#editModal<?= isset($status["id"])?$status["id"]:''; ?>"
                                            data-id="<?= isset($status["id"])?$status["id"]:''; ?>">
                                        <i class="fa fa-envelope-o"> </i> Email Setup
                                    </button>
                                </td>
                                <td>
                                    <button class="btn <?= !empty($status["text"])?"btn-primary":""; ?>  btn-xs" data-toggle="modal"
                                            data-target="#editModaltext<?= isset($status["id"])?$status["id"]:''; ?>"
                                            data-id="<?=isset($status["id"])?$status["id"]:''; ?>"> <i class="fa fa-file-text-o"> </i>  Text Message Setup</button>
                                </td>

<!--                                <td>-->
<!--                                    <button class="btn --><?//= !empty($status["text"])?"btn-primary":""; ?><!--  btn-xs" data-toggle="modal"-->
<!--                                            data-target="#editModaltext--><?//= isset($status["id"])?$status["id"]:''; ?><!--"-->
<!--                                            data-id="--><?//= isset($status["id"])?$status["id"]:''; ?><!--"> <i class="fa fa-file-text-o"> </i>  Sub-Notification</button>-->
<!--                                </td>-->

                            </tr>
                            <?php
                        }
                        $status=[];
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>

        <?php
        foreach ($statuses as $status) {
        ?>
            <div class="modal fade" id="editModal<?= isset($status["id"])?$status["id"]:''; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <form class="" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Edit Notification Email</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <input type="text" class="form-control" disabled name="status" value="<?= isset($status["name"])?ucfirst(implode(' ',explode('_',$status["name"]))):''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="">Subject</label>
                                    <input type="text" class="form-control" name="subject" value="<?= isset($status["subject"])?$status["subject"]:''; ?>">
                                </div>
                                <div class="form-group">

                                    <textarea name="emaildata"><?= isset($status["email"])?$status["email"]:''; ?></textarea>

                                </div>

                                <p class="text text-info" style="font-size:small; color: #0b0b0b; border: 1px solid #777; padding: 5px; background: #eef; border-radius: 5px;">
                                    <frameset>
                                        Insert the square brackets code where you want the relevant information to show <br>
                                        <table style="font-size: 10px; color: #8F44AD; width: 100%">
                                            <tr><td>Sender Name {{sender_name}} </td><td> Receiver Name {{receiver_name}} </td>
                                            <td>Receiver Email {{receiver_email}} </td><tr><td> Sender Email {{sender_email}} </td>
                                            <td>Receiver Number {{receiver_number}} </td><td> Sender Number {{sender_number}} </td></tr>
                                            <tr><td>Waybill Number {{waybill_number}} </td><td> Trace Link {{tracelink}} </td>
                                            <td>Amount Due {{amount_due}} </td></tr>
                                        </table>

                                    </frameset>

                                </p>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="task" value="email">
                                <input type="hidden" name="status_id" value="<?= isset($status["id"])?$status["id"]:''; ?>">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <input type="submit" class="btn btn-primary" value="Save changes">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        <div class="modal fade" id="editModaltext<?= isset($status["id"])?$status["id"]:''; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <form class="" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Edit Notification Text</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Status</label>
                                <input type="text" class="form-control" disabled name="status" value="<?= isset($status["name"])?ucfirst(implode(' ',explode('_',$status["name"]))):''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="">Test Message</label>
                                <input type="text" style="height: 70px" class="form-control" name="textdata" value="<?= isset($status["text"])?$status["text"]:''; ?>">
                                <hr>
                                <p class="text text-info" style="font-size:small; color: #0b0b0b; border: 1px solid #777; padding: 5px; background: #eef; border-radius: 5px;">
                                    <frameset>
                                    <legend style="font-size: small; color: #0b0b0b;">Legend</legend>
                                    Insert the square brackets code where you want the relevant information to show <br>
                                        <table>
                                            <tr><td>Sender Name as {{sender_phone}} </td><td> Receiver Name as {{receiver_phone}} </td></tr>
                                            <tr><td>Receiver Email as {{receiver_email}} </td><td> Sender Email as {{sender_phone}} </td></tr>
                                            <tr><td>Receiver Number {{receiver_phone}} </td><td> Sender Number {{sender_phone}} </td></tr>
                                            <tr><td>Waybill Number as {{waybill_number}} </td><td> Trace Link as {{tracelink}} </td></tr>
                                            <tr><td>Amount Due as {{amount_due}} </td><td></td></tr>
                                        </table>

                                    </frameset>

                                </p>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="task" value="text">
                            <input type="hidden" name="status_id"  value="<?= isset($status["id"])?$status["id"]:''; ?>">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" class="btn btn-primary" value="Save changes">
                        </div>
                    </div>
                </form>
            </div>
        </div>
            <?php
        }

        ?>
</div>
</div>

<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=l5jy597tw71uifpxxpvl3h7bnd9x2zyh6ckpkbvz58sbx13n"></script>

<script>tinymce.init({selector: 'textarea'});</script>
