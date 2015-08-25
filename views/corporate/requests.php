<?php
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
$stats = array(
    'total'=> '20000',
    'used'=> '12230.63',
    'class'=> 'success'
);
$from_date = '1970/01/01 00:00:00';
$to_date = '2015/09/09 23:59:59';
?>


<?php

$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> New Request</button>';
$this->params['graph_stats'] = $this->render('../elements/corporate/credit_limit',['stats'=>$stats]);

?>

<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class=" clearfix">
            <div class="pull-left">
                <?= $this->render('../elements/parcels_date_filter',['from_date'=>$from_date, 'to_date'=>$to_date]); ?>
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
            <?php if(true): //count($parcels) ?>
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <!--						<th style="width: 20px"><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>-->
                    <th style="width: 20px">No.</th>
                    <th>Waybill No.</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Weight</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><a href="<?= Url::to(['site/viewwaybill?id=1']); ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                    </tr>

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
                    <h4 class="modal-title" id="myModalLabel">Create a new request</h4>
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
                        <fieldset class="col-xs-6">
                            <legend>Shipment Details</legend>
                            <div class="row">
                                <div class="form-group col-xs-4">
                                    <label for="">Weight (kg)</label>
                                    <input type="text" class="form-control validate required number">
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="">Dimension</label>
                                    <input type="text" class="form-control validate required">
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="">Quantity</label>
                                    <input type="text" class="form-control validate required integer">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Other Information</label>
                                <textarea class="form-control validate"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Expected Pickup time</label>
                                    <input type="text" class="form-control validate required">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Expected Time of Delivery</label>
                                    <input type="text" class="form-control validate required">
                                </div>
                            </div>
                        </fieldset>
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

<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>