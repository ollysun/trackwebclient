<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Billing: City - State Mapping';
$this->params['breadcrumbs'] = array(
    array(
        'label' => 'Billing',
        'url' => ['billing/']
    ),
    array('label' => 'City - State Mapping')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
$this->params['content_header_button'] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Map a City</button>';
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
                    <th>Onforwarding Charge(<span class="currency naira"></span>)</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($onForwardingCities) && is_array(($onForwardingCities))):
                    $row = 1;
                    foreach ($onForwardingCities as $onforwardingCity) {
                        ?>
                        <tr>
                        <td><?= $row++; ?></td>
                        <td class="n<?= ucwords(Calypso::getValue($onforwardingCity, 'city.id')); ?>"><?= ucwords(Calypso::getValue($onforwardingCity, 'city.name')); ?></td>
                        <td><?= ucwords(Calypso::getValue($onforwardingCity, 'state.name')); ?></td>
                        <td><?= strtoupper("{$onforwardingCity['branch']['name']} ({$onforwardingCity['branch']['code']})"); ?></td>
                        <td class="t<?= $onforwardingCity['id']; ?>"><?= ucwords(Calypso::getValue($onforwardingCity, 'city.transit_time')); ?></td>
                        <td><?= number_format(Calypso::getValue($onforwardingCity, 'onforwarding_charge.amount')); ?></td>
                        <td>
                            <form method="post" action="<?= Url::to("/billing/unlinkcityfromcharge") ?>">
                                <input type="hidden" name="city"
                                       value="<?= Calypso::getValue($onforwardingCity, 'city.id'); ?>">
                                <input type="hidden" name="charge"
                                       value="<?= Calypso::getValue($onforwardingCity, 'onforwarding_charge.id'); ?>">
                                <button type="submit" data-confirm="true" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete
                                </button>
                            </form>
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
        <form class="" method="post" action="<?= Url::to("/billing/linkcitytocharge") ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Map a City to Onforwarding Charge</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>City</label>
                                <select class="form-control" name="city">
                                    <?php
                                    if (isset($cities) && is_array(($cities))):
                                        foreach ($cities as $city) {
                                            ?>
                                            <option
                                                value="<?= $city['id'] ?>"><?= strtoupper($city['name']); ?></option>
                                            <?php
                                        }
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Onforwarding charge</label>
                        <select class="form-control" name="charge">
                            <?php
                            if (isset($charges) && is_array(($charges))):
                                foreach ($charges as $charge) {
                                    ?>
                                    <option value="<?= $charge['id'] ?>"><?= strtoupper($charge['name']); ?></option>
                                    <?php
                                }
                            endif;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="create">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Map City</button>
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

