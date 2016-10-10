<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


/* @var $this yii\web\View */
$this->title = 'Audit Trail';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['admin/managebranches'],
        'label' => 'Administrator'
    ),
    array('label' => $this->title)
);

?>

<!-- this page specific styles -->

<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                <?= $this->render('../elements/admin/audit_trail_filter', $filter); ?>
            </div>
        </div>
    </div>


    <div class="main-box-body">
        <?php if(count($logs) > 0) :  //count($auditTrail)
            $sn = $filter['offset'];?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Service</th>
                        <th>Action Performed</th>
                        <th>Performed By</th>
                        <th>Date</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($logs as $log) {?>
                        <tr>
                            <td><?=(++$sn) ?></td>
                            <td><?=$log['service'] ?></td>
                            <td><?=$log['action_name'] ?></td>
                            <td><?=$log['username'] ?></td>
                            <td><?=$log['start_time'] ?></td>
                            <td><?=$log['ip_address'] ?></td>
                            <td><?=$log['client'] ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs btnShowEditDetail" data-toggle="modal"
                                        data-target="#auditDetailModal"
                                        data-id="<?= Calypso::getValue($log, 'id'); ?>"
                                        data-username="<?= Calypso::getValue($log, 'username'); ?>"
                                        data-service="<?= Calypso::getValue($log, 'service'); ?>"
                                        data-start-time="<?= Calypso::getValue($log, 'start_time'); ?>"
                                        data-end-time="<?= Calypso::getValue($log, 'end_time'); ?>"
                                        data-ip-address="<?= Calypso::getValue($log, 'ip_address'); ?>"
                                        data-client="<?= Calypso::getValue($log, 'client'); ?>"
                                        data-parameters='<?= Calypso::getValue($log, 'parameters'); ?>'
                                        data-action-name="<?= Calypso::getValue($log, 'action_name'); ?>">
                                    <i class="fa fa-search"></i> Detail
                                </button>
                            </td>

                        </tr>

                    <?php }?>


                    </tbody>
                </table>
            </div>
            <?=
                $this->render('../elements/pagination_and_summary', ['first' => $filter['offset'], 'last'=>$sn, 'total_count'=> $total_count,'page_width'=>$filter['page_width']]) ?>
        <?php else:  ?>
            There are no logs matching the specified criteria.
        <?php endif;  ?>
    </div>
</div>

<!-- this page specific scripts -->
<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]])?>

<?php $this->registerJsFile('@web/js/audit.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>


<!-- Modal -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" role="dialog" aria-labelledby="auditDetailModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Audit Trail Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-6 form-group">
                        <label for="">Username</label>
                        <div class="form-control-static" id="username"></div>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label for="">Date</label>
                        <div class="form-control-static" id="date"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-6 form-group">
                        <label for="">IP Address</label>
                        <div class="form-control-static" id="ipAddress"></div>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label for="">User Agent</label>
                        <div class="form-control-static" id="userAgent"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-6 form-group">
                        <label for="">Service</label>
                        <div class="form-control-static" id="service"></div>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label for="">Action</label>
                        <div class="form-control-static" id="actionName"></div>
                    </div>
                </div>


                <table id="data_create" class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="width:20px">S/N</th>
                        <th>Property</th>
                        <th>Value</th>
                    </tr>
                    </thead>
                    <tbody id="parameters">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
