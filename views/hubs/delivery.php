<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
$this->title = 'Shipment: Generate Manifest for Delivery';
$this->params['breadcrumbs'] = [['label' => 'Sorted Shipments']];
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>

<div class="main-box">

    <div class="clearfix">
        <div class="main-box-header table-search-form">
            <form class="clearfix" method="get">
                <div class="pull-left">
                    <div class="pull-left form-group">
                        <label for="branch_type">Branch type</label><br>
                        <select id="branch_type" class="form-control input-sm branch_type" name="btype">
                            <option value="exp" <?php echo ($btype == "exp") ? "selected" : ''; ?>>Express Centres
                            </option>
                            <option value="hub" <?php echo ($btype == "hub") ? "selected" : ''; ?>>Hub</option>
                        </select>
                    </div>
                    <div class="pull-left form-group">
                        <label for="branch_name" id="hub_branch_label">Branch Name</label><br>
                        <select id="branch_name" class="form-control input-sm branch_name"
                                name="bid" <?php echo isset($to_branch_id) ? "data-bid='$to_branch_id'" : ''; ?>>
                            <option>Select Name...</option>
                        </select>
                    </div>
                    <div class="pull-left">
                        <label for="">&nbsp;</label><br>
                        <button type="submit" class="btn btn-sm btn-default" id="btn_apply_dest"><i
                                class="fa fa-search"></i></button>
                    </div>
                </div>

                <div class="pull-right clearfix">
                    <div class="table-search-form form-inline clearfix">
                        <div class="pull-left form-group">
                            <label for="">&nbsp;</label><br>
                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" id="btnCreateBag">
                                Create bag
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="manifest">
                                Generate Manifest
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <form class="clearfix" method="get" id="records_filter">
                <div class="pull-left hidden">
                    <p class="form-control-static input-sm">Showing 1 to 49 of 49 shipments</p>
                </div>
                <div class="pull-right form-group form-group-sm form-inline">
                    <label for="page_width">Records</label>
                    <select name="page_width" id="page_width" class="form-control ">
                        <?php
                        $page_width = isset($page_width) ? $page_width : 50;
                        for ($i = 50; $i <= 500; $i += 50) {
                            ?>
                            <option <?= $page_width == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </form>

        </div>
    </div>


    <div class="main-box-body">
        <div class="table-responsive">
            <?php if (!empty($parcel_delivery)) { ?>
                <table id="next_dest" class="table table-hover next_dest">
                    <thead>
                    <tr>
                        <th style="width: 20px;">
                            <div class='checkbox-nice'>
                                <input id='chk_all' type='checkbox' class='chk_all'><label for='chk_all'></label>
                            </div>
                        </th>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No</th>
                        <th>Origin</th>
                        <th>Next Destination</th>
                        <th>Final Destination</th>
                        <th>Weight (Kg)</th>
                        <th>Age analysis</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $row = $offset;
                    foreach ($parcel_delivery as $parcels) {
                        ++$row;
                        echo "<tr data-waybill='" . Calypso::getValue($parcels, 'waybill_number') . "' ";
                        echo "data-to-branch-id='" . Calypso::getValue($parcels, 'to_branch.id') . "'>";
                        echo "<td>
                                                <div class='checkbox-nice'>
                                                    <input name='waybills[]' id='chk_{$row}' type='checkbox' class='chk_next'><label for='chk_{$row}'></label>
                                                </div>
                                              </td>";
                        echo "<td>{$row}</td>";
                        echo "<td><a href='/shipments/view?waybill_number=" . Calypso::getValue($parcels, 'waybill_number') . "'>" . Calypso::getValue($parcels, 'waybill_number') . "</a></td>";
                        echo "<td>" . ucwords(Calypso::getValue($parcels, 'sender_address.city.name') . ', ' . Calypso::getValue($parcels, 'sender_address.state.name')) . "</td>";
                        echo "<td>" . strtoupper(Calypso::getValue($parcels, 'to_branch.name')) . "</td>";
                        echo "<td>" . ucwords(Calypso::getValue($parcels, 'receiver_address.city.name') . ', ' . Calypso::getValue($parcels, 'receiver_address.state.name')) . "</td>";
                        echo "<td>" . Calypso::getValue($parcels, 'weight') . "</td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'total_count' => $total_count, 'page_width' => $page_width]) ?>

            <?php } else { ?>
                <p>No record to display</p>
            <?php } ?>
        </div>
    </div>

</div>
</div>

<!-- Modal -->
<div class="modal fade" id="genManifest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="delivery">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Generate Dispatch Manifest</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="payload" name="payload"/>

                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="dlg_location">Next Destination</label>
                                <input class="form-control" id="dlg_location" readonly="true"/>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Staff ID</label>

                                <div class="input-group">
                                    <input class="form-control" id="staff">

                                    <div class="input-group-btn">
                                        <button type="button" id="btn_staff" class="btn btn-default"><i
                                                class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="staff_info" style="display: none;">
                        <hr/>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Staff Name</label>

                                <p id="staff_name">Staff Name</p>
                            </div>
                            <div class="col-xs-6">
                                <label>Email</label>

                                <p id="staff_email">Role</p>
                            </div>
                            <div class="col-xs-6">
                                <label>Phone Number</label>

                                <p id="staff_phone">Staff Name</p>
                            </div>
                            <div class="col-xs-6">
                                <label>Role</label>

                                <p id="staff_role">Role</p>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <table class="table table-bordered table-condensed" id="tbl_manifest">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Final Destination</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnGenerate">Generate</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="createBag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create Bag from Items</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="payload" name="payload"/>
                <div class="row">
                    <div class="col-xs-6 form-group">
                        <label for="parcels_destination">Parcels Destination</label>
                        <div class="form-control-static"><strong id="parcels_destination"></strong></div>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label for="seal_id">SEAL ID</label>
                        <input class="form-control" id="seal_id"/>
                    </div>
                </div>
                <br>
                <p>Set Bag Destination</p>
                <div class="row">
                    <div class="col-xs-6 form-group">
                        <label for="branch_type">Branch type</label><br>
                        <select id="branch_type" class="form-control input-sm branch_type" name="btype">
                            <option value="exp">Express Centres</option>
                            <option value="hub">Hub</option>
                        </select>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label for="to_branch" id="hub_branch_label">Branch Name</label><br>
                        <select id="to_branch" class="form-control input-sm branch_name" name="bid">
                            <option>Select Name...</option>
                        </select>
                    </div>
                </div>
                <hr/>
                <table class="table table-bordered table-condensed" id="bag_parcels_table">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Waybill No.</th>
                        <th>Final Destination</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnBag">Create a Bag</button>
            </div>
        </div>
    </div>
</div>


<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/hub_delivery.js', ['depends' => [JqueryAsset::className()]]); ?>
