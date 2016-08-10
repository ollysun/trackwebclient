<?php
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Util;
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use Adapter\Util\Calypso;
use yii\web\JqueryAsset;
use yii\web\View;


$this->title = 'Reports';
$this->params['breadcrumbs'] = array(
    array('label' => 'Shipments')
);
$downloadURL = Url::to('downloadreport?') . parse_url(Url::to(), PHP_URL_QUERY);
?>

<?= Html::cssFile('@web/css/libs/select2.css') ?>

<?php
$this->params['content_header_button'] = "<a href='" . $downloadURL . "' class='btn btn-primary'><i class='fa fa-download'></i> Download as CSV</a>";
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                <form method="get">
                    <link href="/css/libs/datepicker.css" rel="stylesheet">
                    <div class="clearfix">
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Creation Date</label><br>
                            <input name="start_created_date" class="form-control date-range" data-provide="datepicker"
                                   data-date-format="yyyy/mm/dd" data-date-end-date="0d"
                                   value="<?= $start_created_date; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for=""></label><br>
                            <input name="end_created_date" class="form-control date-range" data-provide="datepicker"
                                   data-date-format="yyyy/mm/dd" data-date-end-date="0d"
                                   value="<?= $end_created_date; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Modified Date</label><br>
                            <input name="start_modified_date" class="form-control date-range" data-provide="datepicker"
                                   data-date-format="yyyy/mm/dd" data-date-end-date="0d"
                                   value="<?= $start_modified_date; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for=""></label><br>
                            <input name="end_modified_date" class="form-control date-range" data-provide="datepicker"
                                   data-date-format="yyyy/mm/dd" data-date-end-date="0d"
                                   value="<?= $end_modified_date; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Min. WG/PC</label><br>
                            <input name="min_weight" class="form-control date-range"
                                   value="<?= $filters['min_weight']; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Max. WG/PC</label><br>
                            <input name="max_weight" class="form-control date-range"
                                   value="<?= $filters['max_weight']; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Min. Amount (<span class="naira"></span>)</label><br>
                            <input name="min_amount_due" class="form-control date-range"
                                   value="<?= $filters['min_amount_due']; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Max. Amount (<span class="naira"></span>)</label><br>
                            <input name="max_amount_due" class="form-control date-range"
                                   value="<?= $filters['max_amount_due']; ?>">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Shipment Status</label><br>
                            <select name="status" id="" class="form-control filter-status">
                                <option value="">All</option>
                                <?php foreach ($statuses as $status) { ?>
                                    <option
                                        value="<?= $status; ?>" <?= $filters['status'] == $status ? 'selected' : '' ?>><?= ServiceConstant::getStatus($status); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">For Return</label><br>
                            <select name="for_return" id="" class="form-control  filter-status">
                                <option value="">All</option>
                                <option
                                    value="<?= ServiceConstant::TRUE; ?>" <?= $filters['for_return'] == ServiceConstant::TRUE ? 'selected' : '' ?>>
                                    Yes
                                </option>
                                <option
                                    value="<?= ServiceConstant::FALSE; ?>" <?= ($filters['for_return'] == ServiceConstant::FALSE && strlen($filters['for_return'])) ? 'selected' : '' ?>>
                                    No
                                </option>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Cash on Delivery</label><br>
                            <select name="cash_on_delivery" id="" class="form-control  filter-status">
                                <option value="">All</option>
                                <option
                                    value="<?= ServiceConstant::TRUE; ?>" <?= $filters['cash_on_delivery'] == ServiceConstant::TRUE ? 'selected' : '' ?>>
                                    Yes
                                </option>
                                <option
                                    value="<?= ServiceConstant::FALSE; ?>" <?= ($filters['cash_on_delivery'] == ServiceConstant::FALSE && strlen($filters['cash_on_delivery'])) ? 'selected' : '' ?>>
                                    No
                                </option>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Shipping Type</label><br>
                            <select name="shipping_type" id="" class="form-control  filter-status">
                                <option value="">All</option>
                                <?php foreach ($shipping_types as $shipping_type) { ?>
                                    <option
                                        value="<?= $shipping_type; ?>" <?= $filters['shipping_type'] == $shipping_type ? 'selected' : '' ?>><?= ServiceConstant::getShippingType($shipping_type); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Delivery Type</label><br>
                            <select name="delivery_type" id="" class="form-control  filter-status">
                                <option value="">All</option>
                                <?php foreach ($delivery_types as $delivery_type) { ?>
                                    <option
                                        value="<?= $delivery_type; ?>" <?= $filters['delivery_type'] == $delivery_type ? 'selected' : '' ?>><?= ServiceConstant::getDeliveryType($delivery_type); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Delivery Route</label><br>
                            <select name="route_id" id="" class="form-control  filter-status">
                                <option value="">All</option>
                                <?php foreach ($routes as $route) { ?>
                                    <option
                                        value="<?= $route['id']; ?>" <?= $filters['route_id'] == $route['id'] ? 'selected' : '' ?>><?= strtoupper($route['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Payment method</label><br>
                            <select name="payment_type" id="" class="form-control  filter-status">
                                <option value="">All</option>
                                <?php foreach ($payment_methods as $method) { ?>
                                    <option
                                        value="<?= $method; ?>" <?= $filters['payment_type'] == $method ? 'selected' : '' ?>><?= ServiceConstant::getPaymentMethod($method); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="pull-left form-group form-group-sm">
                            <label for="">Request Type</label><br>
                            <select name="request_type" id="" class="form-control  filter-status">
                                <option value="">All</option>
                                <?php foreach ($request_types as $method) { ?>
                                    <option
                                        value="<?= $method; ?>" <?= $filters['request_type'] == $method ? 'selected' : '' ?>><?= ServiceConstant::getRequestType($method); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <?php if(Calypso::isCooperateUser()){?>
                            <input type="hidden" name="company_id" value="<?= Calypso::getInstance()->session('user_session')['company']['id'] ?>">
                        <?php }elseif(Calypso::getInstance()->session('user_session')['role_id'] != ServiceConstant::USER_TYPE_SALES_AGENT){?>

                        <div class="pull-left form-group form-group-sm">
                            <label for="">Branch Type</label><br>
                            <select name="branch_type" id="branch_type" class="form-control filter-status">
                                <option value="all" selected>All</option>
                                <option
                                    value="hub" <?= (Calypso::getValue($filters, 'branch_type', false) == 'hub') ? ' selected="selected"' : '' ?>>
                                    Hub
                                </option>
                                <option
                                    value="ec" <?= (Calypso::getValue($filters, 'branch_type', false) == 'ec') ? ' selected="selected"' : '' ?>>
                                    Express Centre
                                </option>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Originating Branch</label><br>
                            <select name="created_branch_id" id="created_branch_select"
                                    class="form-control  filter-status" multiple="true">
                                <?php
                                $branch_type = Calypso::getValue($filters, 'branch_type', false);
                                if ($branch_type == 'ec') {
                                    $branches = $ecs;
                                } elseif ($branch_type == 'hub') {
                                    $branches = $hubs;
                                } else {
                                    $branches = array_merge($ecs, $hubs);
                                }
                                foreach ($branches as $branch) { ?>
                                    <option
                                        value="<?= $branch['id']; ?>" <?= $filters['created_branch_id'] == $branch['id'] ? 'selected' : '' ?>><?= strtoupper($branch['name']); ?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="created_branch_id" value=""/>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Current Branch</label><br>
                            <select id="current_branch_select" class="form-control  filter-status" multiple="true>
                                <?php

                            foreach ($branches as $branch) { ?>
                                    <option
                                        value="<?= $branch['id']; ?>
                            " <?= $filters['from_branch_id'] == $branch['id'] ? 'selected' : '' ?>
                            ><?= strtoupper($branch['name']); ?></option>
                            <?php } ?>
                            </select>
                            <input type="hidden" name="from_branch_id" value=""/>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Select Corporate</label><br>
                            <select name="company_id" id="current_branch_select" class="form-control  filter-status">
                                <option value="">All</option>
                                <?php foreach ($companies as $company) { ?>
                                <option
                                    value=<?= $company['id']; ?> <?= ($company['id'] == $selected_company) ? 'selected' : '' ?>>
                                    <?= strtoupper($company['name']); ?></option>
                                    <?php } ?>
                            </select>
                        </div>

                        <?php } ?>

                        <div class="pull-left form-group form-group-sm">
                            <label for="">Select Negative Status</label><br>
                            <select name="return_reason_comment" id="current_branch_select" class="form-control  filter-status">
                                <option value="">None</option>
                                <?php foreach ($return_reasons as $return_reason) { ?>
                                    <option
                                        value="<?= strtoupper($return_reason['meaning_of_status']); ?>"
                                        <?= (strtoupper($return_reason['meaning_of_status']) == $selected_return_reason) ? 'selected' : ''?>>
                                        <?= strtoupper($return_reason['meaning_of_status']); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="pull-left">
                            <label>&nbsp;</label><br>
                            <button class="btn btn-default btn-sm" id="apply" type="submit"><i class="fa fa-filter"></i>
                                APPLY
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="pull-right clearfix">
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if (!empty($parcels)): ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No.</th>
                        <th>Reference No.</th>
                        <th>Receiver</th>
                        <th>Receiver Phone</th>
                        <th>Created Date</th>
                        <th>Delivery Route</th>
                        <th>Return Status</th>
                        <th>Current Status</th>
                        <th>Last Modified</th>
                        <th>Delivery Type</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = $offset;
                    if (isset($parcels) && is_array($parcels)) {
                        foreach ($parcels as $parcel) {
                            ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= strtoupper(Calypso::getValue($parcel, 'parcel_waybill_number')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($parcel, 'parcel_reference_number')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($parcel, 'receiver_firstname') . ' ' . Calypso::getValue($parcel, 'receiver_lastname')) ?></td>
                                <td><?= Calypso::getValue($parcel, 'parcel_reference_number') ?></td>
                                <td><?= Util::formatDate(ServiceConstant::DATE_TIME_FORMAT, Calypso::getValue($parcel, 'parcel_created_date')); ?></td>
                                <td><?= Calypso::getValue($parcel, 'route_name'); ?></td>
                                <td><?= Calypso::getDisplayValue($parcel, 'parcel_comment_comment'); ?></td>
                                <td><?= ServiceConstant::getStatus($parcel['parcel_status']); ?></td>
                                <td><?= Util::formatDate(ServiceConstant::DATE_TIME_FORMAT, Calypso::getValue($parcel, 'parcel_modified_date')); ?>
                                    (<?= Util::ago(Calypso::getValue($parcel, 'parcel_modified_date')); ?>)
                                </td>
                                <td><?= ucwords(ServiceConstant::getDeliveryType(Calypso::getValue($parcel, 'parcel_delivery_type'))); ?></td>
                                <td>
                                    <a href="<?= Url::toRoute(['/shipments/view?waybill_number=' . Calypso::getValue($parcel, 'parcel_waybill_number')]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i></a>
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
<?php $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/select2.js', ['depends' => [AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/utils.js', ['depends' => [JqueryAsset::className()]]) ?>
<?php $this->registerJs('var ecs = ' . Json::encode($ecs) . '; var hubs = ' . Json::encode($hubs) . '; var filters = ' . Json::encode($filters) . ';', View::POS_HEAD); ?>
<?php $this->registerJsFile('@web/js/report.js', ['depends' => [JqueryAsset::className()]]) ?>


