<?php
use Adapter\Globals\ServiceConstant;
?>

    <form id="records_filter_form">
        <link href="/css/libs/datepicker.css" rel="stylesheet">

        <div class="clearfix">

            <br/>

            <div class="pull-left form-group form-group-sm">
                <label for="">Creation Date</label><br>
                <input name="start_created_date" class="form-control date-range" data-provide="datepicker"
                       data-date-format="yyyy/mm/dd" data-date-end-date="0d"
                       value="<?= $filters['start_created_date']; ?>">
            </div>
            <div class="pull-left form-group form-group-sm" style="margin-right: 10px;">
                <label for=""></label><br>
                <input name="end_created_date" class="form-control date-range" data-provide="datepicker"
                       data-date-format="yyyy/mm/dd" data-date-end-date="0d"
                       value="<?= $filters['end_created_date']; ?>">
            </div> &nbsp;&nbsp;&nbsp;

            <div class="pull-left form-group form-group-sm"  style="margin-right: 10px;">
                <label for="">For Return</label><br>
                <select name="for_return" id="" class="form-control  filter-status">
                    <option value="">All</option>
                    <option
                        value="<?= ServiceConstant::TRUE; ?>" <?= $filters['for_return'] == ServiceConstant::TRUE ? 'selected' : '' ?>>
                        Yes
                    </option>
                    <option
                        value="<?= ServiceConstant::FALSE; ?>" <?= ($filters['for_return'] == ServiceConstant::FALSE && strlen($filters['for_return'])) ? 'selected' : '' ?>>
                        No
                    </option>
                </select>
            </div>

            <div class="pull-left form-group form-group-sm"  style="margin-right: 10px;">
                <label for="">Shipping Type</label><br>
                <select name="shipping_type" id="" class="form-control  filter-status">
                    <option value="">All</option>
                    <?php foreach ($shipping_types as $shipping_type) { ?>
                        <option
                            value="<?= $shipping_type; ?>" <?= $filters['shipping_type'] == $shipping_type ? 'selected' : '' ?>><?= ServiceConstant::getShippingType($shipping_type); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="pull-left form-group form-group-sm"  style="margin-right: 10px;">
                <label for="">dispatcher</label><br>
                <input name="dispatcher" class="form-control"
                       value="<?= $filters['dispatcher']; ?>">
            </div>




            <div class="pull-left form-group form-group-sm"  style="margin-right: 10px;">
                <label for="page_width">Records</label><br/>
                <div class="input-group input-group-date-range">
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
            </div>

            <div class="pull-left">
                <label>&nbsp;</label><br>
                <button class="btn btn-default btn-sm" id="apply" type="submit"><i class="fa fa-filter"></i>
                    APPLY
                </button>
            </div>

        </div>

    </form>

<?php $this->registerJsFile('@web/js/record_filter.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>