<option value="">Select Company</option>
<?php use Adapter\Util\Calypso;

foreach ($companies as $company): ?>
    <option <?= $selectedCompany == Calypso::getValue($company, 'id') ? 'selected' : '' ?>
        value="<?= Calypso::getValue($company, 'id') ?>"><?= strtoupper(Calypso::getValue($company, 'name', '')); ?></option>
<?php endforeach; ?>