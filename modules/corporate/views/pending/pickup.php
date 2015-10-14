<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'Pickup Requests';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['corporate'],
        'label' => 'Corporate'
    ),
    array('label' => 'Pickup Requests')
);
?>

<?php
$this->params['content_header_button'] = '';
?>
<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>
<?php echo Calypso::showFlashMessages(); ?>
    <div class="main-box">
        <div class="main-box-header table-search-form clearfix">
            <div class=" clearfix">
                <div class="pull-left">
                    <?= $this->render('../elements/date_filter', ['from_date' => $from_date, 'to_date' => $to_date]); ?>
                </div>
                <div class="pull-right clearfix">
                    <form class="form-inline clearfix">
                        <div class="pull-left form-group">
                            <label for="searchInput">Search</label><br>

                            <div class="input-group input-group-sm input-group-search">
                                <input id="searchInput" type="text" name="search" placeholder="Waybill Number"
                                       class="search-box form-control">

                                <div class="input-group-btn">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="pull-left hidden">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-sm btn-default"><i class="fa fa-download"></i> Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-box-body">
            <div class="table-responsive">
                <?php if (count($requests) > 0): ?>
                    <table id="table" class="table table-hover dataTable">
                        <thead>
                        <tr>
                            <th style="width: 20px">S/N</th>
                            <th>Request ID</th>
                            <th>Company</th>
                            <th>Waybill No</th>
                            <th>Description</th>
                            <th>Pickup</th>
                            <th>Pickup Add., City, State</th>
                            <th>Destination</th>
                            <th>Dest. Add., City, State</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = $offset; foreach ($requests as $request): ?>
                            <tr>
                                <td><?= ++$i; ?></td>
                                <td><?= Calypso::getValue($request, 'id'); ?></td>
                                <td><?= strtoupper(Calypso::getValue($request, 'company.name', '')); ?></td>
                                <td></td>
                                <td><?= Calypso::getValue($request, 'shipment_description'); ?></td>
                                <td><?= Calypso::getValue($request, 'pickup_name');?> (<?= Calypso::getValue($request, 'pickup_phone_number');?>)</td>
                                <td><?= Calypso::getValue($request, 'pickup_address')  . ', ' . strtoupper(Calypso::getValue($request, 'pickup_city.name', '')) . ', ' . strtoupper(Calypso::getValue($request, 'pickup_state.name', ''))?></td>
                                <td><?= Calypso::getValue($request, 'destination_name');?> (<?= Calypso::getValue($request, 'destination_phone_number');?>)</td>
                                <td><?= Calypso::getValue($request, 'destination_address')  . ', ' . strtoupper(Calypso::getValue($request, 'destination_city.name', '')) . ', ' . strtoupper(Calypso::getValue($request, 'destination_state.name', ''))?></td>
                                <td><?= strtoupper(Calypso::getValue($request, 'status')); ?></td>
                                <td>
                                    <a title="View this request" href="<?= Url::toRoute(['/corporate/request/viewpickup', 'id' => Calypso::getValue($request, 'id')]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>

                                    <a title="Create parcel" href="<?= Url::toRoute(['/parcels/new', 'pickup_request_id' => Calypso::getValue($request, 'id')]) ?>"
                                       class="btn btn-xs btn-primary"><i class="fa fa-copy"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]); ?>
                <?php else: ?>
                    There are no new requests.
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php $this->registerJsFile('@web/js/libs/bootstrap-select.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/corporate_requests.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>