<?php
/**
 * Created by Ademu Anthony.
 * User: ELACHI
 * Date: 10/30/2016
 * Time: 11:30 AM
 */

use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Admin: Business Managers';
$this->params['breadcrumbs'] = array(
    array('label' => 'Business Managers')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bm_myModal"><i class="fa fa-plus"></i> Add a Business Manager</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header">
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th style="text-align: left;">Name</th>
                    <th style="text-align: left;">Region</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($business_managers) && is_array(($business_managers))):
                    $row = 1;
                    foreach ($business_managers as $business_manager) {
                        ?>
                        <tr>
                            <td><?= $row++; ?></td>
                            <td style="text-align: left;" class="n<?= $business_manager['id']; ?>"><?= ucwords($business_manager['name']); ?></td>
                            <td style="text-align: left;" class="d<?= $business_manager['id']; ?>"><?= ucwords($business_manager['region_name']); ?></td>

                            <td><?= ($business_manager['status'] == ServiceConstant::ACTIVE ? 'Active' : 'Inactive'); ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                        data-target="#bm_editModal" data-id="<?= $business_manager['id']; ?>" data-region-id="<?= $business_manager['region_id'] ?>" data-staff-id="<?= $business_manager['staff_id'] ?>"><i
                                        class="fa fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                endif;
                ?>
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-left pagination">
                    <?php
                    $pagination = new \yii\data\Pagination(['totalCount' => $total_count, 'pageSize' => 30]);
                    echo "Displaying <strong>" . ($pagination->offset + 1) . "</strong> to " . ($pagination->offset + $pagination->limit) . " of {$pagination->totalCount}";
                    ?>
                </div>
                <div class="pull-right">
                    <?php
                    echo \yii\widgets\LinkPager::widget(['pagination' => $pagination]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="bm_myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate" method="post" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add a New BM</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Staff Id</label>
                        <input class="form-control required" name="staff_id">
                    </div>

                    <div class="form-group">
                        <label>Region</label>
                        <select class="form-control" name="region_id">
                            <?php foreach ($regions as $region):?>
                            <option value="<?=$region['id']?>"><?= $region['name']?></option>
                            <?php endforeach;?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="create">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add BM</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="bm_editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="" method="post" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit a BM</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Staff Id</label>
                        <input readonly id="staff_id" class="form-control required" name="staff_id">
                    </div>

                    <div class="form-group">
                        <label>Region</label>
                        <select class="form-control" id="region_id" name="region_id">
                            <?php foreach ($regions as $region):?>
                                <option value="<?=$region['id']?>"><?= $region['name']?></option>
                            <?php endforeach;?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <input type="hidden" name="task" value="edit">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/business_manager.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>