<?php
use Adapter\Util\Calypso;
use yii\helpers\Url;
use Adapter\ParcelAdapter;
use Adapter\Globals\ServiceConstant;

$this->title = 'Shipments: Returned';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label' => 'Returned')
);

$user_data = $this->context->userData;
?>

<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <form class="table-search-form form-inline pull-right clearfix">
            <div class="pull-left form-group">
                <label for="searchInput">Search</label><br>

                <div class="input-group input-group-sm input-group-search">
                    <input id="searchInput" type="text" name="search" placeholder="Search by Waybill number"
                           class="search-box form-control">

                    <div class="input-group-btn">
                        <button class="btn btn-default" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div class="pull-left">
            <?= $this->render('../elements/parcels_date_filter', array('from_date' => $from_date, 'to_date' => $to_date)); ?>
        </div>
    </div>
    <div class="main-box-body">
        <?php if (!empty($parcels)) { ?>
            <div class="table-responsive">
                <table class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No.</th>
                        <th>Reference No.</th>
                        <th>Sender</th>
                        <th>Sender Phone</th>
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
                        foreach ($parcels as $parcel) {
                            ?>
                            <tr>
                                <td><?= ++$row; ?></td>
                                <td><?= $parcel['waybill_number']; ?></td>
                                <td><?= $parcel['reference_number']; ?></td>
                                <td><?= ucwords($parcel['sender']['firstname'] . ' ' . $parcel['sender']['lastname']) ?></td>
                                <td><?= $parcel['sender']['phone'] ?></td>
                                <?php if($user_data['role_id'] == ServiceConstant::USER_TYPE_ADMIN) { ?>
                                    <td><?= strtoupper(Calypso::getValue($parcel, "created_branch.name")) ?></td>
                                    <td><?= ParcelAdapter::getCurrentLocation($parcel); ?></td>
                                <?php } ?>
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
        <?php } else { ?>
            There are no shipments that were returned.
        <?php } ?>
    </div>
</div>


<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>