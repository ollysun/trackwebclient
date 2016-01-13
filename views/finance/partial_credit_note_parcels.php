<?php
use Adapter\Util\Calypso;

?>


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
                    <th>deducted Amount</th>
                    <th>New Net Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 0;
                foreach ($credit_note_parcels as $credit_note_parcel) { ?>
                    <tr>
                        <td><?= ++$count; ?></td>
                        <td><?= Calypso::getValue($credit_note_parcel, 'invoiceParcel.waybill_number') ?></td>
                        <td><?= $company_name ?></td>
                        <td><?= Calypso::getValue($credit_note_parcel, 'amount_due') ?></td>
                        <td>
                            <input type="text" class="form-control" style="width:50px;"
                                   value=<?= Calypso::getValue($credit_note_parcel, 'invoiceParcel.discount') ?> readonly>
                        </td>
                        <td><?= Calypso::getValue($credit_note_parcel, 'invoiceParcel.net_amount') ?></td>
                        <td><?= Calypso::getValue($credit_note_parcel, 'creditNoteParcel.deducted_amount') ?></td>
                        <td><?= Calypso::getValue($credit_note_parcel, 'creditNoteParcel.new_net_amount') ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <a href="#">
                <button type="button" class="btn btn-primary">Print Credit Note</button>
            </a>
        </div>
    </div>
</div>
