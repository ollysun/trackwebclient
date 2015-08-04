<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

if (empty($user_data)) {
	$user_not_found = true;
	$user_fullname = 'Customer not found';
}
else {
	$user_not_found = false;
	$user_fullname = ucwords($user_data['firstname'].' '.$user_data['lastname']);
}

/* @var $this yii\web\View */
$this->title = 'Customer History: <span class="text-muted">'.$user_fullname.'</span>';
$this->params['breadcrumbs'] = array(
	array(
		'label'=>'Customer History',
		'url'=> ['shipments/customerhistory']
	),
	array('label'=> $user_fullname)
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
			<form action="" class="table-search-form form-inline clearfix">
              <div class="pull-left">
                  <label for="searchInput">Search customer:</label><br>
                  <div class="input-group input-group-sm input-group-search">
                      <input id="searchInput" type="text" name="search" placeholder="Phone number" class="search-box form-control" value="<?= $search ? $search : '' ?>">
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
			<?php
				if($user_not_found) {
			?>
			<div class="alert alert-info">
				<p>We could not find a customer with phone number <strong><?= $search ?></strong>. Please enter another phone number and search again.</p>
			</div>
			<?php
				}
				else {
					$parcels = $user_data['parcel'];
			?>
				<table id="table" class="table table-hover">
					<thead>
						<tr>
							<th>S/N</th>
							<th>Waybill No.</th>
							<th>Shipper</th>
							<th>Receiver</th>
							<th>Created at</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if (empty($parcels)) {
							 echo '<tr class="text-center"><td colspan="7">The customer has no shipments to display.</td></tr>';
							}
							else {
								$i = $offset + 1;
								foreach($parcels as $parcel) {

						?>
							<tr data-shipment-id="<?= $parcel['id'] ?>">
								<td><?= $i ?></td>
								<td><?= $parcel['waybill_number'] ?></td>
								<td data-sender-id="<?= $parcel['sender_id'] ?>"><?= $parcel['sender_id'] ?></td>
								<td data-receiver-id="<?= $parcel['receiver_id'] ?>"><?= $parcel['receiver_id'] ?></td>
								<td><?= date('j M Y @ h:m',strtotime($parcel['created_date'])); ?></td>
								<td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
								<td><a href="<?= Url::to(['site/viewwaybill?id='.$parcel['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
							</tr>
						<?php
								$i++;
								}
							}
						?>
					</tbody>
				</table>
				<?php //var_dump($parcels); ?>
			<?php
				}
			?>
		</div>
	</div>
</div>


<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
