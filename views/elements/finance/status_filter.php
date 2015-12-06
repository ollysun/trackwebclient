<option value="">Select Status</option>
<?php use Adapter\Globals\ServiceConstant;

foreach ($statuses as $status): ?>
    <option <?= $selectedStatus == $status ? 'selected' : '' ?>
        value="<?= $status ?>"><?= ServiceConstant::getStatus($status); ?></option>
<?php endforeach; ?>