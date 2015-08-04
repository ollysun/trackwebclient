<?php
use yii\helpers\Html;
use Adapter\Globals\ServiceConstant;

/* @var $this yii\web\View */
$this->title = 'New Hub Mapping';
$this->params['breadcrumbs'] = array(
    array('label' => 'New Hub Mapping')
);
?>

<!-- this page specific styles -->
<?= Html::cssFile('@web/css/libs/dataTables.fixedHeader.css') ?>
<?= Html::cssFile('@web/css/libs/dataTables.tableTools.css') ?>

<?php
//$this->params['content_header_button'] = '';
?>
<?php
    function mapIt($from, $to, $check){
        $cat = 6;
        if($from['state']['region_id']!==$to['state']['region_id']){
            $cat = 5;
        }
        elseif($from['state_id']!==$to['state_id']){
            $cat = 4;
        }
        return $cat==$check ? "selected":"";
    }
?>
<div class="main-box">
    <div class="main-box-header">
        <h2>Hub mapping for <?=ucwords($hub['name']);?></h2>
    </div>
    <div class="main-box-body">
        <form class="table-responsive" method="post">
            <table id="table" class="table table-bordered">
                <thead>
                <tr>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Zone</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        $total=count($hubs);
                        for($i=0;$i<$total;$i++){
                            if($hub['id']!=$hubs[$i]['id']){
                            ?>
                    <tr>
                        <?php if($i==0){?><td rowspan="<?=count($hubs)-1;?>"><?=strtoupper($hub['code']);?></td><?php }?>
                        <td><?=ucwords($hubs[$i]['name'])." (".strtoupper($hubs[$i]['code']).")";?></td>
                        <td>
                            <input type="hidden" name="branches[]" value="<?=$hubs[$i]['id'];?>">
                            <select name="zones[]" class="form-control input-sm">
                            <?php
                            if (isset($zones) && is_array(($zones))):
                                foreach ($zones as $zone) { ?>
                                <option value="<?=$zone['id'];?>" <?=mapIt($hub,$hubs[$i],$zone['id']);?>><?=ucwords($zone['name']);?> (<?=$zone['code'];?>)</option>
                                <?php } endif;?>
                            </select>
                        </td>
                    </tr>
                    <?php } }?>
                </tbody>
            </table>
            <div class="clearfix">
                <input type="hidden" name="from_id" value="<?=$hub['id'];?>">
                <button class="pull-right btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- this page specific scripts -->
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.fixedHeader.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/dataTables.tableTools.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/libs/jquery.dataTables.bootstrap.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

