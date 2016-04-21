<?php
use Adapter\BranchAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use app\assets\TrackingAsset;


/* @var $this yii\web\View */

$this->title = 'Tracking Portal';
?>

<?php if ($tracking_info): ?>
    <div class="tracking-wrap">
        <?php //Please wrap for loop around .tracking-item ?>
        <div class="tracking-item">
            <div class="clearfix">
                <h1 class="pull-left">Tracking for #<?= ServiceConstant::humanizeWaybillNumber($tracking_number) ?></h1>
                <h4 class="pull-right text-muted">
                    Status:
                    <?php if (Calypso::getDisplayValue($tracking_info, 'parcel_return_comment.comment', false)): ?>
                        <strong id="status" title="Reason for return"
                                data-content="<?= Calypso::getDisplayValue($tracking_info, 'parcel_return_comment.comment') ?>"
                                data-placement="bottom"
                                class="text-danger">
                            <?= (Calypso::getValue($tracking_info, 'parcel.return_status', 0) != 0) ? ServiceConstant::getStatus(null, Calypso::getValue($tracking_info, 'parcel.return_status')) :
                                Calypso::getDisplayValue($current_state_info, 'description', 'N/A') ?></strong>
                        <?php $this->registerJsFile('@web/js/libs/bootstrap.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
                        <?php $this->registerJs('$("#status").popover("show")'); ?>
                    <?php else: ?>
                        <strong
                            class="text-danger">
                            <?= (Calypso::getValue($tracking_info, 'parcel.return_status', 0) != 0) ? ServiceConstant::getStatus(null, Calypso::getValue($tracking_info, 'parcel.return_status')) :
                                Calypso::getDisplayValue($current_state_info, 'description', 'N/A') ?></strong>
                    <?php endif; ?>
                </h4>
            </div>
            <br>

            <div class="row text-center text-uppercase">
                <div class="col-xs-4">
                    <label class="tracking-info-label">Consignee's name</label>

                    <div
                        class="tracking-info-value"><?= ucfirst(Calypso::getDisplayValue($tracking_info, 'receiver.firstname', '')) . " " . ucfirst(Calypso::getDisplayValue($tracking_info, 'receiver.lastname', '')) ?></div>
                </div>
                <div class="col-xs-4">
                    <label class="tracking-info-label">Packages</label>

                    <div
                        class="tracking-info-value"><?= Calypso::getDisplayValue($tracking_info, 'parcel.no_of_package', 'N/A') ?></div>
                </div>
                <div class="col-xs-4">
                    <label class="tracking-info-label">Weight/Piece</label>

                    <div
                        class="tracking-info-value"><?= Calypso::getDisplayValue($tracking_info, 'parcel.weight', 'N/A') ?>
                        Kg
                    </div>
                </div>
            </div>

            <div class="tracking-location-wraps">
                <?php $points[] = []; ?>
                <?php foreach (Calypso::getValue($tracking_info, 'history', []) as $info): ?>

                    <?php if (!in_array(Calypso::getValue($info, 'from_branch.id'), $points)): ?>
                        <div class="tracking-location arrived-in">
                            <?php if (Calypso::getValue($info, 'from_branch.branch_type') == BranchAdapter::BRANCH_TYPE_EC): ?>
                                <div class="fa fa-home tracking-logo"></div>
                            <?php elseif (Calypso::getValue($info, 'from_branch.branch_type') == BranchAdapter::BRANCH_TYPE_HUB): ?>
                                <div class="fa fa-building-o tracking-logo"></div>
                            <?php elseif (true): ?>
                                <div class="fa fa-usertracking-logo"></div>
                            <?php endif; ?>
                            <div
                                class="tracking-name"><?= ucwords(Calypso::getDisplayValue($info, 'from_branch.name', '')) ?></div>
                            <div class="tracking-circle"></div>
                            <div class="tracking-bar-full"></div>
                            <div class="tracking-bar"></div>
                            <div class="tracking-status">
                        <span
                            class="tracking-status-inner date"><?= Util::convertToTrackingDateFormat(Calypso::getValue($info, 'created_date', '')) ?></span>
                        <span
                            class="tracking-status-inner time"><?= Util::convertDateTimeToTime(Calypso::getValue($info, 'created_date', '')) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!in_array(Calypso::getValue($info, 'to_branch.id'), $points)): ?>
                        <?php if (in_array(Calypso::getValue($info, 'type'), ['transitional'])): ?>
                            <div class="tracking-location in-transit">
                                <?php if (Calypso::getValue($info, 'to_branch.branch_type') == BranchAdapter::BRANCH_TYPE_EC): ?>
                                    <i class="fa fa-home tracking-logo"></i>
                                <?php elseif (Calypso::getValue($info, 'to_branch.branch_type') == BranchAdapter::BRANCH_TYPE_HUB): ?>
                                    <i class="fa fa-building-o tracking-logo"></i>
                                <?php elseif (true): ?>
                                    <i class="fa fa-usertracking-logo"></i>
                                <?php endif; ?>
                                <div
                                    class="tracking-name"><?= ucwords(Calypso::getDisplayValue($info, 'to_branch.name', '')) ?></div>
                                <div class="tracking-circle"></div>
                                <div class="tracking-bar-full"></div>
                                <div class="tracking-bar"></div>
                                <div class="tracking-status">
                            <span
                                class="tracking-status-inner">In transit to <?= ucwords(Calypso::getDisplayValue($info, 'to_branch.name', '')) ?></span>
                                </div>
                            </div>

                        <?php else: ?>
                            <div
                                class="tracking-location <?= (Calypso::getValue($info, 'status') != ServiceConstant::FOR_SWEEPER) ? 'arrived-in' : '' ?>">
                                <?php if (Calypso::getValue($info, 'to_branch.branch_type') == BranchAdapter::BRANCH_TYPE_EC): ?>
                                    <div class="fa fa-home tracking-logo"></div>
                                <?php elseif (Calypso::getValue($info, 'to_branch.branch_type') == BranchAdapter::BRANCH_TYPE_HUB): ?>
                                    <div class="fa fa-building-o tracking-logo"></div>
                                <?php elseif (true): ?>
                                    <div class="fa fa-usertracking-logo"></div>
                                <?php endif; ?>
                                <div
                                    class="tracking-name"><?= ucwords(Calypso::getDisplayValue($info, 'to_branch.name', '')) ?></div>
                                <div class="tracking-circle"></div>
                                <div class="tracking-bar-full"></div>
                                <div class="tracking-bar"></div>
                                <div class="tracking-status">
                        <span
                            class="tracking-status-inner date"><?= Util::convertToTrackingDateFormat(Calypso::getValue($info, 'created_date', '')) ?></span>
                        <span
                            class="tracking-status-inner time"><?= Util::convertDateTimeToTime(Calypso::getValue($info, 'created_date', '')) ?></span>

                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php
                    $points[] = $info['from_branch']['id'];
                    $points[] = $info['to_branch']['id'];
                endforeach ?>
                <div
                    <?php
                    if (Calypso::getValue($current_state_info, 'status') == ServiceConstant::BEING_DELIVERED) {
                        $class = 'in-transit';
                    } else if (Calypso::getValue($current_state_info, 'status') == ServiceConstant::DELIVERED) {
                        $class = 'arrived-in';
                    } else if (Calypso::getValue($current_state_info, 'status') == ServiceConstant::RETURNED) {
                        $class = 'returned';
                    } else {
                        $class = '';
                    }
                    ?>
                    class="tracking-location <?= $class ?>">
                    <i class="fa fa-user tracking-logo"></i>

                    <div class="tracking-name">You</div>
                    <div class="tracking-circle"></div>
                    <div class="tracking-bar-full"></div>
                    <div class="tracking-bar"></div>
                    <div class="tracking-status">
                        <span><?= (Calypso::getValue($current_state_info, 'status') == ServiceConstant::BEING_DELIVERED) ? "On it's way to you" : '' ?></span>
                        <?php if (in_array(Calypso::getValue($current_state_info, 'status'), [ServiceConstant::DELIVERED, ServiceConstant::RETURNED]) && Calypso::getValue($tracking_info, 'delivery_receipt', false)): ?>
                            <span
                                class="tracking-status-inner date"><?= Util::convertToTrackingDateFormat(Calypso::getValue($tracking_info, 'delivery_receipt.delivered_at', '')) ?></span>
                            <span
                                class="tracking-status-inner time"><?= Util::convertDateTimeToTime(Calypso::getValue($tracking_info, 'delivery_receipt.delivered_at', '')) ?></span>
                            <br><a
                            <?= (Calypso::getValue($tracking_info, 'delivery_receipt.receipt_type') == 'returned') ? 'class="btn btn-sm btn-danger"' :
                                    'class="btn btn-sm btn-success"'?>
                            tabindex="0" role="button" data-toggle="popover"
                                   data-placement="left"
                                <?= (Calypso::getValue($tracking_info, 'delivery_receipt.receipt_type') == 'returned') ? 'data-title="Proof of Return Information">Proof of Return' :
                                    'data-title="Proof of Delivery Information">Proof of Delivery'?></a>
                            <div id="pod" style="display:none;">
                                <div class="form-group">
                                    <label>Received by</label>

                                    <div
                                        class="form-control-static"><?= Calypso::getDisplayValue($tracking_info, 'delivery_receipt.name', 'N/A') ?></div>
                                </div>
                                <div class="form-group">
                                    <label>Date</label>

                                    <div
                                        class="form-control-static"><?= Util::convertToTrackingDateFormat(Calypso::getValue($tracking_info, 'delivery_receipt.delivered_at', '')) ?></div>
                                </div>
                                <?php if (Calypso::getDisplayValue($tracking_info, 'delivery_receipt.receipt_path', false)): ?>
                                    <div class="form-group">
                                        <label>Signature</label>
                                        <img class="signature"
                                             src="<?= Calypso::getDisplayValue($tracking_info, 'delivery_receipt.receipt_path', '') ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php
                            $this->registerJsFile('@web/js/libs/bootstrap.min.js', ['depends' => [TrackingAsset::className()]]);
                            $this->registerJsFile('@web/js/tracking-proof-of-delivery.js', ['depends' => [TrackingAsset::className()]]);
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row empty-tracking-no">
        <div class="col-xs-6 col-xs-offset-3 text-center">
            <?php if ($count > 10): ?>
                <h1>Error</h1>
                <p class="text-muted"> Sorry, You can't search for more than ten parcels </p>
            <?php elseif (is_array($tracking_info)): ?>
                <h1>Waybill / Tracking Number <strong><?= $tracking_number ?></strong> not found</h1>
                <p class="text-muted">WayBill Number not recognized by the system please contact
                    <a href=""> customerservice@courierplus-ng.com </a> for immediate resolution</p>
            <?php else: ?>
                <h1>Error</h1>
                <p class="text-muted">WayBill Number not recognized by the system please contact
                    <a href=""> customerservice@courierplus-ng.com </a> for immediate resolution</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>