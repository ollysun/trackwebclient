<?php
use yii\helpers\Html;
use Adapter\Util\Calypso;
use yii\helpers\Url;
use yii\web\View;


/* @var $this yii\web\View */
$this->title = 'Credit note';
?>

<?= Html::cssFile('@web/css/compiled/print-invoice.css') ?>

<div class="invoice-page">
    <?= $this->render('../elements/finance/print_header'); ?>
    <div class="text-center"><h4>CREDIT NOTE - ORIGINAL</h4></div>
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
                <?= strtoupper(Calypso::getValue($credit_note_details[0], 'invoice.address')); ?>
            </td>
            <td>
                <?= strtoupper(Calypso::getValue($credit_note_details[0], 'invoice.to_address')); ?>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="table table-bordered double-border">
        <tbody>
        <tr>
            <td width="40%">Document Number</td>
            <td> <?= Calypso::getValue($credit_note_details[0], "creditNote.credit_note_number") ?> </td>
        </tr>
        <tr>
            <td>Created Date</td>
            <td> <?= Calypso::getValue($credit_note_details[0], 'creditNote.created_at'); ?> </td>
        </tr>
        </tbody>
    </table>
    <br><br>

    <table class="table">
        <caption><strong>Being Credit Note
                for <?= Calypso::getValue($credit_note_details[0], 'invoice.invoice_number') ?></strong></caption>
        <thead>
        <tr class="text-normal is-bordered">
            <th>
                <strong> S/N </strong>
            </th>
            <th>
                <strong>Our Ref</strong>
            </th>

            <th>
                <strong>Total Charge</strong> <br>
            </th>

            <th>
                <strong> Discount </strong> <br>
            </th>

            <th style="text-align:right">
                <strong>Credit Amount</strong> <br>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 0;
        $totalAmount = 0.00;
        $companyName = Calypso::getValue($credit_note_details[0], 'name');?>
        <?php for($i=0; $i < 39; $i++){?>
        <tr class="is-bordered">

                <td> <?= $i; ?></td>
        <td>1</td>
        <td>1 </td>
        <td>1  </td>
        <td>1</td>
<!--        --><?php //$totalAmount += $newNetAmount; ?>

        </tr>
        <?php }?>

        //        foreach ($credit_note_parcels as $credit_note_parcel) {
//            $newNetAmount = Calypso::getValue($credit_note_parcel, 'creditNoteParcel.new_net_amount');
//            $vatCharge = '75.00';
//            ?>
<!--            <tr class="is-bordered">-->
<!--                <td>--><?//= ++$i; ?><!--</td>-->
<!--                <td> --><?//= Calypso::getValue($credit_note_parcel, 'invoiceParcel.waybill_number') ?><!--<br> Shipment is-->
<!--                    for --><?//= $companyName ?><!-- </td>-->
<!--                <td>--><?//= Calypso::getValue($credit_note_parcel, 'invoiceParcel.net_amount') ?><!--</td>-->
<!--                <td> --><?//= Calypso::getValue($credit_note_parcel, 'creditNoteParcel.deducted_amount') ?><!--  </td>-->
<!--                <td align="right"> --><?//= $newNetAmount; ?><!--</td>-->
<!--                --><?php //$totalAmount += $newNetAmount; ?>
<!--            </tr>-->
<!--        --><?php //} ?>
        </tbody>
    </table>

    <table class="table table-bordered">
        <tbody>
        <tr>
            <td><strong>VAT Total</strong></td>
            <td class="invoice-total-amt-cell"><?= $vatCharge = 0 ?> NGN</td>
            <?php $totalAmount += floatval($vatCharge); ?>
        </tr>
        <tr class="double-border">
            <td><strong>Credit Note Total</strong></td>
            <td class="invoice-total-amt-cell"><?= number_format($totalAmount, 2) ?> NGN</td>
        </tr>
        </tbody>
    </table>

    <div>
        <div>
            <strong>Amount In Words NGN : one* five* seven* five* 0/100</strong>

            <div class="small">
                Any queries on this invoice should be notified in writing within 15 days from date of invoice <br>
                This invoice is payable strictly within 0 days from date of invoice. <br>
                Any amount not paid within these terms will be subject to an interest charge of 5% per month <br>
                VAT NO : ISV10-002799562 <br>
                TIN NO : 03303688-0001 <br>
                A/C NO : 0046001502 (Access Bank Plc)
            </div>
        </div>
    </div>
    <br><br>

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
    </div>
</div>

<?php $this->registerJs("window.print();", View::POS_READY, 'print'); ?>
