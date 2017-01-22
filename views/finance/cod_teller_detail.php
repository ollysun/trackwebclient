<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 1/20/2017
 * Time: 11:14 AM
 */
//
use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Util;
use yii\helpers\Url;
?>

<div class="box-main">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-right clearfix">
                <?php if($status != ServiceConstant::TELLER_APPROVED) :?>
                    <a title="Approve teller" href="<?= Url::toRoute(['/finance/approvesalesteller?id=' . $id]) ?>"
                       class="btn btn-xs btn-success">Approve</a>
                <?php endif;?>

                <?php if($status != ServiceConstant::TELLER_DECLINED) :?>
                    <a title="Reject teller" href="<?= Url::toRoute(['/finance/delinesalesteller?id=' . $id]) ?>"
                       class="btn btn-xs btn-danger">Decline</a>
                <?php endif;?>

            </div>
        </div>
    </div>

    <br/><br/>
    <div class="main-box-body">
        <?php if (!empty($teller_parcels)): ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">

                    <thead>
                    <tr>
                        <th></th>
                        <th>Account No: <?= $teller_no ?> | Teller No: <?= $teller_no ?></th>
                    </tr>

                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill Number</th>
                        <th>Amount</th>
                        <th>Created Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 0;
                    if (isset($teller_parcels) && is_array($teller_parcels)) {
                        $tolal_amount = 0;
                        foreach ($teller_parcels as $teller) {
                            $tolal_amount += Calypso::getValue($teller, 'parcel.delivery_amount');
                            ?>

                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= strtoupper(Calypso::getValue($teller, 'parcel.waybill_number')); ?></td>
                                <td><span class="currency naira"></span><?= strtoupper(Calypso::getValue($teller, 'parcel.delivery_amount')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($teller, 'parcel.created_date')); ?></td>
                                <td><?= Util::formatDate(ServiceConstant::DATE_FORMAT, Calypso::getValue($teller, 'parcel.created_date')); ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <th style="text-align: right;">Expected Amount:</th>
                        <th style="text-align: left;"><?= $tolal_amount ?></th>

                        <th style="text-align: right;">Submited Amount:</th>
                        <th style="text-align: left;"><?= $amount_paid ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            There are no tellers matching the specified criteria.
        <?php endif; ?>
    </div>
</div>

