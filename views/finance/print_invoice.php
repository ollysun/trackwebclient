<?php
use Adapter\Util\Util;
use yii\helpers\Html;
use Adapter\Util\Calypso;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Invoices';
?>

<?= Html::cssFile('@web/css/compiled/print-invoice.css') ?>

<div class="invoice-page">
    <?= $this->render('../elements/finance/print_header'); ?>
    <div class="text-center"><h4>INVOICE - ORIGINAL</h4></div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="50%">Invoicing address</th>
                <th width="50%">Invoice To</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td height="120px">
                    <?= ucwords(Calypso::getValue($invoice, 'address'));?>
                </td>
                <td>
                    <?= ucwords(Calypso::getValue($invoice, 'to_address'));?>
                </td>
            </tr>
            <tr>
                <td>VAT Reg. Nr</td>
                <td>Page Nr. 1/1</td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered double-border">
        <tbody>
            <tr>
                <td width="25%">Account Number</td>
                <td width="25%"><?= Calypso::getValue($invoice, 'account_number');?></td>
                <td width="25%">Document Date</td>
                <td width="25%"><?= Calypso::getValue($invoice, 'current_date');?></td>
            </tr>
            <tr>
                <td>Document Number</td>
                <td><?= Calypso::getValue($invoice, 'invoice_number');?></td>
                <td>VAT Date</td>
                <td><?= Calypso::getValue($invoice, 'current_date');?></td>
            </tr>
        </tbody>
    </table>
    <table class="table is-bordered">
        <thead class="text-center">
            <tr>
                <th colspan="7">For services rendered up to the <?= Calypso::getValue($invoice, 'current_date');?> - as per Specifications enclosed</th>
            </tr>
            <tr>
                <th>Volumes Summary For Period</th>
                <th>Total Shipments</th>
                <th>Total Pieces</th>
                <th>Total Weight</th>
                <th>Base</th>
                <th>Disc</th>
                <th>Total EXCLUDING VAT</th>
            </tr>
        </thead>
        <tbody>
            <tr class="double-border">
                <td>SUB-TOTAL ALL Services (excluding Surcharge)</td>
                <td><?= Calypso::getValue($invoice, 'total_shipments');?></td>
                <td><?= Calypso::getValue($invoice, 'total_pieces');?></td>
                <td><?= number_format(Calypso::getValue($invoice, 'total_weight'), 1);?></td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'base'), 2);?> NGN</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'discount'), 2);?> NGN</td>
                <td class="text-right invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'total_excluding_vat'), 2);?> NGN</td>
            </tr>
            <tr class="double-border">
                <td colspan="6">Stamp duty charge</td>
                <td class="text-right invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'stamp_duty'), 2);?> NGN</td>
            </tr>
            <tr class="double-border">
                <td colspan="6">NET TOTAL NET (Including Surcharge)</td>
                <td class="text-right invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'new_total_net'), 2);?> NGN</td>
            </tr>
        </tbody>
    </table>
    <table class="table table-is-bordered">
        <tbody>
            <tr>
                <td class="vat-rate-col"><strong>Vat Rate</strong></td>
                <td>ST STANDARD</td>
                <td>5.00</td>
                <td class="text-right">Base</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'total_excluding_vat'), 2);?> NGN</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'st_standard_vat'), 2);?> NGN</td>
                <td class="invoice-total-amt-cell"></td>
            </tr>
            <tr>
                <td><strong>Vat Rate</strong></td>
                <td>XR EXEMPTED</td>
                <td></td>
                <td class="text-right">Base</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'stamp_duty'), 2);?> NGN</td>
                <td class="invoice-amt-cell">0.00 NGN</td>
                <td class="invoice-total-amt-cell"></td>
            </tr>
            <tr>
                <td>Total</td>
                <td></td>
                <td></td>
                <td class="text-right">Base</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'total_excluding_vat'), 2);?> NGN</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'st_standard_vat'), 2);?> NGN</td>
                <td class="invoice-total-amt-cell"></td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td><strong>VAT Total</strong></td>
                <td class="invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'st_standard_vat'), 2);?> NGN</td>
            </tr>
            <tr class="double-border">
                <td><strong>TOTAL TO PAY (VAT Included)</strong></td>
                <td class="invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'total_to_pay'), 2);?> NGN</td>
            </tr>
        </tbody>
    </table>

    <div>
        <strong>Amount In Words NGN : <?= ucwords(Util::convert_number_to_words(Calypso::getValue($invoice, 'total_to_pay_naira'))) . ' Naira';?> <?= ucwords(Util::convert_number_to_words(Calypso::getValue($invoice, 'total_to_pay_kobo'))) . ' Kobo';?></strong>
        <div class="small">
            Any queries on this invoice should be notified in writing within 7 days from date of invoice <br>
            This invoice is payable strictly within 0 days from date of invoice. <br>
            Any amount not paid within these terms will be subject to an interest charge of 5% per month <br>
            VAT NO : ISV10-002799562 <br>
            TIN NO : 03303688-0001 <br>
            A/C NO : 0046001502 (Access Bank Plc) <br><br>
        </div>
    </div>
    <div class="invoice-middle-separator"></div>
    <div>
        <div class="row">
            <div class="col-xs-8">
                <div class="fill-in">
                    <span class="fill-in-label">The Invoice is received by: Name</span>
                    <div class="fill-in-answer"></div>
                </div>

            </div>
            <div class="col-xs-4">
                <div class="fill-in">
                    <span class="fill-in-label">Sign./Date</span>
                    <div class="fill-in-answer"></div>
                </div>
            </div>
        </div>
        <h4 class="text-center">Please Raise Cheque In The Name of (COURIERPLUS SERVICES LIMITED)</h4>
        <em>Please detach below this line and send through the business manager or our courier to our finance department</em>
        <hr class="dashed-line"></hr>
        <h4 class="text-center text-capitalize">PAYMENT NOTIFICATION SLIP/PAYMENT ADVICE</h4>
        <div class="row">
            <div class="col-xs-6">
               <div class="fill-in">
                    <span class="fill-in-label">Account number</span>
                    <div class="fill-in-answer"></div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="fill-in">
                    <span class="fill-in-label">Invoice number</span>
                    <div class="fill-in-answer"></div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="fill-in">
                    <span class="fill-in-label">Invoice amount paid</span>
                    <div class="fill-in-answer"></div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="fill-in">
                    <span class="fill-in-label">Payment Date</span>
                    <div class="fill-in-answer"></div>
                </div>
            </div>
        </div>
        <div class="fill-in">
            <span class="fill-in-label">Amount deducted if not fully paid</span>
            <div class="fill-in-answer"></div>
        </div>
        <div class="fill-in">
            <span class="fill-in-label">Reason for Deduction</span>
            <div class="fill-in-answer"></div>
        </div>
        <div class="fill-in">
            <span class="fill-in-label">Name, Signature and Date</span>
            <div class="fill-in-answer"></div>
        </div>
    </div>
</div>

<div class="invoice-page">
    <?= $this->render('../elements/finance/print_header'); ?>
    <div class="rotate-wrap">
        <div class="rotate">
            <h4 class="text-center">Invoice No: <?= Calypso::getValue($invoice, 'invoice_number');?></h4>
            <table class="table table-bordered is-double-bordered">
                <thead>
                    <tr class="is-double-bordered">
                        <th>S/N</th>
                        <th>HAWB</th>
                        <th>Credit Note ID</th>
                        <th>PU Date</th>
                        <th>Reference - City</th>
                        <th>Reference</th>
                        <th>Consignee</th>
                        <th>Address</th>
                        <th>Origin</th>
                        <th>Town</th>
                        <th>Credit note description</th>
                        <th>Weight/Piece</th>
                        <th>Description</th>
                        <th>Actual</th>
                        <th>Credit note</th>
                        <th class="invoice-total-amt-cell is-double-bordered">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>50624975</td>
                        <td></td>
                        <td>06-Oct-15</td>
                        <td>AKURE</td>
                        <td>VC OFFICE</td>
                        <td>MANAGING DIRECTOR/CEO</td>
                        <td>29A, ARAROMI STREET, BY MARCATHY POLICE BARACK LAGOS ISLAND</td>
                        <td>AKURE</td>
                        <td>ONIKAN - I</td>
                        <td></td>
                        <td>0.5</td>
                        <td>Result</td>
                        <td>1,500.00</td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered">1,500.00</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="invoice-total-amt-cell is-double-bordered"></td>
                    </tr>
                    <tr>
                        <td colspan="15">INVOICE VALUE</td>
                        <td class="invoice-total-amt-cell is-double-bordered"><?=  number_format(Calypso::getValue($invoice, 'total_excluding_vat'), 2)?></td>
                    </tr>
                    <tr>
                        <td colspan="15">Add 5% VAT</td>
                        <td class="invoice-total-amt-cell is-double-bordered"><?=  number_format(Calypso::getValue($invoice, 'st_standard_vat'), 2)?></td>
                    </tr>
                    <tr>
                        <td colspan="15">Stamp Duty Charge</td>
                        <td class="invoice-total-amt-cell is-double-bordered"><?=  number_format(Calypso::getValue($invoice, 'stamp_duty'), 2)?></td>
                    </tr>
                    <tr class="double-border">
                        <td colspan="15"><strong>AMOUNT DUE</strong></td>
                        <td class="invoice-total-amt-cell is-double-bordered"><?=  number_format(Calypso::getValue($invoice, 'total_to_pay'), 2)?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>