<?php include('includes/layouts/head_signed_in.php'); ?>
<?php include('includes/layouts/sidebar_ec.php'); ?>

<!-- this page specific styles -->
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_PATH; ?>/assets/css/libs/dataTables.fixedHeader.css">
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_PATH; ?>/assets/css/libs/dataTables.tableTools.css">

<div id="content-wrapper">
	<?php
		$content_header_title = 'Customer History';
		$breadcrumb_arr = array(
			array(
				'label' => 'Home',
				'link' => ROOT_PATH.'/dashboard.php'
			),
			array(
				'label' => 'Customer History',
				'link' => ROOT_PATH.'/customer_history_search.php'
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
							<th>S/N</th>
							<th>Customer Name</th>
							<th>Email address</th>
							<th>Phone no</th>
							<th>Address</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>1</td>
							<td>Aderopo Olusegun</td>
							<td>aderopo.olusegun@gmail.com</td>
							<td>08050001234</td>
							<td>51B, Billings Way, Oregun, Ikeja, NG</td>
							<td><a href="customer_history.php" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
						</tr>
						<tr>
							<td>1</td>
							<td>Aderopo Olusegun</td>
							<td>aderopo.olusegun@gmail.com</td>
							<td>08050001234</td>
							<td>51B, Billings Way, Oregun, Ikeja, NG</td>
							<td><a href="customer_history.php" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
						</tr>
						<tr>
							<td>1</td>
							<td>Aderopo Olusegun</td>
							<td>aderopo.olusegun@gmail.com</td>
							<td>08050001234</td>
							<td>51B, Billings Way, Oregun, Ikeja, NG</td>
							<td><a href="customer_history.php" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
						</tr>
						<tr>
							<td>1</td>
							<td>Aderopo Olusegun</td>
							<td>aderopo.olusegun@gmail.com</td>
							<td>08050001234</td>
							<td>51B, Billings Way, Oregun, Ikeja, NG</td>
							<td><a href="customer_history.php" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
						</tr>
						<tr>
							<td>1</td>
							<td>Aderopo Olusegun</td>
							<td>aderopo.olusegun@gmail.com</td>
							<td>08050001234</td>
							<td>51B, Billings Way, Oregun, Ikeja, NG</td>
							<td><a href="customer_history.php" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
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