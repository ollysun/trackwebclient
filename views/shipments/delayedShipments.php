<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


/* @var $this yii\web\View */
$this->title = 'Delayed Shipments';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['admin/managebranches'],
        'label' => 'Administrator'
    ),
    array('label' => $this->title)
);

?>

<!-- this page specific styles -->

<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                <?= $this->render('../elements/shipments/delayed_shipments_filter', ['branches' => $branches]); ?>
            </div>
        </div>
    </div>


    <div class="main-box-body">
        <?php if(true) :  //count($auditTrail) ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill Number</th>
                        <th>From Branch</th>
                        <th>To Branch</th>
                        <th>Driver</th>
                        <th>Takeoff Time</th>
                        <th>Arrival Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sn = 0;
                    foreach ($shipments as $exception) {?>
                        <tr>
                            <td><?=(++$sn) ?></td>
                            <td>
                                <a href='/shipments/view?waybill_number=<?= Calypso::getValue($exception, 'waybill_number'); ?>'>
                                    <?= Calypso::getValue($exception['parcel'], 'waybill_number') ?></a>
                            </td>
                            <td><?=$exception['from_branch']['name'] ?></td>
                            <td><?=$exception['to_branch']['name'] ?></td>
                            <td><?=$exception['holder']['fullname'] ?></td>
                            <td><?=$exception['start_date_time'] ?></td>
                            <td><?=isset($exception['end_date_time'])?$exception['end_date_time']:"Not Arrived" ?></td>

                        </tr>

                    <?php }?>


                    </tbody>
                </table>
            </div>
            <?php //= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
        <?php else:  ?>
            There are no delayed shipments for the specified criteria.
        <?php endif;  ?>
    </div>
</div>

<!-- this page specific scripts -->
<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]])?>

