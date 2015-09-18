<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use yii\widgets\LinkPager;

$signed_in = Calypso::getInstance()->session('user_session');

//dummy parcels array
$parcels = [
	array(
		'id' => 1,
		'waybill_number' => '#000011',
		'status' => 1,
		'sender_id' => 2,
		'receiver_id' => 1,
		'created_date' => '2015/09/18 09:22:04',
		'sender' => array(
			'id' => 2,
			'firstname' => 'jide',
			'lastname' => 'banjoko'
		),
		'receiver' => array(
			'id' => 2,
			'firstname' => 'jide',
			'lastname' => 'banjoko'
		)
	),
	array(
		'id' => 2,
		'waybill_number' => '#000011',
		'status' => 1,
		'sender_id' => 2,
		'receiver_id' => 1,
		'created_date' => '2015/09/18 09:22:04',
		'sender' => array(
			'id' => 2,
			'firstname' => 'jide',
			'lastname' => 'banjoko'
		),
		'receiver' => array(
			'id' => 2,
			'firstname' => 'jide',
			'lastname' => 'banjoko'
		)
	),
];

$tracking_no = '#12342039293';


/* @var $this yii\web\View */
$this->title = 'Tracking no: <span class="text-muted">' . $tracking_no . '</span>';
$this->params['breadcrumbs'] = array(
	array(
		'label'=>'Parcel History',
		'url'=> ['site/tracksearch']
	),
	array('label'=> $tracking_no)
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
		<?php if (!$signed_in) { ?>
			<br>
			<h2>There were multiple shipments found for the tracking number you entered. Please select one to continue</h2>
			<br>
		<?php } else { ?>
		<div class="pull-right">
			<form action="" class="table-search-form form-inline clearfix">
              <div class="pull-left">
                  <label for="searchInput">Search:</label><br>
                  <div class="input-group input-group-sm input-group-search">
                      <input id="searchInput" type="text" name="search" placeholder="Waybill / Tracking number" class="search-box form-control" value="<?= !empty($search) ? $search : '' ?>">
                      <div class="input-group-btn">
                          <button class="btn btn-default" type="submit">
                              <i class="fa fa-search"></i>
                          </button>
                      </div>
                  </div>
              </div>
          </form>
		</div>
		<?php } ?>
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
		<?php
			if (!empty($parcels)) {
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
						$i = empty($offset) ? 0 : $offset; //simply $1 = 0; or $i = $offset;
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
						<td><a href="<?= Url::to(['site/track?id='.$parcel['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
					</tr>
				<?php
						}
				?>
				</tbody>
			</table>
			<!-- Uncomment below for pagination  -->
			<?php //echo $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
		<?php
			}
		?>
		</div>
	</div>
</div>
