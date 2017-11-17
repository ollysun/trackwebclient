<form method="post" id="request-returns" action="<?= \yii\helpers\Url::to('/shipments/requestreturn') ?>">
    <input type="hidden" name="waybill_numbers" value>
    <input type="hidden" name="comment" value>
    <input type="hidden" name="extra_note" value>
    <input type="hidden" name="attempted_delivery" value>
    <input type="hidden" name="task" value="request_return">
</form>

<?php $this->registerJsFile('@web/js/bootbox.min.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/return.js?v=1.0.0.1', ['depends' => [\app\assets\AppAsset::className()]]) ?>

