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
            <div class="pull-left form-group form-group-sm<?= (!empty($hideStatusFilter) && $hideStatusFilter)? ' hidden' : '' ?>">
                <label for="">Filter status</label><br>
                <select name="status" id="" class="form-control filter-status">
                    <option value="-1">Select Status</option>
                    <option value="to_branch_id=<?= $branchId?>" <?= $filter == "to_branch_id=$branchId" ? 'selected' : ''?>>Incoming</option>
                    <option value="from_branch_id=<?= $branchId?>" <?= $filter == "from_branch_id=$branchId" ? 'selected' : ''?>>Outgoing</option>
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