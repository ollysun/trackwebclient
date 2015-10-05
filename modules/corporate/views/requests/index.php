<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Corporate Requests';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['corporate/requests'],
        'label' => 'Corporate'
    ),
    array('label'=> 'Requests')
);
/*$stats = array(
    'total'=> '20000',
    'used'=> '12230.63',
    'class'=> 'success'
);*/
$from_date = '1970/01/01 00:00:00';
$to_date = '2015/09/09 23:59:59';
?>

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#pickupModal"><i class="fa fa-plus"></i> Pickup Request</button> <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Shipment Request</button>';
//$this->params['graph_stats'] = $this->render('../elements/corporate/credit_limit',['stats'=>$stats]);

?>
<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>

<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class=" clearfix">
            <div class="pull-left">
                <?= $this->render('../elements/date_filter',['from_date'=>$from_date, 'to_date'=>$to_date]); ?>
            </div>
            <div class="pull-right clearfix">
                <form class="form-inline clearfix">
                    <div class="pull-left form-group">
                        <label for="searchInput">Search</label><br>
                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="search" placeholder="Waybill Number" class="search-box form-control">
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
                    <button type="button" class="btn btn-sm btn-default"><i class="fa fa-download"></i> Download</button>
                </div>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <?php if(count($shipments) > 0):  ?>
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Request ID</th>
                    <th>Request Type</th>
                    <th>Waybill No</th>
                    <th>Description</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Weight</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($shipments as $shipment): $i = $offset;?>
                    <tr>
                        <td><?= ++$i;?></td>
                        <td><?= Calypso::getValue($shipment, 'id');?></td>
                       <!-- TODO Make Dynamic -->
                        <td>Shipment </td>
                        <td><?= Calypso::getValue($shipment, 'reference_number');?></td>
                        <td><?= Calypso::getValue($shipment, 'description');?></td>
                        <td><?= Calypso::getValue($shipment, 'receiver_firstname') . ' ' . Calypso::getValue($shipment, 'receiver_lastname');?></td>
                        <td><?= Calypso::getValue($shipment, 'receiver_phone_number');?></td>
                        <td><?= Calypso::getValue($shipment, 'estimated_weight');?></td>
                        <td><?= Calypso::getValue($shipment, 'status');?></td>
                        <td><a href="<?= Url::toRoute(['/site/viewwaybill?id=1']); ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?= ''; //$this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]); ?>
            <?php else:  ?>
                    There are no new requests.
            <?php endif;  ?>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form data-keyboard-submit="disable" class="validate-form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create a new Shipment Request</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <fieldset class="col-xs-6">
                            <legend>Receiver Details</legend>

                            <?php $prefix = 'receiver'; ?>

                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="">First Name</label>
                                        <input name="firstname[<?=$prefix?>]" id="firstname_<?=$prefix?>" type="text" class="form-control validate required name active-validate">

                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="">Last Name</label>
                                        <input name="lastname[<?=$prefix?>]" id="lastname_<?=$prefix?>" type="text" class="form-control validate required name active-validate">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="">Email address</label>
                                        <input name="email[<?=$prefix?>]" id="email_<?=$prefix?>" type="text" class="form-control <?php echo $prefix;?> validate active-validate required email">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="">Phone number</label>
                                        <input name="phone[<?=$prefix?>]" id="phone_<?=$prefix?>" type="text" class="form-control <?php echo $prefix;?>SearchFlyOutPanelTrigger validate active-validate required phone" data-target="#<?php echo $prefix;?>SearchFlyOutPanel">
                                        <span class="help-block">Format: 234xxxxxxxxxx</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Company</label>
                                <input name="address[<?=$prefix?>][]" id="address_<?=$prefix?>_1" class="form-control validate required">
                            </div>
                            <div class="form-group">
                                <label for="">Address</label>
                                <input name="address[<?=$prefix?>][]" id="address_<?=$prefix?>_2" class="form-control validate required ">
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-4">
                                    <label for="country_<?=$prefix?>">Country</label>
                                    <select name="country[<?=$prefix?>]" class="form-control validate required" id="country_<?=$prefix?>">
                                        <option value='' selected>Select Country...</option>
                                <?php
                                if (isset($countries) && is_array($countries['data'])) {
                                    foreach ($countries['data'] as $item) {
                                        ?>
                                                <option value="<?=$item['id']?>"><?=strtoupper($item['name']);?></option>
                                <?php
                                }}
                                ?>
                                    </select>
                                </div>

                                <div class="form-group col-xs-4">
                                    <label for="state_<?=$prefix?>">State</label>
                                    <select name="state[<?=$prefix?>]" class="form-control validate required" disabled="disabled" id="state_<?=$prefix?>"></select>
                                </div>

                                <div class="form-group col-xs-4">
                                    <label for="city_<?=$prefix?>">City</label>
                                    <select name="city[<?=$prefix?>]" class="form-control validate required" disabled="disabled" id="city_<?=$prefix?>"></select>
                                </div>
                            </div>

                        </fieldset>
                        <div class="col-xs-6">
                            <fieldset>
                                <legend>Shipment Details</legend>
                                <div class="row">
                                    <div class="form-group col-xs-4">
                                        <label for="">Estimated Wgt (Kg)</label>
                                        <input type="text" class="form-control validate required non-zero-number">
                                    </div>
                                    <div class="form-group col-xs-3">
                                        <label for="">No of packages</label>
                                        <input type="text" class="form-control validate required non-zero-integer">
                                    </div>
                                    <div class="form-group col-xs-5">
                                        <label>Shipment Value</label>
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <select name="currency" id="currencySelect" class="selectpicker" data-width="70px" data-style="btn-default" title="Please choose a currency">
                                                    <option title="NGN" value="NGN" selected="selected">Naira</option>
                                                </select>
                                            </div>
                                            <input name="parcel_value" type="text" class="form-control validate non-zero-number" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Parcel Description</label>
                                    <textarea class="form-control validate"></textarea>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Ecommerce</legend>
                                <div class="form-group">
                                    <label for="">Company name</label>
                                    <select class="form-control validate"></select>
                                </div>
                                <div class="form-group">
                                    <label for="">Cash on Delivery</label>
                                    <input type="text" class="form-control validate number">
                                </div>
                                <div class="form-group">
                                    <label for="">Order no</label>
                                    <input type="text" class="form-control validate ">
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Initiate Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="pickupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form data-keyboard-submit="disable" class="validate-form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create a new Pickup Request</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <fieldset class="col-xs-6">
                            <legend>Pickup Detail</legend>
                            <div class="form-group">
                                <label for="">Address</label>
                                <input type="text" class="form-control validate required">
                            </div>
                            <div class="form-group">
                                <label for="">State</label>
                                <select name="" id="" class="form-control validate required"></select>
                            </div>
                            <div class="form-group">
                                <label for="">City</label>
                                <select name="" id="" class="form-control validate required"></select>
                            </div>
                            <div class="form-group">
                                <label for="">Contact Name</label>
                                <input type="text" class="form-control validate required">
                            </div>
                            <div class="form-group">
                                <label for="">Contact Phone number</label>
                                <input type="text" class="form-control validate required phone">
                                <span class="help-block">Format: 234xxxxxxxxxx</span>
                            </div>
                        </fieldset>
                        <fieldset class="col-xs-6">
                            <legend>Destination Detail</legend>
                            <div class="form-group">
                                <label for="">Address</label>
                                <input type="text" class="form-control validate required">
                            </div>
                            <div class="form-group">
                                <label for="">State</label>
                                <select name="" id="" class="form-control validate required"></select>
                            </div>
                            <div class="form-group">
                                <label for="">City</label>
                                <select name="" id="" class="form-control validate required"></select>
                            </div>
                            <div class="form-group">
                                <label for="">Contact Name</label>
                                <input type="text" class="form-control validate required">
                            </div>
                            <div class="form-group">
                                <label for="">Contact Phone number</label>
                                <input type="text" class="form-control validate required phone">
                                <span class="help-block">Format: 234xxxxxxxxxx</span>
                            </div>
                        </fieldset>
                        <fieldset class="col-xs-12">
                            <legend>Shipment Detail</legend>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label>Shipment Description</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label>Request Details</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Request Pickup</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $this->registerJsFile('@web/js/libs/bootstrap-select.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>