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
            <span class="help-block">You can reprice using either registration Number or Invoice Number</span>
            <div class="pull-left">
                <?= $this->render('../elements/reprice_filter',['from_date'=>$from_date,'to_date'=>$to_date]) ?>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <div class="input-group input-group-sm input-group-search">
                            <label for="regNo">Registration Number</label>
                            <input id="regNo" type="text" name="regNo"
                                   class="search-box form-control"><br>
                            <label for="invoiceNo">Invoice Number</label>
                            <input id="invoiceNo" type="text" name="invoiceNo"
                                   class="search-box form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>