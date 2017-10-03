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
<form method="post" action="<?= Url::to("/finance/generatecreditnote"); ?>">
    <div class="modal fade" id="generateCreditNote">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Generate Credit Note for Invoice (<span id="invoiceNumberLabel"></span>)
                    </h4>
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
                    <input type="hidden" name="invoice_number" id="invoice_number" class="form-control">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a href="#">
                        <button type="submit" class="btn btn-primary">Generate Credit Note</button>
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
                    <h4 class="modal-title" id="view_invoiceNumber"></h4>
                </div>
                <div class="modal-body">

                    <div id="viewInvoiceLoading" style="text-align: center;">
                        <i class="fa fa-spin fa-spinner fa-4x"></i>

                        <p>Loading invoice parcels...</p>
                    </div>
                    <table id="viewInvoiceTable" class="table table-hover dataTable hide">
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
                        </tbody>
                    </table>

                    <div class="clearfix">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Invoice address</label>
                                <textarea disabled id="view_invoiceAddress" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Invoice To</label>
                                <textarea disabled id="view_invoiceTo" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Reference</label>
                                <textarea disabled id="view_reference" class="form-control" rows="3"></textarea>
                            </div>

                        </div>


                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Account Number</label>
                                <input disabled id="view_accountNumber" type="text" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Stamp Duty</label>
                                <input disabled id="view_stampDuty" type="text" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Currency</label> <br>
                                <select id="view_currency" disabled class="form-control">
                                    <option>NGN</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <a id="viewPrintInvoice" href="#" class="btn btn-success">
                        Print
                    </a>
                    <a id="viewDownloadCSVInvoice" href="#" class="btn btn-primary">
                        Download CSV
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
                <form id="mainform">
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
                            <input type="checkbox" name="download">
                            <input type="submit" class="btn btn-primary btn-sm" value="Download CSV" >
                            <button class="btn btn-default btn-sm"><i class="fa fa-search"></i> View</button>
                        </input>

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
                        <th style="width: 20px" class="datatable-nosort">
                            <div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label
                                    for="chbx_w_all"> </label></div>
                        </th>
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
                            <td>
                                <div class="checkbox-nice">

                                    <input id="chbx_w_<?= ++$i; ?>" class="checkable"
                                           data-invoice_number="<?= strtoupper(Calypso::getValue($invoice, 'invoice_number')); ?>"
                                           type="checkbox"><label
                                        for="chbx_w_<?= $i; ?>"> </label>
                                </div>
                            </td>
                            <td><?= ++$i; ?></td>
                            <td><?= Calypso::getValue($invoice, 'invoice_number'); ?></td>
                            <td><?= strtoupper(Calypso::getValue($invoice, 'company.name')); ?></td>
                            <td><?= Calypso::getValue($invoice, 'total'); ?></td>
                            <td><?= Calypso::getValue($invoice, 'credit_note.credit_note_number'); ?></td>
                            <td>
                                <?php
                                ?>
                                <button
                                    data-approve_invoice="true" class="btn btn-primary btn-xs" data-toggle="modal">
                                    Approve
                                </button>

                                <span class="hide"><?= json_encode($invoice)?></span>

                                <button
                                    data-view_invoice="true" class="btn btn-primary btn-xs" data-toggle="modal"
                                    data-target="#viewInvoice">
                                    View
                                </button>

                                <?php if (is_null(Calypso::getValue($invoice, 'credit_note.id'))): ?>
                                    <button data-company_name="<?= Calypso::getValue($invoice, 'company.name'); ?>"
                                            data-invoice_number="<?= Calypso::getValue($invoice, 'invoice_number'); ?>"
                                            class="btn btn-primary btn-xs" data-generate_credit_note="true"
                                            data-toggle="modal"
                                            data-target="#generateCreditNote">Generate Credit
                                        Note
                                    </button>
                                <?php endif; ?>

                                <?php if(Calypso::userIsInRole(ServiceConstant::USER_TYPE_ADMIN)): ?>
                                    <a onclick="return confirm('Are you sure you want to recreate this invoice?')" class="btn btn-success btn-xs" href="/finance/recreateinvoice?invoice_number=<?= Calypso::getValue($invoice, 'invoice_number'); ?>">Recreate</a>
                                <?php endif;?>
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
            <input name="deducted_amount[]" data-waybill='{{waybill_number}}' data-net_amount="{{net_amount}}"
                   type="text" class="form-control" style="width:100px;margin-left: 65px;" value="0">
            <input type='hidden' name='invoice_parcel[]' value='{{invoice_parcel_id}}'>
            <input type='hidden' data-parcel_waybill='{{waybill_number}}' name='new_net_amount[]'
                   value='{{net_amount}}'>
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

<script type="text/html" id="viewInvoiceParcelsTmpl">
    <tr>
        <td>{{index}}</td>
        <td>{{waybill_number}}</td>
        <td>{{company_name}}</td>
        <td>{{amount}}</td>
        <td>
            <input type="text" disabled class="form-control" style="width:100px;margin-left: 65px;"
                   value="{{discount}}">
        </td>
        <td>{{net_amount}}</td>
    </tr>
</script>
<script type="text/html" id="viewInvoiceTotalTmpl">
    <tr>
        <td></td>
        <td><b>NET TOTAL</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b>{{total}}</b></td>
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
<?php $this->registerJsFile('@web/js/finance/invoice.js?v=1.0.2', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/response_handler.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table_util.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

