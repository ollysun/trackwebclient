<?php
use Adapter\Util\Util;
use yii\helpers\Html;
use Adapter\Util\Calypso;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Invoices';
?>
<?= Html::cssFile('@web/css/compiled/print-invoice.css?v=1') ?>

    <div class="invoice-page continuous" style="<?= $template_header_page_height ?>">
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
                    <?= ucwords(Calypso::getValue($invoice, 'address')); ?>
                </td>
                <td>
                    <?= ucwords(Calypso::getValue($invoice, 'to_address')); ?>
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
                <td width="25%">Reg No.</td>
                <td width="25%"><?= Calypso::getValue($invoice, 'company.reg_no'); ?></td>
                <td width="25%">Document Date</td>
                <td width="25%"><?= Calypso::getValue($invoice, 'current_date'); ?></td>
            </tr>
            <tr>
                <td>Document Number</td>
                <td><?= Calypso::getValue($invoice, 'invoice_number'); ?></td>
                <td>VAT Date</td>
                <td><?= Calypso::getValue($invoice, 'current_date'); ?></td>
            </tr>
            </tbody>
        </table>
        <table class="table is-bordered">
            <thead class="text-center">
            <tr>
                <th colspan="7">For services rendered up to the <?= Calypso::getValue($invoice, 'current_date'); ?> - as
                    per
                    Specifications enclosed
                </th>
            </tr>
            <tr>
                <th>Volumes Summary For Period</th>
                <th>Total Shipments</th>
                <th>Total Packages</th>
                <th>Total Weight/Piece</th>
                <th>Base</th>
                <th>Disc</th>
                <th>Total EXCLUDING VAT</th>
            </tr>
            </thead>
            <tbody>
            <tr class="double-border">
                <td>SUB-TOTAL ALL Services (excluding Surcharge)</td>
                <td><?= Calypso::getValue($invoice, 'total_shipments'); ?></td>
                <td><?= Calypso::getValue($invoice, 'total_pieces'); ?></td>
                <td><?= number_format(Calypso::getValue($invoice, 'total_weight'), 1); ?></td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'base'), 2); ?> NGN</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'discount'), 2); ?> NGN</td>
                <td class="text-right invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'total_excluding_vat'), 2); ?>
                    NGN
                </td>
            </tr>
            <tr class="double-border">
                <td colspan="6">Stamp duty charge</td>
                <td class="text-right invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'stamp_duty'), 2); ?>
                    NGN
                </td>
            </tr>
            <tr class="double-border">
                <td colspan="6">NET TOTAL NET (Including Surcharge)</td>
                <td class="text-right invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'new_total_net'), 2); ?>
                    NGN
                </td>
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
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'total_excluding_vat'), 2); ?>
                    NGN
                </td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'st_standard_vat'), 2); ?>NGN
                </td>
                <td class="invoice-total-amt-cell"></td>
            </tr>
            <tr>
                <td><strong>Vat Rate</strong></td>
                <td>XR EXEMPTED</td>
                <td></td>
                <td class="text-right">Base</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'stamp_duty'), 2); ?>NGN
                </td>
                <td class="invoice-amt-cell">0.00 NGN</td>
                <td class="invoice-total-amt-cell"></td>
            </tr>
            <tr>
                <td>Total</td>
                <td></td>
                <td></td>
                <td class="text-right">Base</td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'total_excluding_vat'), 2); ?>
                    NGN
                </td>
                <td class="invoice-amt-cell"><?= number_format(Calypso::getValue($invoice, 'st_standard_vat'), 2); ?>NGN
                </td>
                <td class="invoice-total-amt-cell"></td>
            </tr>
            </tbody>
        </table>
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td><strong>VAT Total</strong></td>
                <td class="invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'st_standard_vat'), 2); ?>
                    NGN
                </td>
            </tr>
            <tr class="double-border">
                <td><strong>TOTAL TO PAY (VAT Included)</strong></td>
                <td class="invoice-total-amt-cell"><?= number_format(Calypso::getValue($invoice, 'total_to_pay'), 2); ?>
                    NGN
                </td>
            </tr>
            </tbody>
        </table>

        <div>
            <strong>Amount In Words NGN
                : <?= ucwords(Util::convert_number_to_words(Calypso::getValue($invoice, 'total_to_pay_naira'))) . ' Naira'; ?> <?= ucwords(Util::convert_number_to_words(Calypso::getValue($invoice, 'total_to_pay_kobo'))) . ' Kobo'; ?></strong>

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
            <em>Please detach below this line and send through the business manager or our courier to our finance
                department</em>
            <hr class="dashed-line"/>
            <h4 class="text-center text-capitalize">PAYMENT NOTIFICATION SLIP/PAYMENT ADVICE</h4>

            <div class="row">
                <div class="col-xs-6">
                    <div class="fill-in">
                        <span class="fill-in-label">Account number</span>

                        <div class="fill-in-answer"><?= Calypso::getValue($invoice, 'account_number') ?></div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="fill-in">
                        <span class="fill-in-label">Invoice number</span>

                        <div class="fill-in-answer"><?= Calypso::getValue($invoice, 'invoice_number') ?></div>
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
        <br>
        <br>
        <br>
    </div>
<?php

?>
    <div class="invoice-page continuous" style="<?= $parcelPages ?>">
        <div class="">
            <h4 class="text-center">Invoice No: <?= Calypso::getValue($invoice, 'invoice_number'); ?></h4>
            <table class="table table-bordered is-double-bordered" style="font-size: 9px;">
                <thead>
                <tr class="is-double-bordered">
                    <th>S/N</th>
                    <th>HAWB</th>
                    <th>Credit Note ID</th>
                    <th>PU Date</th>
                    <th>Consignee</th>
                    <th>Address</th>
                    <th>Origin</th>
                    <th>Town</th>
                    <th>Credit note description</th>
                    <th>Weight/Pcs</th>
                    <th class="invoice-total-amt-cell" style="text-align: left">Name of Receiver</th>
                    <th>Date/Time Received</th>
                    <th>Actual</th>
                    <th>Other Charges</th>
                    <th class="invoice-total-amt-cell is-double-bordered">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                for ($i = 0; $i < count($invoiceParcels); $i++):
                    $invoiceParcel = Calypso::getValue($invoiceParcels, "$i");
                    ?>
                    <tr style="height: 45px;">
                        <td><?= $i + 1; ?></td>
                        <td><?= Calypso::getValue($invoiceParcel, 'waybill_number'); ?></td>
                        <td><?= !is_null($invoiceParcel) ? Calypso::getValue($invoice, 'credit_note.credit_note_number') : '<br/><br/>'; ?></td>
                        <td><?= Util::formatDate(\Adapter\Globals\ServiceConstant::DATE_FORMAT, Calypso::getValue($invoiceParcel, 'parcel.created_date')); ?></td>
                        <td><?= strtoupper(Calypso::getValue($invoiceParcel, 'receiver.firstname') . ' ' . Calypso::getValue($invoiceParcel, 'receiver.lastname')); ?></td>
                        <td style="height: 000px;"><?= strtoupper(mb_strimwidth(Calypso::getValue($invoiceParcel, 'receiver_address.street_address1', ""), 0, 25, "...")); ?></td>
                        <td><?= strtoupper(Calypso::getValue($invoiceParcel, 'sender_city.name')) ?></td>
                        <td><?= strtoupper(Calypso::getValue($invoiceParcel, 'receiver_city.name')) ?></td>
                        <td></td>
                        <td><?= Calypso::getValue($invoiceParcel, 'parcel.weight'); ?></td>
                        <td><?= Calypso::getValue($invoiceParcel, 'delivery_receipt.name'); ?></td>
                        <td><?= Calypso::getValue($invoiceParcel, 'delivery_receipt.delivered_at'); ?></td>
                        <td><?= number_format(Calypso::getValue($invoiceParcel, 'parcel.amount_due'), 2); ?></td>
                        <?php
                        $extra_charges_array = [
                            Calypso::getValue($invoiceParcel, 'parcel.insurance'),
                            Calypso::getValue($invoiceParcel, 'parcel.storrage_demurrage'),
                            Calypso::getValue($invoiceParcel, 'parcel.cost_of_crating'),
                            Calypso::getValue($invoiceParcel, 'parcel.duty_charge'),
                            Calypso::getValue($invoiceParcel, 'parcel.handling_charge'),
                            Calypso::getValue($invoiceParcel, 'parcel.others')
                        ];
                        ?>
                        <td><?= $total_charge = Util::calculateExtraCharges($extra_charges_array); ?></td>
                        <td class="invoice-total-amt-cell is-double-bordered"><?= number_format(Calypso::getValue($invoiceParcel, 'net_amount') + $total_charge, 2); ?></td>
                    </tr>
                <?php endfor; ?>
                <tr>
                    <td colspan="13">INVOICE VALUE</td>
                    <td></td>
                    <td class="invoice-total-amt-cell is-double-bordered"><?= number_format(Calypso::getValue($invoice, 'total_excluding_vat'), 2) ?></td>
                </tr>
                <tr>
                    <td colspan="13">Add 5% VAT</td>
                    <td></td>
                    <td class="invoice-total-amt-cell is-double-bordered"><?= number_format(Calypso::getValue($invoice, 'st_standard_vat'), 2) ?></td>
                </tr>
                <tr>
                    <td colspan="13">Stamp Duty Charge</td>
                    <td></td>
                    <td class="invoice-total-amt-cell is-double-bordered"><?= number_format(Calypso::getValue($invoice, 'stamp_duty'), 2) ?></td>
                </tr>
                <tr class="double-border">
                    <td colspan="13"><strong>AMOUNT DUE</strong></td>
                    <td></td>
                    <td class="invoice-total-amt-cell is-double-bordered"><?= number_format(Calypso::getValue($invoice, 'total_to_pay'), 2) ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php
 if(false):
?>
    <div class="invoice-page continuous" style="<?= $page_height ?>">
        <h4 class="text-center">Other Charges for Invoice</h4>
        <table class="table table-bordered" style="font-size: 9px;">
            <thead>
            <tr>
                <th rowspan="2">S/N</th>
                <th rowspan="2">Waybill no</th>
                <th rowspan="2">Reference</th>
                <th colspan="6" style="text-align:center">Charges</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Insurance</th>
                <th>Storrage/Demurrage</th>
                <th>Cost of Crating</th>
                <th>Duty Charge</th>
                <th>Handling Charge</th>
                <th>Others</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < count($invoiceParcels); $i++):
                $invoiceParcel = Calypso::getValue($invoiceParcels, "$i");
                ?>
                <tr>
                    <td><?= $i + 1; ?></td>
                    <td><?= Calypso::getValue($invoiceParcel, 'waybill_number'); ?></td>
                    <td><?= Calypso::getValue($invoiceParcel, 'parcel.reference_number'); ?></td>
                    <td><?= Calypso::getDisplayValue($invoiceParcel, 'parcel.insurance', 0); ?></td>
                    <td><?= Calypso::getDisplayValue($invoiceParcel, 'parcel.storrage_demurrage', 0); ?></td>
                    <td><?= Calypso::getDisplayValue($invoiceParcel, 'parcel.cost_of_crating', 0); ?></td>
                    <td><?= Calypso::getDisplayValue($invoiceParcel, 'parcel.duty_charge', 0); ?></td>
                    <td><?= Calypso::getDisplayValue($invoiceParcel, 'parcel.handling_charge', 0); ?></td>
                    <td><?= Calypso::getDisplayValue($invoiceParcel, 'parcel.others', 0); ?></td>
                    <?php
                    $extra_charges_array = [
                        Calypso::getValue($invoiceParcel, 'parcel.insurance'),
                        Calypso::getValue($invoiceParcel, 'parcel.storrage_demurrage'),
                        Calypso::getValue($invoiceParcel, 'parcel.cost_of_crating'),
                        Calypso::getValue($invoiceParcel, 'parcel.duty_charge'),
                        Calypso::getValue($invoiceParcel, 'parcel.handling_charge'),
                        Calypso::getValue($invoiceParcel, 'parcel.others')
                    ];
                    ?>
                    <td><?= Util::calculateExtraCharges($extra_charges_array); ?></td>
                </tr>
            <?php endfor; ?>

            </tbody>
        </table>
    </div>

<? endif;?>