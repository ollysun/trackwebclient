<?php
	use yii\helpers\Html;
?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>
<div class="clearfix">
	<div class="pull-left">
		<label for="">From:</label><br>
		<input name="" id="" class="form-control date-range" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
	</div>

	<div class="pull-left">
		<label for="">To:</label><br>
		<input name="" id="" class="form-control date-range"  data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
	</div>
	<div class="pull-left">
		<label for="">Filter status</label><br>
		<select name="" id="" class="form-control  filter-status"></select>
	</div>
	<div class="pull-left">
		<label>&nbsp;</label><br>
		<button class="btn btn-default"><i class="fa fa-search"></i></button>
	</div>
</div>

<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>