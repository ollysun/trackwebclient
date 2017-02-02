<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 2/2/2017
 * Time: 9:48 AM
 */

?>

<?php echo \Adapter\Util\Calypso::showFlashMessages(); ?>
<div class="main-box no-header">
    <div class="main-box-body">
        <form method="post">
            <div class="row">
                <div class="col-xs-12">
                    <label for="">Filter By [waybill number or reference number]</label>
                    <input type="text" name="by" value="<?= $by ?>" class="form-control">
                </div>


            </div>
            <div class="row">
                <div class="col-xs-12">
                    <label for="">Number</label>
                    <textarea name="numbers" class="form-control"><?= $numbers ?></textarea>
                </div>

            </div>

            <br><br>
            <div class="row">

                <div class="col-xs-12">
                    <input value="Validate" type="submit" class="btn btn-primary">
                </div>
            </div>
        </form>

    </div>
</div>
