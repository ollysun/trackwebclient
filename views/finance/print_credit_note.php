<?php
use yii\helpers\Html;
use Adapter\Util\Calypso;
use yii\helpers\Url;

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
                ARM PENSIONS MANAGERS (PFA) LIMITED<br>
                5, MEKUNWEN ROAD, OFF OYINKAN ABAYOMI DRIVE, IKOYI<br>
                LAGOS, NIGERIA
            </td>
            <td>
                ARM PENSIONS MANAGERS (PFA) LIMITED<br>
                5, MEKUNWEN ROAD, OFF OYINKAN ABAYOMI DRIVE, IKOYI<br>
                LAGOS, NIGERIA
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
            <td width="25%">7011010099</td>
            <td width="25%">Document Date</td>
            <td width="25%">28/10/2015</td>
        </tr>
        <tr>
            <td>Document Number</td>
            <td>CNS 2015181</td>
            <td>VAT Date</td>
            <td>28/10/2015</td>
        </tr>
    </tbody>
</table>
<table class="table">
    <thead>
        <tr>
            <th class="text-center" colspan="7">Being Credit Note for awb 50637473 (SEPT 2015)</th>
        </tr>
        <tr class="text-normal is-bordered">
            <th>
                1<br><br>
            </th>
            <th>
                <strong>Our Ref</strong> <br>
                000000050637473 <br>
                Shipment is for Rene Collection
            </th>
            <th>
                <strong>Your Ref</strong> <br> <br> <br>
            </th>
            <th class="text-right">
                <strong>Total Charge</strong> <br>
                1500.00 <br> <br>
            </th>
            <th class="text-right">
                <strong>Should be</strong> <br>
                0.00 <br> <br>
            </th>
            <th>
                V/C <br> ST <br> <br>
            </th>
            <th class="text-right">
                <strong>Credit Amount</strong> <br>
                1500.00 NGN <br> <br>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="is-bordered">
            <td colspan="6">NET TOTAL</td>
            <td class="text-right invoice-total-amt-cell">1500.00 NGN</td>
        </tr>
        <tr class="is-bordered">
            <td colspan="4"></td>
            <td>VAT Rate</td>
            <td>Base</td>
            <td class="text-right invoice-total-amt-cell"></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3">ST STANDARD</td>
            <td>5.00%</td>
            <td>1500.00 NGN</td>
            <td class="text-right invoice-total-amt-cell">75.00 NGN</td>
        </tr>
        <tr>

        </tr>
    </tbody>
</table>

<table class="table table-bordered">
    <tbody>
        <tr>
            <td><strong>VAT Total</strong></td>
            <td class="invoice-total-amt-cell">75.00 NGN</td>
        </tr>
        <tr class="double-border">
            <td><strong>Credit Note Total</strong></td>
            <td class="invoice-total-amt-cell">1575.00 NGN</td>
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