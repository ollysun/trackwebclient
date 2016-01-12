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
            <h1 class="pull-left">Tracking for #<?= $tracking_number ?></h1>
            <h4 class="pull-right text-muted">
                Status:
                <?php if(Calypso::getDisplayValue($current_state_info, 'status') == ServiceConstant::RETURNED): ?>
                    <strong id="status" title="Reason for return" data-content="Reason for return details" data-placement="bottom"
                    class="text-danger"><?= Calypso::getDisplayValue($current_state_info, 'description', 'N/A') ?></strong>
                    <?php $this->registerJsFile('@web/js/libs/bootstrap.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
                    <?php $this->registerJs('$("#status").popover("show")'); ?>
                <?php else: ?>
                 <strong
                    class="text-danger"><?= Calypso::getDisplayValue($current_state_info, 'description', 'N/A') ?></strong>
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

                <div class="tracking-info-value"><?= Calypso::getDisplayValue($tracking_info, 'parcel.weight', 'N/A') ?>Kg
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
                    <?php if (Calypso::getValue($current_state_info, 'status') == ServiceConstant::DELIVERED && Calypso::getValue($tracking_info, 'delivery_receipt', false)): ?>
                        <span
                            class="tracking-status-inner date"><?= Util::convertToTrackingDateFormat(Calypso::getValue($current_state_info, 'created_date', '')) ?></span>
                        <span
                            class="tracking-status-inner time"><?= Util::convertDateTimeToTime(Calypso::getValue($current_state_info, 'created_date', '')) ?></span>
                        <br><a class="btn btn-sm btn-success" tabindex="0" role="button" data-toggle="popover"
                               data-placement="left" title="Proof of Delivery Information">Proof of Delivery</a>
                        <div id="pod" style="display:none;">
                            <div class="form-group">
                                <label>Received by</label>

                                <div
                                    class="form-control-static"><?= Calypso::getDisplayValue($tracking_info, 'delivery_receipt.name', 'N/A') ?></div>
                            </div>
                            <div class="form-group">
                                <label>Date</label>

                                <div
                                    class="form-control-static"><?= Util::convertToTrackingDateFormat(Calypso::getValue($tracking_info, 'delivery_receipt.created_at', '')) ?></div>
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
            <?php if (is_array($tracking_info)): ?>
                <h1>Waybill / Tracking Number <strong><?= $tracking_number ?></strong> not found</h1>
                <p class="text-muted">The waybill / tracking number you entered could not be found on our
                    system. Please
                    search with another number</p>
            <?php else: ?>
                <h1>Error</h1>
                <p class="text-muted">There was an error fetching tracking information. Please try again
                    later.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>