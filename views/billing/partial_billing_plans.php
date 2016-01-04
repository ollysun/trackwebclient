<label for="">Billing lan name</label>
<select name="billing Plan" class="form-control validate required">
    <option value="">Select Company</option>
    <?php foreach ($billing_plans as $billing_plan): ?>
        <option
            value="<?= Calypso::getValue($company, 'id'); ?>"><?= $billing_plan ?>
        </option>
    <?php endforeach; ?>
</select>
