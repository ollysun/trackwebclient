<?php
use yii\helpers\Url;
?>

    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" id="unsort_btn">Unsort</button>
    <form id="unsort_form" action="<?= Url::to('/hubs/unsort') ?>" method="post">
        <input id="unsort_waybill_numbers" type="hidden" name="waybill_numbers"/>
    </form>

<?php $this->registerJsFile('@web/js/bootbox.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/unsort.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>