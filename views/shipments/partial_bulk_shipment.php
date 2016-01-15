<?php

use Adapter\Util\Calypso;
use yii\helpers\Json;

?>
<div class="row">
    <div class="col-xs-12 alert" id="message_area">
    </div>
    <div class="col-xs-6">
        <div class="form-group">
            <label for="company_select">Company</label>
            <select id="company_select" class="form-control">
                <option value="">Select a Company</option>
                <?php foreach ($companies as $company): ?>
                    <option
                        data-billing_plans='<?= Json::encode(Calypso::getValue($billing_plans, Calypso::getValue($company, 'id'), [])) ?>'
                        value="<?= Calypso::getValue($company, 'id') ?>">
                        <?= strtoupper(Calypso::getValue($company, 'name')) ?>
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
    <div class="col-xs-6">
        <div class="form-group">
            <label for="payment_method_select">Payment Method</label>
            <select id="payment_method_select" class="form-control">
                <?php foreach ($payment_methods as $payment_method): ?>
                    <option
                        value="<?= Calypso::getValue($payment_method, 'id') ?>"><?= strtoupper(Calypso::getValue($payment_method, 'name')) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
<br/><br/>
<div class="row">
    <div class="col-xs-4 col-xs-offset-4">
        <button id="bulk_upload_btn" class="btn btn-primary">Upload Bulk Shipment File</button>
        <input name="dataFile" type="file" id="bulk_upload_file_btn" class="hide"/>
        <input name="company_id" id="company_id_input" type="hidden"/>
        <input name="billing_plan_id" id="billing_plan_id_input" type="hidden"/>
        <input name="payment_type" id="payment_type_input" type="hidden"/>
        <br/>
    </div>
    <br/><br/>

    <div class="col-xs-12">
        <p class="text-center" id="uploaded_file_name"></p>
    </div>
</div>

<?= $this->registerJsFile('@web/js/libs/jquery.form.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/bulk_shipment.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
