<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


/* @var $this yii\web\View */
$this->title = 'Shipment Exceptions';
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
                <?= $this->render('../elements/shipments/exception_filter', ['branches' => $branches]); ?>
            </div>
        </div>
    </div>


    <div class="main-box-body">
        <?php if(true) :  //count($auditTrail) ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill Number</th>
                        <th>Defaulter Branch</th>
                        <th>Detector Branch</th>
                        <th>Action Description</th>
                        <th>Date Detected</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sn = 0;
                    foreach ($exceptions as $exception) {?>
                        <tr>
                            <td><?=(++$sn) ?></td>
                            <td>
                                <a href='/shipments/view?waybill_number=<?= Calypso::getValue($exception, 'waybill_number'); ?>'>
                                    <?= Calypso::getValue($exception, 'waybill_number') ?></a>
                            </td>
                            <td><?=$exception['defaulter_branch_name'] ?></td>
                            <td><?=$exception['detector_branch_name'] ?></td>
                            <td><?=$exception['action_description'] ?></td>
                            <td><?=$exception['creation_date'] ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs btnShowEditDetail">
                                    <i class="fa fa-search"></i> Detail
                                </button>
                            </td>

                        </tr>

                    <?php }?>


                    </tbody>
                </table>
            </div>
            <?php //= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
        <?php else:  ?>
            There are no parcels matching the specified criteria.
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
