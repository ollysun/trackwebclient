<?php

namespace Adapter;

use Adapter\Globals;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use Adapter\Util\Response;
use yii\helpers\Json;
use Adapter\Util\Util;

/**
 * Created by PhpStorm.
 * User: Raphael Ikediashi
 * Date: 2/20/2017
 * Time: 1:43 PM
 */

class ExportedParcelTrackingAdapter extends BaseAdapter
{

    public function addTrackAssigned(array $data)
    {
        return $this->request(ServiceConstant::URL_EXPORTED_PARCEL_TRACKING, $data, self::HTTP_POST);
    }


}
