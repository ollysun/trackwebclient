<?php

use Adapter\Util\Calypso;
use yii\helpers\Json;

?>

<div class="row">
    <div class="col-xs-6">
        <div class="form-group">
            <label for="company_select">Company</label>
            <select id="company_select" class="form-control">
                <option>Select a Company</option>
                <?php foreach ($companies as $company): ?>
                    <option
                        data-billing_plans='<?= Json::encode(Calypso::getValue($billing_plans, Calypso::getValue($company, 'id'))) ?>'
                        value="<?= Calypso::getValue($company, 'id') ?>">
                        <?= ucwords(Calypso::getValue($company, 'name')) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="form-group">
            <label for="company_billing_plan_select">Company Billing Plan</label>
            <select id="company_billing_plan_select" class="form-control">
                <option>Select a Company</option>
            </select>
        </div>
    </div>
</div>
