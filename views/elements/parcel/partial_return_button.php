<?php use Adapter\Globals\ServiceConstant;

if (!in_array($parcel['status'], [ServiceConstant::DELIVERED, ServiceConstant::BEING_DELIVERED, ServiceConstant::CANCELLED]) && !$parcel['for_return'] && !in_array($parcel['entity_type'], [ServiceConstant::ENTITY_TYPE_BAG, ServiceConstant::ENTITY_TYPE_PARENT])) : ?>

    <button data-return="<?= $parcel['waybill_number'] ?>" title="Request shipment return" type="button"
            class="btn btn-xs btn-danger" name="parcel_id"><i class="fa fa-refresh"></i></button>

<?php endif; ?>



