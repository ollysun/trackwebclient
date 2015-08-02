<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Shipments: Due to Sweep';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label'=> 'Due to sweep')
);
$show_next = false;
$show_prev = false;

$link = "";
if($search){
    $fro = date('Y/m/d',strtotime($from_date));
    $to = date('Y/m/d',strtotime($to_date));
    $link = "&search=true&to=".urlencode($to)."&from=".urlencode($fro);
}

if( count($parcels) >= $page_width ){
    $show_next = true;
}else{
    $show_next = false;
}


if($offset <= 0){
    $show_prev = false;
}elseif (($offset - $page_width) >= 0){
    $show_prev = true;
}

?>
<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>


<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class="clearfix">
            <form class="form-inline pull-right clearfix">
                <div class="pull-left form-group">
                    <label for="searchInput">Search</label><br>
                    <div class="input-group input-group-sm input-group-search">
                        <input id="searchInput" type="text" name="search" placeholder="" class="search-box form-control">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="pull-left">
                <label>&nbsp;</label><br>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="manifest">Generate Manifest</button>
                <!--<button type="button" onclick="javascript:window.print();" class="btn btn-sm btn-default">Generate Sweep Run</button>-->
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <!--						<th style="width: 20px"><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>-->
                    <th style="width: 20px">No.</th>
                    <th>Waybill No.</th>
                    <th>Shipper</th>
                    <th>Shipper Phone</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($parcels) && is_array($parcels)){
                    $i = 1;$count = $offset + 1;
                    foreach($parcels as $parcel){
                        ?>
                        <tr>
                            <!--						<td><div class="checkbox-nice"><input id="chbx_w_000--><?//= $i ?><!--" type="checkbox"><label for="chbx_w_0001"> </label></div></td>-->
                            <td><?= $count++ ?></td>
                            <td><?= strtoupper($parcel['waybill_number']); ?></td>
                            <td><?= strtoupper($parcel['sender']['firstname'].' '. $parcel['sender']['lastname']) ?></td>
                            <td><?= $parcel['sender']['phone'] ?></td>
                            <td><?= strtoupper($parcel['receiver']['firstname'].' '. $parcel['receiver']['lastname']) ?></td>
                            <td><?= $parcel['receiver']['phone'] ?></td>
                            <td><?= date('Y/m/d @ H:m',strtotime($parcel['created_date'])); ?></td>
                            <td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
                            <td><a href="<?= Url::to(['site/viewwaybill?id='.$parcel['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                        </tr>
                    <?php
                    }}
                ?>

                </tbody>
            </table>
            <div class="pull-right form-group">
                <?php if($show_prev): ?>
                    <a href="<?= Url::to(['shipments/forsweep?offset='.($offset - $page_width).$link]) ?>" class="btn btn-primary btn-sm">Prev</a>
                <?php endif;  ?>
                <?php if($show_next): ?>
                    <a href="<?= Url::to(['shipments/forsweep?offset='.($offset + $page_width).$link]) ?>" class="btn btn-primary btn-sm">Next</a>
                <?php endif;  ?>
            </div>
        </div>
    </div>
</div>



<!-- this page specific scripts -->
<?= $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>


