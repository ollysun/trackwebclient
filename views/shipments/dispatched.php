<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use yii\web\View;


$this->title = 'Shipments: Dispatched';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label'=> 'Dispatched')
);

?>

<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<div class="main-box">
    <?php if(count($parcels) > 0) { ?>
    <div class="main-box-header clearfix">
        <div class="pull-left">

            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#passwordModal">Receive from Dispatcher</button>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#passwordModal"><i class="fa fa-check"></i> Mark as delivered</button>
        </div>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width: 20px"><div class="checkbox-nice"><input id="chbx_w_all" type="checkbox"><label for="chbx_w_all"> </label></div></th>
                    <!-- <th style="width: 20px;"></th> -->
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Receiver</th>
                    <th>Receiver Phone</th>
                    <th>Dispatcher</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($parcels)) {
                        $row = $offset;
                        foreach ($parcels as $parcel) {
                            ?>
                            <tr data-waybill='<?= $parcel['waybill_number'] ?>'>
                                <td><div class="checkbox-nice"><input id="chk_<?= ++$row; ?>" value="<?= $parcel['waybill_number'] ?>" type="checkbox"><label for="chk_<?= $row; ?>"> </label></div></td>
                                <td><?= $row; ?></td>
                                <td><?= $parcel['waybill_number']; ?></td>
                                <td><?= ucwords($parcel['receiver']['firstname'].' '. $parcel['receiver']['lastname']) ?></td>
                                <td><?= $parcel['receiver']['phone'] ?></td>
                                <td><?= ucwords($parcel['holder']['fullname']); ?></td>
                                <td></td>
                                <td><a href="<?= Url::to(['site/viewwaybill?id=' . $parcel['id']]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
            <?= $this->render('../elements/pagination_and_summary',['first'=>$offset,'last'=>$row,'page_width'=>$page_width,'total_count'=>$total_count]) ?>
        </div>
    </div>
    <?php } else { ?>
    <div class="main-box-body">
        <div class="alert alert-info text-center" role="alert">
            There are no parcels that are being delivered.
        </div>
    </div>
    <?php } ?>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="arrived_parcels" class="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Authenticate</h4>
                </div>
                <div class="modal-body">
                    <p>Please enter your password to authenticate this operation.</p>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Authenticate</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
$ex='$("#chbx_w_all").change(function () {
    $("input:checkbox").prop("checked", $(this).prop("checked"));
});';
$this->registerJs($ex,View::POS_READY);
?>
