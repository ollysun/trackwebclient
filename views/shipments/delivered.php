<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


$this->title = 'Shipments: Delivered';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label' => 'Delivered')
);

$user_data = $this->context->userData;
?>

<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class="pull-left">
            <?= $this->render('../elements/parcels_date_filter', array('from_date' => $from_date, 'to_date' => $to_date)); ?>
        </div>
    </div>
    <div class="main-box-body">
        <?php if(!empty($parcels)) { ?>
        <div class="table-responsive">
            <table class="table table-hover dataTable">
                <thead>
                <tr>
                    <!-- <th style="width: 20px;"></th> -->
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Reference No.</th>
                    <th>Sender</th>
                    <th>Sender Phone</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                        <th>Originating Branch</th>
                        <th>Current Location</th>
                    <?php } ?>
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
                           <td><?= ++$row; ?></td>
                            <td><?= $parcel['waybill_number']; ?></td>
                            <td><?= $parcel['reference_number']; ?></td>
                            <td><?= ucwords($parcel['sender']['firstname'].' '. $parcel['sender']['lastname']) ?></td>
                            <td><?= $parcel['sender']['phone'] ?></td>
                            <td><?= ucwords(Calypso::getDisplayValue($parcel, 'delivery_receipt.name', '')) ?></td>
                            <td><?= Calypso::getDisplayValue($parcel, 'delivery_receipt.phone_number', '') ?></td>
                            <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                                <td><?= strtoupper(Calypso::getValue($parcel, "created_branch.name")) ?></td>
                                <td><?= ParcelAdapter::getCurrentLocation($parcel); ?></td>
                            <?php } ?>
                            <td><a href="<?= Url::toRoute(['/shipments/view?waybill_number=' . $parcel['waybill_number']]) ?>"
                                   class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                        </tr>
                    <?php }
                } ?>
                </tbody>
            </table>
            <?= $this->render('../elements/pagination_and_summary',['first'=>$offset,'last'=>$row,'page_width'=>$page_width,'total_count'=>$total_count]) ?>
        </div>
        <?php } else { ?>
            There are no shipments that were delivered.
        <?php } ?>
    </div>
</div>


<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>