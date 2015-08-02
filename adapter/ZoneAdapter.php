<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 7/30/2015
 * Time: 8:50 PM
 */

namespace Adapter;

namespace Adapter;

use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class ZoneAdapter extends BaseAdapter
{
    public function createZone($data)
    {
        return $this->request(ServiceConstant::URL_ZONES_ADD, $data, self::HTTP_POST);
    }
    public function editZone($data)
    {
        return $this->request(ServiceConstant::URL_ZONES_EDIT, $data, self::HTTP_POST);
    }
    public function getZones()
    {
        return $this->request(ServiceConstant::URL_ZONES_GET_ALL, [], self::HTTP_GET);
    }
}