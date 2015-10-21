<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use yii\web\View;


$this->title = 'Shipments: Dispatched';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label' => 'Dispatched')
);

?>

<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <?php if (!empty($parcels)) { ?>
        <div class="main-box-header clearfix">
            <div class="pull-left">
                <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#passwordModal"
                        data-action="receive">Receive from Dispatcher
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="moal" data-target="#passwordModal"
                        data-action="deliver"><i class="fa fa-check"></i> Mark as delivered
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#passwordModal"
                        data-action="return"><i class="fa fa-check"></i> Mark as Returned
                </button>
            </div>
        </div>
    <?php } ?>
    <?php if(!empty($parcels)) { ?>
    <div class="main-box-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width: 20px"><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>
                    <!-- <th style="width: 20px;"></th> -->
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Dispatcher</th>
                    <th>Status</th>
                    <th>Return Status</th>
                    <th>Age analysis</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($parcels)) {
                        $row = $offset;
                        $i = 1;
                        foreach ($parcels as $parcel) {
                            ?>
                            <tr>
                                <td>
                                    <div class="checkbox-nice"><input class="checkable" id="chbx_w_<?= $i; ?>"
                                                                      class="checkable"
                                                                      data-waybill="<?= strtoupper($parcel['waybill_number']); ?>"
                                                                      type="checkbox"><label
                                            for="chbx_w_<?= $i++; ?>"> </label></div>
                                </td>
                                <td><?= ++$row; ?></td>
                                <td><?= $parcel['waybill_number']; ?></td>
                                <td><?= ucwords($parcel['receiver']['firstname'] . ' ' . $parcel['receiver']['lastname']) ?></td>
                                <td><?= $parcel['receiver']['phone'] ?></td>
                                <td><?= ucwords($parcel['holder']['fullname']); ?></td>
                                <td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
                                <td><?= ServiceConstant::getReturnStatus($parcel); ?></td>
                                <td></td>
                                <td>
                                    <a href="<?= Url::toRoute(['/shipments/view?waybill_number=' . $parcel['waybill_number']]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
                <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'page_width' => $page_width, 'total_count' => $total_count]) ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="main-box-body">
            There are no parcels that are being delivered.
        </div>
    <?php } ?>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="arrived_parcels" class="" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Authenticate</h4>
                </div>
                <div class="modal-body">
                    <p>Please enter your password to authenticate this operation.</p>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="waybills" id="waybills">
                    <input type="hidden" name="task" id="task">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Mark</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->registerJsFile('@web/js/shipment_dispatched.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>