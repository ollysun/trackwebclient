<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


/* @var $this yii\web\View */
$this->title = 'Audit Trail';
$this->params['breadcrumbs'] = array(
    array(
    'url' => ['admin/managebranches'],
    'label' => 'Administrator'
    ),
    array('label' => 'Company Registration')
);

?>

<!-- this page specific styles -->

<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                <?= $this->render('../elements/admin/audit_trail_filter'); ?>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if(true) :  //count($auditTrail) ?>
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Action Performed</th>
                    <th>Action Performed by</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal">More details</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
            <?php //= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
            <?php else:  ?>
                There are no parcels matching the specified criteria.
            <?php endif;  ?>
    </div>
</div>

<!-- this page specific scripts -->
<?php
$this->registerJsFile('@web/js/libs/bootstrap-datepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]])?>



<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Audit Trail Details</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-xs-6 form-group">
                <label for="">IP Address</label>
                <div class="form-control-static">127.0.0.1</div>
            </div>
            <div class="col-xs-6 form-group">
                <label for="">User Agent</label>
                <div class="form-control-static">Chrome</div>
            </div>
        </div>
        <table id="data_create" class="table table-bordered">
            <thead>
                <tr>
                    <th style="width:20px">S/N</th>
                    <th>Property</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <table id="data_update" class="table table-bordered hidden">
            <thead>
                <tr>
                    <th style="width:20px">S/N</th>
                    <th>Property</th>
                    <th>Before</th>
                    <th>After</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td></td>
                    <td class="bg-danger">Hello</td>
                    <td class="bg-success">Hell1</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td></td>
                    <td class="bg-danger">Hello</td>
                    <td class="bg-success">Hell1</td>
                </tr>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
