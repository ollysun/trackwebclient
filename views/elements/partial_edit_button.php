<?php
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;
if(isset($parcel['edit_access'])){

//$branch_type = $this->context->userData['branch']['branch_type'];
if($parcel['edit_access'] || Calypso::getCurrentBranchType() == ServiceConstant::BRANCH_TYPE_HQ){
?>
<a href=<?= \yii\helpers\Url::to('/parcels/new?edit=1&id=' . Calypso::getValue($parcel, 'id')); ?> class="btn btn-default btn-xs">Edit</a>

<?php }} ?>