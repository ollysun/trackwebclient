<?php
use Adapter\Util\Calypso;
?>
<label for="">Billing Plan name</label>
<select name="base_billing_plan_id" class="form-control validate required">
    <option value="">Select Company</option>
    <?php foreach ($billing_plan_names as $billing_plan_name): ?>
        <option
            value="<?= Calypso::getValue($billing_plan_name, 'id'); ?>"><?= Calypso::getValue($billing_plan_name, 'name'); ?>
        </option>
    <?php endforeach; ?>
</select>
