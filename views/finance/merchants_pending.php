<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Merchant Reconcialitions: Pending';
$this->params['breadcrumbs'] = array(
	array(
	'url' => ['finance/'],
	'label' => 'Reconcialitions'
	),
	array(
	'url' => ['finance/merchantsdue'],
	'label' => 'Merchants'
	),
	array('label'=> 'Pending')
);

?>


<?php
	//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
	<div class="main-box-header clearfix">
  		<div class="pull-left">
			<?= $this->render('../elements/finance/merchant_filter', ['merchant_type'=>'pending']) ?>
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
            <table id="table" class="table table-hover">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Merchant </th>
                    <th>Account Number</th>
                    <th>Account Name</th>
                    <th>Bank</th>
                    <th>Cash to collect</th>
                    <th>Status</th>
                    <th>Action</th>
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
                	<td><a href="#" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>

                </tbody>
            </table>
		</div>
	</div>
</div>
