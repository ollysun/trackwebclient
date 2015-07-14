<?php
use Adapter\Globals\ServiceConstant;
?>
<div class="clearfix">
	<div class="pull-left">
		<label for="">From:</label><br>
		<input name="" id="" class="form-control date-range">
	</div>

	<div class="pull-left">
		<label for="">To:</label><br>
		<input name="" id="" class="form-control date-range">
	</div>
	<div class="pull-left">
		<label for="">Filter status</label><br>
		<select name="" id="" class="form-control  filter-status">
            <option value="-1">NOT APPLICABLE</option>
            <?php
            $statuses = ServiceConstant::getStatusRef();
            for($i=0;$i < count($statuses);$i++){
                ?>
                <option value="<?= $statuses[$i] ?>"><?= strtoupper(ServiceConstant::getStatus($statuses[$i])); ?></option>
            <?php
            }
            ?>
		</select>
	</div>
	<div class="pull-left">
		<label>&nbsp;</label><br>
		<button class="btn btn-default"><i class="fa fa-search"></i></button>
	</div>
</div>