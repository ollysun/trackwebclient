<?php
use yii\helpers\Html;
?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>
<?php
use Adapter\Globals\ServiceConstant;
if(!isset($filter)){$filter="-1";}
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
            <div class="pull-left form-group form-group-sm<?= (!empty($hideStatusFilter) && $hideStatusFilter)? ' hidden' : '' ?>">
                <label for="">Filter status</label><br>
                <select name="date_filter" id="" class="form-control  filter-status">
                    <option value="-1">NOT APPLICABLE</option>
                    <?php
                    $statuses = ServiceConstant::getStatusRef();
                    for($i=0;$i < count($statuses);$i++){
                        if($statuses[$i]==4){continue;}
                        ?>
                        <option <?= $statuses[$i]==$filter?'selected':'' ?> value="<?= $statuses[$i] ?>"><?= strtoupper(ServiceConstant::getStatus($statuses[$i])); ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="pull-left form-group form-group-sm hidden">
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
<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>