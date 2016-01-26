<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;

$this->title = 'Credit Note';

$this->params['breadcrumbs'] = array(
    array(
        'url' => ['finance/creditnote'],
        'label' => 'Finance'
    ),
    array('label' => $this->title)
);

?>

<!-- View Credit Note Modal -->
<form>
    <div class="modal fade" id="viewInvoice">
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
        </div>
    </div>
    <div class="main-box-body">
        <?php if ($creditNotes): ?>
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
                    foreach ($creditNotes as $creditNote): ?>
                        <tr>
                            <td><?= ++$i; ?></td>
                            <td><?= Calypso::getValue($creditNote, 'invoice_number'); ?></td>
                            <td><?= strtoupper(Calypso::getValue($creditNote, 'company.name')); ?></td>
                            <td><?= Calypso::getValue($creditNote, 'invoice.total'); ?></td>
                            <td><?= Calypso::getValue($creditNote, 'credit_note_number'); ?></td>
                            <td>
                                <button class="btn btn-primary btn-xs "
                                        data-toggle="modal"
                                        data-target="#viewInvoice"
                                        data-company_name="<?= strtoupper(Calypso::getValue($creditNote, 'company.name')); ?>"
                                        data-credit_note_no="<?= Calypso::getValue($creditNote, 'credit_note_number'); ?>"
                                        id="viewCreditNoteDetails">
                                    View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        <?php else: ?>
            There are no credit notes matching the specified criteria.
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
<?php $this->registerJsFile('@web/js/finance/credit_note.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
