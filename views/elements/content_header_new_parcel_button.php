<?php
use Adapter\Globals\ServiceConstant;
use yii\helpers\Url;

?>
<a href="<?= Url::toRoute(['/parcels/new']) ?>" class="btn btn-primary">
    <i class="fa fa-plus"></i> Create a New Shipment
</a>

<?php if (in_array($this->context->userData['role_id'], [ServiceConstant::USER_TYPE_OFFICER,
    ServiceConstant::USER_TYPE_COMPANY_ADMIN, ServiceConstant::USER_TYPE_COMPANY_OFFICER])): ?>
    <button id="create_bulk_shipment_btn" class="btn btn-primary">
        <i class="fa fa-plus"></i> Create Bulk Shipment</button>
<?php endif; ?>
