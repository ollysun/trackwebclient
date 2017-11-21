<?php
use yii\helpers\Url;
?>

<div class="form-group">
	<label>Filter Status</label><br>
	<select id="merchant_type_filter" class="form-control input-sm">
		<option value="pending" <?php if(isset($merchant_type) && $merchant_type === 'pending') { echo 'selected="selected"';} ?>>Pending</option>
		<option value="due" <?php if(isset($merchant_type) && $merchant_type === 'due') { echo 'selected="selected"';} ?>>Due</option>
		<option value="paid" <?php if(isset($merchant_type) && $merchant_type === 'paid') { echo 'selected="selected"';} ?>>Paid</option>
	</select>
</div>

<?php
	$str = "
		var select = $('#merchant_type_filter');
			var Url = {
				pending: '".Url::toRoute(['/finance/merchantspending'])."',
				due: '".Url::toRoute(['/finance/merchantsdue'])."',
				paid: '".Url::toRoute(['/finance/merchantspaid'])."'
			};
			select.on('change', function() {
				var val = $(this).val();
				window.location = Url[val];
			});";

	$this->registerJs($str);
	unset($str);
	unset($merchant_type);
?>