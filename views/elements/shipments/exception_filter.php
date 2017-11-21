<?php
use yii\helpers\Html;
?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>

    <form>
        <div class="clearfix">


            <div class="pull-left form-group form-group-sm">
                <label for="">Branch:</label><br>

                <div class="input-group">
                    <select id="previous_branch" name="defaulter_branch_id" class="form-control">
                        <option value="">All Branch</option>
                        <?php foreach ($branches as $branch) { ?>
                            <option value="<?php echo $branch['id'];?>"><?php echo $branch['name']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="pull-left form-group form-group-sm">
                <label for="">From:</label><br>
                <div class="input-group input-group-date-range">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input name="start_date" id="" class="form-control date-range" value=""
                           data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                </div>
            </div>

            <div class="pull-left form-group form-group-sm">
                <label for="">To:</label><br>
                <div class="input-group input-group-date-range">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input name="end_date" id="" class="form-control date-range"  value=""
                           data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                </div>
            </div>

            <div class="pull-left">
                <label>&nbsp;</label><br>
                <button class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
<?php $this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>