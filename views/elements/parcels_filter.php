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
                <input name="from" id="" class="form-control date-range" value="<?= $from_date != '-1'? date('Y/m/d', strtotime($from_date)):'';?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>

            <div class="pull-left form-group form-group-sm">
                <label for="">To:</label><br>
                <input name="to" id="" class="form-control date-range"  value="<?=  date('Y/m/d', strtotime($to_date));?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
            </div>
            <div class="pull-left form-group form-group-sm">
                <label for="">Filter status</label><br>
                <select name="date_filter" id="" class="form-control  filter-status">
                    <option value="-1">NOT APPLICABLE</option>
                    <?php
                    $statuses = ServiceConstant::getStatusRef();
                    for($i=0;$i < count($statuses);$i++){
                        ?>
                        <option value="<?= $statuses[$i] ?>"><?= strtoupper(ServiceConstant::getStatus($statuses[$i])); ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="pull-left form-group form-group-sm">
                <label for="">Records</label><br>
                <select name="page_width" id="" class="form-control ">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                </select>
            </div>
            <div class="pull-left">
                <label>&nbsp;</label><br>
                <button class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>