<?php
use yii\helpers\Html;
?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>

<form>
    <div class="clearfix">

        <div class="pull-left form-group form-group-sm">
            <label for="">From:</label><br>
            <div class="input-group input-group-date-range">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input name="from" class="form-control date-range" value="<?=  date('Y/m/d', strtotime($start_time));?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>
        </div>

        <div class="pull-left form-group form-group-sm">
            <label for="">To:</label><br>
            <div class="input-group input-group-date-range">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input name="to" id="" class="form-control date-range"  value="<?=  date('Y/m/d', strtotime($end_time));?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>
        </div>
        <div class="pull-left form-group form-group-sm">
            <label for="">Service Name</label><br>
            <input class="form-control" name="service" value="<?=$service?>">
        </div>
        <div class="pull-left form-group form-group-sm">
            <label for="">Action Name</label><br>
            <input class="form-control" name="action" value="<?=$action?>">
        </div>
        <div class="pull-left form-group form-group-sm">
            <label for="">Action Performed by</label><br>
            <input class="form-control" name="username" value="<?=$username?>">
        </div>

        <div class="pull-left form-group form-group-sm">
            <label for="">Records</label><br>
            <select name="page_width" id="page_width" class="form-control ">
                <?php
                for($i = 50; $i <= 500; $i+=50){
                    ?>
                    <option <?= $page_width==$i?'selected':'' ?> value="<?= $i ?>"><?= $i ?></option>
                    <?php
                }
                ?>
            </select>
        </div>

        <div class="pull-left">
            <label>&nbsp;</label><br>
            <button class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
<?php $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>