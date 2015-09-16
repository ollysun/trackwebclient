<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use yii\widgets\LinkPager;

if (empty($user)) {
	$user_not_found = true;
	$user_fullname = 'Customer not found';
}
else {
	$user_not_found = false;
	$user_fullname = ucwords($user['firstname'].' '.$user['lastname']);
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
		<?php
			if($user_not_found) {
		?>
			<div class="pull-left">
				<label>&nbsp;</label>
				<div class="form-control-static text-danger">Invalid customer</div>
			</div>
		<?php
			}
		?>
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
			<!-- <div class="alert alert-info">
				<p>We could not find a customer with phone number <strong><?= $search ?></strong>. Please enter another phone number and search again.</p>
			</div> -->
			<?php
				}
				else {
					//$parcels = $parcel_data['parcel'];
			?>
				<?php
					if (empty($parcels)) {
					 echo '<p class="text-center">The customer has no shipments to display.</p>';
					}
					else {
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
								$i = $offset;
								foreach($parcels as $parcel) {

						?>
							<tr data-shipment-id="<?= $parcel['id'] ?>">
								<?php $i++; ?>
								<td><?= $i ?></td>
								<td><?= $parcel['waybill_number'] ?></td>
								<td data-sender-id="<?= $parcel['sender_id'] ?>"><?= ucwords($parcel['sender']['firstname'].' '.$parcel['sender']['lastname']); ?></td>
								<td data-receiver-id="<?= $parcel['receiver_id'] ?>"><?= ucwords($parcel['receiver']['firstname'].' '.$parcel['receiver']['lastname']); ?></td>
								<td><?= date('j M Y h:ma',strtotime($parcel['created_date'])); ?></td>
								<td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
								<td><a href="<?= Url::to(['shipments/view?id='.$parcel['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
							</tr>
						<?php
								}
						?>
						</tbody>
					</table>
					<?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
				<?php
					}
				?>
			<?php
				}
			?>
		</div>
	</div>
</div>
