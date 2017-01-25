<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


/* @var $this yii\web\View */
$this->title = 'All Shipments';
$this->params['breadcrumbs'][] = 'Shipments';


$link = "";
if($search){
    $fro = date('Y/m/d',strtotime($from_date));
    $to = date('Y/m/d',strtotime($to_date));
    $link = "&search=true&to=".urlencode($to)."&from=".urlencode($fro)."&page_width=".$page_width;
    if(!is_null($filter)){$link.= '&date_filter='. $filter;}
}
$user_data = $this->context->userData;

?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal"
 data-target="#teller-modal">Submit COD Teller</button> <button type="button" class="btn btn-primary" data-toggle="modal"
 data-target="#rtd-teller-modal">Submit Rtd Teller</button>';
?>


<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                <?= $this->render('../elements/parcels_filter',['from_date'=>$from_date,'to_date'=>$to_date,'page_width'=>$page_width,'filter'=>$filter, 'cash_on_delivery' =>$cash_on_delivery, 'hideStatusFilter'=>false]) ?>
            </div>
            <div class="pull-right clearfix">

                <form class="table-search-form form-inline clearfix">
                    <div class="pull-left">
                        <label for="searchInput">Search</label><br>
                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="search" placeholder="Search by Waybill or Reference No." class="search-box form-control">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="pull-left hidden">
                    <label>&nbsp;</label><br>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Select an action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if(count($parcels)) : ?>
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px" class="datatable-nosort">
                        <div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label
                                for="chbx_w_all"> </label></div>
                    </th>
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Reference No.</th>
                    <th>Shipper</th>
                    <th>Shipper Phone</th>
                    <th>Receiver</th>
                    <th>Request Type</th>
                    <th>Created Date</th>
                    <th>Pieces</th>
                    <th>Return Status</th>
                    <th>Shipment Status</th>
                    <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                        <th>Originating Branch</th>
                        <th>Current Location</th>
                    <?php } ?>
                    <th>Shipment Age</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($parcels) && is_array($parcels)){
                    $i = $offset;
                    foreach($parcels as $parcel){
                        ?>
                        <tr>
                            <td>
                                <?php if(Calypso::getValue($parcel, 'cash_on_delivery', '0') == '1'): ?>
                                <div class="checkbox-nice">

                                    <input id="chbx_w_<?= ++$i; ?>" class="checkable"
                                           data-waybill="<?= strtoupper($parcel['waybill_number']); ?>"
                                           data-sender="<?= strtoupper($parcel['sender']['firstname'] . ' ' . $parcel['sender']['lastname']) ?>"
                                           data-amount_due="<?= Calypso::getValue($parcel, 'delivery_amount') ?>"
                                           type="checkbox"><label
                                        for="chbx_w_<?= $i; ?>"> </label>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><?= ++$i; ?></td>
                            <td><?= strtoupper($parcel['waybill_number']); ?></td>
                            <td><?= strtoupper($parcel['reference_number']); ?></td>
                            <td><?= strtoupper($parcel['sender']['firstname'].' '. $parcel['sender']['lastname']) ?></td>
                            <td><?= $parcel['sender']['phone'] ?></td>
                            <td><?= strtoupper($parcel['receiver']['firstname'].' '. $parcel['receiver']['lastname']) ?></td>
                            <td><?= ServiceConstant::getRequestType($parcel['request_type']) ?></td>
                            <td><?= date(ServiceConstant::DATE_TIME_FORMAT,strtotime($parcel['created_date'])); ?></td>
                            <td><?= $parcel['no_of_package']; ?></td>
                            <td><?= $parcel['return_reason']['comment'] ?></td>
                            <td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
                            <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                                <td><?= strtoupper(Calypso::getValue($parcel, "created_branch.name")) ?></td>
                                <td><?= ParcelAdapter::getCurrentLocation($parcel); ?></td>
                            <?php } ?>
                            <td><?= ParcelAdapter::getAgeAnalysis($parcel); ?></td>
                            <td>
                                <a href="<?= Url::toRoute(['/shipments/view?waybill_number='.$parcel['waybill_number']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a>

                                <?= $this->render('../elements/partial_edit_button', ['parcel' => $parcel]) ?>
                                <?= $this->render('../elements/parcel/partial_cancel_button', ['waybill_number' => $parcel['waybill_number'], 'status' => $parcel['status']]) ?>
                                <?php if($parcel['status'] == ServiceConstant::DELIVERED):?>
                                <button data-toggle="modal" data-target="#pod-modal" class="btn btn-xs btn-default" data-waybill-number="<?= $parcel['waybill_number'] ?>" title="Edit POD">POD</button>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }}
                ?>

                </tbody>
            </table>
        </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
            <?php else:  ?>
                There are no parcels matching the specified criteria.
            <?php endif;  ?>
    </div>
</div>


<div class="modal fade" id="teller-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="" class="validate-form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Submit Teller Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <label for="">Bank</label>
                            <select class="form-control validate required" name="bank_id" id="bank_id">
                                <?php
                                if (isset($banks) && is_array($banks['data'])) {
                                    foreach ($banks['data'] as $item) {
                                        ?>
                                        <option
                                            value="<?= $item['id'] ?>"><?= strtoupper($item['name']); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-6 form-group">
                            <label for="">Account no</label>
                            <input type="text" class="form-control validate required non-zero-integer"
                                   name="account_no">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <label for="">Teller no</label>
                            <input type="text" class="form-control validate required non-zero-integer" name="teller_no">
                        </div>
                        <div class="col-xs-6 form-group">
                            <label for="">Amount paid</label>

                            <div class="input-group">
                                <span class="input-group-addon currency naira"></span>
                                <input id="amount_paid" type="text" class="form-control validate required non-zero-number"
                                       name="amount_paid">
                            </div>
                        </div>
                    </div>
                    <div class="form-group hidden">
                        <label>Teller Snapshot (optional)</label>
                        <input type="file" class="form-control">
                    </div>

                    <hr/>
                    <table class="table table-bordered table-condensed" id="teller-modal-table">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Sender name</th>
                        </tr>
                        </thead>
                        <tbody></tbody>

                        <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right;">Add Waybill</td>
                            <td>
                                <div class=" form-group">

                                    <div class="input-group input-group-sm input-group-search">
                                        <input id="addWaybillNumber" type="text" name="waybill_number" placeholder="Search by Waybill or Reference No."
                                               class="search-box form-control">

                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="btnAddWaybill">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="submit_teller">
                    <input type="hidden" id="waybill_numbers" name="waybill_numbers">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitTeller">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="pod-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="" class="validate-form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">POD</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label for="">Waybill Number</label>
                            <input id="pod_waybill_number" readonly type="text" class="form-control validate required" name="waybill_number">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label for="">Receiver</label>
                            <input type="text" class="form-control required" name="receiver">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <label for="">Date</label>
                            <input type="date" class="form-control required" name="date">
                        </div>
                        <div class="col-xs-3 form-group">
                            <label for="">Hour</label>

                            <div class="input-group">
                                <input id="pod_hour" type="text" class="form-control validate required non-zero-number"
                                       name="hour">
                            </div>
                        </div>
                        <div class="col-xs-3 form-group">
                            <label for="">Minute</label>

                            <div class="input-group">
                                <input id="pod_minute" type="text" class="form-control validate required non-zero-number"
                                       name="minute">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="update_pod">
                    <input type="hidden" id="pod_waybill_number" name="waybill_number">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdatePod">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="rtd-teller-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="" class="validate-form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Submit Teller Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <label for="">Bank</label>
                            <select class="form-control validate required" name="bank_id" id="rtd_bank_id">
                                <?php
                                if (isset($banks) && is_array($banks['data'])) {
                                    foreach ($banks['data'] as $item) {
                                        ?>
                                        <option
                                                value="<?= $item['id'] ?>"><?= strtoupper($item['name']); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-6 form-group">
                            <label for="">Account no</label>
                            <input type="text" class="form-control validate required non-zero-integer"
                                   name="account_no">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <label for="">Teller no</label>
                            <input type="text" class="form-control validate required non-zero-integer" name="teller_no">
                        </div>
                        <div class="col-xs-6 form-group">
                            <label for="">Amount paid</label>

                            <div class="input-group">
                                <span class="input-group-addon currency naira"></span>
                                <input id="rtd_amount_paid" type="text" class="form-control validate required non-zero-number"
                                       name="amount_paid">
                            </div>
                        </div>
                    </div>
                    <div class="form-group hidden">
                        <label>Teller Snapshot (optional)</label>
                        <input type="file" class="form-control">
                    </div>

                    <hr/>
                    <table class="table table-bordered table-condensed" id="rtd-teller-modal-table">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Sender name</th>
                        </tr>
                        </thead>
                        <tbody></tbody>

                        <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right;">Add Waybill</td>
                            <td>
                                <div class="form-group">

                                    <div class="input-group input-group-sm input-group-search">
                                        <input id="rtd_addWaybillNumber" type="text" name="waybill_number" placeholder="Search by Waybill or Reference No."
                                               class="search-box form-control">

                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="rtd_btnAddWaybill">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="submit_rtd_teller">
                    <input type="hidden" id="rtd_waybill_numbers" name="waybill_numbers">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="rtd_btnSubmitTeller">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->render('../elements/parcel/partial_cancel_shipment_form') ?>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]])?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]])?>

<?php $this->registerJsFile('@web/js/submit_teller.js?v=1.0.3', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/parcel_pod.js?v=1.0.0', ['depends' => [\app\assets\AppAsset::className()]]) ?>

