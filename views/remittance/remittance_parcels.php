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
//$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" id="submit_btn">Pay Selected</button>';
?>


<?php echo Calypso::showFlashMessages(); ?>

    <div class="main-box">
        <div class="main-box-header table-search-form ">
            <div class="clearfix">
                <div class="pull-right clearfix">

                </div>
            </div>
        </div>
        <div class="main-box-body">
            <?php if(count($parcels)) : ?>
                <div class="table-responsive">
                    <form method="post" id="form">
                        <table id="table" class="table table-hover dataTable">
                            <thead>
                            <tr>
                                <th style="width: 20px">S/N</th>
                                <th>Waybill Number</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(isset($parcels) && is_array($parcels)){
                                $i = 0;
                                foreach($parcels as $parcel){
                                    ?>
                                    <tr>
                                        <td><?= (++$i); ?></td>
                                        <td><?= strtoupper(Calypso::getValue($parcel, 'waybill_number')); ?></td>
                                        <td><?= strtoupper(Calypso::getValue($parcel, 'amount')); ?></td>
                                    </tr>
                                    <?php
                                }}
                            ?>

                            </tbody>
                        </table>
                    </form>

                </div>
            <?php else:  ?>
                No record found
            <?php endif;  ?>
        </div>
    </div>