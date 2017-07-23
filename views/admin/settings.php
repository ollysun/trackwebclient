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
            <form method="post" >
            <div class="panel panel-default">
                <div class="panel-heading" data-toggle="collapse" data-target="#credit_limit">
                    <i class="fa fa-cogs"> </i> Credit Limit Settings
                </div>

                <div class="panel-body collapse" id="credit_limit" >
                    <div class="row" style="padding: 10px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Admin Emails (Separate by comma)</label>
                                <input name="alert_emails" type="email" class="form-control" value="<?= isset($sets["credit_limit"]->alert_emails)?$sets["credit_limit"]->alert_emails:''; ?>" >
                            </div>

                            <div class="form-group">
                                <label>Email Subject</label>
                                <input name="email_subject" type="text" class="form-control" value="<?= isset($sets["credit_limit"]->email_subject)?$sets["credit_limit"]->email_subject:''; ?>">
                            </div>

                            <div class="form-group">

                                <textarea name="email_body"><?= isset($sets["credit_limit"]->email_body)?$sets["credit_limit"]->email_body:''; ?></textarea>

                            </div>



                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label data-toggle="Test" title="The Percentage credit limit reached such that a mail will be triggered">
                                    Percentage to Trigger Alert <i class="fa fa-question-circle"> </i>
                                </label> <input name="limit_percentage" type="number" maxlength="3" max="100" min="0" class="form-control" required
                                                value="<?= isset($sets["credit_limit"]->limit_percentage)?$sets["credit_limit"]->limit_percentage:''; ?>"> <br>
                            </div>
                            <input type="checkbox" checked name="send_to_client"
                                   value="<?= isset($sets["credit_limit"]->send_to_client)?'checkbox':''; ?>"> Check to Alert Clients Also
                        </div>
                        <div class="col-md-4">
                            <p class="text text-info" style="font-size:small; color: #0b0b0b; border: 1px solid #777; padding: 5px; background: #eef; border-radius: 5px;">
                                <frameset>
                                    Insert the square brackets code where you want the relevant information to show <br>
                                    <table style="font-size: 10px; color: #8F44AD; width: 100%">
                                        <tr><td>Sender Name </td><td> {{sender_name}} </td></tr><tr><td> Receiver Name </td><td> {{receiver_name}} </td></tr>
                                        <tr><td>Receiver Email </td><td> {{receiver_email}} </td></tr><tr><td> Sender Email </td><td> {{sender_email}} </td></tr>
                                        <tr><td>Receiver Number </td><td> {{receiver_number}} </td></tr><tr><td> Sender Number </td><td> {{sender_number}} </td></tr>
                                        <tr><td>Waybill Number  </td><td>{{waybill_number}} </td></tr><tr><td> Trace Link </td><td> {{tracelink}} </td></tr>
                                        <tr> <td>Amount Due </td><td> {{amount_due}} </td></tr><tr><td></td></tr>
                                    </table>

                                </frameset>

                            </p>


                        </div>



                    </div>
                    <input type="text" value="credit_limit" name="setting_name" hidden>
                    <input type="submit" value="Save Settings" name="submit">
                </div>
            </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=l5jy597tw71uifpxxpvl3h7bnd9x2zyh6ckpkbvz58sbx13n"></script>

<script>tinymce.init({selector: 'textarea'});</script>
