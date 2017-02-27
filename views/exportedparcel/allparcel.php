<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;

/* @var $this yii\web\View */
$this->title = 'Exported Parcel';
$this->params['breadcrumbs'][] = 'ExportedParcel';


$link = "";
if(@$search){
    $fro = date('Y/m/d',strtotime($from_date));
    $to = date('Y/m/d',strtotime($to_date));
    $link = "&search=true&to=".urlencode($to)."&from=".urlencode($fro)."&page_width=".$page_width;
    if(!is_null($filter)){$link.= '&date_filter='. $filter;}
}
$user_data = $this->context->userData;

?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>


<?php echo Calypso::showFlashMessages(); ?>

<div class="main-box">
    <div class="main-box-header table-search-form ">
        <div class="clearfix">
            <div class="pull-left">
                <?= $this->render('../elements/exportedparcels_filter',['from_date'=>$from_date,'to_date'=>$to_date,'page_width'=>$page_width, 'hideStatusFilter'=>false, 'agent_assignment' => $agent_assignment,'agents'=>$agents]) ?>
            </div>
            <div class="pull-right clearfix">

<!--                <form class="table-search-form form-inline clearfix">-->
<!--                    <div class="pull-left">-->
<!--                        <label for="searchInput">Search</label><br>-->
<!--                        <div class="input-group input-group-sm input-group-search">-->
<!--                            <input id="searchInput" type="text" name="search" placeholder="Search by Waybill or Reference No." class="search-box form-control">-->
<!--                            <div class="input-group-btn">-->
<!--                                <button class="btn btn-default" type="submit">-->
<!--                                    <i class="fa fa-search"></i>-->
<!--                                </button>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </form>-->
                <div class="pull-left hidden">
                    <label>&nbsp;</label><br>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Select an action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-box-body">
        <?php if(count($parcels)) : ?>
        <div class="table-responsive">
            <table id="table" class="table table-hover dataTable">
                <thead>
                <tr>
                    <th style="width: 20px">S/N</th>
                    <th>Waybill No.</th>
                    <th>Reference No.</th>
                    <th>Request Type</th>
                    <th>Created Date</th>
                    <th>Pieces</th>
                    <th>Destination Country</th>
                    <th><?php if(($agent_assignment == 'Assigned') or empty($agent_assignment)):?>
                        Agent
                    <?php endif?>
                    </th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php

                if(isset($parcels) && is_array($parcels)){

                    $i = $offset;
                    foreach($parcels as $parcel){
                        //dd($parcel['exportedParcel']['id']);
                        //dd($parcel['reference_number']);
                        ?>
                        <tr>
                            <td><?= ++$i; ?></td>
                            <td><?= strtoupper($parcel['waybill_number']); ?></td>
                            <td><?= strtoupper($parcel['reference_number']); ?></td>
                            <td><?= ServiceConstant::getRequestType($parcel['request_type']) ?></td>
                            <td><?= date(ServiceConstant::DATE_TIME_FORMAT,strtotime($parcel['created_date'])); ?></td>
                            <td><?= $parcel['no_of_package']; ?></td>
                            <td><?= Calypso::getValue($parcel, 'country.name') ?></td>
                            <td><?php if((isset($parcel["exportedParcel"]["parcel_id"]) && $parcel["exportedParcel"]["parcel_id"]==$parcel["id"])):?>
                                <?= Calypso::getValue($parcel, 'agent.name') ?>

                            <?php endif;?>

                                <?php if(!(isset($parcel["exportedParcel"]["parcel_id"]) && $parcel["exportedParcel"]["parcel_id"]==$parcel["id"])):?>

                                    <button type="button" class="OpenDialog btn btn-sx btn-danger" data-toggle="modal" data-target="#myModal" data-id="<?= $parcel['id']; ?>" data-ref_no="<?= $parcel['reference_number']; ?>"><i class="fa fa-user">&nbsp;</i>Assign</button>

                                <?php endif;?>

                            </td>
                            <td>
                                <?php if((isset($parcel["exportedParcel"]["parcel_id"]) && $parcel["exportedParcel"]["parcel_id"]==$parcel["id"])):?>

                                    <button type="button" class="OpenDialogTrack btn btn-sx btn-danger" data-toggle="modal" data-target="#myModalTrack" data-id="<?= $parcel['exportedParcel']['id']; ?>" data-ref_no="<?= $parcel['reference_number']; ?>"><i class="fa fa-user">&nbsp;</i>Update status</button>

                                <?php endif;?>
                                <a href="<?= Url::toRoute(['/shipments/view?waybill_number='.$parcel['waybill_number']]) ?>" class="btn btn-xs btn-default"><i class="fa fa-eye">&nbsp;</i> View</a>
                            </td>
                        </tr>
                    <?php
                    }}
                ?>

                </tbody>
            </table>
        </div>
            <?= $this->render('../elements/pagination_and_summary', ['first' => $offset, 'last'=>$i, 'total_count'=> $total_count,'page_width'=>$page_width]) ?>
            <?php else:  ?>
                There are no parcels matching the specified criteria.
            <?php endif;  ?>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="form-horizontal" method="post" action="allparcel">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Link agent to exported parcel. <span style="font-weight: bold;font-size: 14px;">Reference No.: <span id="parcel-number"></span></span></h4>
                </div>

                <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-xs-1"></div>
                                <div class="col-xs-5">
                                    <label>Agents</label>
                                    <div class="list-group">
                                        <select name="agent_id" id="" class="form-control  filter-status">
                                            <?php
                                            if (isset($agents) && is_array($agents)) {
                                                foreach ($agents as $agent) {
                                                    ?>
                                                    <option
                                                            value="<?= $agent['id'] ?>"><?= strtoupper($agent['name']); ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <label id="loading_label"></label>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <label>Reference No</label>
                                    <div class="input-group">
                                        <input id="agent_tracking_number" name="agent_tracking_number" value="" class="form-control">
                                    </div>
                                    <div class="input-group">
                                        <label id="loading_label"></label>
                                    </div>
                                </div>

                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="assign_agent">
                    <input type="hidden" name="parcel_id" id="parcelId" value=""/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="arrived_parcels_btn" type="submit" class="btn btn-primary">Assign</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="myModalTrack" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form class="form-horizontal" method="post" action="allparcel">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Status Information. <span style="font-weight: bold;font-size: 14px;">Reference No.: <span id="parcel-number"></span></span></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-5">
                                <div class="pull-left form-group form-group-sm">
                                    <label for="">Date:</label><span style="font-size: 10px">(supply date)</span><br>
                                    <div class="input-group input-group-date-range">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="commentdate" id="" class="form-control date-range" value="<?=  date('Y/m/d', strtotime($to_date));?>" placeholder="DD/MM/YYYY">
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label id="loading_label"></label>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="pull-left form-group form-group-sm">
                                    <label for="">Time:</label><span style="font-size: 10px">(supply time)</span><br>
                                    <div class="input-group input-group-date-range">
                                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                        <?php date_default_timezone_set('Africa/Lagos'); ?>
                                        <input type="text" name="commenttime" id="" class="form-control" value="<?=  date('g:i a');?>" placeholder="HH:MM:am/pm">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-11">
                                <div class="pull-left form-group form-group-sm">
                                    <label for="">Comment:</label><br>
                                    <div class="input-group">
                                        <textarea class="form-control" cols="50" rows="3" name="comment"></textarea>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label id="loading_label"></label>
                                </div>
                            </div>
                            </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="task" value="track_entering">
                    <input type="hidden" name="exportedparcel_id" id="exportparcelId" value=""/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="arrived_parcels_btn" type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/hub_util.js', ['depends' => [\app\assets\AppAsset::className()]])?>
<?php $this->registerJsFile('@web/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/table.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/validate.js', ['depends' => [\app\assets\AppAsset::className()]])?>

<?php $this->registerJsFile('@web/js/submit_teller.js?v=1.0.3', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/get_parcel_id.js?v=1.0.3', ['depends' => [\app\assets\AppAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/parcel_pod.js?v=1.0.0', ['depends' => [\app\assets\AppAsset::className()]]) ?>
