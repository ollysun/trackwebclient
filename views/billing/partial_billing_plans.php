<?php
use Adapter\Util\Calypso;
?>
<label for="">Billing Plan name</label>
<select name="billing Plan" class="form-control validate required">
    <option value="">Select Company</option>
    <?php foreach ($billing_plans as $billing_plan): ?>
        <option
            value="<?= Calypso::getValue($billing_plan, 'id'); ?>"><?= Calypso::getValue($billing_plan, 'name'); ?>
        </option>
    <?php endforeach; ?>
</select>
