<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Shipments: Dispatched';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label'=> 'Dispatched')
);

?>

<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
    <div class="main-box-header clearfix">
        <div class="pull-right">

            <button type="button" class="btn btn-default btn-sm">Receive from Dispatcher</button>
            <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Mark as delivered</button>
        </div>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width: 20px"><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>
                    <!-- <th style="width: 20px;"></th> -->
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Dispatcher</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><div class="checkbox-nice"><input id="chbx_w_1" type="checkbox"><label for="chbx_w_1"> </label></div></td>
                        <td>1</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><a class="btn btn-xs btn-default" href="#"><i class="fa fa-eye"></i> View</a></td>
                    </tr>
                    <tr>
                        <td><div class="checkbox-nice"><input id="chbx_w_2" type="checkbox"><label for="chbx_w_2"> </label></div></td>
                        <td>2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


