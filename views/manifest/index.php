<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Manifests';
$this->params['breadcrumbs'] = array(
   /* array(
        'url' => ['manifests/index'],
        'label' => 'Manifests'
    ),*/
    array('label'=> $this->title)
);

?>
<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>



<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class=" clearfix">
            <div class="pull-left">
            	<?php
            		$from_date = Date('Y/m/d');
            		$to_date = Date('Y/m/d');
            		$filter = '';
            	?>
                <?= $this->render('../elements/manifests_filter',['from_date'=>$from_date,'to_date'=>$to_date,'filter'=>$filter]) ?>
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
        		<?php $manifests = 'hello'; ?>
            <?php if(!empty($manifests)): ?>
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Manifest No.</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Driver</th>
                    <th>No of Shipments</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($manifests) && is_array($manifests)){
                    $i = $offset;
                    foreach($manifests as $manifest){
                        ?>
                        <tr>
                            <!--	<td><div class="checkbox-nice"><input id="chbx_w_000--><?//= $i ?><!--" type="checkbox"><label for="chbx_w_000--><?//= $i ?><!--"> </label></div></td>-->
                            <td><?= ++$i ?></td>

                            <td><a href="<?= Url::to(['manifest/view?id='.$manifest['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                        </tr>
                    <?php
                    }}
                ?>

                </tbody>
            </table>
            <?php //= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
            <?php else:  ?>
                    There are no manifests matching the specified criteria.
            <?php endif;  ?>
        </div>
    </div>
</div>



<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>


