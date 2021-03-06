<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;

/* @var $this yii\web\View */
$this->title = 'Shipments: Arrival';
$this->params['breadcrumbs'] = array(
    /*array(
    'url' => ['site/managebranches'],
    'label' => 'Manage Branches'
    ),*/
    array('label' => 'Receive Shipments')
);

$user_data = $this->context->userData;
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
//$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add New Staff</button>';
?>

<div class="main-box">
    <div class="main-box-header table-search-form">
        <div class="clearfix">

            <!--
            <form class="table-search-form form-inline pull-right clearfix">
                <div class="pull-left form-group">
                    <label for="searchInput">Search</label><br>
                    <div class="input-group input-group-sm input-group-search">
                        <input id="searchInput" type="text" name="search" placeholder="Search by Waybill or Reference No."
                               class="search-box form-control" value="<?php echo(isset($search)?$search:'') ?>">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
          -->

            <form class="clearfix">
                <div class="pull-left hidden">
                    <div class="pull-left form-group">
                        <label for="">Branch type</label><br>
                        <select class="form-control input-sm">
                            <option>Hub</option>
                            <option>Express Centre</option>
                            <option>Kaduna</option>
                        </select>
                    </div>
                    <div class="pull-left form-group">
                        <label for="">Branch Name</label><br>
                        <select class="form-control input-sm">
                            <option>Ibadan</option>
                            <option>Lagos</option>
                            <option>Kaduna</option>
                        </select>
                    </div>
                    <div class="pull-left">
                        <label for="">&nbsp;</label><br>
                        <button id="records_filter" type="submit" class="btn btn-sm btn-default">Apply</button>
                    </div>
                </div>

                <div class="form-group form-group-sm form-inline">
                    <br/>
                    <label for="page_width">Records</label>
                    <select name="page_width" id="page_width" class="form-control ">
                        <?php
                        $page_width = isset($page_width) ? $page_width : 50;
                        for ($i = 50; $i <= 500; $i += 50) {
                            ?>
                            <option <?= $page_width == $i ? 'selected' : '' ?>
                                value="<?= $i ?>"><?= $i ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </form>

            <div class="pull-right clearfix">
                <form class="table-search-form form-inline clearfix">
                    <div class="pull-left form-group">
                        <label for="searchInput">&nbsp;</label><br>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#myModal">
                            Receive
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <?php
            if (count($parcel_next) > 0){
            ?>
            <table id="next_dest" class="table table-hover next_dest dataTable">
                <thead>
                <tr>
                    <th style="width: 20px;" class="datatable-nosort"></th>
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No</th>
                    <th>Reference No</th>
                    <th>Origin</th>
                    <th>Next Destination</th>
                    <th>Final Destination</th>
                    <th>Request Type</th>
                    <th>Return Status</th>
                    <th>Weight/Piece</th>
                    <?php if ($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                        <th>Originating Branch</th>
                        <th>Current Location</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php

                if (isset($parcel_next)) {
                    $row = $offset;
                    foreach ($parcel_next as $parcels) {
                        ++$row;
                        ?>
                        <tr data-waybill='<?= $parcels['waybill_number'] ?>'>
                            <td>
                                <div class='checkbox-nice'>
                                    <input name='waybills[]' id='chk_<?= $row; ?>' type='checkbox'
                                           class='chk_next'><label
                                        for='chk_<?= $row; ?>'></label>
                                </div>
                            </td>
                            <td><?= $row; ?></td>
                            <td>
                                <a href='/shipments/view?waybill_number=<?= Calypso::getValue($parcels, 'waybill_number'); ?>'><?= Calypso::getValue($parcels, 'waybill_number') ?></a>
                            </td>
                            <td><?= Calypso::getValue($parcels, 'reference_number') ?></td>
                            <td><?= ucwords(Calypso::getValue($parcels, 'sender_address.city.name') . ', ' . Calypso::getValue($parcels, 'sender_address.state.name')); ?></td>
                            <td></td>
                            <td><?= ucwords(Calypso::getValue($parcels, 'receiver_address.city.name') . ', ' . Calypso::getValue($parcels, 'receiver_address.state.name')); ?></td>
                            <td><?= ServiceConstant::getRequestType($parcels['request_type']) ?></td>
                            <td><?= Calypso::getValue($parcels, 'return_reason.comment'); ?></td>
                            <td><?= Calypso::getValue($parcels, 'weight') ?></td>
                            <?php if ($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                                <td><?= strtoupper(Calypso::getValue($parcels, "created_branch.name")) ?></td>
                                <td><?= ParcelAdapter::getCurrentLocation($parcels); ?></td>
                            <?php } ?>
                        </tr>
                    <?php }
                }
                ?>
                </tbody>
            </table>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        </div>
        <?php } else { ?>
            <div class="alert alert-info text-center" role="alert">
                <p><strong>No shipment received today</strong></p>
            </div>
        <?php } ?>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="arrived_parcels" class="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Accept Shipments into Hub</h4>
                </div>
                <div class="modal-body">

                    <form class="">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Staff ID</label>

                                    <div class="input-group">
                                        <input id="staff_no" value="" class="form-control">

                                        <div class="input-group-btn">
                                            <button type="button" data-branch_type="hub" id="get_arrival"
                                                    class="btn btn-default">Load
                                            </button>
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

                    <br/>
                    <div class="row hidden" id="force-receive-btn-div">
                        <div class="col-md-4">
                            <button id="force-receive-btn" class="btn btn-danger btn-block">Force Receive Parcels</button>
                        </div>
                        <div class="col-md-8">
                            <em style="font-size: 11px;">Click on this button to receive parcels that you cannot find on the system</em>
                        </div>
                    </div>
                    <!-- force receiving parcels -->
                    <div id="force-receive-div" class="row hidden">
                        <input type="hidden" name="force_receive" id="force_receive" value="false">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Previous Hub</label>

                                <div class="input-group">
                                    <select id="previous_branch" name="previous_branch" class="form-control">
                                        <option>Select Previous Hub</option>
                                        <?php foreach ($branches as $branch) { ?>
                                            <option value="<?php echo $branch['id'];?>"><?php echo $branch['name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-8">
                            <div class="form-group">
                                <label>Waybill Number</label>

                                <div class="input-group">
                                    <input id="get_parcel_by_number_input" class="form-control" placeholder="Enter a waybill number">

                                    <div class="input-group-btn">
                                        <button type="button" data-branch_type="hub" id="get_parcel_by_number_btn"
                                                class="btn btn-default">Get Parcel
                                        </button>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label id="get_parcel_by_number_loading_label"></label>
                                </div>
                            </div>
                        </div>

                    </div>

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
                    <button id="arrived_parcels_btn" type="button" class="btn btn-primary">Accept</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/hub_util.js?v=1.1', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/record_filter.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>


