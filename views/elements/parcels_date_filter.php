<?php
use yii\helpers\Html;
?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>
<?php
use Adapter\Globals\ServiceConstant;
?>
    <form>
        <div class="clearfix">
            <div class="pull-left form-group form-group-sm">
                <label for="">From:</label><br>
                <div class="input-group input-group-date-range">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input name="from" id="" class="form-control date-range" value="<?= date('Y/m/d', strtotime($from_date)); ?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                </div>
            </div>

            <div class="pull-left form-group form-group-sm">
                <label for="">To:</label><br>
                <div class="input-group input-group-date-range">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input name="to" id="" class="form-control date-range"  value="<?=  date('Y/m/d', strtotime($to_date));?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                </div>
            </div>

            <div class="pull-left">
                <label>&nbsp;</label><br>
                <button id="records_filter" class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
            </div>

            <div class="form-group form-group-sm form-inline pull-right">
                <br/>

                <label for="page_width">Records</label>
                <select name="page_width" id="page_width" class="form-control ">
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
        </div>
    </form>
<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<?php $this->registerJsFile('@web/js/record_filter.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

