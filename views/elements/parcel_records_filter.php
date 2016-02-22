
<form id="records_filter_form">
    <div class="form-group form-group-sm form-inline pull-left">
        <br/>
        <label for="page_width">Records</label>
        <?php $page = $page_width; ?>

        <select name="page_width" id="page_width_record" class="form-control ">
            <?php
            $page_width = isset($page_width) ? $page_width : 50;
            for ($i = 50; $i <= 500; $i += 50) {
                ?>
                <option <?= $page_width == $i ? 'selected' : '' ?>
                    value="<?= $i ?>"><?= $i ?></option>
                <?php
            }
            ?>
        </select>
    </div>

</form>

<?php $this->registerJsFile('@web/js/record_filter.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
