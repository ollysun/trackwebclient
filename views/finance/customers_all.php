<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;
use yii\widgets\LinkPager;


$this->title = 'Customers Reconcialitions: All';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['finance/'],
        'label' => 'Reconcialitions'
    ),
    array(
        'url' => ['finance/customersall'],
        'label' => 'Customers'
    ),
    array('label' => 'All')
);

?>


<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                 <form method="get">
                     <link href="/css/libs/datepicker.css" rel="stylesheet">
                    <div class="clearfix">
                        <div class="pull-left form-group form-group-sm">
                            <label for="">From:</label><br>
                            <input name="from" value="<?=$from_date?>"class="form-control date-range" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">To:</label><br>
                            <input name="to" value="<?=$to_date?>" class="form-control date-range" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label for="">Payment method</label><br>
                            <select name="payment_type" id="" class="form-control  filter-status">
                                <option value=""<?=($payment_type==''?'selected':'');?>>All method</option>
                                <option value="<?= ServiceConstant::REF_PAYMENT_METHOD_CASH; ?>" <?=($payment_type==ServiceConstant::REF_PAYMENT_METHOD_CASH?'selected':'');?>><?=ServiceConstant::getPaymentMethod(ServiceConstant::REF_PAYMENT_METHOD_CASH); ?></option>
                                <option value="<?= ServiceConstant::REF_PAYMENT_METHOD_POS; ?>" <?=($payment_type==ServiceConstant::REF_PAYMENT_METHOD_POS?'selected':'');?>><?=ServiceConstant::getPaymentMethod(ServiceConstant::REF_PAYMENT_METHOD_POS); ?></option>
                                <option value="<?= ServiceConstant::REF_PAYMENT_METHOD_CASH_POS; ?>" <?=($payment_type==ServiceConstant::REF_PAYMENT_METHOD_CASH_POS?'selected':'');?>><?=ServiceConstant::getPaymentMethod(ServiceConstant::REF_PAYMENT_METHOD_CASH_POS); ?></option>
                                <option value="<?= ServiceConstant::REF_PAYMENT_METHOD_DEFERRED; ?>" <?=($payment_type==ServiceConstant::REF_PAYMENT_METHOD_DEFERRED?'selected':'');?>><?=ServiceConstant::getPaymentMethod(ServiceConstant::REF_PAYMENT_METHOD_DEFERRED); ?></option>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm hidden">
                            <label for="">Records</label><br>
                            <select name="page_width" id="page_width" class="form-control " disabled>
                                <option>10</option>
                                <option>20</option>
                                <option>50</option>
                                <option>100</option>
                                <option>200</option>
                            </select>
                        </div>
                        <div class="pull-left">
                            <label>&nbsp;</label><br>
                            <button class="btn btn-default btn-sm" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="pull-right clearfix">
                <form class="table-search-form form-inline clearfix">
                    <div class="pull-left">
                        <label for="searchInput">Search</label><br>
                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="waybillnumber" placeholder="Waybill Number" class="search-box form-control" value="<?= $search ? $search : '' ?>">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">

            <?php if (count($parcels) > 0) { ?>
                <table id="table" class="table table- table-striped table-hover">
                    <thead>
                    <tr>
                        <th rowspan="2" style="width: 20px">S/N</th>
                        <th rowspan="2">Waybill No.</th>
                        <th rowspan="2">Amt. Invoiced (<span class="currency naira"></span>)</th>
                        <th rowspan="1" colspan="3">Amount Collected (<span class="currency naira"></span>)</th>
                        <th rowspan="2">EC</th>
                        <th rowspan="2">EC Officer</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th rowspan="1">Cash</th>
                        <th rowspan="1">POS</th>
                        <th rowspan="1">POS ID</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (isset($parcels)) {
                        $row = $offset;
                        foreach ($parcels as $parcel) {
                            ?>
                            <tr>
                                <td><?= ++$row; ?></td>
                                <td><?= strtoupper($parcel['waybill_number']); ?></td>
                                <td><?= number_format($parcel['amount_due'], 2, '.', ','); ?></td>
                                <td><?= ($parcel['cash_amount'] > 0) ? Calypso::getInstance()->formatCurrency($parcel['cash_amount']) : '-'; ?></td>
                                <td><?= ($parcel['pos_amount'] > 0) ? Calypso::getInstance()->formatCurrency($parcel['pos_amount']) : '-'; ?></td>
                                <td><?= ($parcel['pos_trans_id'] > 0) ? $parcel['pos_trans_id'] : '-'; ?></td>
                                <td><?= ucwords($parcel['from_branch']['name']); ?></td>
                                <td>N/A</td>
                                <td>
                                    <a href="<?= Url::to(['site/viewwaybill?id=' . $parcel['id']]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
                <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$row, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>

            <?php } else { ?>
                <p>No matching record found</p>
            <?php } ?>
        </div>
    </div>
</div>
<?php $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?><?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
