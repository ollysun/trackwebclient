<?php
/**
 * Created by PhpStorm.
 * User: Moses Olalere
 * Date: 11/4/2017
 * Time: 11:36 PM
 */
use yii\helpers\Html;
?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>
<?php
use Adapter\Globals\ServiceConstant;
if(!isset($filter)){$filter="-1";}
?>
        <div class="clearfix">
            <div class="pull-left form-group form-group-sm">
                <label for="">From:</label><br>
                <div class="input-group input-group-date-range">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input name="from_date" id="" class="form-control date-range" value="<?= date('Y/m/d', strtotime($from_date)); ?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                </div>
            </div>

            <div class="pull-left form-group form-group-sm">
                <label for="">To:</label><br>
                <div class="input-group input-group-date-range">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input name="to_date" id="" class="form-control date-range"  value="<?=  date('Y/m/d', strtotime($to_date));?>" data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                </div>
            </div>
        </div>
<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/record_filter.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>