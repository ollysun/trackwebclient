<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

$this->title = 'Corporate Shipments';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['finance/index'],
        'label' => 'Finance'
    ),
    array('label' => $this->title)
);

?>

<!-- Generate Invoices Modal -->
<div class="modal fade" id="generateInvoice">
    <form class="validate-form" method="post" action="<?= Url::to("/finance/createinvoice"); ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Generate Invoice</h4>
                </div>
                <div class="modal-body">
                    <span id="single_invoice" class="hidden">
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
                            <tbody id="invoice_parcels">
                            </tbody>
                        </table>
                        <div class="clearfix">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Invoice address</label>
                                <textarea name="address" class="form-control validate required" rows="2"></textarea>

                                <div class="checkbox">
                                    <label>
                                        <input id="same_as_invoice_to" type="checkbox">
                                        Same as Invoice To
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Invoice To</label>
                                <textarea name="to_address" class="form-control validate required" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Reference</label>
                                <textarea name="reference" class="form-control validate required" rows="3"></textarea>
                            </div>

                        </div>


                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Accont Number</label>
                                <input name="account_number" type="text" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Stamp Duty</label>
                                <input name="stamp_duty" type="text" class="form-control validate required number">
                            </div>

                            <div class="form-group">
                                <label>Currency</label> <br>
                                <select name="currency" class="form-control">
                                    <option selected>NGN</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    </span>
                    <span id="multiple_invoice" class="hidden">
                        <div class="panel-group" id="bulk_invoice">

                        </div>
                    </span>




                </div>
                <div class="modal-footer">
                    <input type="hidden" name="total" class="form-control net_total_field">
                    <input type="hidden" name="company_id" id="company_id" class="form-control">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a href="#">
                        <button type="button" id="generate_Invoice_btn" class="btn btn-primary">Generate Invoice</button>
                    </a>
                </div>
            </div>
        </div>

    </form>
</div>

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
                        <div class="pull-left form-group form-group-sm">
                            <label>Status</label> <br>
                            <select name="status" class="form-control" style="width: 150px">
                                <option value="">Select Status</option>
                                <?php foreach ($statuses as $status): ?>
                                    <?php if ($status != ServiceConstant::CANCELLED) { ?>
                                        <option <?= $selectedStatus == $status ? 'selected' : '' ?>
                                            value="<?= $status ?>"><?= ServiceConstant::getStatus($status); ?></option>
                                    <?php } ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="pull-left">
                            <label>&nbsp;</label><br>
                            <button class="btn btn-default btn-sm" id="records_filter"><i class="fa fa-search"></i>
                            </button>
                        </div>

                        <div class="form-group form-group-sm form-inline">
                            <br/>
                            <label for="page_width">Records</label>
                            <select name="page_width" id="page_width" class="form-control ">
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
                ?>
            </div>
            <div class="pull-right clearfix">
                <label>&nbsp;</label><br>
                <button id="generateInvoiceBtn" class="btn btn-primary" data-toggle="modal"
                        data-target="#generateInvoice">Generate Invoice
                </button>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if ($corporateParcels): ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px" class="datatable-nosort">
                            <div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label
                                    for="chbx_w_all"> </label></div>
                        </th>
                        <th style="width: 20px">No.</th>
                        <th>Waybill No.</th>
                        <th>Reference No.</th>
                        <th>Company Name</th>
                        <th>Payment Method</th>
                        <th>Billing Method</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Invoice</th>
                        <th>Credit Note</th>
                        <th width="14%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = $offset;
                    foreach ($corporateParcels as $corporateParcel):
                        $amountDue = !empty($corporateParcel['discounted_amount_due']) ? $corporateParcel['discounted_amount_due'] : $corporateParcel['amount_due'];
                        ?>
                        <tr>
                            <td>
                                <div class="checkbox-nice">
                                    <?php if (is_null(Calypso::getValue($corporateParcel, 'invoice_parcel.id'))): ?>
                                        <input name="parcel"
                                               data-company_id="<?= Calypso::getValue($corporateParcel, 'company.id') ?>"
                                               data-company_address="<?= Calypso::getValue($corporateParcel, 'company.address') ?>"
                                               data-amount_due="<?= $amountDue ?>"
                                               data-company_name="<?= Calypso::getValue($corporateParcel, 'company.name') ?>"
                                               data-waybill_number="<?= Calypso::getValue($corporateParcel, 'waybill_number') ?>"
                                               data-account_number="<?= Calypso::getValue($corporateParcel, 'company.reg_no') ?>"
                                               data-reference_number="<?= Calypso::getValue($corporateParcel, 'reference_number') ?>"
                                               id="corporate_parce_<?= $i ?>" class="checkable" type="checkbox">
                                        <label for="corporate_parce_<?= $i ?>"> </label>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= ++$i; ?></td>
                            <td><?= Calypso::getValue($corporateParcel, 'waybill_number'); ?></td>
                            <td><?= Calypso::getValue($corporateParcel, 'reference_number'); ?></td>
                            <td><?= strtoupper(Calypso::getValue($corporateParcel, 'company.name')); ?></td>
                            <td><?= strtoupper(Calypso::getValue($corporateParcel, 'payment_type.name', '')); ?></td>
                            <td><?= strtoupper(Calypso::getValue($corporateParcel, 'billing_type', '')); ?></td>
                            <td><?= ServiceConstant::getStatus(Calypso::getValue($corporateParcel, 'status')); ?></td>
                            <td><?= $amountDue ?></td>
                            <td><?= Calypso::getValue($corporateParcel, 'invoice_parcel.invoice_number', 'N/A'); ?></td>
                            <td>n/a</td>
                            <td>
                                <a href="<?= Url::to(['/shipments/view', 'waybill_number' => Calypso::getValue($corporateParcel, 'waybill_number')]) ?>"
                                   class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a>
                                <?= $this->render('../elements/partial_edit_button', ['parcel' => $corporateParcel] ); ?>
                                <?= $this->render('../elements/parcel/partial_cancel_button', ['waybill_number' => $corporateParcel['waybill_number'], 'status' => $corporateParcel['status']]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        <?php else: ?>
            There are no corporate parcels matching the specified criteria.
        <?php endif; ?>
    </div>
</div>


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
    <script type="text/html" id="accordion_content">
        <div class="panel panel-default invoice" data-index={{data_index}}>
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" style="color: #ffffff" data-parent="#accordion" href="#collapse{{index}}">
                        {{company_name}}
                    </a>
                </h4>
            </div>
            <div id="collapse{{index}}" class="panel-collapse collapse {{collapse_status}}">
                <div class="panel-body">
                    <table id="" class="table table-hover dataTable invoice_parcels">
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
                        <tbody id="">
                        {{invoiceList}}
                        </tbody>
                    </table>
                    <div class="clearfix">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Invoice address</label>
                                <textarea disabled name="address" class="form-control validate required" rows="2">{{address}}</textarea>

                                <div class="checkbox">
                                    <label>
                                        <input data-address="{{address}}" class="same_as_invoice_to" type="checkbox">
                                        Same as Invoice To
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Invoice To</label>
                                <textarea onkeyup="updateAddress(this,'to_address',{{data_index}})" name="to_address" class="to_address form-control validate required" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Reference</label>
                                <textarea onkeyup="updateAddress(this,'reference',{{data_index}})" name="reference" class="reference_number form-control validate required" rows="3">{{reference}}</textarea>
                            </div>

                        </div>


                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Accont Number</label>
                                <input name="account_number" type="text" value="{{account_number}}" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Stamp Duty</label>
                                <input name="stamp_duty" value="" type="text" class="form-control validate required number">
                            </div>

                            <div class="form-group">
                                <label>Currency</label> <br>
                                <select name="currency" class="form-control">
                                    <option selected>NGN</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>
<script type="text/html" id="invoiceParcelTemplate">
    <tr>
        <td>{{serial_number}}</td>
        <td>{{waybill_number}}</td>
        <td>{{company_name}}</td>
        <td>{{amount}}</td>
        <td>
            <input type='text' name='discount[]' data-amount='{{amount}}' data-waybill='{{waybill_number}}'
                   class='form-control' style='width:50px;' value='0' data-index="{{index}}">
            <input type='hidden' name='waybill_number[]' value='{{waybill_number}}'>
            <input type='hidden' data-parcel_waybill='{{waybill_number}}' name='net_amount[]' value='{{amount}}'>
        </td>
        <td data-waybill='{{waybill_number}}'>{{amount}}</td>
    </tr>
</script>
<script type="text/html" id="invoiceTotal">
    <tr>
        <td></td>
        <td><b>NET TOTAL</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b class='net_total'></b></td>
    </tr>
</script>

<?= $this->render('../elements/parcel/partial_cancel_shipment_form') ?>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/form-watch-changes.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/finance/corporate_shipment.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>