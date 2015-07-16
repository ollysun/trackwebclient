<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'New Parcels';
$this->params['breadcrumbs'] = array(
	array(
		'url' => ['site/parcels'],
		'label' => 'Parcels'
	),
	array('label'=> $this->title)
);

?>
<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>


<?php
	$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
	<div class="main-box-header table-search-form clearfix">
		<div class=" clearfix">
			<div class="pull-left">
				<?= $this->render('../elements/parcels_filter',[]) ?>
			</div>
			<div class="pull-right clearfix">
                <form class="form-inline clearfix">
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
				<div class="pull-left hidden">
					<label>&nbsp;</label><br>
					<button type="button" class="btn btn-sm btn-default"><i class="fa fa-download"></i> Download</button>
				</div>
			</div>
		</div>
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover  table-bordered">
                <thead>
                <tr>
                    <!--						<th style="width: 20px"><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>-->
                    <th style="width: 20px">No.</th>
                    <th>Waybill No.</th>
                    <th>Shipper</th>
                    <th>Shipper Phone</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
				<tbody>
                <?php
                if(isset($parcels) && is_array($parcels)){
                    $i = 1;
                    foreach($parcels as $parcel){
                        ?>
                        <tr>
                            <!--						<td><div class="checkbox-nice"><input id="chbx_w_000--><?//= $i ?><!--" type="checkbox"><label for="chbx_w_0001"> </label></div></td>-->
                            <td><?= $i++ ?></td>
                            <td><?= strtoupper($parcel['waybill_number']); ?></td>
                            <td><?= strtoupper($parcel['sender']['firstname'].' '. $parcel['sender']['lastname']) ?></td>
                            <td><?= $parcel['sender']['phone'] ?></td>
                            <td><?= strtoupper($parcel['receiver']['firstname'].' '. $parcel['receiver']['lastname']) ?></td>
                            <td><?= $parcel['receiver']['phone'] ?></td>
                            <td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
                            <td><a href="<?= Url::to(['site/viewwaybill?id='.$parcel['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                        </tr>
                    <?php
                    }}
                ?>

                </tbody>
			</table>
		</div>
	</div>
</div>



<!-- this page specific scripts -->
<?= $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?= $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
