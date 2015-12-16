<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Invoices';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['finance/index'],
        'label' => 'Finance'
    ),
    array('label' => $this->title)
);

?>

<!-- Generate Credit Note Modal -->
<form>
    <div class="modal fade" id="generateCreditNote">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Generate Credit Note</h4>
                </div>
                <div class="modal-body">
                    <div id="loading" style="text-align: center;">
                        <i class="fa fa-spin fa-spinner fa-4x"></i>
                        <p>Loading invoice parcels...</p>
                    </div>
                    <table id="table" class="hide table table-hover dataTable">
                        <thead>
                        <tr>
                            <th style="width: 20px">No.</th>
                            <th>Waybill No.</th>
                            <th>Company Name</th>
                            <th>Amount</th>
                            <th>Discount</th>
                            <th>Net Amount</th>
                            <th>Deducted Amount</th>
                            <th>New Net Amount</th>
                        </tr>
                        </thead>
                        <tbody id="invoiceParcels">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a href="#">
                        <button type="button" class="btn btn-primary">Generate Credit Note</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- View Invoice Modal -->
<form>
    <div class="modal fade" id="viewInvoice">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">58904</h4>
                </div>
                <div class="modal-body">
                    <table id="table" class="table table-hover dataTable">
                        <thead>
                        <tr>
                            <th style="width: 20px">No.</th>
                            <th>Waybill No.</th>
                            <th>Company Name</th>
                            <th>Amount</th>
                            <th>Discount (%)</th>
                            <th>Net Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>7123456</td>
                            <td>Delivered</td>
                            <td>1000</td>
                            <td>
                                <input type="text" class="form-control" style="width:50px;" value="15">
                            </td>
                            <td>850</td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>1234567</td>
                            <td>Delivered</td>
                            <td>2000</td>
                            <td>
                                <input type="text" class="form-control" style="width:50px;" value="15">
                            </td>
                            <td>1700</td>
                        </tr>

                        </tbody>
                    </table>

                    <div class="clearfix">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Invoice address</label>
                                <textarea class="form-control" rows="2"></textarea>

                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox">
                                        Same as Invoice To
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Invoice To</label>
                                <textarea class="form-control" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Reference</label>
                                <textarea class="form-control" rows="3"></textarea>
                            </div>

                        </div>


                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Accont Number</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Stamp Duty</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Currency</label> <br>
                                <select class="form-control">
                                    <option>NGN</option>
                                </select>
                            </div>


                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <a href="#">
                        <button type="button" class="btn btn-primary">Print</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class=" clearfix">
            <div class="pull-left">
                <form>
                    <div class="clearfix">
                        <div class="pull-left form-group form-group-sm">
                            <label for="">From:</label><br>

                            <div class="input-group input-group-date-range">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input name="from" id="" class="form-control date-range" value="<?= $fromDate; ?>"
                                       data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                            </div>
                        </div>

                        <div class="pull-left form-group form-group-sm">
                            <label for="">To:</label><br>

                            <div class="input-group input-group-date-range">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input name="to" id="" class="form-control date-range" value="<?= $toDate; ?>"
                                       data-provide="datepicker" data-date-format="yyyy/mm/dd" data-date-end-date="0d">
                            </div>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label>Company</label> <br>
                            <select name="company" class="form-control" style="width: 150px">
                                <?= $this->render('../elements/finance/company_filter', ['companies' => $companies, 'selectedCompany' => $selectedCompany]) ?>
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
            </div>
            <div class="pull-right clearfix">
                <label>&nbsp;</label><br>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if ($invoices): ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">No.</th>
                        <th>Invoice Doc. No.</th>
                        <th>Company Name</th>
                        <th>Amount Invoiced</th>
                        <th>Credit Note</th>
                        <th width="14%">Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $i = $offset;
                    foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?= ++$i; ?></td>
                            <td><?= Calypso::getValue($invoice, 'invoice_number'); ?></td>
                            <td><?= strtoupper(Calypso::getValue($invoice, 'company.name')); ?></td>
                            <td><?= Calypso::getValue($invoice, 'total'); ?></td>
                            <td><?= Calypso::getValue($invoice, 'credit_note.credit_note_number'); ?></td>
                            <td>
                                <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#viewInvoice">
                                    View
                                </button>

                                <?php if (is_null(Calypso::getValue($invoice, 'credit_note.id'))): ?>
                                    <button data-company_name="<?= Calypso::getValue($invoice, 'company.name'); ?>"  data-invoice_number="<?= Calypso::getValue($invoice, 'invoice_number'); ?>" class="btn btn-primary btn-xs" data-generate_credit_note="true" data-toggle="modal"
                                            data-target="#generateCreditNote">Generate Credit
                                        Note
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        <?php else: ?>
            There are no invoices matching the specified criteria.
        <?php endif; ?>
    </div>
</div>
<script type="text/html" id="invoiceParcelTmpl">
    <tr>
        <td>{{index}}</td>
        <td>{{waybill_number}}</td>
        <td>{{company_name}}</td>
        <td>{{amount}}</td>
        <td>{{discount}}</td>
        <td>{{net_amount}}</td>
        <td>
            <input name="deducted_amount[]" data-waybill='{{waybill_number}}' data-net_amount="{{net_amount}}" type="text" class="form-control" style="width:100px;margin-left: 65px;" value="0">
            <input type='hidden' name='invoice_parcels[]' value='{{invoice_parcel_id}}'>
            <input type='hidden' data-parcel_waybill='{{waybill_number}}' name='new_net_amount[]' value='{{net_amount}}'>
        </td>
        <td data-waybill='{{waybill_number}}'>{{net_amount}}</td>
    </tr>
</script>
<script type="text/html" id="invoiceParcelTotalTmpl">
    <tr>
        <td></td>
        <td><b>NET TOTAL</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b id='net_total'></b></td>
    </tr>
</script>


<div class="modal fade" id="teller-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="" class="validate-form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Submit Teller Details</h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="submit_teller">
                    <input type="hidden" id="waybill_numbers" name="waybill_numbers">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- this page specific scripts -->

<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/form-watch-changes.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/finance/invoice.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/response_handler.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
