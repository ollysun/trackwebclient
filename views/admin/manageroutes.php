<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Manage Routes';
$this->params['breadcrumbs'] = array(

    array('label' => 'Manage Routes')
);
?>

<!-- this page specific styles -->

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add a Route</button>';
?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box">
    <div class="main-box-header table-search-form clearfix">
        <div class=" clearfix">
            <div class="pull-right clearfix">
                <form class="form-inline clearfix">
                    <div class="pull-left form-group">
                        <label for="searchInput">Search</label><br>
                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="search" placeholder="Route name" class="search-box form-control">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="pull-left hidden">
                    <label>&nbsp;</label><br>
                    <button type="button" class="btn btn-sm btn-default"><i class="fa fa-download"></i> Download</button>
                </div>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if ($total_count > 0) : ?>
            <div class="table-responsive">
                <table id="table" class="table table-hover dataTable">
                    <thead>
                    <tr>
                        <th style="width: 20px">S/N</th>
                        <th>Route Name</th>
                        <th>Hub</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $row = $offset;
                    if (isset($routes) && is_array(($routes))):
                        foreach ($routes as $route) {
                            ?>
                            <tr>
                            <td><?= ++$row; ?></
                            <td></td>
                            <td class="n<?= ucwords($route['id']); ?>"><?= ucwords($route['name']); ?></td>
                            <td><?= strtoupper("{$route['branch_name']} ({$route['branch_code']})"); ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                        data-target="#editModal" data-id="<?= $route['id']; ?>"
                                        data-branch-id="<?= $route['branch_id']; ?>"><i class="fa fa-edit"></i> Edit
                                </button>
                            </td>
                            </tr><?php
                        }
                    endif;
                    ?>
                    </tbody>
                </table>
            </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $row, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
        <?php else: ?>
            There are no Routes matching the specified name.
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add a New Route</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-8 form-group">
                            <label>Route Name</label>
                            <input class="form-control" name="route_name">
                        </div>
                        <div class="col-xs-4 form-group">
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
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="create">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Route</button>
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
                    <h4 class="modal-title" id="myModalLabel">Edit a Route</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-8 form-group">
                            <label>Route Name</label>
                            <input class="form-control required" name="route_name">
                        </div>
                        <div class="col-xs-4 form-group">
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

<?php $this->registerJsFile('@web/js/routes.js', ['depends' => [\app\assets\AppAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>


