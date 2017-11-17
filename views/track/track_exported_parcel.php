<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 10/14/2016
 * Time: 9:03 AM
 */
use Adapter\Util\Util;

$this->title = 'Tracking Portal';
?>


<div class="tracking-wrap">

    <div class="tracking_item">

        <div class="row">
            <div class="col-md-8 col-md-offset-2" style="background: #fff;">
                <iframe src="http://tracing.sodexi.fr/sigma/TRACKING.jspm?partner=null&search=<?=$tracking_number?>" width="100%" height="450" ></iframe>
            </div>
        </div>

    </div>

</div>
