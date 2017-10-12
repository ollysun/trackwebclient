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

$is_hub = isset($branch['branch_type']) && $branch['branch_type'] == ServiceConstant::BRANCH_TYPE_HUB;
$is_admin = isset($branch['branch_type']) && $branch['branch_type'] == ServiceConstant::BRANCH_TYPE_HQ;

?>


<?= Html::cssFile('@web/css/libs/bootstrap-select.min.css') ?>
<?= Html::cssFile('@web/css/libs/select2.css') ?>
<?= Html::cssFile('@web/css/libs/datepicker.css') ?>

<?php echo Calypso::showFlashMessages(); ?>
<form id="shipment_create" action="#" target="async_frame" method="post" enctype="multipart/form-data"
      class="validate-form add-required-asterisks" data-keyboard-submit data-watch-changes>
    <div id="cancelForm" class="form-group">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label for="">Waybill Numbers</label>
                    <textarea name="waybills" class="form-control validate length"
                              data-validate-length-type='word'
                              data-validate-max-length="50">
                    </textarea>

                </div>
                <div class="form-group">
                    <label for="">Company Name</label>
                    <select name="companyName"  class="form-control" id="corporate_select">
                        <option>Choose a Company</option>
                        <?php
                        foreach($companies as $company):?>
                            <option value="<?= Calypso::getValue($company, 'id')?>">
                                <?= strtoupper(Calypso::getValue($company, 'name'))?>
                            </option>
                        <?php endforeach;?>
                    </select>

                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-default">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
        </div>
    </div>
    </div>
</form>

