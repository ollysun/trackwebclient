<?php
use \Adapter\Util;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;

//$user_type = Calypso::getInstance()->session('user_session')['branch']['branch_type'];
if ($status != ServiceConstant::CANCELLED && Calypso::getCurrentBranchType() == ServiceConstant::BRANCH_TYPE_HQ):
    ?>

    <button title="Cancel this shipment" type="button" data-waybill_number='<?= $waybill_number ?>'
            class="btn btn-xs btn-danger cancel-shipment"><i
            class="fa fa-times"></i></button>

    <?php $this->registerJsFile('@web/js/cancel_shipment.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>

<?php endif;
?>
