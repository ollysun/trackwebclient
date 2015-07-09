<?php include('includes/layouts/head_signed_in.php'); ?>
<?php include('includes/layouts/sidebar_ec.php'); ?>

<!-- this page specific styles -->
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_PATH; ?>/assets/css/libs/dataTables.fixedHeader.css">
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_PATH; ?>/assets/css/libs/dataTables.tableTools.css">

<div id="content-wrapper">
	<?php
		$content_header_title = 'Customer History: <small>Aderopo Olusegun</small>';
		$breadcrumb_arr = array(
			array(
				'label' => 'Home',
				'link' => ROOT_PATH.'/dashboard.php'
			),
			array(
				'label' => 'Customer History',
				'link' => ROOT_PATH.'/customer_history_search.php'
			),
			array(
				'label' => 'Aderopo Olusegun',
				'link' => ROOT_PATH.'/customer_history.php'
			)
		);
	?>
	<?php include('includes/partials/content_header.php') ?>

	<div class="main-box">
		<div class="main-box-header">

		</div>
		<div class="main-box-body">
			<div class="table-responsive">
				<table id="table" class="table table-hover">
					<thead>
						<tr>
							<th>[--]</th>
							<th>Waybill No.</th>
							<th>Shipper</th>
							<th>Receiver</th>
							<th>Created at</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><div class="checkbox-nice"><input id="chbx_w_0001" type="checkbox"><label for="chbx_w_0001"> </label></div></td>
							<td>4F95310912352</td>
							<td>Aderopo Olusegun</td>
							<td>Aderopo Olusegun</td>
							<td>25 Jun 2015 @ 12:08</td>
							<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
						</tr>
						<tr>
							<td><div class="checkbox-nice"><input id="chbx_w_0002" type="checkbox"><label for="chbx_w_0002"> </label></div></td>
							<td>4F95310912352</td>
							<td>Aderopo Olusegun</td>
							<td>Aderopo Olusegun</td>
							<td>25 Jun 2015 @ 12:08</td>
							<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
						</tr>
						<tr>
							<td><div class="checkbox-nice"><input id="chbx_w_0003" type="checkbox"><label for="chbx_w_0003"> </label></div></td>
							<td>4F95310912352</td>
							<td>Aderopo Olusegun</td>
							<td>Aderopo Olusegun</td>
							<td>25 Jun 2015 @ 12:08</td>
							<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
						</tr>
						<tr>
							<td><div class="checkbox-nice"><input id="chbx_w_0004" type="checkbox"><label for="chbx_w_0004"> </label></div></td>
							<td>4F95310912352</td>
							<td>Aderopo Olusegun</td>
							<td>Aderopo Olusegun</td>
							<td>25 Jun 2015 @ 12:08</td>
							<td><a href="#" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
						</tr>
						<tr>
							<td><div class="checkbox-nice"><input id="chbx_w_0005" type="checkbox"><label for="chbx_w_0005"> </label></div></td>
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

	<?php include_once('includes/layouts/footer.php'); ?>
</div> <!-- /#content-wrapper -->

<!-- this page specific scripts -->
<script src="<?php echo ROOT_PATH; ?>/assets/js/libs/jquery.dataTables.js"></script>
<script src="<?php echo ROOT_PATH; ?>/assets/js/libs/dataTables.fixedHeader.js"></script>
<script src="<?php echo ROOT_PATH; ?>/assets/js/libs/dataTables.tableTools.js"></script>
<script src="<?php echo ROOT_PATH; ?>/assets/js/libs/jquery.dataTables.bootstrap.js"></script>

<?php include('includes/layouts/foot_signed_in.php'); ?>