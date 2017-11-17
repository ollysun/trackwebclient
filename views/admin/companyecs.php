<?php
use yii\helpers\Html;
use Adapter\Util\Calypso;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Company Express Centre';
$this->params['breadcrumbs'] = array(
    array('label' => 'Company Express Centre')
);

?>


<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add Company Express Centre</button>';
?>

<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>

<?php echo Calypso::showFlashMessages(); ?>

    <div class="main-box">
        <div class="main-box-header table-search-form">
            <div class="clearfix">
                <div class="pull-right clearfix hide">
                    <form method="get" enctype="application/x-www-form-urlencoded"
                          class="table-search-form form-inline clearfix">
                        <div class="pull-left form-group">
                            <label for="searchInput">Search</label><br>

                            <div class="input-group input-group-sm input-group-search">
                                <input id="searchInput" type="text" name="search" placeholder="Company name"
                                       class="search-box form-control">

                                <div class="input-group-btn">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="main-box-body">
            <div class="table-responsive">
                <?php if (count($companyEcs) > 0) { ?>
                    <table id="table" class="table table-hover dataTable">
                        <thead>
                        <tr>
                            <th style="width: 20px">S/N</th>
                            <th>EC name</th>
                            <th>Associated Company name</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = $offset;
                        foreach ($companyEcs as $companyEc): ?>
                            <tr>
                                <td><?= ++$i; ?></td>
                                <td><?= strtoupper(Calypso::getValue($companyEc, 'branch.name')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($companyEc, 'company.name')); ?></td>
                                <td><?= strtoupper(Calypso::getValue($companyEc, 'created_by.fullname')); ?></td>
                                <td>
                                    <button
                                        data-id="<?=Calypso::getValue($companyEc, 'id'); ?>"
                                        data-company_id="<?=Calypso::getValue($companyEc, 'company_id'); ?>"
                                        data-branch_id="<?=Calypso::getValue($companyEc, 'branch_id'); ?>"
                                        type="button" class="btn btn-default btn-xs editCompanyEc" data-toggle="modal"
                                        data-target="#editModal"><i class="fa fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last' => $i, 'total_count' => $total_count, 'page_width' => $page_width]) ?>
                <?php } else { ?>
                    <div class="alert alert-info text-center" role="alert">
                        <p><strong>No Companies Express Centres created</strong></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form class="validate-form" method="post" action="<?= Url::to('/admin/linkectocompany')?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add a New Company Express Centre</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label for="">Company</label>
                                <select name="company_id" class="form-control validate required">
                                    <option value="">Select Company</option>
                                    <?php foreach($companies as $company):?>
                                        <option value="<?= Calypso::getValue($company, 'id');?>"><?= strtoupper(Calypso::getValue($company, 'name'))?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label for="">Express Centre</label>
                                <select name="branch_id" class="form-control validate required">
                                    <option value="">Select Express Centre</option>
                                    <?php foreach($ecs as $ec):?>
                                        <option value="<?= Calypso::getValue($ec, 'id');?>"><?= strtoupper(Calypso::getValue($ec, 'code') . '  ' . Calypso::getValue($ec, 'name') . ' ' . Calypso::getValue($ec, 'state.name'))?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form class="validate-form" method="post" action="<?= Url::to('/admin/relinkectocompany')?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit Company Express Centre</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label for="">Company</label>
                                <select name="company_id" class="form-control validate required">
                                    <option value="">Select Company</option>
                                    <?php foreach($companies as $company):?>
                                        <option value="<?= Calypso::getValue($company, 'id');?>"><?= strtoupper(Calypso::getValue($company, 'name'))?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label for="">Express Centre</label>
                                <select name="branch_id" class="form-control validate required">
                                    <option value="">Select Express Centre</option>
                                    <?php foreach($ecs as $ec):?>
                                        <option value="<?= Calypso::getValue($ec, 'id');?>"><?= strtoupper(Calypso::getValue($ec, 'code') . '  ' . Calypso::getValue($ec, 'name') . ' ' . Calypso::getValue($ec, 'state.name'))?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input name="id" type="hidden" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/corporate/company_ecs.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

