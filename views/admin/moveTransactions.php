<?php
/**
 * Created by PhpStorm.
 * User: Moses Olalere
 * Date: 11/3/2017
 * Time: 1:33 PM
 */
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


/* @var $this yii\web\View */
$this->title = 'Move Transactions';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['admin/movetransactions'],
        'label' => 'Administrator'
    ),
    array('label' => $this->title)
);
?>
<?php echo Calypso::showFlashMessages(); ?>

<div class="row">
    <form id="moveTransaction" method="post">
        <div id="cancelForm" class="form-group">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="">Waybill Numbers</label>
                        <span class="help-block">You can enter multple waybill no separated by commas (,) or newline</span>
                        <textarea name="waybills" class="form-control validate length"
                                  style="height: 30em;"
                                  data-validate-length-type='word'
                                  data-validate-max-length="50">
                    </textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Company Name</label>
                        <select name="companyId" id="companyId"  class="form-control" id="corporate_select">
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
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
