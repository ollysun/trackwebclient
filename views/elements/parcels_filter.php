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

            <?php if(isset($cash_on_delivery)):?>
            <div class="pull-left form-group form-group-sm">
                <label for="">Cash on Delivery</label><br>
                <select name="cash_on_delivery" id="" class="form-control  filter-status">
                    <option value="">All</option>
                    <option
                        value="<?= ServiceConstant::TRUE; ?>" <?= $cash_on_delivery == ServiceConstant::TRUE ? 'selected' : '' ?>>
                        Yes
                    </option>
                    <option
                        value="<?= ServiceConstant::FALSE; ?>" <?= ($cash_on_delivery == ServiceConstant::FALSE && strlen($cash_on_delivery)) ? 'selected' : '' ?>>
                        No
                    </option>
                </select>
            </div>
            <?php endif;?>

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
                <button id="records_filter" class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
            </div>

            <div class="form-group form-group-sm form-inline">
                <br/>
                <label for="page_width">Records</label>
                <select name="page_width" id="page_width_2" class="form-control ">
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
$this->registerJsFile('@web/js/record_filter.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>