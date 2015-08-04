<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Customer History';
$this->params['breadcrumbs'] = array(
	array(
		'label'=>'Customer History'
	)
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	//$this->params['content_header_button'] = '';
?>

<div class="main-box">
	<div class="main-box-header">

	</div>
	<div class="main-box-body">
		<br>

		<h2 class="text-muted text-center">Search to find and view customer history details</h2>
		<br>
		<form class="row" action="<?= Url::to(['shipments/customerhistorydetails']) ?>">
			<div class="col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 form-group">
				<div class="input-group input-group-lg">
					<input type="text" name="search" class="form-control" placeholder="Search by email or phone number">
					<div class="input-group-btn">
						<button class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
					</div>
				</div>
			</div>
		</form>
		<br><br>
	</div>
</div>

	<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
