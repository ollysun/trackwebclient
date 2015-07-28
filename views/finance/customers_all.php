<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


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
            <table id="table" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th rowspan="2" style="width: 20px">S/N</th>
                    <th rowspan="2">Waybill No.</th>
                    <th rowspan="2">Amt. Invoiced (<span class="currency naira"></span>)</th>
                    <th rowspan="1" colspan="3">Amount Collected (<span class="currency naira"></span>)</th>
                    <th rowspan="2">EC</th>
                    <th rowspan="2">EC Officer</th>
                    <th rowspan="2">Status</th>
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    <th rowspan="1">Cash</th>
                    <th rowspan="1">POS</th>
                    <th rowspan="1">POS ID</th>
                </tr>
                </thead>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td>
                		<a href="#" class="btn btn-xs btn-default"><i class="fa fa-envelope">&nbsp;</i> Escalate</a>
                		<a href="#" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a>
                	</td>

                </tbody>
            </table>
		</div>
	</div>
</div>
