<?php
use yii\helpers\Url;
?>

<div class="form-group">
	<label>Branch type</label><br>
	<select id="branch_type_filter" class="form-control input-sm">
		<option value="hub" <?php if(isset($branch_type) && $branch_type === 'hub') { echo 'selected="selected"';} ?>>Hub</option>
		<option value="ec" <?php if(isset($branch_type) && $branch_type === 'ec') { echo 'selected="selected"';} ?>>Express Centre</option>
	</select>
</div>

<?php
	$str = "
		var select = $('#branch_type_filter');
			var Url = {
				ec: '".Url::to(['site/manageecs'])."',
				hub: '".Url::to(['site/managebranches'])."'
			};
			select.on('change', function() {
				var val = $(this).val();
				window.location = Url[val];
			});";

	$this->registerJs($str);
	unset($str);
	unset($branch_type);
?>