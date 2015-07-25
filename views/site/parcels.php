<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'All Shipments';
$this->params['breadcrumbs'][] = 'Shipments';
$show_next = false;
$show_prev = false;

if(count($parcels) >= $page_width ){
    $show_next = true;
}else{
    $show_next = false;
}


if($offset <= 0){
    $show_prev = false;
}elseif (($offset - $page_width) >= 0){
    $show_prev = true;
}

$link = "";
if($search){
	$fro = date('Y/m/d',strtotime($from_date));
	$to = date('Y/m/d',strtotime($to_date));
	$link = "&search=true&to=".urlencode($to)."&from=".urlencode($fro)."&page_width=".$page_width;
}
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
	<div class="main-box-header table-search-form ">
		<div class="clearfix">
			<div class="pull-left">
				<?= $this->render('../elements/parcels_filter',['from_date'=>$from_date,'to_date'=>$to_date]) ?>
			</div>
			<div class="pull-right clearfix">

                <form class="table-search-form form-inline clearfix">
                    <div class="pull-left">
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
					<div class="btn-group btn-group-sm">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Select an action <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="#">Separated link</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="main-box-body">
		<div class="table-responsive">
			<table id="table" class="table table-hover ">
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
                    $count = $offset + 1;
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
                <a href="<?= Url::to(['site/parcels?offset='.($offset - $page_width).$link]) ?>" class="btn btn-primary btn-sm">Prev</a>
                <?php endif;  ?>
                <?php if($show_next): ?>
                    <a href="<?= Url::to(['site/parcels?offset='.($offset + $page_width).$link]) ?>" class="btn btn-primary btn-sm">Next</a>
                <?php endif;  ?>
            </div>
		</div>
	</div>
</div>


<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>


<script type="text/javascript">
	/*$(document).ready(function() {
		var table = $('#table').dataTable({
			'info': false,
			'pageLength': 50,
			//'sDom': 'lTfr<"clearfix">tip',
			'sDom': 'Tfr<"clearfix">tip',
			'oTableTools': {
				"sRowSelect": "multi",
				"sSwfPath": "<?php //echo ROOT_PATH; ?>/assets/swf/copy_csv_xls_pdf.swf",
	            'aButtons': [
	                {
	                    'sExtends':    'collection',
	                    'sButtonText': '<i class="fa fa-cloud-download"></i>&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down"></i>',
	                    'aButtons':    [ 'csv', 'xls', 'pdf', 'copy', 'print' ]
	                }
	            ]
	        }
		});

	   var tt = new $.fn.dataTable.TableTools( table );
		$( tt.fnContainer() ).insertBefore('div.dataTables_wrapper');

		new $.fn.dataTable.FixedHeader( table );
	});*/
</script>