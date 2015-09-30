<?php
use yii\helpers\Html;
use Adapter\Util\Calypso;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Companies Registration';
$this->params['breadcrumbs'] = array(
    /*array(
    'url' => ['site/managebranches'],
    'label' => 'Administrator'
    ),*/
    array('label' => 'Company Registration')
);

?>


<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add Company</button>';
?>

<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>

<?php echo Calypso::showFlashMessages(); ?>

    <div class="main-box">
        <div class="main-box-header table-search-form">
            <div class="clearfix">
                <div class="pull-right clearfix">
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
                <?php if (count($companies) > 0) { ?>
                    <table id="table" class="table table-hover ">
                        <thead>
                        <tr>
                            <th style="width: 20px">S/N</th>
                            <th>Company name</th>
                            <th>Email</th>
                            <th>Phone number</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($companies as $company): $i = $offset; ?>
                            <tr>
                                <td><?= ++$i; ?></td>
                                <td><?= strtoupper(Calypso::getValue($company, 'name')); ?></td>
                                <td><?= Calypso::getValue($company, 'email'); ?></td>
                                <td><?= Calypso::getValue($company, 'phone_number'); ?></td>
                                <td><?= Calypso::getValue($company, 'address'); ?></td>
                                <td>
                                    <a href="<?= Url::to(['admin/viewcompany?id=' . Calypso::getValue($company, 'id')]); ?>"
                                       class="btn btn-xs btn-default"><i
                                            class="fa fa-eye">&nbsp;</i> View</a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-default btn-xs hide" data-toggle="modal"
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
                        <p><strong>No Companies created</strong></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form class="validate-form" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add a New Company</h4>
                    </div>
                    <div class="modal-body">
                        <fieldset>
                            <legend>Company Details</legend>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Name</label>
                                    <input type="text" name="company[name]" class="form-control validate required name">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Registration No</label>
                                    <input type="text" name="company[reg_no]" class="form-control validate required">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Email address</label>
                                    <input type="text" name="company[email]"
                                           class="form-control validate required email">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Phone number</label>
                                    <input type="text" name="company[phone_number]"
                                           class="form-control validate required phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="company[address]" class="form-control validate required">
                            </div>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">State</label>
                                    <select id="state" class="form-control validate required">
                                        <option value="" selected>Select State</option>
                                        <?php foreach (Calypso::getValue($locations, 'states', []) as $state): ?>
                                            <option
                                                value="<?= Calypso::getValue($state, 'id', '') ?>"><?= strtoupper(Calypso::getValue($state, 'name', '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">City</label>
                                    <select name="company[city_id]" id="city" class="form-control validate required">
                                        <option value="" selected>Select a State</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                        <br>

                        <div class="row">
                            <fieldset class="col-xs-6">
                                <legend>Relationship Officer</legend>
                                <div class="row">
                                    <div class="col-xs-4 form-group">
                                        <label for="">Staff ID</label>
                                        <input type="text" id="staff" class="form-control validate required">
                                        <input id="staffId" type="hidden" name="company[relations_officer_id]"/>
                                    </div>
                                    <div class="col-xs-4 form-group">
                                        </br>
                                        <button type="button" id="loadStaff" class="btn btn-primary btn-xs">Load
                                        </button>
                                    </div>
                                    <div class="col-xs-4 form-group">
                                        </br>
                                        <p id="staffName"></p>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="col-xs-6 hide">
                                <legend>Business Offers</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">Credit Limit <span class="currency naira"></span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Discount (%)</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <br>

                        <div class="row">
                            <fieldset class="col-xs-6">
                                <legend>Primary Contact</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">First name</label>
                                        <input name="primary_contact[firstname]" type="text"
                                               class="form-control validate required name">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Last name</label>
                                        <input name="primary_contact[lastname]" type="text"
                                               class="form-control validate required name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label for="">Email address</label>
                                        <input name="primary_contact[email]" type="text"
                                               class="form-control validate required email">
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label for="">Phone number</label>
                                        <input name="primary_contact[phone_number]" type="text"
                                               class="form-control validate required phone">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="col-xs-6">
                                <legend>Secondary Contact
                                    <small>(optional)</small>
                                </legend>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <label for="enableSecondaryContact">Secondary Contact Available?</label>
                                        <input type="checkbox" id="enableSecondaryContact"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">First name</label>
                                        <input name="secondary_contact[firstname]" data-secondary_contact disabled
                                               type="text" class="form-control name">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Last name</label>
                                        <input name="secondary_contact[lastname]" data-secondary_contact disabled
                                               type="text" class="form-control name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label for="">Email address</label>
                                        <input name="secondary_contact[email]" data-secondary_contact disabled
                                               type="text" class="form-control email">
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label for="">Phone number</label>
                                        <input name="secondary_contact[phone_number]" data-secondary_contact disabled
                                               type="text" class="form-control phone">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form class="">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit Company</h4>
                    </div>
                    <div class="modal-body">
                        <fieldset>
                            <legend>Company Details</legend>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Name</label>
                                    <input type="text" class="form-control validate required name">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Registration No</label>
                                    <input type="text" class="form-control validate required">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Email address</label>
                                    <input type="text" class="form-control validate required email">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Phone number</label>
                                    <input type="text" class="form-control validate required phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" class="form-control validate required">
                            </div>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">State</label>
                                    <select name="" id="" class="form-control validate required"></select>
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">City</label>
                                    <select name="" id="" class="form-control validate required"></select>
                                </div>
                            </div>
                        </fieldset>
                        <br>

                        <div class="row">
                            <fieldset class="col-xs-6">
                                <legend>Relationship Officer</legend>
                                <div class="row">
                                    <div class="col-xs-4 form-group">
                                        <label for="">Staff ID</label>
                                        <input type="text" class="form-control validate required">
                                    </div>
                                    <div class="col-xs-4 form-group">
                                        <label for="">Name</label>
                                        <input type="text" readonly="readonly" class="form-control validate required">
                                    </div>
                                    <div class="col-xs-4 form-group">
                                        <label for="">Email address</label>
                                        <input type="text" readonly="readonly" class="form-control validate required">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="col-xs-6 hide">
                                <legend>Business Offers</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">Credit Limit <span class="currency naira"></span></label>
                                        <input type="text" class="form-control number">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Discount (%)</label>
                                        <input type="text" class="form-control number">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <br>

                        <div class="row">
                            <fieldset class="col-xs-6">
                                <legend>Primary Contact</legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">First name</label>
                                        <input type="text" class="form-control validate required name">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Last name</label>
                                        <input type="text" class="form-control validate required name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label for="">Email address</label>
                                        <input type="text" class="form-control validate required email">
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label for="">Phone number</label>
                                        <input type="text" class="form-control validate required phone">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="col-xs-6">
                                <legend>Secondary Contact
                                    <small>(optional)</small>
                                </legend>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label for="">First name</label>
                                        <input data-secondary_contact="true" type="text"
                                               class="form-control validate name">
                                    </div>
                                    <div class="col-xs-6 form-group">
                                        <label for="">Last name</label>
                                        <input data-secondary_contact="true" type="text"
                                               class="form-control validate name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label for="">Email address</label>
                                        <input data-secondary_contact="true" type="text"
                                               class="form-control validate email">
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label for="">Phone number</label>
                                        <input data-secondary_contact="true" type="text"
                                               class="form-control validate phone">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/companies.js', ['depends' => [\app\assets\AppAsset::className()]]) ?>