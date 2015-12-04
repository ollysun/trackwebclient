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
<form>
    <div class="modal fade" id="generateInvoice">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Generate Invoice</h4>
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

                        <tr>
                            <td></td>
                            <td><b>NET TOTAL</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>2550</b></td>
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
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a href="#">
                        <button type="button" class="btn btn-primary">Generate Invoice</button>
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
                                <option value="">Select Company</option>
                                <?php foreach ($companies as $company): ?>
                                    <option <?= $selectedCompany == Calypso::getValue($company, 'id') ? 'selected' : '' ?>
                                        value="<?= Calypso::getValue($company, 'id') ?>"><?= strtoupper(Calypso::getValue($company, 'name', '')); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="pull-left form-group form-group-sm">
                            <label>Status</label> <br>
                            <select name="status" class="form-control" style="width: 150px">
                                <option value="">Select Status</option>
                                <?php foreach ($statuses as $status): ?>
                                    <option <?= $selectedStatus == $status ? 'selected' : '' ?>
                                        value="<?= $status ?>"><?= ServiceConstant::getStatus($status); ?></option>
                                <?php endforeach; ?>
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
                <button id="generateInvoiceBtn" class="btn btn-primary" data-toggle="modal" data-target="#generateInvoice">Generate Invoice
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
                            <div class="checkbox-nice"></div>
                        </th>
                        <th style="width: 20px">No.</th>
                        <th>Waybill No.</th>
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
                    foreach ($corporateParcels as $corporateParcel): ?>
                        <tr>
                            <td>
                                <div class="checkbox-nice">
                                    <?php if(is_null(Calypso::getValue($corporateParcel, 'invoice_parcel.id'))):?>
                                        <input
                                            data-company_id="<?= Calypso::getValue($corporateParcel, 'company.id')?>"
                                            data-amount_due="<?= Calypso::getValue($corporateParcel, 'amount_due')?>"
                                            data-company_name="<?= Calypso::getValue($corporateParcel, 'company.name')?>"
                                            data-waybill_number="<?= Calypso::getValue($corporateParcel, 'waybill_number')?>"
                                            id="corporate_parce_<?=$i?>" class="checkable" type="checkbox">
                                        <label for="corporate_parce_<?=$i?>"> </label>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= ++$i; ?></td>
                            <td><?= Calypso::getValue($corporateParcel, 'waybill_number'); ?></td>
                            <td><?= strtoupper(Calypso::getValue($corporateParcel, 'company.name')); ?></td>
                            <td><?= strtoupper(Calypso::getValue($corporateParcel, 'payment_type.name', '')); ?></td>
                            <td><?= strtoupper(Calypso::getValue($corporateParcel, 'billing_type', '')); ?></td>
                            <td><?= ServiceConstant::getStatus(Calypso::getValue($corporateParcel, 'status')); ?></td>
                            <td><?= Calypso::getValue($corporateParcel, 'amount_due') ?></td>
                            <td><?= Calypso::getValue($corporateParcel, 'invoice_parcel.invoice_number', 'N/A'); ?></td>
                            <td>n/a</td>
                            <td>
                                <a href="<?= Url::to(['/shipments/view', 'waybill_number' => Calypso::getValue($corporateParcel, 'waybill_number')]) ?>"
                                   class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a>
                                <button class="btn btn-default btn-xs">Edit</button>
                            </td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        <?php else: ?>
            There are no parcels matching the specified criteria.
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


<!-- this page specific scripts -->

<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/keyboardFormSubmit.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/form-watch-changes.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/finance/corporate_shipment.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
