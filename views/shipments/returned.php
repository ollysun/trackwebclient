<?php
use Adapter\Util\Calypso;
use yii\helpers\Url;


$this->title = 'Shipments: Returned';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['shipments/all'],
        'label' => 'Shipments'
    ),
    array('label' => 'Returned')
);

?>

<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <form class="table-search-form form-inline pull-right clearfix">
            <div class="pull-left form-group">
                <label for="searchInput">Search</label><br>

                <div class="input-group input-group-sm input-group-search">
                    <input id="searchInput" type="text" name="search" placeholder="Search by Waybill number"
                           class="search-box form-control">

                    <div class="input-group-btn">
                        <button class="btn btn-default" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div class="pull-left">
            <?= $this->render('../elements/parcels_date_filter', array('from_date' => $from_date, 'to_date' => $to_date)); ?>
        </div>
    </div>
    <div class="main-box-body">
        <?php if (!empty($parcels)) { ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Waybill No.</th>
                        <th>Sender</th>
                        <th>Sender Phone</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($parcels)) {
                        $row = $offset;
                        foreach ($parcels as $parcel) {
                            ?>
                            <tr>
                                <td><?= ++$row; ?></td>
                                <td><?= $parcel['waybill_number']; ?></td>
                                <td><?= ucwords($parcel['sender']['firstname'] . ' ' . $parcel['sender']['lastname']) ?></td>
                                <td><?= $parcel['sender']['phone'] ?></td>
                                <td>
                                    <a href="<?= Url::toRoute(['/shipments/view?waybill_number=' . $parcel['waybill_number']]) ?>"
                                       class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a></td>
                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
                <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'page_width' => $page_width, 'total_count' => $total_count]) ?>
            </div>
        <?php } else { ?>
            There are no shipments that were returned.
        <?php } ?>
    </div>
</div>


