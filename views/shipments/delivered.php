<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;


$this->title = 'Shipments: Delivered';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label'=> 'Delivered')
);

?>

<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class="pull-left">
            <?= $this->render('../elements/parcels_date_filter', array('from_date'=>$from_date, 'to_date'=>$to_date)); ?>
        </div>
    </div>
    <div class="main-box-body">
        <?php if(!empty($parcels)) { ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <!-- <th style="width: 20px;"></th> -->
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Sender</th>
                    <th>Sender Phone</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($parcels)) {
                    $row = $offset;
                    $i = 1;
                    foreach ($parcels as $parcel) {
                        ?>
                        <tr>
                           <td><?= ++$row; ?></td>
                            <td><?= $parcel['waybill_number']; ?></td>
                            <td><?= ucwords($parcel['sender']['firstname'].' '. $parcel['sender']['lastname']) ?></td>
                            <td><?= $parcel['sender']['phone'] ?></td>
                            <td><?= ucwords($parcel['receiver']['firstname'].' '. $parcel['receiver']['lastname']) ?></td>
                            <td><?= $parcel['receiver']['phone'] ?></td>
                            <td><?= ServiceConstant::getStatus($parcel['status']); ?></td>
                            <td><a href="<?= Url::to(['shipments/view?waybill_number=' . $parcel['waybill_number']]) ?>"
                                   class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                        </tr>
                    <?php }
                } ?>
                </tbody>
            </table>
            <?= $this->render('../elements/pagination_and_summary',['first'=>$offset,'last'=>$row,'page_width'=>$page_width,'total_count'=>$total_count]) ?>
        </div>
        <?php } else { ?>
            There are no shipments that were delivered.
        <?php } ?>
    </div>
</div>


