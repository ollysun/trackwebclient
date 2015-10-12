<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'Pending Shipment Requests';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['corporate'],
        'label' => 'Corporate'
    ),
    array('label' => 'Pending Shipment Requests')
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
                            <th>Waybill No</th>
                            <th>Description</th>
                            <th>Receiver</th>
                            <th>Receiver Phone</th>
                            <th>Reference Number</th>
                            <th>Weight</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = $offset;
                        foreach ($requests as $request): ?>
                            <tr>
                                <td><?= ++$i; ?></td>
                                <td><?= Calypso::getValue($request, 'id'); ?></td>
                                <td></td>
                                <td><?= Calypso::getValue($request, 'description'); ?></td>
                                <td><?= Calypso::getValue($request, 'receiver_firstname') . ' ' . Calypso::getValue($request, 'receiver_lastname'); ?></td>
                                <td><?= Calypso::getValue($request, 'receiver_phone_number'); ?></td>
                                <td><?= Calypso::getValue($request, 'reference_number'); ?></td>
                                <td><?php $weight = Calypso::getValue($request, 'estimated_weight');
                                    echo is_null($weight) ? '' : $weight . ' KG';
                                    ?></td>
                                <td><?= strtoupper(Calypso::getValue($request, 'status')); ?></td>
                                <td></td>
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