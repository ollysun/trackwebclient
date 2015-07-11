<?php
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'Parcels: Due to Sweep';
$this->params['breadcrumbs'] = array(
	array(
	'url' => ['site/parcels'],
	'label' => 'Parcels'
	),
	array('label'=> 'Due to sweep')
);

?>
<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>


<?php
	$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
	<div class="main-box-header clearfix">
		<div class="pull-left">
			<label>&nbsp;</label><br>
			<button type="button" class="btn btn-default">Generate Sweep Run</button>

		</div>
		<form class="table-search-form form-inline pull-right">

			<div class="">
				<label for="searchInput">Search</label><br>
				<div class="input-group">
					<input id="searchInput" type="text" name="search" placeholder="" class="search-box form-control">
					<div class="input-group-btn">
						<button class="btn btn-default" type="submit">
							<i class="fa fa-search"></i>
						</button>
					</div>
				</div>
			</div>

		</form>
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover">
				<thead>
					<tr>
						<th><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>
						<th>Waybill No.</th>
						<th>Shipper</th>
						<th>Receiver</th>
						<th>Created at</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><div class="checkbox-nice"><input id="chbx_w_0001" type="checkbox"><label for="chbx_w_0001"> </label></div></td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="<?= Url::to(['site/viewwaybill']) ?>" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
					<tr>
						<td><div class="checkbox-nice"><input id="chbx_w_0002" type="checkbox"><label for="chbx_w_0002"> </label></div></td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="<?= Url::to(['site/viewwaybill']) ?>" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
					<tr>
						<td><div class="checkbox-nice"><input id="chbx_w_0003" type="checkbox"><label for="chbx_w_0003"> </label></div></td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="<?= Url::to(['site/viewwaybill']) ?>" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
					<tr>
						<td><div class="checkbox-nice"><input id="chbx_w_0004" type="checkbox"><label for="chbx_w_0004"> </label></div></td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="<?= Url::to(['site/viewwaybill']) ?>" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
					<tr>
						<td><div class="checkbox-nice"><input id="chbx_w_0005" type="checkbox"><label for="chbx_w_0005"> </label></div></td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="<?= Url::to(['site/viewwaybill']) ?>" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
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
