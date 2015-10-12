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
$this->params['content_header_button'] = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#pickupModal"><i class="fa fa-plus"></i> Pickup Request</button>';
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
                                <td></td>
                                <td><?= Calypso::getValue($request, 'shipment_description'); ?></td>
                                <td><?= Calypso::getValue($request, 'pickup_name');?> (<?= Calypso::getValue($request, 'pickup_phone_number');?>)</td>
                                <td><?= Calypso::getValue($request, 'pickup_address')  . ', ' . strtoupper(Calypso::getValue($request, 'pickup_city.name', '')) . ', ' . strtoupper(Calypso::getValue($request, 'pickup_state.name', ''))?></td>
                                <td><?= Calypso::getValue($request, 'destination_name');?> (<?= Calypso::getValue($request, 'destination_phone_number');?>)</td>
                                <td><?= Calypso::getValue($request, 'destination_address')  . ', ' . strtoupper(Calypso::getValue($request, 'destination_city.name', '')) . ', ' . strtoupper(Calypso::getValue($request, 'destination_state.name', ''))?></td>
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

    <div class="modal fade" id="pickupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form action="<?= Url::to("/corporate/request/createpickup")?>" data-keyboard-submit="disable" class="validate-form" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create a new Pickup Request</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <fieldset class="col-xs-6">
                                <legend>Pickup Detail</legend>
                                <div class="form-group">
                                    <label for="">Address</label>
                                    <input name="pickup[address]" type="text" class="form-control validate required">
                                </div>
                                <div class="form-group">
                                    <label for="">State</label>
                                    <select name="pickup[state_id]" id="pickup_state_id"
                                            data-city_target="pickup_city_id"
                                            class="form-control validate required">
                                        <option value="" selected>Select State</option>
                                        <?php
                                        $states = is_null($states) ? [] : $states;
                                        foreach ($states as $state): ?>
                                            <option
                                                value="<?= Calypso::getValue($state, 'id') ?>"><?= strtoupper(Calypso::getValue($state, 'name')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">City</label>
                                    <select name="pickup[city_id]" id="pickup_city_id" class="form-control validate required">
                                        <option value="" selected>Select State First</option>
                                    </select>

                                </div>
                                <div class="form-group">
                                    <label for="">Contact Name</label>
                                    <input name="pickup[name]" type="text" class="form-control validate required">
                                </div>
                                <div class="form-group">
                                    <label for="">Contact Phone number</label>
                                    <input name="pickup[phone_number]" type="text" class="form-control validate required phone">
                                    <span class="help-block">Format: 234xxxxxxxxxx</span>
                                </div>
                            </fieldset>
                            <fieldset class="col-xs-6">
                                <legend>Destination Detail</legend>
                                <div class="form-group">
                                    <label for="">Address</label>
                                    <input name="destination[address]" type="text" class="form-control validate required">
                                </div>
                                <div class="form-group">
                                    <label for="">State</label>
                                    <select name="destination[state_id]" id="destination_state_id"
                                                                       data-city_target="destination_city_id"
                                                                       class="form-control validate required">
                                        <option value="" selected>Select State</option>
                                        <?php
                                        $states = is_null($states) ? [] : $states;
                                        foreach ($states as $state): ?>
                                            <option
                                                value="<?= Calypso::getValue($state, 'id') ?>"><?= strtoupper(Calypso::getValue($state, 'name')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">City</label>
                                    <select name="destination[city_id]" id="destination_city_id" class="form-control validate required">
                                        <option value="" selected>Select State First</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Contact Name</label>
                                    <input name="destination[name]" type="text" class="form-control validate required">
                                </div>
                                <div class="form-group">
                                    <label for="">Contact Phone number</label>
                                    <input name="destination[phone_number]" type="text" class="form-control validate required phone">
                                    <span class="help-block">Format: 234xxxxxxxxxx</span>
                                </div>
                            </fieldset>
                            <fieldset class="col-xs-12">
                                <legend>Shipment Detail</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label>Shipment Description</label>
                                        <input name="shipment_description" type="text" class="form-control">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label>Request Details</label>
                                        <textarea name="request_detail" class="form-control"></textarea>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button name="pickup" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Request Pickup</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php $this->registerJsFile('@web/js/libs/bootstrap-select.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/corporate_requests.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>