<?php
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;

$branch_type = $this->context->userData['branch']['branch_type'];
if($parcel['edit_access'] || $branch_type == ServiceConstant::BRANCH_TYPE_HQ){
?>
<a href=<?= '../parcels/new?edit=1&id=' . Calypso::getValue($parcel, 'id') ?>>
<button class="btn btn-default btn-xs">Edit</button>
</a>

<?php } ?>