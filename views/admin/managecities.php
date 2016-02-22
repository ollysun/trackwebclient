<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Manage Cities';
$this->params['breadcrumbs'] = array(
    array(
        'label' => 'Admin',
        'url' => ['admin/']
    ),
    array('label' => 'Manage Cities')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add a City</button>';
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
                    <th>City Name</th>
                    <th>State</th>
                    <th>Hub</th>
                    <th>Transit Time</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($cities) && is_array(($cities))):
                    $row = 1;
                    foreach ($cities as $city) {
                        ?>
                        <tr>
                        <td><?= $row++; ?></td>
                        <td class="n<?= ucwords(Calypso::getValue($city, 'id')); ?>"><?= ucwords(Calypso::getValue($city, 'name')); ?></td>
                        <td><?= ucwords(Calypso::getValue($city, 'state.name')); ?></td>
                        <td><?= strtoupper("{$city['branch']['name']} ({$city['branch']['code']})"); ?></td>
                        <td class="t<?=$city['id'];?>"><?= ucwords(Calypso::getValue($city, 'transit_time')); ?></td>
                        <td>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                    data-target="#editModal" data-id="<?= Calypso::getValue($city, 'id'); ?>"
                                    data-state-id="<?= Calypso::getValue($city, 'state.id'); ?>" data-branch-id="<?= Calypso::getValue($city, 'branch_id'); ?>"><i class="fa fa-edit"></i> Edit
                            </button>
                        </td>
                        </tr><?php
                    }
                endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="" method="post" action="<?= Url::to("/admin/savecity")?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add a New City</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>City Name</label>
                        <input class="form-control" name="city_name">
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>State</label>
                                <select class="form-control" name="state">
                                    <?php
                                    if (isset($states) && is_array(($states))):
                                        foreach ($states as $state) {
                                            ?>
                                            <option value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
                                            <?php
                                        }
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Hub</label>
                                <select class="form-control" name="branch_id">
                                    <?php
                                    if (isset($hubs) && is_array(($hubs))):
                                        foreach ($hubs as $hub) {
                                            ?>
                                            <option value="<?= $hub['id'] ?>"><?= strtoupper($hub['name']); ?></option>
                                            <?php
                                        }
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Transit Time</label>
                                <input type="text" class="form-control" name="transit_time">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="create">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add City</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate" method="post" action="<?= Url::to("/admin/editcity")?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit City</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>City Name</label>
                        <input class="form-control required" name="city_name">
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>State</label>
                                <select class="form-control" name="state">
                                    <?php
                                    if (isset($states) && is_array(($states))):
                                        foreach ($states as $state) {
                                            ?>
                                            <option value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
                                            <?php
                                        }
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Hub</label>
                                <select class="form-control" name="branch_id">
                                    <?php
                                    if (isset($hubs) && is_array(($hubs))):
                                        foreach ($hubs as $hub) {
                                            ?>
                                            <option value="<?= $hub['id'] ?>"><?= strtoupper($hub['name']); ?></option>
                                            <?php
                                        }
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Transit Time</label>
                                <input type="text" class="form-control" name="transit_time">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="edit">
                    <input type="hidden" name="id" value="">
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
<?php $this->registerJsFile('@web/js/city.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>

