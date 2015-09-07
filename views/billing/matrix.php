<?php
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Util\Calypso;

/* @var $this yii\web\View */
$this->title = 'Billing: Matrix';
$this->params['breadcrumbs'] = array(
	array(
		'label' => 'Billing',
		'url' => ['billing/']
	),
	array('label'=> 'Matrix')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
	$this->params['content_header_button'] = '';
?>
<?php
$hub_data = [];
$hub_data_col_indexes = [];

?>
<?php echo Calypso::showFlashMessages(); ?>
<div class="main-box">
	<div class="main-box-header">
	</div>
	<div class="main-box-body">
		<table id="table" class="table table-matrix table-bordered table-condensed">
			<thead>
				<tr>
					<th></th>
					<?php
					//var_dump($hubs);
					//var_dump($matrixMap);
					$i = 0;
					foreach($hubs as $hub){
						$hub_data[$hub['id']] = $hub;
						$hub_data_col_indexes[$hub['id']] = $i++;
						?>
						<th><?= strtoupper($hub['name']); ?></th>
					<?php
					}
					?>
					<!--<th>A01</th>
					<th>A02</th>
					<th>A03</th>
					<th>A04</th>
					<th>A05</th>
					<th>A06</th>
					<th>A07</th>
					<th>A08</th>
					<th>A09</th>
					<th>A10</th>
					<th>A11</th>
					<th>A12</th>
					<th>A13</th>
					<th>A14</th>
					<th>A15</th>
					<th>A16</th>
					<th>A17</th>
					<th>A18</th>
					<th>A19</th>
					<th>A20</th>
					<th>A21</th>
					<th>A22</th>
					<th>A23</th>
					<th>A24</th>-->
				</tr>
			</thead>
			<tbody>
			<?php
			$i = 0;$diagonal = '';$can_focus = true;
			//$diagonal = 'matrix_diagonal';
			$y = 0;$d = [];$already_rendered = [];
			foreach($hubs as $hub){

				?>
				<tr>
					<td><?= strtoupper($hub['name']); ?></td>
					<?php
					for($x = 0; $x < count($hubs); $x++) {
						$diagonal = $hub_data_col_indexes[$hub['id']] == $x ? 'matrix_diagonal':'';
//						$can_focus = $hub_data_col_indexes[$hub['id']] != $x;
						if(isset($matrixMap[$hub['id'].'_'.$hubs[$x]['id']])){
							$d = $matrixMap[$hub['id'].'_'.$hubs[$x]['id']];
							$already_rendered[$hub['id'].'_'.$hubs[$x]['id']] = true;
						}
						if(isset($matrixMap[$hubs[$x]['id'].'_'.$hub['id']])){
							$d = $matrixMap[$hubs[$x]['id'].'_'.$hub['id']];
							$already_rendered[$hubs[$x]['id'].'_'.$hub['id']] = true;
						}
							?>
							<td data-payload='<?= json_encode($d); ?>' data-from="<?= $hub['id'] ?>"
								data-to="<?= $hubs[$x]['id']; ?>"
								class="<?= $diagonal; ?><?= $can_focus ? ' matrix_cell' : ''; ?>"><?= $can_focus ? (sizeof($d) > 0 ? $d['zone']['code'] : 'N/S') : ''; ?></td>
							<?php
						
					}
					?>
					<!--<td class="<?/*= $diagonal; */?>"></td>
					<td class="matrix_cell">NW</td>-->

				</tr>
				<?php
				$y++;
			}
			?>

			</tbody>
		</table>
		<br>
	</div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	  	<form id="update_zone_mapping_form" class="">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Edit Matrix Entry</h4>
	      </div>
	      <div class="modal-body row">
				<div class="form-group col-xs-3">
					<label for="">From</label>
					<input id="from_text" class="form-control" readonly="readonly">
					<input id="from" name="from_branch_id" type="hidden" class="form-control">
				</div>
				<div class="form-group col-xs-3">
					<label for="">To</label>
					<input id="to_text" class="form-control" readonly="readonly">
					<input id="to" name="to_branch_id" type="hidden" class="form-control">
					<input id="zone_mapping_id" name="zone_matrix_id" type="hidden" class="form-control">
				</div>
				<div class="form-group col-xs-6">
					<label for="">Billing Zone</label>
					<select id="zone_id" name="zone_id" class="form-control">

						<?php
						if(!is_null($zones_list) && is_array($zones_list)) {
							foreach($zones_list as $item){
								if($item['status'] != 1){continue;}
							?>
								<option value="<?= $item['id']; ?>"><?= strtoupper($item['name'].' ('.$item['code'].')'); ?></option>
							<?php
							}
						}
						?>
					</select>
				</div>
	      </div>
	      <div class="modal-footer">
			  <button id="remove_mapping" type="button" class="btn btn-danger pull-left">Remove Mapping</button>
			  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button id="update_mapping" type="button" class="btn btn-primary">Save changes</button>
	      </div>
	    </div>
	  	</form>
  </div>
</div>


<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedColumns.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php //$this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php //$this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php //$this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/billing_matrix.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
