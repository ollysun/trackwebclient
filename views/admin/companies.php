<?php
use yii\helpers\Html;
use Adapter\Util\Calypso;

/* @var $this yii\web\View */
$this->title = 'Companies Registration';
$this->params['breadcrumbs'] = array(
	/*array(
	'url' => ['site/managebranches'],
	'label' => 'Administrator'
	),*/
	array('label'=> 'Company Registration')
);

?>


<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Add Company</button>';
?>

<?=Html::cssFile('@web/css/libs/bootstrap-select.min.css')?>

<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form">
        <div class="clearfix">
            <div class="pull-right clearfix">
                <form method="get" enctype="application/x-www-form-urlencoded" class="table-search-form form-inline clearfix">
                    <div class="pull-left form-group">
                        <label for="searchInput">Search</label><br>

                        <div class="input-group input-group-sm input-group-search">
                            <input id="searchInput" type="text" name="search" placeholder="Company name" class="search-box form-control">
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
            <?php if(true) { // count > 0 ?>
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
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal"
                                    data-target="#editModal"><i class="fa fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php } else {  ?>
                <div class="alert alert-info text-center" role="alert">
                    <p><strong>No Companies created</strong></p>
                </div>
            <?php }  ?>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="validate-form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add a New Company</h4>
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
                                <select name="" id="" class="form-control validate required">
                                    <?php foreach($states as $state):?>
                                        <option value="<?= Calypso::getValue($state, 'id', '')?>"><?= strtoupper(Calypso::getValue($state, 'name', ''));?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-xs-6 form-group">
                                <label for="">City</label>
                                <select name="" id="" class="form-control validate required">
                                    <?php foreach($cities as $city):?>
                                        <option value="<?= Calypso::getValue($city, 'id', '')?>"><?= strtoupper(Calypso::getValue($city, 'name', ''));?></option>
                                    <?php endforeach; ?>
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
                        <fieldset class="col-xs-6">
                            <legend>Business Offers</legend>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Credit Limit <span class="currency naira"></span></label>
                                    <input type="text" class="form-control validate required number">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Discount (%)</label>
                                    <input type="text" class="form-control validate number">
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
                            <legend>Secondary Contact <small>(optional)</small></legend>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                        <fieldset class="col-xs-6">
                            <legend>Business Offers</legend>
                            <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label for="">Credit Limit <span class="currency naira"></span></label>
                                    <input type="text" class="form-control validate required number">
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label for="">Discount (%)</label>
                                    <input type="text" class="form-control validate number">
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
                            <legend>Secondary Contact <small>(optional)</small></legend>
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


