<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 12/7/2016
 * Time: 9:56 AM
 */

use yii\helpers\Html;
use yii\web\JqueryAsset;
use app\assets\AppAsset;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use Adapter\Globals\ServiceConstant;
use yii\helpers\Url;


$this->title = 'Batch Discount';
$this->params['breadcrumbs'] = array(
    array('label' => 'Finance')
);

?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Discount Settings</div>

            <div class="panel-body">
                <form class="form-group" enctype="multipart/form-data" method="post">
                <div class="col-md-3">
                        <label class="">Upload Batched CSV</label>:
                        <div class="form-control">
                            <input type="file" name="batchcsv"  >
                        </div>
                        <hr>
                        <label class="">Initial Discount Handling</label>:
                        <div class="">
                            <input type="radio" name="set" value="0" checked > Preserve Initial Discount <br>
                            <input type="radio" name="set" value="1"> Override Initial Discount
                        </div>
                        <br>
                        <button type="submit" class="btn btn-success">Submit</button>

                </div>
                <div class="col-md-3">
                    <label>
                        Invoice Action
                    </label>
                    <select name="update_invoice" class="form-control-transparent">
                        <option value="0">No, do not update Invoice</option>
                        <option value="1">Yes, please update Invoice</option>
                    </select> ⇳
                </div>
                </form>
                <div class="col-md-3 pull-right">
                    <a href="samplecsv"> <button class="btn-primary">Download CSV Sample</button> </a>
                </div>
            </div>
        </div>

