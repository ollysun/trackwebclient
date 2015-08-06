<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use yii\data\Pagination;
use yii\widgets\LinkPager;


$this->title = 'Customers Reconcialitions: All';
$this->params['breadcrumbs'] = array(
	array(
	'url' => ['finance/'],
	'label' => 'Reconcialitions'
	),
	array(
	'url' => ['finance/customersall'],
	'label' => 'Customers'
	),
	array('label'=> 'All')
);

?>


<?php
	//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
	<div class="main-box-header clearfix">
  		<div class="pull-left">
			<?php //echo $this->render('../elements/finance/merchant_filter', ['merchant_type'=>'due']) ?>
		</div>
      <form class="table-search-form form-inline pull-right clearfix">
         <div class="pull-left form-group">
             <label for="searchInput">Search</label><br>
             <div class="input-group input-group-sm input-group-search">
                 <input id="searchInput" type="text" name="search" placeholder="Waybill no." class="search-box form-control">
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

            <?php if(count($parcels['parcels']) > 0) { ?>
            <table id="table" class="table table- table-striped table-hover">
                <thead>
                <tr>
                    <th rowspan="2" style="width: 20px">S/N</th>
                    <th rowspan="2">Waybill No.</th>
                    <th rowspan="2">Amt. Invoiced (<span class="currency naira"></span>)</th>
                    <th rowspan="1" colspan="3">Amount Collected (<span class="currency naira"></span>)</th>
                    <th rowspan="2">EC</th>
                    <th rowspan="2">EC Officer</th>
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    <th rowspan="1">Cash</th>
                    <th rowspan="1">POS</th>
                    <th rowspan="1">POS ID</th>
                </tr>
                </thead>
                <tbody>

                <?php
                if (isset($parcels)) {
                    $row = $offset;
                foreach ($parcels['parcels'] as $parcel) {
                ?>
                <tr>
                	<td><?= ++$row; ?></td>
                	<td><?= strtoupper($parcel['waybill_number']); ?></td>
                	<td><?= number_format($parcel['amount_due'],2,'.',','); ?></td>
                	<td><?= ($parcel['cash_amount']>0) ? number_format($parcel['cash_amount'],2,'.',','):'-' ; ?></td>
                	<td><?= ($parcel['pos_amount']>0) ? number_format($parcel['pos_amount'],2,'.',','):'-'; ?></td>
                    <td></td>
                    <td><?= ucwords($parcel['from_branch']['name']);?></td>
                    <td>N/A</td>
                	<td>
                		<a href="<?= Url::to(['site/viewwaybill?id=' . $parcel['id']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a>
                	</td>
                </tr>
                <?php } } ?>
                </tbody>
            </table>
                <div class="clearfix">
                    <div class="pull-left">Showing <?= "".($offset+1)." to ".$row." of ".$total_count;?>
                    </div>
                    <div class="pull-right"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
                </div>

            <?php } else {  ?>
                <div class="alert alert-info text-center" role="alert">
                    <p><strong>No matching record found</strong></p>
                </div>
            <?php }  ?>
		</div>
	</div>
</div>
