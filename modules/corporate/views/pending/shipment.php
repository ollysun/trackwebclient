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
                    <?= $this->render('../elements/date_filter', ['from_date' => $from_date, 'to_date' => $to_date, 'companyId' => $companyId]); ?>
                </div>
                <div class="pull-right clearfix">
                    <form id="company_filter_form" class="form-inline clearfix">
                        <div class="pull-left form-group">
                            <label for="searchInput">Filter by Company</label><br>
                            <select name="company_id" id="company_filter" class="form-control text-muted">
                                <option>Select Company</option>
                                <?php foreach($companies as $company):?>
                                    <option value="<?= Calypso::getValue($company, 'id')?>"><?= strtoupper(Calypso::getValue($company, 'name', ''))?></option>
                                <?php endforeach; ?>
                            </select>

                            <button type="button" id="clearFilters" class="btn btn-sm btn-default"> Clear Filters
                            </button>
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
                            <th>Receiver</th>
                            <th>Receiver Phone</th>
                            <th>Reference Number</th>
                            <th>Weight/Piece</th>
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
                                <td><?= strtoupper(Calypso::getValue($request, 'company.name', '')); ?></td>
                                <td></td>
                                <td><?= Calypso::getValue($request, 'description'); ?></td>
                                <td><?= Calypso::getValue($request, 'receiver_firstname') . ' ' . Calypso::getValue($request, 'receiver_lastname'); ?></td>
                                <td><?= Calypso::getValue($request, 'receiver_phone_number'); ?></td>
                                <td><?= Calypso::getValue($request, 'reference_number'); ?></td>
                                <td><?php $weight = Calypso::getValue($request, 'estimated_weight');
                                    echo is_null($weight) ? '' : $weight . ' KG';
                                    ?></td>
                                <td><?= strtoupper(Calypso::getValue($request, 'status')); ?></td>
                                <td>
                                    <a title="View this request" href="<?= Url::toRoute(['/corporate/request/viewshipment', 'id' => Calypso::getValue($request, 'id')]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>

                                    <a title="Create parcel" href="<?= Url::toRoute(['/parcels/new', 'shipment_request_id' => Calypso::getValue($request, 'id')]) ?>"
                                       class="btn btn-xs btn-primary"><i class="fa fa-mail-forward"></i></a>

                                    <?php if(Calypso::getValue($request, 'status') == \Adapter\CompanyAdapter::STATUS_PENDING):?>
                                        <form method="post" action="<?= Url::to('/corporate/pending/declineshipment'); ?>">
                                            <input type="hidden" name="request_id" value="<?= Calypso::getValue($request, 'id');?>" />
                                            <input type="hidden" name="comment" value="" />
                                            <button data-decline="true" type="submit" title="Decline this request"
                                                    class="btn btn-xs btn-danger"><i class="fa fa-times"></i></button>
                                        </form>
                                    <?php endif; ?>
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
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/bootstrap-select.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/bootbox.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/corporate_requests.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>