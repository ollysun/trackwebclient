<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 1/25/2017
 * Time: 8:49 AM
 */

use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use Adapter\ParcelAdapter;
use yii\helpers\Url;
?>

<?php
$this->params['content_header_button'] = $status == 27?'': '<button type="button" class="btn btn-primary" id="submit_btn">Pay Selected</button>';
?>


<?php echo Calypso::showFlashMessages(); ?>

    <div class="main-box">
        <div class="main-box-header table-search-form ">
            <div class="clearfix">
                <div class="pull-right clearfix">

                    <form class="table-search-form form-inline clearfix">
                        <div class="pull-left">
                            <label for="searchInput">Status</label><br>
                            <select name="status" class="form-control">
                                <option value="25" <?= $status == 25? 'selected':'' ?>>AWAITING CLEARANCE</option>
                                <option value="26" <?= $status == 26? 'selected':'' ?>>READY FOR PAYOUT</option>
                                <option value="27" <?= $status == 27? 'selected':'' ?>>PAID</option>
                            </select>
                        </div>

                        <div class="pull-left">
                            <label for="searchInput">Registration Number</label><br>
                            <div class="input-group input-group-sm input-group-search">
                                <input id="searchInput" type="text" name="registration_number" value="<?= isset($registration_number)?$registration_number:'' ?>"
                                       placeholder="Search by Customer registration number" class="search-box form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="main-box-body">
            <?php if(count($payments)) : ?>
                <div class="table-responsive">
                    <form method="post" id="form">
                        <input type="hidden" name="current_status" value="<?= $status ?>">
                        <table id="table" class="table table-hover dataTable">
                            <thead>
                            <tr>
                                <th style="width: 20px" class="datatable-nosort">
                                    <div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label
                                            for="chbx_w_all"> </label></div>
                                </th>
                                <th style="width: 20px">S/N</th>
                                <th>Company Name</th>
                                <th>Registration No.</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(isset($payments) && is_array($payments)){
                                $i = $offset;
                                foreach($payments as $payment){
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="checkbox-nice">
                                                <input id="chbx_w_<?= ++$i; ?>" class="checkable" name="companies[<?= $i ?>]"
                                                       value="<?= strtoupper($payment['id']); ?>"
                                                       data-company-id="<?= strtoupper($payment['id']); ?>"
                                                       data-amount_due="<?= Calypso::getValue($payment, 'amount') ?>"
                                                       type="checkbox"><label for="chbx_w_<?= $i; ?>"> </label>
                                            </div>
                                        </td>
                                        <td><?= $i; ?></td>
                                        <td style="text-align: left;"><?= strtoupper(Calypso::getValue($payment, 'name')); ?></td>
                                        <td><?= strtoupper(Calypso::getValue($payment, 'reg_no')); ?></td>

                                        <td><?= strtoupper(Calypso::getValue($payment, 'email')); ?></td>
                                        <td><?= strtoupper(Calypso::getValue($payment, 'phone_number')); ?></td>

                                        <td><span class="currency naira"></span><?= strtoupper($payment['amount']); ?></td>
                                        <td>
                                            <a href="<?= Url::toRoute(['details?reg_no='.$payment['reg_no'].'&status='.$status]) ?>"
                                               class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View Details</a>

                                        </td>
                                    </tr>
                                    <?php
                                }}
                            ?>

                            </tbody>
                        </table>
                    </form>

                </div>
                <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
            <?php else:  ?>
                No record found
            <?php endif;  ?>
        </div>
    </div>



<?php $this->registerJsFile('@web/js/remittance.js?v=1.0.2', ['depends' => [\app\assets\AppAsset::className()]]) ?>