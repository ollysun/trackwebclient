<?php
use Adapter\Globals\ServiceConstant;
use \Adapter\Util;

if (!in_array($parcel['status'], [ServiceConstant::DELIVERED, ServiceConstant::BEING_DELIVERED, ServiceConstant::CANCELLED]) && !$parcel['for_return'] && !in_array($parcel['entity_type'], [ServiceConstant::ENTITY_TYPE_BAG, ServiceConstant::ENTITY_TYPE_PARENT])) : ?>

    <button data-return="<?= $parcel['waybill_number'] ?>"
            data-reasons='<?= \yii\helpers\Json::encode($reasons_list) ?>'
            data-attempted_delivery = '<?= isset($attempted_delivery)? 1 : 0;?>'
            title="Request shipment return" type="button"
            class="btn btn-xs btn-danger" name="parcel_id"><i class="fa fa-refresh"></i>
    </button>

<?php endif; ?>



