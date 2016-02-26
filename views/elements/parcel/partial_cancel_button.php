<?php
use \Adapter\Util;
use Adapter\Globals\ServiceConstant;

if(!in_array($status,[ServiceConstant::CANCELLED])):
?>

<button title="Cancel this shipment"  type="button" data-waybill_number= '<?= $waybill_number ?>'
        class="btn btn-xs btn-danger cancel-shipment"><i
        class="fa fa-times"></i></button>

<?php $this->registerJsFile('@web/js/cancel_shipment.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>

<?php endif; ?>
