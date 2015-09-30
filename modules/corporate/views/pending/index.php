<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Corporate Requests: Pending';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['corporate/requests'],
        'label' => 'Corporate'
    ),
    array('label'=> 'Pending Requests')
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

$this->params['graph_stats'] = $this->render('../elements/corporate/credit_limit',['stats'=>$stats]);

?>

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
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Sender</th>
                    <th>Sender's Contact</th>
                    <th>Receiver</th>
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
                        <td><a href="#" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil">&nbsp;</i> Edit</a></td>
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
                    <h4 class="modal-title" id="myModalLabel">Edit request</h4>
                </div>
                <div class="modal-body">
                    <fieldset class="">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger">Decline</button>
                    <button type="submit" class="btn btn-primary">Approve</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>