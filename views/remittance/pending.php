<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 1/25/2017
 * Time: 8:49 AM
 */

use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use Adapter\ParcelAdapter;
use yii\helpers\Url;
?>

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" id="submit_btn">Pay Selected</button>';
?>


<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-right clearfix">

                <form class="table-search-form form-inline clearfix">
                    <div class="pull-left">
                        <label for="searchInput">Registration Number</label><br>
                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="registration_number" value="<?= isset($registration_number)?$registration_number:'' ?>"
                                   placeholder="Search by Customer registration number" class="search-box form-control">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if(count($payments)) : ?>
            <div class="table-responsive">
                <form method="post" id="form">
                    <table id="table" class="table table-hover dataTable">
                        <thead>
                        <tr>
                            <th style="width: 20px" class="datatable-nosort">
                                <div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label
                                            for="chbx_w_all"> </label></div>
                            </th>
                            <th style="width: 20px">S/N</th>
                            <th>Company Name</th>
                            <th>Registration No.</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(isset($payments) && is_array($payments)){
                            $i = $offset;
                            foreach($payments as $payment){
                                ?>
                                <tr>
                                    <td>
                                        <div class="checkbox-nice">
                                            <input id="chbx_w_<?= ++$i; ?>" class="checkable" name="companies[<?= $i ?>]"
                                                   value="<?= strtoupper($payment['id']); ?>"
                                                   data-company-id="<?= strtoupper($payment['id']); ?>"
                                                   data-amount_due="<?= Calypso::getValue($payment, 'amount') ?>"
                                                   type="checkbox"><label for="chbx_w_<?= $i; ?>"> </label>
                                        </div>
                                    </td>
                                    <td><?= $i; ?></td>
                                    <td><?= strtoupper(Calypso::getValue($payment, 'name')); ?></td>
                                    <td><?= strtoupper(Calypso::getValue($payment, 'reg_no')); ?></td>

                                    <td><?= strtoupper(Calypso::getValue($payment, 'email')); ?></td>
                                    <td><?= strtoupper(Calypso::getValue($payment, 'phone_number')); ?></td>

                                    <td><span class="currency naira"></span><?= strtoupper($payment['amount']); ?></td>
                                    <td>
                                        <a href="<?= Url::toRoute(['payview?company_id='.$payment['id']]) ?>"
                                           class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View Details</a>

                                    </td>
                                </tr>
                                <?php
                            }}
                        ?>

                        </tbody>
                    </table>
                </form>

            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
        <?php else:  ?>
            No record found
        <?php endif;  ?>
    </div>
</div>


<div class="modal fade" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="" class="validate-form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Submit Teller Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <label for="">Bank</label>
                            <select class="form-control validate required" name="bank_id" id="bank_id">
                                <?php
                                if (isset($banks) && is_array($banks['data'])) {
                                    foreach ($banks['data'] as $item) {
                                        ?>
                                        <option
                                            value="<?= $item['id'] ?>"><?= strtoupper($item['name']); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-6 form-group">
                            <label for="">Account no</label>
                            <input type="text" class="form-control validate required non-zero-integer"
                                   name="account_no">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <label for="">Teller no</label>
                            <input type="text" class="form-control validate required non-zero-integer" name="teller_no">
                        </div>
                        <div class="col-xs-6 form-group">
                            <label for="">Amount paid</label>

                            <div class="input-group">
                                <span class="input-group-addon currency naira"></span>
                                <input id="amount_paid" type="text" class="form-control validate required non-zero-number"
                                       name="amount_paid">
                            </div>
                        </div>
                    </div>
                    <div class="form-group hidden">
                        <label>Teller Snapshot (optional)</label>
                        <input type="file" class="form-control">
                    </div>

                    <hr/>
                    <table class="table table-bordered table-condensed" id="teller-modal-table">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Waybill No.</th>
                            <th>Sender name</th>
                        </tr>
                        </thead>
                        <tbody></tbody>

                        <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right;">Add Waybill</td>
                            <td>
                                <div class=" form-group">

                                    <div class="input-group input-group-sm input-group-search">
                                        <input id="addWaybillNumber" type="text" name="waybill_number" placeholder="Search by Waybill or Reference No."
                                               class="search-box form-control">

                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="btnAddWaybill">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="submit_teller">
                    <input type="hidden" id="waybill_numbers" name="waybill_numbers">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitTeller">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


<?php $this->registerJsFile('@web/js/remittance.js?v=1.0.2', ['depends' => [\app\assets\AppAsset::className()]]) ?>