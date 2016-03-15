<?php
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;


if($parcel['is_related_to_this_branch'] || $this->context->userData['branch']['branch_type'] == ServiceConstant::BRANCH_TYPE_HQ){
?>
<a href=<?= '../parcels/new?edit=1&id=' . Calypso::getValue($parcel, 'id') ?>>
<button class="btn btn-default btn-xs">Edit</button>
</a>

<?php } ?>