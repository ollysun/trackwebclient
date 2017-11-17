<?php
/**
 * Created by PhpStorm.
 * User: Moses Olalere
 * Date: 11/4/2017
 * Time: 11:18 PM
 */
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;


/* @var $this yii\web\View */
$this->title = 'Reprice Parcel';
$this->params['breadcrumbs'] = array(
    array(
        'url' => ['admin/repriceparcels'],
        'label' => 'Administrator'
    ),
    array('label' => $this->title)
);
?>
<?php echo Calypso::showFlashMessages(); ?>

<div class="row">
    <form id="moveTransaction" method="post">
        <div id="cancelForm" class="form-group">
            <div class="pull-left">
                <?= $this->render('../elements/reprice_filter',['from_date'=>$from_date,'to_date'=>$to_date]) ?>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="">Registration Numbers</label>
                        <textarea name="regNo" class="form-control validate length"
                                  data-validate-length-type='word'
                                  data-validate-max-length="50">
                    </textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>