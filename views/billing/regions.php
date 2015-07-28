<?php
use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Billing: Regions';
$this->params['breadcrumbs'] = array(
    array(
        'label' => 'Billing',
        'url' => ['billing/']
    ),
    array('label' => 'Regions')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#region_myModal"><i class="fa fa-plus"></i> Add a Region</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header">
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table id="table" class="table table-hover ">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($regions) && is_array(($regions))):
                    $row = 1;
                    foreach ($regions as $region) {
                        ?>
                        <tr>
                            <td><?= $row++; ?></td>
                            <td class="n<?= $region['id']; ?>"><?= $region['name']; ?></td>
                            <td class="d<?= $region['id']; ?>"><?= $region['description']; ?></td>
                            <td><?= ($region['active_fg'] == ServiceConstant::ACTIVE ? 'Active' : 'Inactive'); ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                        data-target="#region_editModal" data-id="<?= $region['id']; ?>"><i
                                        class="fa fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-default btn-xs hidden" data-toggle="modal"
                                        data-target="#region_status" data-id="<?= $region['id']; ?>"
                                        data-status="<?= $region['active_fg']; ?>"><i
                                        class="fa fa-edit"></i> Status
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                endif;
                ?>
                </tbody>
            </table>
            <div class="clearfix hidden">
                <div class="pull-left pagination">
                    <?php
                    $pagination = new \yii\data\Pagination(['totalCount' => 190000, 'pageSize' => 30]);
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
<div class="modal fade" id="region_myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate" method="post" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add a New Region</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control required" name="name">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="">Country</label>
                                <select name="country_id" id="country_id" class="form-control required">
                                    <option value="">Choose One</option>
                                    <?php
                                    if (isset($countries) && is_array(($countries))):
                                        foreach ($countries as $country) {
                                            ?>
                                            <option
                                                value="<?= $country['id']; ?>"><?= ucwords($country['name']); ?></option>
                                        <?php }
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="">Activate Region?</label>
                                <select name="status" class="form-control required">
                                    <option value="<?= ServiceConstant::ACTIVE; ?>">Yes</option>
                                    <option value="<?= ServiceConstant::INACTIVE; ?>">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="create">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Region</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="region_editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="" method="post" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit a Region</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control required" name="name">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description"></textarea>
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

<div class="modal fade" id="region_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="" method="post" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit a Region</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Status</label>
                        <select name="status" class="form-control required">
                            <option value="<?= ServiceConstant::INACTIVE; ?>">No</option>
                            <option value="<?= ServiceConstant::ACTIVE; ?>">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <input type="hidden" name="task" value="status">
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
<?php $this->registerJsFile('@web/js/regions.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
