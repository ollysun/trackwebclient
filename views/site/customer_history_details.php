<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Customer History: <small>Aderopo Olusegun</small>';
$this->params['breadcrumbs'] = array(
	array(
		'label'=>'Customer History',
		'url'=> ['site/customerhistory']
	),
	array('label'=> 'Aderopo Olusegun')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	//$this->params['content_header_button'] = '';
?>


<div class="main-box">
	<div class="main-box-header table-search-form clearfix">
		<div class="pull-right">
			<form class="table-search-form form-inline clearfix">
              <div class="pull-left">
                  <label for="searchInput">Search customer:</label><br>
                  <div class="input-group input-group-sm input-group-search">
                      <input id="searchInput" type="text" name="search" placeholder="Email or phone number" class="search-box form-control">
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
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover">
				<thead>
					<tr>
						<th>S/N</th>
						<th>Waybill No.</th>
						<th>Shipper</th>
						<th>Receiver</th>
						<th>Created at</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
					<tr>
						<td>2</td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
					<tr>
						<td>3</td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
					<tr>
						<td>4</td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
					<tr>
						<td>5</td>
						<td>4F95310912352</td>
						<td>Aderopo Olusegun</td>
						<td>Aderopo Olusegun</td>
						<td>25 Jun 2015 @ 12:08</td>
						<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>


<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
