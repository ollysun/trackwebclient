<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'New Shipments';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label' => $this->title)
);

$link = "";
if ($search) {
    $fro = date('Y/m/d', strtotime($from_date));
    $to = date('Y/m/d', strtotime($to_date));
    $link = "&search=true&to=" . urlencode($to) . "&from=" . urlencode($fro) . "&page_width=" . $page_width;
    if (!is_null($filter)) {
        $link .= '&date_filter=' . $filter;
    }
}
?>
<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>


<?php
$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button') . ' <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#teller-modal">Submit Teller</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class=" clearfix">
            <div class="pull-left">
                <?= $this->render('../elements/parcels_filter', ['from_date' => $from_date, 'to_date' => $to_date, 'page_width' => $page_width, 'filter' => $filter, 'hideStatusFilter' => true]) ?>
            </div>
            <div class="pull-right clearfix">
                <form class="form-inline clearfix">
                    <div class="pull-left form-group">
                        <label for="searchInput">Search</label><br>
                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="search" placeholder="Waybill Number" class="search-box form-control">
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
                    <button type="button" class="btn btn-sm btn-default"><i class="fa fa-download"></i> Download</button>
                </div>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if (!empty($parcels)): ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px" class="datatable-nosort">
                            <div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label
                                    for="chbx_w_all"> </label></div>
                        </th>
                        <th style="width: 20px">No.</th>
                        <th>Waybill No.</th>
                        <th>Shipper</th>
                        <th>Shipper Phone</th>
                        <th>Receiver</th>
                        <th>Receiver Phone</th>
                        <th>Created Date</th>
                        <th># of Pcs</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = $offset;
                    if (isset($parcels) && is_array($parcels)) {
                        foreach ($parcels as $parcel) {;
                            ?>
                            <tr>
                                <td>
                                    <div class="checkbox-nice">

                                        <input id="chbx_w_<?= ++$i; ?>" class="checkable"
                                               data-waybill="<?= strtoupper($parcel['waybill_number']); ?>"
                                               data-sender="<?= strtoupper($parcel['sender']['firstname'] . ' ' . $parcel['sender']['lastname']) ?>"
                                               type="checkbox"><label
                                            for="chbx_w_<?= $i; ?>"> </label>
                                    </div></td>
                                <td><?= $i ?></td>
                                <td><?= strtoupper($parcel['waybill_number']); ?></td>
                                <td><?= strtoupper($parcel['sender']['firstname'] . ' ' . $parcel['sender']['lastname']) ?></td>
                                <td><?= $parcel['sender']['phone'] ?></td>
                                <td><?= strtoupper($parcel['receiver']['firstname'] . ' ' . $parcel['receiver']['lastname']) ?></td>
                                <td><?= $parcel['receiver']['phone'] ?></td>
                                <td><?= date(ServiceConstant::DATE_TIME_FORMAT, strtotime($parcel['created_date'])); ?></td>
                                <td><?= $parcel['no_of_package']; ?></td>
                                <td>
                                    <a href="<?= Url::to(['site/viewwaybill?id=' . $parcel['id']]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a>
                                    <?php if (in_array($parcel['status'], [ServiceConstant::FOR_DELIVERY, ServiceConstant::FOR_SWEEPER])) : ?>
                                        <form method="post">
                                            <button type="submit" class="btn btn-xs btn-danger" name="parcel_id"><i
                                                    class="fa fa-times">
                                                    &nbsp;</i>Cancel
                                            </button>
                                            <input type="hidden" name="waybill_numbers" value="<?= $parcel['waybill_number'] ?>">
                                            <input type="hidden" name="task" value="cancel_shipment">
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        <?php else: ?>
            There are no parcels matching the specified criteria.
        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="teller-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="" class="validate-form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                            <input type="text" class="form-control validate required non-zero-integer" name="account_no">
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
                                <input type="text" class="form-control validate required non-zero-number" name="amount_paid">
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
                    </table>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="submit_teller">
                    <input type="hidden" id="waybill_numbers" name="waybill_numbers">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnGenerate">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- this page specific scripts -->
<?= $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/form-watch-changes.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/submit_teller.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
