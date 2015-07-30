<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use \Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'Manage Express Centres';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['site/managebranches'],
        'label' => 'Manage Branches'
    ),
    array('label' => 'Express Centres')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
//$this->params['content_header_button'] = $this->render('../elements/content_header_new_parcel_button');
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header table-search-form">
        <form class="form-inline clearfix" id="filter" method="post">
            <div class="pull-left">
                <?= $this->render('../elements/branch_type_filter', ['branch_type' => 'ec']) ?>
            </div>

            <div class="pull-right clearfix">
                <div class="form-group pull-left">
                    <label for="">Filter by Hub</label><br>
                    <select class="form-control input-sm" id="filter_hub_id" name="filter_hub_id">
                        <option value="">All Express Centres</option>
                        <?php
                        if (isset($hubs) && is_array(($hubs))):
                            foreach ($hubs as $hub) {
                                ?>
                                <option
                                    value="<?= $hub['id']; ?>" <?= ($hub['id'] == $filter_hub_id) ? 'selected' : ''; ?>><?= ucwords($hub['name']) . " (" . strtoupper($hub['code']) . ")"; ?></option>
                            <?php }
                        endif
                        ?>
                    </select>
                </div>
                <div class="pull-left">
                    <label for="">&nbsp;</label><br>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i
                            class="fa fa-plus"></i> Add Express Centre
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="main-box-body">
        <div class="table-responsive">
            <table id="table" class="table table-hover ">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>EC Code</th>
                    <th>EC Name</th>
                    <?php if(empty($filter_hub_id)) {?><th>Parent Hub</th><?php } ?>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($centres) && is_array(($centres))):
                    $count = 1;
                    foreach ($centres as $centre) {
                        ?>
                        <tr class="text-center">
                            <td><?= $count++; ?></td>
                            <td><?= strtoupper($centre['code']); ?></td>
                            <td><?= $centre['name']; ?></td>
                            <?php if(empty($filter_hub_id)) {?><td><?= ucwords($centre['parent']['name']); ?></td><?php } ?>
                            <td><?= $centre['address']; ?></td>
                            <td><?= ($centre['status'] == ServiceConstant::ACTIVE ? 'Active' : 'Inactive'); ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                        data-target="#editModal" data-id="<?= $centre['id']; ?>"><i
                                        class="fa fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                        data-target="#status" data-id="<?= $centre['id']; ?>" data-toggle="tooltip" data-original-title="Change status"><i
                                        class="fa fa-edit"></i> Status
                                </button>
                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#relink" data-id="<?= $centre['id']; ?>" data-toggle="tooltip" data-original-title="Change Parent Hub"><i
                                        class="fa fa-retweet"></i>
                                </button>
                            </td>
                        </tr>
                        <?php
                    }  endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate" method="post" action="#">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add a New Express Centre</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>EC name</label>
                        <input class="form-control required" name="name">
                    </div>
                    <div class="">
                        <div class=""></div>
                    </div>
                    <div class="form-group">
                        <label>Parent Hub</label>
                        <select class="form-control required" name="hub_id" id="hub_id">
                            <option value="">Select One</option>
                            <?php
                            if (isset($hubs) && is_array(($hubs))):
                                foreach ($hubs as $hub) {
                                    ?>
                                    <option
                                        value="<?= $hub['id']; ?>"><?= ucwords($hub['name']) . " (" . strtoupper($hub['code']) . ")"; ?></option>
                                    <?php
                                }
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                    <div class="form-group hidden">
                        <label>Activate EC?</label>
                        <select class="form-control" name="status">
                            <option value="<?= ServiceConstant::ACTIVE ?>">Active</option>
                            <option value="<?= ServiceConstant::INACTIVE ?>">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="create">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create EC</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate" method="post" action="#">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit an Express Centre</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Activate EC?</label>
                        <select class="form-control" name="status">
                            <option value="<?= ServiceConstant::ACTIVE ?>">Active</option>
                            <option value="<?= ServiceConstant::INACTIVE ?>">Inactive</option>
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

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit an Express Centre</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>EC name</label>
                        <input class="form-control" name="name">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <select class="form-control" name="state_id">
                            <?php
                            if(isset($States) && is_array(($States))):
                                foreach($States as $state){
                                    ?>
                                    <option value="<?= $state['id'] ?>"><?= strtoupper($state['name']); ?></option>
                                    <?php
                                }
                            endif;
                            ?>
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

<div class="modal fade" id="relink" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="validate" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Change Parent Hub</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Parent Hub</label>
                        <select class="form-control required" name="hub_id" id="hub_id">
                            <option value="">Select One</option>
                            <?php
                            if (isset($hubs) && is_array(($hubs))):
                                foreach ($hubs as $hub) {
                                    ?>
                                    <option
                                        value="<?= $hub['id']; ?>"><?= ucwords($hub['name']) . " (" . strtoupper($hub['code']) . ")"; ?></option>
                                    <?php
                                }
                            endif;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <input type="hidden" name="task" value="relink">
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
<?php $this->registerJsFile('@web/js/manage_branches.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>


