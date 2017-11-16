<?php
/**
 * Created by PhpStorm.
 * User: Moses Olalere
 * Date: 10/10/2017
 * Time: 12:28 AM
 */

use Adapter\Util\Calypso;
use Adapter\Globals\ServiceConstant;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

$this->title = 'Cancel wayBill Transactions';
$this->params['breadcrumbs'] = array(
    array(
        ///'url' => ['site/parcels'],
        'label' => 'Cancel Transaction'
    ),
    array('label' => $this->title),
);
?>
<?php echo Calypso::showFlashMessages(); ?>
<form id="cancelTransaction" method="post">
    <div id="cancelForm" class="form-group">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label for="">Waybill Numbers</label>
                    <span class="help-block">You can enter multple waybill no separated by commas (,) or newline</span>
                    <textarea name="waybills" class="form-control validate length"
                              style="height: 30em;"
                              data-validate-length-type='word'
                              data-validate-max-length="100">
                    </textarea>

                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-default">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
        </div>
    </div>
    </div>
</form>

