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
                <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#opmodal"
                        data-action="receive">Receive from Dispatcher
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#passwordModal"
                        data-action="deliver"><i class="fa fa-check"></i> Mark as delivered
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#passwordModal"
                        data-action="return"><i class="fa fa-check"></i> Mark as Returned
                </button>
            </div>

            <form class="table-search-form form-inline pull-right clearfix">
                <div class="pull-left form-group">
                    <div class="input-group input-group-sm input-group-search">
                        <input id="searchInput" type="text" name="search" placeholder="Search by Waybill number" class="search-box form-control">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit" formmethod="post">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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
                                                                      type="checkbox" <?= $parcel['for_return'] ? 'data-is-return':'';?>><label
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
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Receiver's Name</label>
                        <input type="text" class="form-control" name="fullname" id="fullname" required>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="email">Receiver's Email</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="--Optional--">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">Receiver's Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="phone" required>
                        </div>
                    </div>

                    <div class="row">

                            <div class='col-sm-6'>
                                <div class="form-group">
                                    <label for="">Date of delivery:</label><br>
                                    <div class="input-group input-group-date-range">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input name="date" id="" class="form-control date-range" value="<?= date('Y/m/d', strtotime($set_date)); ?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d" required>
                                    </div>
                                </div>
                            </div>

                             <div class='col-sm-6'>
                                <div class="form-group">
                                    <label for="">Time of delivery:</label><br>
                                    <div class='input-group timepicker-orient-top'>
                                             <span class="input-group-addon" >
                                                <span class="glyphicon glyphicon-time" ></span>
                                            </span>
                                            <input name='time' id="" class="form-control time-range" data-provide="timepicker" required>
                                    </div>
                                </div>
                            </div>
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

<div class="modal fade" id="opmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="held_parcels" class="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Receive Shipments</h4>
                </div>
                <div class="modal-body">

                    <form class="">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Staff ID</label>
                                    <div class="input-group">
                                        <input id="staff_no" value="RONNY-001" class="form-control">
                                        <div class="input-group-btn">
                                            <button type="button" id="get_arrival" class="btn btn-default">Load</button>
                                        </div>
                                    </div>
                                    <div class="input-group">
                                        <label id="loading_label"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label>Staff Name</label>
                                <div id="sweeper_name" class="form-control-static"><em>Not Available</em></div>
                                <label>Department</label><br>
                                <div id="role" class="form-control-static"><em>Not Available</em></div>
                                <label>Branch of Operation</label><br>
                                <div id="branch" class="form-control-static"><em>Not Available</em></div>
                                <input id="staff_user_id" name="staff_user_id" type="hidden">
                            </div>
                        </div>
                    </form>

                    <br>
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody id="parcel_arrival">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="receive_parcels_btn" type="button" class="btn btn-primary disabled">Accept</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- this page specific scripts -->
<script type="text/javascript">
    var beingdelivered = <?= ServiceConstant::BEING_DELIVERED ?>;
</script>
<?= $this->registerJsFile('@web/js/libs/bootstrap-timepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/requests.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/shipment_dispatched.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>