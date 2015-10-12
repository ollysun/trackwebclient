<?php
	use Adapter\Globals\ServiceConstant;
?>
<form class="clearfix dashboard-choose-branch" method="get">
	<div class="pull-left">
		<label for="branch_type">Branch type</label>
		<select name="" id="branch_type" class="form-control input-sm branch-type">
			<option>All</option>
			<?php if($branch_type == ServiceConstant::BRANCH_TYPE_HQ) : ?>
			<option>Hub</option>
			<?php else : ?>
			<option>EC</option>
			<?php endif; ?>
		</select>
	</div>
	<div class="pull-left">
		<label for="branch_name">Branch name</label>
		<select name="branch" id="branch_name" class="form-control input-sm branch-name"></select>
	</div>
	<div class="pull-left">
		<label>&nbsp;</label><br>
		<button type="submit" class="btn btn-default btn-sm">Submit</button>
	</div>
</form>